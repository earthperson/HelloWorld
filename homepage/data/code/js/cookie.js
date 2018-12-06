setCookie(), extractCookies(), deleteCookie() Функции установки, чтения и удаления строк cookie
<script type="text/javascript">
//<![CDATA[
/**
 * Функция установки строки cookie
 * name - имя cookie
 * value - значение cookie
 * [expires] - дата окончания действия cookie (по умолчанию - до конца сессии)
 * [path] - путь, для которого cookie действительно (по умолчанию - документ, в котором значение было установлено)
 * [domain] - домен, для которого cookie действительно (по умолчанию - домен, в котором значение было установлено)
 * [secure] - логическое значение, показывающее требуется ли защищенная передача значения cookie
 * Пример: 
 * var d = new Date();
 * d.setYear(d.getFullYear() + 10);
 * setCookie('cookie', 'test', d, '', '', 1);
 */
function setCookie(name, value, expires, path, domain, secure) {
    var curCookie = name + "=" + escape(value) +
    ((expires) ? "; expires=" + expires.toUTCString() : "") +
    ((path) ? "; path=" + path : "") +
    ((domain) ? "; domain=" + domain : "") +
    ((secure) ? "; secure" : "");
    if ((name + "=" + escape(value)).length <= 4000) {
	    document.cookie = curCookie;
    }
    return;
}

/**
 * Функция анализирует текущие строки cookie и размещает соответствующие
 * элементы в ассоциативном массиве, индексированном по полю имя.
 * правило индексации ассоциативного массива: cookies["имя"]="значение"
 * 
 * Пример:
 * // сразу установим cookie, чтобы проверить наличие их поддержки
 * document.cookie = 'cookiesenabled=yes';
 * // получим установленные cookies в виде ассоциативного масива
 * extractCookies();
 * if(cookies['cookiesenabled'] == 'yes') {
 *      ..........
 * }
 * @copyright Взято из "Полного справочника по JavaScript" стр. 567-568
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
 * Функция удаления строки cookie
 * name - имя cookie
 */
function deleteCookie(name) {
    document.cookie = name + "=deleted; expires=Thu, 01-Jan-1970 00:00:01 GMT";
    document.cookie = name + "; expires=Thu, 01-Jan-1970 00:00:01 GMT";
    return;
}
//]]>
</script>