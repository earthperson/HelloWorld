<?php
/**
 * ���������� � ������������ ������ � ����� ������������� ������� ������ �����������,
 * � ������������ � ���������� �������� ������, ��������� ���������� ������ ��� ���������
 * �������� ���� �����������, ��-��������� ������������ ���-�� ��������� ������� - 10,
 * ���� ���������� ������� ������� ���-�� ������ ��������������� (��� �������� ���������������)
 * �������������� ����� ��� ������ ������� imagecolorallocate, ��� ���� ����������� ��� ��, ���
 * ��-��������� ������� ��� ����������� ����������� �������� ��� 4 ��������, ������� �������
 * ������������ ��������� ������ ����������� ��� ������� ���������� ���������.
 *
 * Copyright 2007 Ponomarev Dmitry
 * http://dmitry-ponomarev.ru
 *
 * @param int $image_width
 * @param int $image_height
 * @param array $legendarr
 * @param string $fontfile
 * @param int $font_size
 * @param int $font_angle
 * @param string $ttflineheight
 * @param int $field
 * @param int $voting_legend_graphic_span
 * @param int $voting_column_span
 * @param array $userArg
 * @param int $f
 * @return bool
 */
function voting_image(
// ������ �����������
$image_width = 250,
// ������ �����������
$image_height = 200,
// ������ �� ���������� ���������� �������
$legendarr,
// ���� � ����� � TrueType �������
$fontfile,
// ������ ������
$font_size = 10,
// ���� ������� ������
$font_angle = 0,
// ����������� ��������
$ttflineheight = 0.5,
// ������ ���� �������
$field = 5,
// ������ ������� �� �������
$voting_legend_graphic_span = 5,
// ���������� ����� ���������
$voting_column_span = 8,
// ������ �������� ������
$userArg,
// ������ ������ ��� %
$f = 2
 ) {
	// ������� �����������
	// resource imagecreate (int x_size, int y_size)
	$im = @imagecreate($image_width, $image_height);
	// ��������� ������ � ������ �������
	if(!$im) {
		error_log('������ ��� �������� �����������<br />' . "\n", 3, LOG_FILE);
		return false;
	}
	// ���������� ������������ �����
	// int imagecolorallocate (resource image, int red, int green, int blue)
	// ���� ����
	$colorBG = imagecolorallocate($im, 0xD6, 0xEB, 0xF7);
	// ���� ���� � ������ ��������
	$colorAXIS = imagecolorallocate($im, 0x00, 0x00, 0x00);
	// ���� ��������� / ������ ������� 
	$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 220, 101, 29);
	$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 189, 24, 51);
	$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 98, 1, 96);
	$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 0, 0, 0x8B);
	//$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 0, 255, 0);
	//$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 255, 0, 0);
	//$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 0X8A, 0X2B, 0XE2);
	//$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 0XFF, 0XD7, 0x00);
	//$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 0XAF, 0XEE, 0XEE);
	//$colorCOL[] = $colorLEGEND[] = imagecolorallocate($im, 0XA0, 0X52, 0X2D);
	// ���������, ��� ������ �������
	if(count($colorLEGEND) < count($userArg)) {
		$userArg = array_slice($userArg, 0, count($colorLEGEND));
	}
	// ���������, ��� ������ �������
	if(count($colorCOL) < count($userArg)) {
		$userArg = array_slice($userArg, 0, count($colorLEGEND));
	}
	// ���������� ���-�� �������� ������
	$dataCOUNT = count($userArg);
	// ���������� ���-�� ����� � �������
	$legendCOUNT = count($legendarr);
	// ������� �� ����� �����, ���� ����������
	if ($dataCOUNT > $legendCOUNT)
	    $userArg = array_slice($userArg, 0, $legendCOUNT);
	if ($legendCOUNT > $dataCOUNT)
	    $legendarr = array_slice($legendarr, 0, $dataCOUNT);
	// ���� ���� ������� ������������� ��������
	for($i=0; $i<count($userArg); $i++) {
		$userArg[$i] = abs($userArg[$i]);
	}
	// ��������� ������������ �������� �� ��������� ������, ��������� ����
	// ����������� ������ � ���� ����� �������
	$dataMAX = $userArg[0];
	$index_data_MAX = 0;
	for($i=0; $i<count($userArg); $i++) {
		if($userArg[$i] > $dataMAX) {
			$dataMAX = $userArg[$i];
			$index_data_MAX = $i;
		}
	}
	// ������ ����� ���� ��������� ��������
	$sum = 0;
	for($i=0; $i<count($userArg); $i++) {
		$sum += $userArg[$i];		
	}
	// ��������� ���� ������� �������� � ���������
	if($sum != 0) {
		for($i=0; $i<count($userArg); $i++) {
			$COL[$i] = round(($userArg[$i] / $sum), 4);
		}
	}
	// ��������� ����� �������, ����������� �� ���� � ��������� ������������� �� ��������� ������
	// � �������� ������
	if(isset($COL))
	    $descriptionWidth = imagefontwidth($f) * (strlen((string)$COL[$index_data_MAX]) + strlen('% ()') + strlen($userArg[$index_data_MAX]));
	// �������� ���
	// int imagefill (resource image, int x, int y, int col)
	imagefill($im, 0, 0, $colorBG);
	
	// ������ ������ �������� �������
	// ������ �� ����� ���������� �� ����������� � ������� ������� imagettftext, ��� ���� �����������
	// ������������ ���� ���� ������ TrueType. ��� ����������� ������ ���������, ������ ��������� �� UTF-8,
	// ��� �� ��������� � ������������ � �������.
	for($i=0; $i<sizeof($legendarr); $i++) {
		$legendarr[$i] = iconv('Windows-1251', 'UTF-8', $legendarr[$i]);
	}
	// ��� ����, ����� ������ ���������� ������ �������� ���� ����� ������� ���������� ����� ������ ����� �����
	// �������� ������� �� ������� ���� �����������, ������ �������� ��������� ������������ �������, � ������
	// ����� ������������� ��� (������ ������  * ���������� ������� + (����������� �������� * ���������� ������� - 1)
	// ���������� ������ ������ ��������� ������� array imagettfbbox (int size, int angle, string fontfile, string text)
	/* ������� imagettfbbox ���������� ��� ������ �� ������ ���������,
     ���������� ������������ ���������� ������������ ��������������,
     � ������� ����� ������� ������ �����. ������� �������
     ������ ���������� �� ����� � ���� ��������� (x,y):

     (6,7)           (4,5)
       +---------------+
       |���� ������! :)|
       +---------------+
     (0,1)           (2,3)

     ����� ��������� ������� ����� �� ������ ������ ���������� ����������,
     �� �� ������� �������� � ����������� ������ ������ ��� ������������
     �����.

     �� ���� ����� ����� ��������� ������ � ������ ������:
     $height = $coord[1] - $coord[7];
     $width = $coord[2] - $coord[0];
  */
	#imagettfbbox(
	#  FONT_SIZE,			// ������ ������
	#  0,					// ���� ������� ������
	#  FILE_NAME,			// ��� ttf-�����
	#  text					// ���������� ������	
	#);
	// ������ � ������� ����� ������� ������, ��������� ����
	// ����������� ����� ������� ������ ������
	$legendarrMAXLENGTH = strlen($legendarr[0]);
	$index_MAX = 0;
	for($i=0; $i<count($legendarr); $i++) {
		if (strlen($legendarr[$i]) > $legendarrMAXLENGTH) {
			$legendarrMAXLENGTH = strlen($legendarr[$i]);
			$index_MAX = $i;
		}
	}
	$coordTTF = imagettfbbox($font_size, $font_angle, $fontfile, $legendarr[$index_MAX]);
	$heightTTF = $coordTTF[1] - $coordTTF[7];
	//$widthTTF = $coordTTF[2] - $coordTTF[0];
	#imagettftext(
    #		$im,				// ������������� �������
    #		FONT_SIZE,			// ������ ������
    #		0,					// ���� ������� ������
    #		$X, $Y,				// ���������� (x,y), ��������������� ������ �������
    #							// ���� ������� �������
    #		$color,			 	// ���� ������
    #		FILE_NAME,			// ��� ttf-�����
    #		$text				// �����
    #		);
    // ������ ����� = (������ ������  * ���������� ������� + (����������� �������� * ���������� ������� - 1)
    $heightBLOCK = $heightTTF * count($legendarr) + ceil($heightTTF * $ttflineheight) * (count($legendarr) - 1);
    for($i=0; $i<count($legendarr); $i++) {
    	// ������ ������
    	imagettftext($im, $font_size, $font_angle,
    	// ���������� X
    	$field + $heightTTF + 5,
    	// ���������� Y
    	($image_height - $heightBLOCK - $field) + ($heightTTF * ($i + 1) + ceil($heightTTF * $ttflineheight) * $i),
    	$colorLEGEND[$i], $fontfile, $legendarr[$i]);
    	// ������ ������� ��������
    	imagefilledrectangle($im,
    	$field + 2,
    	($image_height - $heightBLOCK - $field) + ceil($heightTTF * $ttflineheight) * $i + $heightTTF * $i + 2,
    	$field + $heightTTF - 2,
    	($image_height - $heightBLOCK - $field) + ($heightTTF * ($i + 1) + ceil($heightTTF * $ttflineheight) * $i) - 2,
    	$colorLEGEND[$i]);    	
    }
    // ������ ���
    // ��� X
    imageline($im,
    $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span,
    $image_width - $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span, $colorAXIS);
    // ��� Y
    imageline($im, $field, $field, $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span, $colorAXIS);
    // ������ ������� �� ����
    //int imagepolygon (resource image, array points, int num_points, int col)
    // ������� �� ��� Y
    $arrow_top_array = array(
    $field, $field,
    $field-3, $field + 3,
    $field+3, $field + 3);
    imagefilledpolygon($im, $arrow_top_array, 3, $colorAXIS);
    // ������� �� ��� X
    $arrow_right_array = array(
    $image_width - $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span,
    $image_width - $field - 3, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span - 3,
    $image_width - $field - 3, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span + 3);
    imagefilledpolygon($im, $arrow_right_array, 3, $colorAXIS);
    // ������ ��������,
    // �� ������ = (������ ��� Y - ���������� ����� ���� * (���������� �������� + 1)) / ���������� ��������
    // �� ������ - ������ ���������� ������� ��������
    // ������ ��� Y
    $axisYheight = $image_height - $heightBLOCK - $field * 2 - $voting_legend_graphic_span - 3;
    // ������ ���������
    if ($axisYheight > ( $voting_column_span * (count($userArg) + 1) ))
        $ColumnHeight = ceil(($axisYheight - $voting_column_span * (count($userArg) + 1)) / count($userArg));
    else {
    	$voting_column_span = 5;
    	$ColumnHeight = ceil(($axisYheight - $voting_column_span * (count($userArg) + 1)) / count($userArg));
    }
    // ���������� X0
    $X0 = $field + 1;
    // ���������� Y0
    $Y0 = $field + 3;
    // ������ �������� � ��������������� ������� ������
    // ��������� ����� �������, �������� ������������� ������������ ��������
    if ($dataMAX != 0) {
    $ColumnWidth = $image_width - $field * 2 - 1 - 3 - $descriptionWidth - 7;     
    for($i = 0; $i < count($userArg); $i++) {    	
    	imagefilledrectangle($im,
    	$X0, $Y0 + $voting_column_span * ($i + 1) + $ColumnHeight * $i, $X0 + floor(($userArg[$i] * $ColumnWidth) / $dataMAX),
    	$Y0  + $voting_column_span * ($i + 1) + $ColumnHeight * ($i + 1),
    	$colorCOL[$i]);
    	// int imagestring (resource image, int font, int x, int y, string s, int col)
    	imagestring($im, $f,
    	$X0 + ($userArg[$i] * $ColumnWidth) / $dataMAX + 5, $Y0 + $voting_column_span * ($i + 1) + $ColumnHeight * $i,
    	((string)($COL[$i] * 100)) . '%' . ' (' . $userArg[$i] . ')', $colorCOL[$i]);
    }
    }
    //imagerectangle($im, 0, 0, $image_width-1, $image_height-1, $colorAXIS);
    // +--------- ������ �� ����������� -----------------------------------------------------------+
    // Always modified
    header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
    // Date in the past
    header("Expires: " . gmdate("D, d M Y H:i:s") . " GMT");
    // HTTP/1.1
    header("Cache-Control: no-store, no-cache, must-revalidate ");
    header("Cache-Control: post-check=0, pre-check=0", false);
    // HTTP/1.0
    header("Pragma: no-cache");
    // +-------------------------------------------------------------------------------------------+
	header('Content-type: image/png');
	// ������ ����c�������� �����
	imageinterlace($im, 1);
	// ������ ���� ���� ����������, ���� �� �����
	imagecolortransparent($im, $colorBG);
	imagepng($im);
	// ����������� ������, ��������������� � ������������
	imagedestroy($im);
	return true;
}

