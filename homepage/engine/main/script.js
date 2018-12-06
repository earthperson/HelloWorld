/**
* Пример использования:
* setTimeout("journalnumcharsfunc()", msecs);
*
* Описание: функция записывает в текстовый узел число введенных в поле формы печатаемых символов.
* Если число введенных символов больше некоторого предела, заданного параметром функции, оформление
* текста меняется
*
* @return undefined
*
* Copyright 2007 Ponomarev Dmitry
* http://dmitry-ponomarev.ru
*/
function journalnumcharsfunc(max) {
	if(document.getElementById) {
		var str = document.journalform.msg.value;
		// разбиваем строку в тех позициях, где встречается символы переноса строки,
		// а затем полученный массив преобразуем обратно в строку уже не содержащую символов переноса строки
		// если в строке нет символа "\r" - под Mozilla, возможно под UNIX
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
* Задает цвет фона и цвет текста.
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
* Функция setElementOpacity(sElemId, nOpacity) принимает два аргумента: sElemId - id элемента, nOpacity - вещественное число от 0.0 до 1.0
* задающее прозрачность в стиле CSS3 opacity.
* http://www.tigir.com/opacity.htm
*/
function getOpacityProperty()
{
	if (typeof document.body.style.opacity == 'string') // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
	return 'opacity';
	else if (typeof document.body.style.MozOpacity == 'string') // Mozilla 1.6 и младше, Firefox 0.8
	return 'MozOpacity';
	else if (typeof document.body.style.KhtmlOpacity == 'string') // Konqueror 3.1, Safari 1.1
	return 'KhtmlOpacity';
	else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) // Internet Exploder 5.5+
	return 'filter';

	return false; //нет прозрачности
}
function setElementOpacity(sElemId, nOpacity)
{
	var opacityProp = getOpacityProperty();
	var elem = document.getElementById(sElemId);

	if (!elem || !opacityProp) return; // Если не существует элемент с указанным id или браузер не поддерживает ни один из известных функции способов управления прозрачностью

	if (opacityProp=="filter")  // Internet Exploder 5.5+
	{
		nOpacity *= 100;

		// Если уже установлена прозрачность, то меняем её через коллекцию filters, иначе добавляем прозрачность через style.filter
		var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
		if (oAlpha) oAlpha.opacity = nOpacity;
		else elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")"; // Для того чтобы не затереть другие фильтры используем "+="
	}
	else // Другие браузеры
	elem.style[opacityProp] = nOpacity;
}

/**
 * Добавление страницы в избранное (закладки)
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
* Функция распознавания браузера
*/
function detectAgent() {
	with(navigator) {
		// Браузер на базе Opera
		if(userAgent.indexOf('Opera') != -1) {
			return 'Opera';
		}
		// Браузер на базе Mozilla
		else if(userAgent.indexOf('Gecko') != -1) {
			return 'Gecko';
		}
		// Браузер на базе IE
		else if(userAgent.indexOf('MSIE') != -1) {
			return 'MSIE';
		}
		// Старый браузер на базе Netscape
		else if(userAgent.indexOf('Mozilla') != -1) {
			return 'Netscape';
		}
		// Неизвестный браузер
		else {
			return 'undefined';
		}
	}
}

var engineMainLoad = true;