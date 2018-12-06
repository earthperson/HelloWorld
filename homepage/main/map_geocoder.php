<?php
$query = urlencode(iconv('windows-1251', 'utf-8', 'Санкт-Петербург, Варшавская ул., 40'));
$key = 'ABQIAAAARVX9GWAQfeAbMRKpg38tgBQFKq6KJXeqPzztW-ljyG2taL5EwhQ-d1EKv6N4vGjoeKmdl1pTnz41UA';
$output = 'csv';
$fp = fsockopen('maps.google.com', 80, $errno, $errstr);
if(!$fp) {
	print $errstr;
}
else {
	$out = "GET http://maps.google.com/maps/geo?q=" . $query . "&key=" . $key . "&output=" . $output . " HTTP/1.1\r\n";
	$out .= "Host: maps.google.com\r\n";
	$out .= "Connection: close\r\n\r\n";
	fwrite($fp, $out);
	while (!feof($fp)) {
		$in .= fgets($fp, 128);
	}
	fclose($fp);
}
print $in;
?>