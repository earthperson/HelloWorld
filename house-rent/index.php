<?php
error_reporting(E_ALL | E_STRICT);
date_default_timezone_set('Europe/Moscow');
mysql_connect('localhost', '', '') or die('Could not connect');
mysql_select_db('') or die('Could not select db');
require_once('Schedule.class.php');
require_once('Scale.class.php');
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01//EN"
   "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
<title>House-rent</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" href="style.css" type="text/css" />
<style type="text/css">
html, body {	
    height: 100%;
    margin: 0px;
    padding: 0px;
    background: #fff;
}

body, p, li, th, td, div, span, font {
    font-size: 10px;
    font-family: Verdana, Tahoma, Arial, Helvetica, sans-serif;
	color: #000;
}
</style>
<body>

<table cellpadding="0" cellspacing="0" border="0" style="margin: 50px;">
  <?php
  $scale = new Scale();
  $scale->setMax(20);
  $scale->setMin(0.1);
  $scale->setInterval(0.5);
  $offset = $scale->getNewOffsets(@$_GET['scale']);
  ?>
  <tr><td align="center" class="scale-control"><a href="index.php?scale=<?=$offset['plus']?>" class="plus">+</a>&nbsp;&nbsp;&nbsp;<a href="index.php?scale=<?=$offset['minus']?>" class="minus">-</a></td></tr>
  <tr><td>&nbsp;</td></tr>
  <tr>
    <td>
    <?php
        $schedule = new Schedule();
        $schedule->setLeftBound('2008-10-01');
        $schedule->setScale(@$_GET['scale']);
        $schedule->render();
    ?>
    </td>
  </tr>
</table>

</body>
</html>
