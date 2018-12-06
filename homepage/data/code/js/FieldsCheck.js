FieldsCheck() ‘ункци€-конструктор проверки вводимых в форму данных
<script type="text/javascript">
//<![CDATA[
/**
 * ‘ункци€-конструктор проверки вводимых в форму данных
 * @copyright http://dmitry-ponomarev.ru
 */
function FieldsCheck() {
	this.Name = function (field) {
		var pattern = /^[a-zа-€ј-яЄ][\wа-€ј-яЄ -]*$/i;
		if(pattern.test(field.value)) {return true;}
		else {return false;}
	}
	this.Passwd = function(field) {
		var pattern = /^[\wа-€ј-яЄ -]+$/i;
		if(pattern.test(field.value)) {return true;}
		else {return false;}
	}
	this.Mail = function(field) {
		var pattern = /[^@]+@(\w+\.)+\w+/;
		if(pattern.test(field.value)) {return true;}
		else {return false;}
	}
	this.Empty = function(field) {
		var pattern = /\S/;
		if(pattern.test(field.value)) {return true;}
		else {return false;}
	}
}
var obj = new FieldsCheck();
//]]>
</script>