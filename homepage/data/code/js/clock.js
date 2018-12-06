����
<script type="text/javascript">
//<![CDATA[
/**
 * ������ �������������: 
 * setInterval("timestamp(id)", msecs);
 * 
 * ��������: ������� ���������� � ��������� ���� (id ������������� ����
 * ����������� � ��������� ���������� �������) ������ � ������� ������ 
 * ������� � �������: H:i:s d F Y.
 * 
 * @param string id
 * @return undefined
 * @copyright http://dmitry-ponomarev.ru
 */
function timestamp(id) {
	function redDay(day) {
		var newNode = document.createElement('B');
		newNode.style.color = 'red';
		newNode.style.fontWeight = 'normal';
		var newText = document.createTextNode(day);
		newNode.appendChild(newText);
		return newNode;
	}
	var str = '';
    var myArray = new Array();
    var myDate = new Date();
    myArray[0] = myDate.getHours();
    if (parseInt(myArray[0]) < 10) {
		myArray[0] = '0' + myArray[0];
    }
    myArray[1] = myDate.getMinutes();
    if (parseInt(myArray[1]) < 10) {
		myArray[1] = '0' + myArray[1];
	}
    myArray[2] = myDate.getSeconds();
	if (parseInt(myArray[2]) < 10) {
		myArray[2] = '0' + myArray[2];
	}
    myArray[3] = myDate.getDate();
    myArray[4] = myDate.getMonth();
    myArray[5] = myDate.getFullYear();
    switch(myArray[4]) {
		case 0: myArray[4] = '������';
        break;
        case 1: myArray[4] = '�������';
        break;
        case 2: myArray[4] = '�����';
        break;
        case 3: myArray[4] = '������';
        break;
        case 4: myArray[4] = '���';
        break;
        case 5: myArray[4] = '����';
        break;
        case 6: myArray[4] = '����';
        break;
        case 7: myArray[4] = '�������';
        break;
        case 8: myArray[4] = '��������';
        break;
        case 9: myArray[4] = '�������';
        break;
        case 10: myArray[4] = '������';
        break;
        default: myArray[4] = '�������';
	}
	myArray[6] = myDate.getDay();
	switch(myArray[6]) {
		case 0:
		myArray[6] = '';
		newNode = redDay('(��.)');		
		break;
		case 1: myArray[6] = '(��.)';
		break;
		case 2: myArray[6] = '(��.)';
		break;
		case 3: myArray[6] = '(��.)';
		break;
		case 4: myArray[6] = '(��.)';
		break;
		case 5: myArray[6] = '(��.)';
		break;
		case 6:
		myArray[6] = '';
		newNode = redDay('(��.)');
		break;		
	}
	for(var i = 0; i < myArray.length; i++) {
		if(i < 2)
			str += myArray[i] + ':';
		else if (i >= 2 && i < 6)
			str += myArray[i] + ' ';
		else
			str += myArray[i];
	}
	if(document.getElementById) {
		document.getElementById(id).firstChild.nodeValue = str;
		// ������� �������� ��� ���������
		if(myArray[6] == '') {
				if(document.getElementById(id).lastChild.nodeName != 'B') {
					document.getElementById(id).appendChild(newNode);
			}
		}
		// �������� �������� ��� ���������
		var blackDays = new RegExp('(��|��|��|��|��)', 'i');
		var redDays = new RegExp('(��|��)', 'i');
		var currentElement = document.getElementById(id);
		if(document.getElementById(id).lastChild.nodeName == 'B') {
			// ���� ��� ���������� ������� ����
			if(blackDays.test(document.getElementById(id).firstChild.nodeValue) && redDays.test(document.getElementById(id).lastChild.firstChild.nodeValue)) {
				document.getElementById(id).removeChild(document.getElementById(id).lastChild);
			}
		}
	}
	return;
}
//]]>
</script>