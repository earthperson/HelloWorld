<?php
/**
 * Устойчивая к неправильным данным и легко настраиваемая функция рисует гистограмму,
 * в соответствии с полученным массивом данных, остальные переменные служат для настройки
 * внешнего вида гистограммы, по-умолчанию максимальное кол-во элементов массива - 10,
 * если необходимо вводить большее кол-во просто зарегистрируйте (или наоборот закомментируйте)
 * дополнительные цвета при помощи функции imagecolorallocate, при этом незабывайте про то, что
 * по-умолчанию внешний вид выдаваемого изображения настроен под 4 элемента, другими словами
 * незабываейте увеличить размер изображения при большем количестве столбиков.
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
// Ширина изображения
$image_width = 250,
// Высота изображения
$image_height = 200,
// Массив со строковыми значениями легенды
$legendarr,
// Путь к файлу с TrueType шрифтом
$fontfile,
// Размер шрифта
$font_size = 10,
// Угол наклона шрифта
$font_angle = 0,
// Межстрочный интервал
$ttflineheight = 0.5,
// Размер поля отступа
$field = 5,
// Отступ графика от легенды
$voting_legend_graphic_span = 5,
// Расстояние между столбцами
$voting_column_span = 8,
// Массив вводимых данных
$userArg,
// Размер шрифта для %
$f = 2
 ) {
	// Создаем изображение
	// resource imagecreate (int x_size, int y_size)
	$im = @imagecreate($image_width, $image_height);
	// обработка ошибки в случае неудачи
	if(!$im) {
		error_log('Ошибка при создании изображения<br />' . "\n", 3, LOG_FILE);
		return false;
	}
	// Определяем используемые цвета
	// int imagecolorallocate (resource image, int red, int green, int blue)
	// Цвет фона
	$colorBG = imagecolorallocate($im, 0xD6, 0xEB, 0xF7);
	// Цвет осей и номера столбика
	$colorAXIS = imagecolorallocate($im, 0x00, 0x00, 0x00);
	// Цвет столбиков / текста легенды 
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
	// проверяем, что цветов хватает
	if(count($colorLEGEND) < count($userArg)) {
		$userArg = array_slice($userArg, 0, count($colorLEGEND));
	}
	// проверяем, что цветов хватает
	if(count($colorCOL) < count($userArg)) {
		$userArg = array_slice($userArg, 0, count($colorLEGEND));
	}
	// подсчитаем кол-во введнных данных
	$dataCOUNT = count($userArg);
	// подсчитаем кол-во строк в легенде
	$legendCOUNT = count($legendarr);
	// сделаем их одной длины, если отличаются
	if ($dataCOUNT > $legendCOUNT)
	    $userArg = array_slice($userArg, 0, $legendCOUNT);
	if ($legendCOUNT > $dataCOUNT)
	    $legendarr = array_slice($legendarr, 0, $dataCOUNT);
	// если были введены отрицательные значения
	for($i=0; $i<count($userArg); $i++) {
		$userArg[$i] = abs($userArg[$i]);
	}
	// определим максимальное значение из введенных данных, используя цикл
	// предположим первое и есть самое большое
	$dataMAX = $userArg[0];
	$index_data_MAX = 0;
	for($i=0; $i<count($userArg); $i++) {
		if($userArg[$i] > $dataMAX) {
			$dataMAX = $userArg[$i];
			$index_data_MAX = $i;
		}
	}
	// найдем сумму всех введенных значений
	$sum = 0;
	for($i=0; $i<count($userArg); $i++) {
		$sum += $userArg[$i];		
	}
	// определим долю каждого значения в процентах
	if($sum != 0) {
		for($i=0; $i<count($userArg); $i++) {
			$COL[$i] = round(($userArg[$i] / $sum), 4);
		}
	}
	// определим длину надписи, указывающей на долю в процентах максимального из введенных данных
	// в масштабе шрифта
	if(isset($COL))
	    $descriptionWidth = imagefontwidth($f) * (strlen((string)$COL[$index_data_MAX]) + strlen('% ()') + strlen($userArg[$index_data_MAX]));
	// Заливаем фон
	// int imagefill (resource image, int x, int y, int col)
	imagefill($im, 0, 0, $colorBG);
	
	// Теперь начнем рисовать легенду
	// Строки мы будем записывать на изображение с помощью функции imagettftext, что дает возможность
	// использовать свой файл шрифта TrueType. Для возможности вывода кириллицы, сменим кодировку на UTF-8,
	// как то требуется в документации к функции.
	for($i=0; $i<sizeof($legendarr); $i++) {
		$legendarr[$i] = iconv('Windows-1251', 'UTF-8', $legendarr[$i]);
	}
	// Для того, чтобы узнать координату левого верхнего угла блока легенды необходимо знать высоту этого блока
	// величину отступа от нижнего края изображения, отступ задается значением передаваемым функции, а высота
	// блока расчитывается как (высота текста  * количество строчек + (межстрочный интервал * количество строчек - 1)
	// Рассчитаем высоту текста используя функцию array imagettfbbox (int size, int angle, string fontfile, string text)
	/* Функция imagettfbbox возвращает нам массив из восьми элементов,
     содержащий всевозможные координаты минимального прямоугольника,
     в который можно вписать данный текст. Индексы массива
     удобно обозначить на схеме в виде координат (x,y):

     (6,7)           (4,5)
       +---------------+
       |Всем привет! :)|
       +---------------+
     (0,1)           (2,3)

     Число элементов массива может на первый взгляд показаться избыточным,
     но не следует забывать о возможности вывода текста под произвольным
     углом.

     По этой схеме легко вычислить ширину и высоту текста:
     $height = $coord[1] - $coord[7];
     $width = $coord[2] - $coord[0];
  */
	#imagettfbbox(
	#  FONT_SIZE,			// размер шрифта
	#  0,					// угол наклона шрифта
	#  FILE_NAME,			// имя ttf-файла
	#  text					// измеряемая строка	
	#);
	// найдем в массиве самую длинную строку, используя цикл
	// предположим самая длинная строка первая
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
    #		$im,				// идентификатор ресурса
    #		FONT_SIZE,			// размер шрифта
    #		0,					// угол наклона шрифта
    #		$X, $Y,				// координаты (x,y), соответствующие левому нижнему
    #							// углу первого символа
    #		$color,			 	// цвет шрифта
    #		FILE_NAME,			// имя ttf-файла
    #		$text				// текст
    #		);
    // высота блока = (высота текста  * количество строчек + (межстрочный интервал * количество строчек - 1)
    $heightBLOCK = $heightTTF * count($legendarr) + ceil($heightTTF * $ttflineheight) * (count($legendarr) - 1);
    for($i=0; $i<count($legendarr); $i++) {
    	// рисуем строки
    	imagettftext($im, $font_size, $font_angle,
    	// координата X
    	$field + $heightTTF + 5,
    	// координата Y
    	($image_height - $heightBLOCK - $field) + ($heightTTF * ($i + 1) + ceil($heightTTF * $ttflineheight) * $i),
    	$colorLEGEND[$i], $fontfile, $legendarr[$i]);
    	// рисуем цветные квадраты
    	imagefilledrectangle($im,
    	$field + 2,
    	($image_height - $heightBLOCK - $field) + ceil($heightTTF * $ttflineheight) * $i + $heightTTF * $i + 2,
    	$field + $heightTTF - 2,
    	($image_height - $heightBLOCK - $field) + ($heightTTF * ($i + 1) + ceil($heightTTF * $ttflineheight) * $i) - 2,
    	$colorLEGEND[$i]);    	
    }
    // рисуем оси
    // ось X
    imageline($im,
    $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span,
    $image_width - $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span, $colorAXIS);
    // ось Y
    imageline($im, $field, $field, $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span, $colorAXIS);
    // рисуем стрелки на осях
    //int imagepolygon (resource image, array points, int num_points, int col)
    // стрелка на оси Y
    $arrow_top_array = array(
    $field, $field,
    $field-3, $field + 3,
    $field+3, $field + 3);
    imagefilledpolygon($im, $arrow_top_array, 3, $colorAXIS);
    // стрелка на оси X
    $arrow_right_array = array(
    $image_width - $field, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span,
    $image_width - $field - 3, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span - 3,
    $image_width - $field - 3, $image_height - $heightBLOCK - $field - $voting_legend_graphic_span + 3);
    imagefilledpolygon($im, $arrow_right_array, 3, $colorAXIS);
    // рисуем столбики,
    // их высота = (высота оси Y - расстояние между ними * (количество столбцов + 1)) / количество столбцов
    // их ширина - массив переданных функции значений
    // высота оси Y
    $axisYheight = $image_height - $heightBLOCK - $field * 2 - $voting_legend_graphic_span - 3;
    // высота столбиков
    if ($axisYheight > ( $voting_column_span * (count($userArg) + 1) ))
        $ColumnHeight = ceil(($axisYheight - $voting_column_span * (count($userArg) + 1)) / count($userArg));
    else {
    	$voting_column_span = 5;
    	$ColumnHeight = ceil(($axisYheight - $voting_column_span * (count($userArg) + 1)) / count($userArg));
    }
    // координата X0
    $X0 = $field + 1;
    // координата Y0
    $Y0 = $field + 3;
    // рисуем столбики и соответствующие строчки оценки
    // определим длину столбца, которому соответствует максимальное значение
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
    // +--------- Запрет на кэширование -----------------------------------------------------------+
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
	// задаем череcстрочный режим
	imageinterlace($im, 1);
	// делаем цвет фона прозрачным, если он задан
	imagecolortransparent($im, $colorBG);
	imagepng($im);
	// освобождаем память, ассоциированную с изображением
	imagedestroy($im);
	return true;
}

