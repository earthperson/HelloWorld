writeGreeting() ����������� � ����������� �� ������� �����
<script type="text/javascript">
//<![CDATA[
/**
 * ����������� � ����������� �� ������� �����
 * @copyright http://dmitry-ponomarev.ru
 */
function writeGreeting() {
	var dateNow = new Date();
	var hh=parseInt(dateNow.getHours());
	if ( 4<=hh && hh<=11) {
		document.write('<h3>������ ����!</h3>');
	}
	else if (12<=hh && hh<=16) {
		document.write('<h3>������ ����!</h3>');
	}
	else if (17<=hh && hh<=23) {
		document.write('<h3>������ �����!</h3>');
	}
	else {
		document.write('<h3>������ ����!</h3>');
	}
}
//]]>
</script>
