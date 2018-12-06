/**
 * Пример использования:
 * setInterval("scroll(expression, id, quantity, offset, textNodeLength)", msecs);
 * 
 * Описание: Функция при каждом вызове последовательно увеличивает количество повторений
 * переданного ей первого аргумента, в текстовом узле (id родительского узла указывается
 * во втором аргументе) до значения, указанного третьим аргументом, смещение задается четвертым
 * аргументом, если оно отрицательное, то отсчитывается от конца текстового узла, в пятом
 * аргументе указывается первоначальная длина текстового узла.
 * 
 * @param string expression
 * @param string id
 * @param number quantity
 * @param number offset
 * @param number textNodeLength
 * @return bool
 *
 * Copyright 2007 Ponomarev Dmitry
 * http://dmitry-ponomarev.ru
 */
function scroll() {
    // присваиваем переменным переданные аргументы
    var i = -1;
    var expression     = scroll.arguments[++i];
    var id             = scroll.arguments[++i];
    var quantity       = scroll.arguments[++i];
    var offset         = scroll.arguments[++i];
    var textNodeLength = scroll.arguments[++i];
    // DOM поддерживается?
    if(document.getElementById) {
        // узел имеет дочерний узел?
		if (document.getElementById(id).hasChildNodes()) {
			var textNode = document.getElementById(id).firstChild;
			// текстовый?
		    if (textNode.nodeType == 3) {
		    	// проверка переданных функции аргументов
				if (
				(typeof(expression) != "string")       || (expression == "")                   || 
				(typeof(id) != "string")               || (typeof(quantity) != "number")       ||
				(typeof(offset) != "number")           || (typeof(textNodeLength) != "number") ||
				(Math.abs(offset) > textNodeLength)
				) { textNode.nodeValue = "Функции scroll() переданы неправильные аргументы!";
				    return false; }
				// длина повторяемого элемента (первого аргумента)
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

/**
* Добавляет строку cookie, если она не установлена, в противном случае
* удаляет, использует ассоциативный массив cookies["имя"]="значение",
* установленный функцией extractCookies()
*
* @return undefined
*/
function screensaver() {
	if(!cookies['screensaver'])	{
		var period = new Date();
		period.setYear(period.getFullYear() + 10);
		document.cookie = "screensaver=no; expires=" + period.toUTCString();
	}
	else {
		document.cookie = "screensaver=nothing; expires=Thu, 01-Jan-1970 00:00:01 GMT";
	}
	return;
}

var engineIndexLoad = true;