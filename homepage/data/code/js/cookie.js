setCookie(), extractCookies(), deleteCookie() ������� ���������, ������ � �������� ����� cookie
<script type="text/javascript">
//<![CDATA[
/**
 * ������� ��������� ������ cookie
 * name - ��� cookie
 * value - �������� cookie
 * [expires] - ���� ��������� �������� cookie (�� ��������� - �� ����� ������)
 * [path] - ����, ��� �������� cookie ������������� (�� ��������� - ��������, � ������� �������� ���� �����������)
 * [domain] - �����, ��� �������� cookie ������������� (�� ��������� - �����, � ������� �������� ���� �����������)
 * [secure] - ���������� ��������, ������������ ��������� �� ���������� �������� �������� cookie
 * ������: 
 * var d = new Date();
 * d.setYear(d.getFullYear() + 10);
 * setCookie('cookie', 'test', d, '', '', 1);
 */
function setCookie(name, value, expires, path, domain, secure) {
    var curCookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires.toUTCString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
    if ((name + "=" + escape(value)).length <= 4000) {
	    document.cookie = curCookie;
    }
    return;
}

/**
 * ������� ����������� ������� ������ cookie � ��������� ���������������
 * �������� � ������������� �������, ��������������� �� ���� ���.
 * ������� ���������� �������������� �������: cookies["���"]="��������"
 * 
 * ������:
 * // ����� ��������� cookie, ����� ��������� ������� �� ���������
 * document.cookie = 'cookiesenabled=yes';
 * // ������� ������������� cookies � ���� �������������� ������
 * extractCookies();
 * if(cookies['cookiesenabled'] == 'yes') {
 *      ..........
 * }
 * @copyright ����� �� "������� ����������� �� JavaScript" ���. 567-568
 */
var cookies = new Object();
function extractCookies()
{	
	var name, value;
	var beginning, middle, end;
	for (name in cookies)
	{ //���� ������� ��������, ������� ��
		cookies = new Object();
		break;
	}
	beginning = 0; //������ � ������ ������ cookie
	while (beginning < document.cookie.length)
	{
		middle = document.cookie.indexOf('=', beginning); //����� '='
		end = document.cookie.indexOf(';', beginning); //����� ';'
		if (end == -1) // ���� ��� ';', �� ��� ��������� ���� cookie
		end = document.cookie.length;
		if ( (middle > end) || (middle == -1) )
		{ // ���� ���� cookie �� ����� ��������...
			name = document.cookie.substring(beginning, end);
			value = "";
		}
		else
		{ // ������� �������� ����
			name = document.cookie.substring(beginning, middle);
			value = document.cookie.substring(middle + 1, end);
		}
		cookies[name] = unescape(value); // �������� � ������
		beginning = end + 2; // ������� �� ������ ���������� ����
	}
	return;
}

/**
 * ������� �������� ������ cookie
 * name - ��� cookie
 */
function deleteCookie(name) {
    document.cookie = name + "=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT";
    document.cookie = name + "; expires=Thu, 01-Jan-1970 00:00:01 GMT";
    return;
}
//]]>
</script>