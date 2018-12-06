/**
* ������ �������������:
* setTimeout("journalnumcharsfunc()", msecs);
*
* ��������: ������� ���������� � ��������� ���� ����� ��������� � ���� ����� ���������� ��������.
* ���� ����� ��������� �������� ������ ���������� �������, ��������� ���������� �������, ����������
* ������ ��������
*
* @return undefined
*
* Copyright 2007 Ponomarev Dmitry
* http://dmitry-ponomarev.ru
*/
function journalnumcharsfunc(max) {
	if(document.getElementById) {
		var str = document.journalform.msg.value;
		// ��������� ������ � ��� ��������, ��� ����������� ������� �������� ������,
		// � ����� ���������� ������ ����������� ������� � ������ ��� �� ���������� �������� �������� ������
		// ���� � ������ ��� ������� "\r" - ��� Mozilla, �������� ��� UNIX
		if (str.indexOf("\r") == -1)
		var arr = str.split("\n");
		else
		var arr = str.split("\r\n");
		str = arr.join('');
		var count = str.length;
		document.getElementById('journalnumcharsid').firstChild.nodeValue = count;
		if(count > max) {
			document.getElementById('journalnumcharsid').style.color = 'red';
			document.getElementById('journalnumcharsid').style.fontWeight = 'bold';
		}
		else {
			document.getElementById('journalnumcharsid').style.color = 'black';
			document.getElementById('journalnumcharsid').style.fontWeight = 'normal';
		}
	}
	return;
}

/**
* ������ ���� ���� � ���� ������.
*
* Copyright 2007 Ponomarev Dmitry
* http://dmitry-ponomarev.ru
*/
function mySiteColor(arg) {
	document.body.className = 'style' + arg;
	var period = new Date();
	period.setYear(period.getFullYear() + 10);
	document.cookie = 'className=style' + arg + ';expires=' + period.toUTCString();
}
/**
* ������� setElementOpacity(sElemId, nOpacity) ��������� ��� ���������: sElemId - id ��������, nOpacity - ������������ ����� �� 0.0 �� 1.0
* �������� ������������ � ����� CSS3 opacity.
* http://www.tigir.com/opacity.htm
*/
function getOpacityProperty()
{
	if (typeof document.body.style.opacity == 'string') // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
	return 'opacity';
	else if (typeof document.body.style.MozOpacity == 'string') // Mozilla 1.6 � ������, Firefox 0.8
	return 'MozOpacity';
	else if (typeof document.body.style.KhtmlOpacity == 'string') // Konqueror 3.1, Safari 1.1
	return 'KhtmlOpacity';
	else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) // Internet Exploder 5.5+
	return 'filter';

	return false; //��� ������������
}
function setElementOpacity(sElemId, nOpacity)
{
	var opacityProp = getOpacityProperty();
	var elem = document.getElementById(sElemId);

	if (!elem || !opacityProp) return; // ���� �� ���������� ������� � ��������� id ��� ������� �� ������������ �� ���� �� ��������� ������� �������� ���������� �������������

	if (opacityProp=="filter")  // Internet Exploder 5.5+
	{
		nOpacity *= 100;

		// ���� ��� ����������� ������������, �� ������ � ����� ��������� filters, ����� ��������� ������������ ����� style.filter
		var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
		if (oAlpha) oAlpha.opacity = nOpacity;
		else elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")"; // ��� ���� ����� �� �������� ������ ������� ���������� "+="
	}
	else // ������ ��������
	elem.style[opacityProp] = nOpacity;
}

/**
 * ���������� �������� � ��������� (��������)
 * @copyright http://www.tigir.com/addbookmark.htm
 */
function addBookmark(url, title) {
	if (!url) url = location.href;
	if (!title) title = document.title;
	//Gecko
	if ((typeof window.sidebar == "object") && (typeof window.sidebar.addPanel == "function")) {
		window.sidebar.addPanel (title, url, "");
	}
	//IE4+
	else if (typeof window.external == "object") {
		window.external.AddFavorite(url, title);
	}
	else {
		return false;
	}
	return true;
}

/**
* ������� ������������� ��������
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

var engineMainLoad = true;