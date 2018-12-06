<?php
/**
 * ����� ��������������, ���� ��������� JavaScript ���������
 *
 * @return string
 */
function noscript() {
	$str = '
<!-- � ��� ��������� ��������� JavaScript! / timestamp(id)-->
<div style="text-align: right; padding-right: 10px;">
<noscript><em>� ��� ��������� ��������� JavaScript!</em></noscript>
<script type="text/javascript">
//<![CDATA[
    document.write(\'<span id="time">&nbsp;</span>\');
//]]>
</script>
</div>
<!-- END � ��� ��������� ��������� JavaScript! / timestamp(id)-->' . "\n";
	return $str;
}

/**
 * �������� ���������� ��� �����������
 *
 * @param float $time
 * @return null
 */
function info($time) {
  $arr = parse_url(HOST);
  $str = $arr['host'];
  $str = str_replace('www.', '', $str);
  print('<!-- ���������� ��� ����������� -->' . "\n"
  . '<div class="info"> &copy; 2007-' . gmdate('Y', time() + 3600 * 3) . ' Ponomarev Dmitry. Hosted by' . "\n"
  . '<a href="' . HOST . '" title="" target="_blank">[ ' . $str . ' ]</a>.</div>' . "\n"
  . '<div class="info">������������� ���������� ����� �������� <u>���</u> ���������� ������.</div>' . "\n");
  if(is_float($time))
    printf('<div class="timeOfGeneration">�������� ������������� �� %.5f ������.</div>' . "\n", $time);
  print('<!-- END ���������� ��� ����������� -->' . "\n");
}
?>