// +-------------------------------------------------------------------------------------------+
// | ������������� ���������� � ����� ������� ��������� �����������                            |
// +-------------------------------------------------------------------------------------------+

require_once 'config.php';
require_once '../config.php';

// ������ ����� �����������
$userArgString = @file_get_contents('../../' . VOTING_DATA) or $userArgString = '0';
// ��������� ������� ��������
$userArg = explode('|', $userArgString);
if (count($userArg) < count($legendarr)) $userArg = array_fill(0, count($legendarr), 0);
voting_image(
// ������ ����������� ����������� �����������
VOTING_IMG_WIDTH,
// ������ ����������� ����������� �����������
VOTING_IMG_HEIGHT,
$legendarr,
// ���� � ����� � TrueType �������
VOTING_TTF_PATH,
// ������ ������
VOTING_TTF_SIZE,
// ���� ������� ������
VOTING_TTF_ANGLE,
// ����������� ��������
VOTING_TTF_LINEHEIGHT,
// ������ ���� �������
VOTING_FIELD,
// ������ ������� �� �������
VOTING_LEGEND_GRAPHIC_SPAN,
// ���������� ����� ���������
VOTING_COLUMN_SPAN,
$userArg,
// ������ ������ ��� %
VOTING_PERCENT_SIZE);
// +-------------------------------------------------------------------------------------------+
?>