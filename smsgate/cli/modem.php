<?php
/**
 * Manufacturer : SIEMENS
 * Model        : MC35i
 * Product name : MC35i
 *
 * @version 1.0.0
 * @author Dmitry Ponomarev <ponomarev.base@gmail.com>
 */
class Modem {

	private $_fp;
	
	private $_ok = false;
	
	private $_delay = 200000;

	public function __construct($manufacturer = null) {
		if(!defined('COMPORT')) {
			define('COMPORT', '/dev/ttyS0');
		}
		$this->_init($manufacturer);
	}

	public function __destruct() {
		sleep(1);
		fclose($this->_fp);
	}

	private function _init($manufacturer) {
		// Open com port
		$this->_fp = fopen(COMPORT, 'w+b');

		if($this->_fp) {
			stream_set_blocking($this->_fp, 0);
			stream_set_timeout($this->_fp, 0, $this->_delay);
			stream_set_write_buffer($this->_fp, 0);
		}
		else {
			throw new Exception("Init: could not open com port\n");
		}

		if($manufacturer) {
			if($this->_streamPutContents("AT&F\r") === false) {
				throw new Exception("Init: modem initialization failed\n");
			}
		}
		else {
			$this->_ping();
		}

		if($this->_streamPutContents("ATE0\r") === false) {
			throw new Exception("Init: modem initialization failed\n");
		}
	}
	
	private function _ping() {
		for($tries = 0; $tries < 3; $tries++) {
			if($this->_streamPutContents("AT\r", 3) === false) {
				continue;
			}
			if(strpos($this->_streamGetContents(), 'OK') !== false) {
				$this->_ok = true;
			}
		}
		if(!$this->_ok) {
			throw new Exception("Init: modem is bad\n");
		}
	}

	private function _streamPutContents($data) {
		usleep($this->_delay);
		return fwrite($this->_fp, $data);
	}

	private function _streamGetContents($result_code = null) {
		if($result_code !== null) {
			while(strpos($buffer = fgets($this->_fp), $result_code) === false) {}
		}
		usleep($this->_delay);
		return stream_get_contents($this->_fp);
	}

	public function check() {
		if(!$this->_ok) {
			throw new Exception("Init: modem is bad\n");
		}
		else {
			return "OK\n";
		}
	}

	public function at($at) {
		$this->_streamPutContents($at."\r");
		return $this->_streamGetContents() . "\n";
	}

	public function ussd($command) {
		$command = trim($command);
		if(empty($command)) {
			throw new ArgumentsException('Ussd: empty command');
		}
		$this->_streamPutContents("AT+CUSD=1,".$command.",15\r");
		preg_match("/[\"'](.*)[\"']/sm",  $this->_streamGetContents('+CUSD'), $matches);
		return (isset($matches[1]) ? $matches[1] : '') . "\n";
	}

	public function sms($da, $text, $flash = false) {
		$da = trim($da, '+');
		$text = trim($text);
		if(!preg_match('/^\d+/', $da)) {
			throw new ArgumentsException('Sms: incorrect destination address');
		}
		if(empty($text)) {
			throw new ArgumentsException('Sms: empty text');
		}
		$da_len = sprintf('%02X', strlen($da));
		while(strlen($da) < 12) {
			$da .= 'F';
		}
		$da_rev = '';
		for($i = 0; $i < strlen($da); $i+=2) {
			$da_rev .= $da[$i+1] . $da[$i];
		}
		$udata = '';
		foreach($this->utf8ToUnicode($text) as $ord) {
			$udata .= sprintf('%04X', $ord);
		}
		$dcs = $flash ? '18' : '08';
		$pdu = '001100' .  $da_len . '91' . $da_rev . '00' . $dcs . 'AA' . sprintf('%02X', strlen($udata) / 2) . $udata;
		$this->_streamPutContents("AT+CMGF=0\r");
		$this->_streamPutContents("AT+CSMS=0\r");
		$this->_streamPutContents("AT^SM20=1,0\r");
		$this->_streamPutContents("AT+CMGS=" . ((strlen($pdu) / 2) - 1) . "\r");
		$this->_streamGetContents('>');
		$this->_streamPutContents($pdu . "\x1A");
		
		if(strpos($this->_streamGetContents(), 'ERROR') !== false) {
			throw new Exception();
		}
	}

