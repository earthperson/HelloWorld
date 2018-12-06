<?php
function journal_form($name, $msg) {
	$str = '
<div class="journalform">
<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="journalform" id="journalform" onsubmit="if(engineCommonLoad) {return validate(this.name, \'journalerror\');}">
<div><label for="name">Имя<sup style="color: maroon;">*</sup>:</label><input type="text" name="name" id="name" size="30" maxlength="30" value="' . $name . '" class="input" style="width:250px;" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="1" /></div>
<div class="journalnumchars" id="journalnumcharsdiv">Введено: <span id="journalnumcharsid" style="color: black;">&nbsp;</span> Максимум: <span style="color: black;">' . JOURNAL_MAX_MSG_LENGTH . '</span></div>
<div><label for="msg">Заметка:</label><textarea name="msg" id="msg" rows="5" cols="30" class="input" style="width:250px;" onfocus="this.style.borderColor = \'#3300cc\'; document.getElementById(\'journalnumcharsdiv\').style.display = \'block\';document.getElementById(\'journalCAPTCHA\').style.display = \'block\';" onblur="this.style.borderColor = \'black\';" tabindex="2">' . $msg .'</textarea></div>
<div id="journalCAPTCHA" style="display: none;">
<div style="padding-left: 70px;">A = <img src="/engine/main/captcha_image1.php?no_buffer=' . md5(uniqid(rand(),true)) . '" alt="" width="20" height="20" /> B = <img src="/engine/main/captcha_image2.php?no_buffer=' . md5(uniqid(rand(), true)) . '" alt="" width="20" height="20" /></div>
<label for="spam">Антиспам:</label><input name="spam" id="spam" class="input" style="width:250px;" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="3" value="Введите A и B через запятую" /></div>
<div><input type="reset" value="Стереть" class="input" onclick="document.journalform.name.focus();" tabindex="5" />&nbsp;&nbsp;&nbsp;<input type="submit" name="journal_submit" id="journal_submit" value="Записать" class="input" tabindex="4" onfocus="document.getElementById(\'journalnumcharsdiv\').style.display = \'block\';" /></div>
</form>
</div>' . "\n";
	return $str;	
}

function description_form($text = '') {
	$str = '
<div class="descriptionform">
<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="descriptionform" id="descriptionform">
<div><label for="description">Description:</label><textarea name="description" id="description" rows="10" cols="40" class="input" style="left: 95px;" tabindex="21" onfocus="this.style.borderColor=\'#3300cc\';" onblur="this.style.borderColor=\'black\';" />' . $text . '</textarea></div>
<div><input type="submit" name="description_submit" value="Edit" class="input" style="left: 95px;" tabindex="22" /></div>
</form>
</div>' . "\n";
	return $str;	
}

function news_form() {
	$str = '
<div class="newsform">
<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="newsform" id="newsform">
<div><label for="news">News:</label><textarea name="news" id="news" rows="10" cols="40" class="input" tabindex="31" onfocus="this.style.borderColor=\'#3300cc\';" onblur="this.style.borderColor=\'black\';" /></textarea></div>
<div><input type="reset" value="Reset" class="input" tabindex="33" />&nbsp;&nbsp;&nbsp;<input type="submit" name="news_submit" value="Add" class="input" tabindex="32" /></div>
</form>
</div>' . "\n";
	return $str;	
}

function logos_form() {
	$str = '
	<br />
<div class="logosform">
<form action="/engine/main/a_logo.php" method="post">
<div><label for="reference">Reference: </label><input type="text" class="input" name="reference" id="reference" style="width: 70%;" onfocus="this.style.borderColor=\'#3300cc\';" onblur="this.style.borderColor=\'black\';" tabindex="11" /></div>
<div><label for="logo">Logotype:</label><input type="text" class="input" name="logo" id="logo" style="width: 70%;" onfocus="this.style.borderColor=\'#3300cc\';" onblur="this.style.borderColor=\'black\';" tabindex="12" /></div>
<div><label for="description_logo">Description:</label><input type="text" class="input" name="description_logo" id="description_logo" style="width: 70%;" onfocus="this.style.borderColor=\'#3300cc\';" onblur="this.style.borderColor=\'black\';" tabindex="13" /></div>
<input type="hidden" name="logos_replace" value="' . $_SERVER['PHP_SELF'] . '" />
<div><input type="reset" value="Reset" class="input" tabindex="15" />&nbsp;&nbsp;&nbsp;<input type="submit" name="logo_submit" value="Add" class="input" tabindex="14" /></div>
</form>
</div>' . "\n";
	return $str;	
}

function colorscheme() {
	$str = 'document.write(\'<div><form action="#"><select size="1" onchange="mySiteColor(this.options[this.selectedIndex].value);">\');' . "\n";
	$str .= 'document.write(\'<optgroup label="Выберите цветовую схему:">\');' . "\n";
	$str .= 'document.write(\'<option value="1" class="style1">по-умолчанию</option>\');' . "\n";
	$arr = array(	
	'levender'     => 'document.write(\'<option value="2" class="style2">levender</option>\');',
	'lightblue'    => 'document.write(\'<option value="3" class="style3">lightblue</option>\');',
	'white'        => 'document.write(\'<option value="4" class="style4">white</option>\');',
	'gray'         => 'document.write(\'<option value="5" class="style5">gray</option>\');',
	);
	ksort($arr);
	$strarr = implode("\n", $arr);
	$str .= $strarr . "\n";
	$str .= 'document.write(\'</optgroup></select></form></div>\');' . "\n";
	if (isset($_COOKIE['className'])) {
		$pattern = 'class="' . $_COOKIE['className'] . '"';
		$replacement = 'class="' . $_COOKIE['className'] . '" selected="selected"';
		$str = str_replace($pattern, $replacement, $str);
	}
	return $str;
}

?>