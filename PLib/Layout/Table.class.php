<?php
require_once 'EM/emulator.lib.php';

class PLib_Layout_Table {
	protected $css_class;
	protected $width;
	protected $mode;
	protected $cells;
	
	public function __construct($css_class = '', $width = '100%') {
		$this->css_class = (string)$css_class;
		$this->width = (string)$width;
		print "<table
			     cellpadding='10'
			     cellspacing='0'
			     border='0'
			     class='" . (empty($this->css_class) ? '' : " {$this->css_class}") . "'
			     " . (empty($this->width) ? '' : " width='{$this->width}'"). '>';
	}
	
	public function header($cells) {
		$this->mode = 'th';
		$this->cells = $cells;
		$this->iterator();
	}
	
	public function row($cells) {
		$this->mode = 'td';
		$this->cells = $cells;
		$this->iterator();
	}
	
	public function footer() {
		
	}
	
	public function th($html = '', $plain_text = true, $attributes = '') {
		$this->mode = 'th';
		$this->cells[] = array('html'=>($plain_text ? plain_text($html) : $html), 'attributes'=>$attributes);
	}
	
	public function td($html = '', $attributes = '', $plain_text = false) {
		if(empty($html)) {
			$html = '-'; 
		}
		$this->mode = 'td';
		$this->cells[] = array('html'=>($plain_text ? br_plain_text($html) : $html), 'attributes'=>$attributes);
	}
	
	public function endRow() {
		$this->iterator();
		$this->cells = null;
		$this->mode = null;
	}
	
	public function end() {
		print "</table>";
	}	
	
	protected function iterator() {
		$cnt = count($this->cells);
		if($cnt > 0) {
			print '<tr>';
			for($i = 0; $i < $cnt; $i++) {
				print "<{$this->mode} valign='top'" . $this->attributes($this->cells[$i]) . ">{$this->cells[$i]['html']}</{$this->mode}>";
			}
			print '</tr>';
		}
	}
	
	protected function attributes($cell) {
		return empty($cell['attributes']) ? '' : " {$cell['attributes']}";
	}
}
?>