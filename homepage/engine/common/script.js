/**
* ������� ����������� ������� ������ cookie � ��������� ���������������
* �������� � ������������� �������, ��������������� �� ���� ���.
* ������� ���������� �������������� �������: cookies["���"]="��������"
*
* ����� �� "������� ����������� �� JavaScript" ���. 567-568
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
* ������ �������������: 
* setInterval("timestamp(id)", msecs);
* 
* ��������: ������� ���������� � ��������� ���� (id ������������� ����
* ����������� � ��������� ���������� �������) ������ � ������� ������ 
* ������� � �������: H:i:s d F Y.
* 
* @param string id
* @return undefined
*
* Copyright 2007 Ponomarev Dmitry
* http://dmitry-ponomarev.ru
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

/**
* �������� ������ ������� ���� ������ �����*
* ����� �� "������� ����������� �� JavaScript" ���. 489
* 
* @return undefined;
*/
function focusFirst() {
	if (document.forms.length > 0 && document.forms[0].elements.length > 0) {
		document.forms[0].elements[0].focus();
	}
	return;
}

function treechange(id, field) {
	if (document.getElementById) {
		if (field.firstChild.firstChild.nodeValue == '+') {
			document.getElementById(id).style.display = 'block';
			field.firstChild.firstChild.nodeValue = '-';
		}
		else {
			document.getElementById(id).style.display = 'none';
			field.firstChild.firstChild.nodeValue = '+';
		}
	}
	return;
}

/**
* ������ �������������: 
* onsubmit="return validate(this.name, id)"
* 
* ��������: ������� ��������� ��������� ������������� ������ � ���� "���:", ��������
* ���������� ������� � ���������� true, ���� ������ ������ ��������, false 
* � ������ ������.
* 
* @param string field
* @param string id
* @return bool
*
* Copyright 2007 Ponomarev Dmitry
* http://dmitry-ponomarev.ru
*/
function validate(field, id) {
  if(document.getElementById) {
	var report = new Array();
	var i = -1;
	if ((field.value == null)||(field.value.length == 0)||(!(/\S/.test(field.value))))
        report[++i] = '������: ���� "���:" ����������� ��� ����������!' + "\n";
	if (field.value.length < 2)
        report[++i] = '������: ��� ������� ��������! �������: ��� �������.' + "\n";
	if (!(/^[a-z�-��-߸][\w�-��-߸ -]*$/i.test(field.value))) {
		report[++i] = '������: ������������ ���! ��� ����� ��������� ����� ��������-�������� �������, ���� �������������, ������, ����� � ������ ���������� � �����.' + "\n";
		field.select();
	}
	if (report.length > 0) {
		if (report.length < 3)
			document.getElementById(id + 'internal').firstChild.nodeValue = report.join('');
		// ���� ��� ������ �����, ������ ����� ������
		else
			document.getElementById(id + 'internal').firstChild.nodeValue = report[0];
		document.getElementById(id).style.display = 'block';
	    field.focus();
	    return false;
	}
  }
  return true;
}

/**
* ������ �������������: 
* onsubmit="return validate_all(this.name, this.mail, id)"
* 
* ��������: ������� ��������� ��������� ������������� ������ � ���� "���:" � � 
* ���� E-mail, �������� ����������� ������� � ���������� true, ���� ������ ������ ��������,
* false � ������ ������. 
* 
* @param string field
* @param string field2
* @param string id
* @return bool
*
* Copyright 2007 Ponomarev Dmitry
* http://dmitry-ponomarev.ru
*/
function validate_all(field, field2, id) {
  if(document.getElementById) {
	  var report = new Array();
	var i = -1;
	if ((field.value == null)||(field.value.length == 0)||(!(/\S/.test(field.value)))) {
		report[++i] = '������: ���� "���:" ����������� ��� ����������!' + "\n";
		field.focus();
	}
	if (field.value.length < 2) {
		report[++i] = '������: ��� ������� ��������! �������: ��� �������.' + "\n";
		field.focus();
	}
	if (!((field2.value == null)||(field2.value.length == 0)||(!(/\S/.test(field2.value))))) {
		if (!(/[^@]+@(\w+\.)+\w+/.test(field2.value))) {
			report[++i] = '������: E-mail ����� ������������ ������.' + "\n";
			field2.select();
			field2.focus();
		}
	}
	if (!(/^[a-z�-��-߸][\w�-��-߸ -]*$/i.test(field.value))) {
		report[++i] = '������: ������������ ���! ��� ����� ��������� ����� ��������-�������� �������, ���� �������������, ������, ����� � ������ ���������� � �����.' + "\n";
		field.select();
		field.focus();
	}
	if (report.length > 0) {
		document.getElementById(id + 'internal').firstChild.nodeValue = report.join('');
		document.getElementById(id).style.display = 'block';
	    return false;
	}
  }
  return true;
}

var engineCommonLoad = true;