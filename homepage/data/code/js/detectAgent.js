detectAgent() Функция распознавания браузера
<script type="text/javascript">
//<![CDATA[
/**
 * Функция распознавания браузера
 * @return string
 * @copyright Взято из "Полного справочника по JavaScript" стр. 580
 */
function detectAgent() {
	with(navigator) {
		// Браузер на базе Opera
		if(userAgent.indexOf('Opera') != -1) {
			return 'Opera';
		}
		// Браузер на базе Mozilla
		else if(userAgent.indexOf('Gecko') != -1) {
			return 'Gecko';
		}
		// Браузер на базе IE
		else if(userAgent.indexOf('MSIE') != -1) {
			return 'MSIE';
		}
		// Старый браузер на базе Netscape
		else if(userAgent.indexOf('Mozilla') != -1) {
			return 'Netscape';
		}
		// Неизвестный браузер
		else {
			return 'undefined';
		}
	}
}
//]]>
</script>