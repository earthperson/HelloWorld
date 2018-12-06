/**
* Функция анализирует текущие строки cookie и размещает соответствующие
* элементы в ассоциативном массиве, индексированном по полю имя.
* правило индексации ассоциативного массива: cookies["имя"]="значение"
*
* Взято из "Полного справочника по JavaScript" стр. 567-568
*/
var cookies = new Object();
function extractCookies()
{	
	var name, value;
	var beginning, middle, end;
	for (name in cookies)
	{ //если имеются значения, удалить их
		cookies = new Object();
		break;
	}
	beginning = 0; //начать с начала строки cookie
	while (beginning < document.cookie.length)
	{
		middle = document.cookie.indexOf('=', beginning); //найти '='
		end = document.cookie.indexOf(';', beginning); //найти ';'
		if (end == -1) // если нет ';', то это последнне поле cookie
		end = document.cookie.length;
		if ( (middle > end) || (middle == -1) )
		{ // если поле cookie не имеет значения...
			name = document.cookie.substring(beginning, end);
			value = "";
		}
		else
		{ // извлечь значение поля
			name = document.cookie.substring(beginning, middle);
			value = document.cookie.substring(middle + 1, end);
		}
		cookies[name] = unescape(value); // добавить в массив
		beginning = end + 2; // пропуск до начала следующего поля
	}
	return;
}

/**
* Пример использования: 
* setInterval("timestamp(id)", msecs);
* 
* Описание: функция записывает в текстовом узле (id родительского узла
* указывается в аргументе переданном функции) строку с текущей меткой 
* времени в формате: H:i:s d F Y.
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
		case 0: myArray[4] = 'января';
        break;
        case 1: myArray[4] = 'февраля';
        break;
        case 2: myArray[4] = 'марта';
        break;
        case 3: myArray[4] = 'апреля';
        break;
        case 4: myArray[4] = 'мая';
        break;
        case 5: myArray[4] = 'июня';
        break;
        case 6: myArray[4] = 'июля';
        break;
        case 7: myArray[4] = 'августа';
        break;
        case 8: myArray[4] = 'сентября';
        break;
        case 9: myArray[4] = 'октября';
        break;
        case 10: myArray[4] = 'ноября';
        break;
        default: myArray[4] = 'декабря';
	}
	myArray[6] = myDate.getDay();
	switch(myArray[6]) {
		case 0:
		myArray[6] = '';
		newNode = redDay('(Вс.)');		
		break;
		case 1: myArray[6] = '(Пн.)';
		break;
		case 2: myArray[6] = '(Вт.)';
		break;
		case 3: myArray[6] = '(Ср.)';
		break;
		case 4: myArray[6] = '(Чт.)';
		break;
		case 5: myArray[6] = '(Пт.)';
		break;
		case 6:
		myArray[6] = '';
		newNode = redDay('(Сб.)');
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
		// вставка красного дня календаря
		if(myArray[6] == '') {
				if(document.getElementById(id).lastChild.nodeName != 'B') {
					document.getElementById(id).appendChild(newNode);
			}
		}
		// удаление красного дня календаря
		var blackDays = new RegExp('(Пн|Вт|Ср|Чт|Пт)', 'i');
		var redDays = new RegExp('(Сб|Вс)', 'i');
		var currentElement = document.getElementById(id);
		if(document.getElementById(id).lastChild.nodeName == 'B') {
			// если уже закончился красный день
			if(blackDays.test(document.getElementById(id).firstChild.nodeValue) && redDays.test(document.getElementById(id).lastChild.firstChild.nodeValue)) {
				document.getElementById(id).removeChild(document.getElementById(id).lastChild);
			}
		}
	}
	return;
}

/**
* Передача фокуса первому полю первой формы*
* Взято из "Полного справочника по JavaScript" стр. 489
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
* Пример использования: 
* onsubmit="return validate(this.name, id)"
* 
* Описание: функция проверяет введенные пользователем данные в поле "Имя:", заданное
* параметром функции и возвращает true, если данные прошли проверку, false 
* в другом случае.
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
        report[++i] = 'Ошибка: Поле "Имя:" обязательно для заполнения!' + "\n";
	if (field.value.length < 2)
        report[++i] = 'Ошибка: Имя слишком короткое! Минимум: два символа.' + "\n";
	if (!(/^[a-zа-яА-Яё][\wа-яА-Яё -]*$/i.test(field.value))) {
		report[++i] = 'Ошибка: Некорректное имя! Имя может содержать любые буквенно-цифровые символы, знак подчеркивания, пробел, дефис и должно начинаться с буквы.' + "\n";
		field.select();
	}
	if (report.length > 0) {
		if (report.length < 3)
			document.getElementById(id + 'internal').firstChild.nodeValue = report.join('');
		// если все ошибки сразу, значит форма пустая
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
* Пример использования: 
* onsubmit="return validate_all(this.name, this.mail, id)"
* 
* Описание: функция проверяет введенные пользователем данные в поле "Имя:" и в 
* поле E-mail, заданные параметрами функции и возвращает true, если данные прошли проверку,
* false в другом случае. 
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
		report[++i] = 'Ошибка: Поле "Имя:" обязательно для заполнения!' + "\n";
		field.focus();
	}
	if (field.value.length < 2) {
		report[++i] = 'Ошибка: Имя слишком короткое! Минимум: два символа.' + "\n";
		field.focus();
	}
	if (!((field2.value == null)||(field2.value.length == 0)||(!(/\S/.test(field2.value))))) {
		if (!(/[^@]+@(\w+\.)+\w+/.test(field2.value))) {
			report[++i] = 'Ошибка: E-mail имеет недопустимый формат.' + "\n";
			field2.select();
			field2.focus();
		}
	}
	if (!(/^[a-zа-яА-Яё][\wа-яА-Яё -]*$/i.test(field.value))) {
		report[++i] = 'Ошибка: Некорректное имя! Имя может содержать любые буквенно-цифровые символы, знак подчеркивания, пробел, дефис и должно начинаться с буквы.' + "\n";
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