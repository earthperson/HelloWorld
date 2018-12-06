documentDimension() ����������� �������� ������� ������� ���� �������� (�������� ���������)
<script type="text/javascript">
//<![CDATA[
/**
 * ����������� �������� ������� ������� ���� �������� (�������� ���������)
 * @return array
 * @copyright ����� �� "������� ����������� �� JavaScript" ���. 589
 */
function documentDimension() {
	// ���������� ������ ������
	if (window.innerWidth) {
		theWindowWidth = window.innerWidth;
	}
	else if (document.body && document.body.clientWidth) {
		theWindowWidth = document.body.clientWidth;
	}
	else if (document.documentElement && document.documentElement.clientWidth) {
		theWindowWidth = document.documentElement.clientWidth;
	}
	// ���������� ������ ������
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