// +-------------------------------------------------------------------------------------------+
// | Инициализация переменных и вызов функции рисования изображения                            |
// +-------------------------------------------------------------------------------------------+

require_once 'config.php';
require_once '../config.php';

// Чтение файла голосования
$userArgString = @file_get_contents('../../' . VOTING_DATA) or $userArgString = '0';
// Получение массива значений
$userArg = explode('|', $userArgString);
if (count($userArg) < count($legendarr)) $userArg = array_fill(0, count($legendarr), 0);
voting_image(
// Ширина изображения гистограммы голосования
VOTING_IMG_WIDTH,
// Высота изображения гистограммы голосования
VOTING_IMG_HEIGHT,
$legendarr,
// Путь к файлу с TrueType шрифтом
VOTING_TTF_PATH,
// Размер шрифта
VOTING_TTF_SIZE,
// Угол наклона шрифта
VOTING_TTF_ANGLE,
// Межстрочный интервал
VOTING_TTF_LINEHEIGHT,
// Размер поля отступа
VOTING_FIELD,
// Отступ графика от легенды
VOTING_LEGEND_GRAPHIC_SPAN,
// Расстояние между столбцами
VOTING_COLUMN_SPAN,
$userArg,
// Размер шрифта для %
VOTING_PERCENT_SIZE);
// +-------------------------------------------------------------------------------------------+
?>