	/**
	 * Takes an UTF-8 string and returns an array of ints representing the
	 * Unicode characters. Astral planes are supported ie. the ints in the
	 * output can be > 0xFFFF. Occurrances of the BOM are ignored. Surrogates
	 * are not allowed.
	 *
	 * Returns false if the input string isn't a valid UTF-8 octet sequence.
	 */
	private function utf8ToUnicode(&$str)
	{
		$mState = 0;     // cached expected number of octets after the current octet
		// until the beginning of the next UTF8 character sequence
		$mUcs4  = 0;     // cached Unicode character
		$mBytes = 1;     // cached expected number of octets in the current sequence

		$out = array();

		$len = strlen($str);
		for($i = 0; $i < $len; $i++) {
			$in = ord($str{$i});
			if (0 == $mState) {
				// When mState is zero we expect either a US-ASCII character or a
				// multi-octet sequence.
				if (0 == (0x80 & ($in))) {
					// US-ASCII, pass straight through.
					$out[] = $in;
					$mBytes = 1;
				} else if (0xC0 == (0xE0 & ($in))) {
					// First octet of 2 octet sequence
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x1F) << 6;
					$mState = 1;
					$mBytes = 2;
				} else if (0xE0 == (0xF0 & ($in))) {
					// First octet of 3 octet sequence
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x0F) << 12;
					$mState = 2;
					$mBytes = 3;
				} else if (0xF0 == (0xF8 & ($in))) {
					// First octet of 4 octet sequence
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x07) << 18;
					$mState = 3;
					$mBytes = 4;
				} else if (0xF8 == (0xFC & ($in))) {
					/* First octet of 5 octet sequence.
					 *
					 * This is illegal because the encoded codepoint must be either
					 * (a) not the shortest form or
					 * (b) outside the Unicode range of 0-0x10FFFF.
					 * Rather than trying to resynchronize, we will carry on until the end
					 * of the sequence and let the later error handling code catch it.
					 */
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 0x03) << 24;
					$mState = 4;
					$mBytes = 5;
				} else if (0xFC == (0xFE & ($in))) {
					// First octet of 6 octet sequence, see comments for 5 octet sequence.
					$mUcs4 = ($in);
					$mUcs4 = ($mUcs4 & 1) << 30;
					$mState = 5;
					$mBytes = 6;
				} else {
					/* Current octet is neither in the US-ASCII range nor a legal first
					 * octet of a multi-octet sequence.
					 */
					return false;
				}
			} else {
				// When mState is non-zero, we expect a continuation of the multi-octet
				// sequence
				if (0x80 == (0xC0 & ($in))) {
					// Legal continuation.
					$shift = ($mState - 1) * 6;
					$tmp = $in;
					$tmp = ($tmp & 0x0000003F) << $shift;
					$mUcs4 |= $tmp;

					if (0 == --$mState) {
						/* End of the multi-octet sequence. mUcs4 now contains the final
						 * Unicode codepoint to be output
						 *
						 * Check for illegal sequences and codepoints.
						 */

						// From Unicode 3.1, non-shortest form is illegal
						if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
						((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
						((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
						(4 < $mBytes) ||
						// From Unicode 3.2, surrogate characters are illegal
						(($mUcs4 & 0xFFFFF800) == 0xD800) ||
						// Codepoints outside the Unicode range are illegal
						($mUcs4 > 0x10FFFF)) {
							return false;
						}
						if (0xFEFF != $mUcs4) {
							// BOM is legal but we don't want to output it
							$out[] = $mUcs4;
						}
						//initialize UTF8 cache
						$mState = 0;
						$mUcs4  = 0;
						$mBytes = 1;
					}
				} else {
					/* ((0xC0 & (*in) != 0x80) && (mState != 0))
					 *
					 * Incomplete multi-octet sequence.
					 */
					return false;
				}
			}
		}
		return $out;
	}
}
