<?php
/**
 * Вывод предупреждения, если поддержка JavaScript отключена
 *
 * @return string
 */
function noscript() {
	$str = '
<!-- У Вас отключена поддержка JavaScript! / timestamp(id)-->
<div style="text-align: right; padding-right: 10px;">
<noscript><em>У Вас отключена поддержка JavaScript!</em></noscript>
<script type="text/javascript">
//<![CDATA[
    document.write(\'<span id="time">&nbsp;</span>\');
//]]>
</script>
</div>
<!-- END У Вас отключена поддержка JavaScript! / timestamp(id)-->' . "\n";
	return $str;
}

/**
 * Печатает информацию для размышления
 *
 * @param float $time
 * @return null
 */
function info($time) {
  $arr = parse_url(HOST);
  $str = $arr['host'];
  $str = str_replace('www.', '', $str);
  print('<!-- информация для размышления -->' . "\n"
  . '<div class="info"> &copy; 2007-' . gmdate('Y', time() + 3600 * 3) . ' Ponomarev Dmitry. Hosted by' . "\n"
  . '<a href="' . HOST . '" title="" target="_blank">[ ' . $str . ' ]</a>.</div>' . "\n"
  . '<div class="info">Использование материалов сайта возможно <u>без</u> разрешения автора.</div>' . "\n");
  if(is_float($time))
    printf('<div class="timeOfGeneration">Страница сгенерирована за %.5f секунд.</div>' . "\n", $time);
  print('<!-- END информация для размышления -->' . "\n");
}
?>