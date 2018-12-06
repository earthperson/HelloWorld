htmlColor() ��������� ����� ���������� imagecolorallocate, �� �������� � ���� '#ffffff'
<?php
/**
 * ��������� ����� ���������� imagecolorallocate, �� �������� � ���� '#ffffff'
 *
 * @param resource $im
 * @param string $color
 * @return mixed
 */
function htmlColor($im, $color) {
	$red = $green = $blue = 0;
	sscanf($color, '#%2x%2x%2x',  $red, $green, $blue);
	return imagecolorallocate($im, $red, $green, $blue);
}
?>