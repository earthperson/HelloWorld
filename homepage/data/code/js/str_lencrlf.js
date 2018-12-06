str_lencrlf() ������� ����������� ���-�� ��������� �������� ��� ����� CR � LF
<script type="text/javascript">
//<![CDATA[
/**
 * ������ �������������: 
 * setTimeout("str_lencrlf(max, id_field, id_node)", msecs);
 * 
 * ������� ���������� � ��������� ���� ����� ��������� � ���� ����� �������� ��� ����� ��������
 * �������� ������� � �������� ������. ���� ����� ��������� �������� ������ ���������� �������,
 * ��������� ������ ���������� �������, ���������� ������ ��������.
 * 
 * @return undefined
 * � 2007 Ponomarev Dmitry
 * http://dmitry-ponomarev.ru
 */
function str_lencrlf(max, id_field, id_node) {
	if(document.getElementById) {
		var str = document.getElementById(id_field).value;
		// ��������� ������ � ��� ��������, ��� ����������� ������� �������� ������,
		// � ����� ���������� ������ ����������� ������� � ������ ��� �� ���������� �������� �������� ������
		// ���� � ������ ��� ������� "\r" - ��� Mozilla, �������� ��� LINUX
	    if (str.indexOf("\r") == -1)
			var arr = str.split("\n");
		else
			var arr = str.split("\r\n");
		str = arr.join('');
		var count = str.length;
		document.getElementById(id_node).firstChild.nodeValue = count;
		if(count > max) {
		    document.getElementById(id_node).style.color = 'red';
		    document.getElementById(id_node).style.fontWeight = 'bold';
		}
		else {
			document.getElementById(id_node).style.color = 'black';
			document.getElementById(id_node).style.fontWeight = 'normal';	 	    
		}
	}
	return;
}
//]]>
</script>
