writeGreeting() Приветствие в зависимости от времени суток
<script type="text/javascript">
//<![CDATA[
/**
 * Приветствие в зависимости от времени суток
 * @copyright http://dmitry-ponomarev.ru
 */
function writeGreeting() {
	var dateNow = new Date();
	var hh=parseInt(dateNow.getHours());
	if ( 4<=hh && hh<=11) {
		document.write('<h3>Доброе утро!</h3>');
	}
	else if (12<=hh && hh<=16) {
		document.write('<h3>Добрый день!</h3>');
	}
	else if (17<=hh && hh<=23) {
		document.write('<h3>Добрый вечер!</h3>');
	}
	else {
		document.write('<h3>Доброй ночи!</h3>');
	}
}
//]]>
</script>
