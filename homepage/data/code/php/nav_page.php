nav_page() ������� ������ ��������� �� ���������
<?php
/**
 * ������� ������ ��������� �� ���������
 * @param int $count
 * @param int $num_page
 * @param string $url
 * @param int $page_nav
 * @copyright http://niko.net.ru/
 */ 
function nav_page(
$count,    // ����� ���-�� �������
$num_page, // ����� ������� ��������
$url,      // ����� URL ��� ������ �� �������� (� ���� ����������� ����� ��������)
$page_nav  // C������ ������ �� �������� �������� ������������
) {
	$begin_loop=1; //��������� �������� � �����
	$end_loop=$count; //�������� �������� � �����
	print('<div class="nav_page">[ �������� (' . $count . '):&nbsp;');
	if ($num_page>$count or $num_page<1) {
		$num_page=1; // �������� �� ������������ ������ ������� ��������
	}
	// ����� � ������� ��� ��� ����� ���������, �������� ����� �� ������� ����
	if ($num_page>$page_nav) {
		print("&nbsp;<a href=\"$url");
		print(($page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0))));
		print("\" class=\"nav_page_func\">(");
		print(($page_nav*(floor($num_page/$page_nav)-1-($num_page%$page_nav==0 ? 1: 0))+1));
		print("-");
		print(($page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0))));
		print(")</a>...&nbsp;");
		$begin_loop=$page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0))+1;
	}
	if ($count>$page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0)+1)) {
		$end_loop=$page_nav*ceil($num_page/$page_nav);
	}
	for ($i = $begin_loop; $i <= $end_loop;  $i++) {
		if ($i==$num_page)
		print("<b>&nbsp;$i&nbsp;</b>");
		else
		{
			print("&nbsp;<a href=\"$url$i\" class=\"nav_page_func\">$i</a>&nbsp;");
		}
	}//for
	if ($count>$page_nav*(floor($num_page/$page_nav)-($num_page%$page_nav==0 ? 1: 0)+1)) {
		print("&nbsp;...<a href=\"$url");
		print(($page_nav*ceil($num_page/$page_nav)+1));
		print("\" class=\"nav_page_func\">(");
		print(($page_nav*ceil($num_page/$page_nav)+1));
		if ($page_nav*ceil($num_page/$page_nav)+1<$count) {
			print("-");
			print(($count<=$page_nav*(ceil($num_page/$page_nav)+1) ? $count: $page_nav*(ceil($num_page/$page_nav)+1)));
		}
		print(")</a>");
	}
	print("&nbsp;]\n</div>\n");
}
?>