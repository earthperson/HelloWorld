detectAgent() ������� ������������� ��������
<script type="text/javascript">
//<![CDATA[
/**
 * ������� ������������� ��������
 * @return string
 * @copyright ����� �� "������� ����������� �� JavaScript" ���. 580
 */
function detectAgent() {
	with(navigator) {
		// ������� �� ���� Opera
		if(userAgent.indexOf('Opera') != -1) {
			return 'Opera';
		}
		// ������� �� ���� Mozilla
		else if(userAgent.indexOf('Gecko') != -1) {
			return 'Gecko';
		}
		// ������� �� ���� IE
		else if(userAgent.indexOf('MSIE') != -1) {
			return 'MSIE';
		}
		// ������ ������� �� ���� Netscape
		else if(userAgent.indexOf('Mozilla') != -1) {
			return 'Netscape';
		}
		// ����������� �������
		else {
			return 'undefined';
		}
	}
}
//]]>
</script>