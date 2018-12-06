FieldsCheck() �������-����������� �������� �������� � ����� ������
<script type="text/javascript">
//<![CDATA[
/**
 * �������-����������� �������� �������� � ����� ������
 * @copyright http://dmitry-ponomarev.ru
 */
function FieldsCheck() {
	this.Name = function (field) {
		var pattern = /^[a-z�-��-߸][\w�-��-߸ -]*$/i;
		if(pattern.test(field.value)) {return true;}
		else {return false;}
	}
	this.Passwd = function(field) {
		var pattern = /^[\w�-��-߸ -]+$/i;
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