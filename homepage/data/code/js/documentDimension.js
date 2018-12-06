documentDimension() Определение размеров рабочей области окна браузера (размеров документа)
<script type="text/javascript">
//<![CDATA[
/**
 * Определение размеров рабочей области окна браузера (размеров документа)
 * @return array
 * @copyright Взято из "Полного справочника по JavaScript" стр. 589
 */
function documentDimension() {
	// Вычисление ширины экрана
	if (window.innerWidth) {
		theWindowWidth = window.innerWidth;
	}
	else if (document.body && document.body.clientWidth) {
		theWindowWidth = document.body.clientWidth;
	}
	else if (document.documentElement && document.documentElement.clientWidth) {
		theWindowWidth = document.documentElement.clientWidth;
	}
	// Вычисление высоты экрана
	if (window.innerHeight) {
		theWindowHeight = window.innerHeight;
	}
	else if (document.body && document.body.clientHeight) {
		theWindowHeight = document.body.clientHeight;
	}
	else if (document.documentElement && document.documentElement.clientHeight) {
		theWindowHeight = document.documentElement.clientHeight;
	}
	var arr = [theWindowWidth, theWindowHeight];
	return arr;
}
//]]>
</script>