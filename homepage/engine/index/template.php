<?php
function saying_form() {
	$str = '
<br />
<div>
<form action="' . $_SERVER['PHP_SELF'] . '" method="post" name="sayingform" id="sayingform">
<div><label for="phrase">Phrase</label><input type="text" name="phrase" id="phrase" size="100" class="input" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="1" /></div>
<div><label for="author">Author</label><input type="text" name="author" id="author" size="100" class="input" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="2" /></div>
<div><label for="description">Description</label><input type="text" name="description" id="description" size="100" class="input" onfocus="this.style.borderColor = \'#3300cc\';" onblur="this.style.borderColor = \'black\';" tabindex="3" /></div>
<div><input type="reset" class="input" onclick="document.sayingform.phrase.focus();" tabindex="4" /><input type="submit" name="add" value="Add" class="input" tabindex="3" /></div>
</form>
</div>' . "\n";
	return $str;	
}
?>