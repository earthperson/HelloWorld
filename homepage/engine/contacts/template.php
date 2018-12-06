<?php
function contacts_form($name, $mail, $msg) {
	$str = '
<div class="contactsform">
<label for="name"><img src="../img/email_off.gif" width="16" height="16" alt="" onmouseover="this.src=\'../img/email_on.gif\'" onmouseout="this.src=\'../img/email_off.gif\'" style="position: relative; top: 4px;" />Написать мне письмо:</label><br />
<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="contactsform" id="contactsform" onsubmit="if(engineCommonLoad) {return validate_all(this.name, this.mail, \'contactserror\');}">
<div><label for="name">Имя<sup style="color: maroon;">*</sup>:</label><input type="text" name="name" id="name" size="30" maxlength="30" value="' . $name . '" class="input" style="width:250px;" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="1" /></div>
<div><label for="mail">E-mail:</label><input type="text" name="mail" id="mail" size="30" value="' . $mail . '" class="input" style="width: 250px;" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="2" /></div>
<div><label for="msg">Сообщение<sup style="color: maroon;">*</sup>:</label><textarea name="msg" id="msg" rows="5" cols="30" class="input" style="width:250px;" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="3">' . $msg .'</textarea></div>
<div><input type="reset" value="Стереть" class="input" onclick="document.contactsform.name.focus();" tabindex="5" />&nbsp;&nbsp;&nbsp;<input type="submit" name="contacts_submit" value="Отправить" class="input" tabindex="4" /></div>
</form>
</div>' . "\n";
	return $str;	
}
?>