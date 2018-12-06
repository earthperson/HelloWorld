Часы
<script type="text/javascript">
//<![CDATA[
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
//]]>
</script>