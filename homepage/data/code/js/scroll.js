scroll() ‘ункци€ прокрутчик текста
<script type="text/javascript">
//<![CDATA[
/**
 * ѕример использовани€:
 * setInterval("scroll(expression, id, quantity, offset, textNodeLength)", msecs);
 * 
 * ќписание: ‘ункци€ при каждом вызове последовательно увеличивает количество повторений
 * переданного ей первого аргумента, в текстовом узле (id родительского узла указываетс€
 * во втором аргументе) до значени€, указанного третьим аргументом, смещение задаетс€ четвертым
 * аргументом, если оно отрицательное, то отсчитываетс€ от конца текстового узла, в п€том
 * аргументе указываетс€ первоначальна€ длина текстового узла.
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
    // присваиваем переменным переданные аргументы
    var i = -1;
    var expression     = scroll.arguments[++i];
    var id             = scroll.arguments[++i];
    var quantity       = scroll.arguments[++i];
    var offset         = scroll.arguments[++i];
    var textNodeLength = scroll.arguments[++i];
    // DOM поддерживаетс€?
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
				) { textNode.nodeValue = "‘ункции scroll() переданы неправильные аргументы!";
				    return false; }
				// длина повтор€емого элемента (первого аргумента)
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