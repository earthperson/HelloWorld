scroll() ������� ���������� ������
<script type="text/javascript">
//<![CDATA[
/**
 * ������ �������������:
 * setInterval("scroll(expression, id, quantity, offset, textNodeLength)", msecs);
 * 
 * ��������: ������� ��� ������ ������ ��������������� ����������� ���������� ����������
 * ����������� �� ������� ���������, � ��������� ���� (id ������������� ���� �����������
 * �� ������ ���������) �� ��������, ���������� ������� ����������, �������� �������� ���������
 * ����������, ���� ��� �������������, �� ������������� �� ����� ���������� ����, � �����
 * ��������� ����������� �������������� ����� ���������� ����.
 * 
 * @param string expression
 * @param string id
 * @param number quantity
 * @param number offset
 * @param number textNodeLength
 * @return bool
 * @copyright http://dmitry-ponomarev.ru
 */
function scroll() {
    // ����������� ���������� ���������� ���������
    var i = -1;
    var expression     = scroll.arguments[++i];
    var id             = scroll.arguments[++i];
    var quantity       = scroll.arguments[++i];
    var offset         = scroll.arguments[++i];
    var textNodeLength = scroll.arguments[++i];
    // DOM ��������������?
    if(document.getElementById) {
        // ���� ����� �������� ����?
		if (document.getElementById(id).hasChildNodes()) {
			var textNode = document.getElementById(id).firstChild;
			// ���������?
		    if (textNode.nodeType == 3) {
		    	// �������� ���������� ������� ����������
				if (
				(typeof(expression) != "string")       || (expression == "")                   || 
				(typeof(id) != "string")               || (typeof(quantity) != "number")       ||
				(typeof(offset) != "number")           || (typeof(textNodeLength) != "number") ||
				(Math.abs(offset) > textNodeLength)
				) { textNode.nodeValue = "������� scroll() �������� ������������ ���������!";
				    return false; }
				// ����� ������������ �������� (������� ���������)
				var multiplier = expression.length;
				if( (textNodeLength + quantity * multiplier) > textNode.length ) {
					if(offset < 0)
						textNode.insertData(textNode.length + offset, expression);
					else
						textNode.insertData(offset, expression);
				}
				else {
					if(offset < 0)
					    textNode.deleteData( (textNode.length - quantity * multiplier + offset), quantity * multiplier);
					else
					    textNode.deleteData(offset, quantity);
				}
				return true;
		    }
		}
    }
    return false;
}
//]]>
</script>