<?php
session_start();
$captchaB = (string)mt_rand(20, 99);
$_SESSION['spamB'] = md5($captchaB);
$im = imagecreate(20,20);
// Регистрируем цвета
$background = imagecolorallocate($im, 98, 1, 96);
$c = imagecolorallocate($im, 0xFF, 0xFF, 0xFF);
imagefill($im, 0, 0, $background);
imagestring($im, 4, 2, 2, $captchaB, $c);
header('Content-type: image/jpeg');
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
// задаем череcстрочный режим
imageinterlace($im, 1);
imagejpeg($im);
// освобождаем память, ассоциированную с изображением
imagedestroy($im);
?>