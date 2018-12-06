str_lencrlf() Функция отображения кол-ва введенных символов без учета CR и LF
<script type="text/javascript">
//<![CDATA[
/**
 * Пример использования: 
 * setTimeout("str_lencrlf(max, id_field, id_node)", msecs);
 * 
 * Функция записывает в текстовый узел число введенных в поле формы символов без учета символов
 * возврата каретки и переноса строки. Если число введенных символов больше некоторого предела,
 * заданного первым параметром функции, оформление текста меняется.
 * 
 * @return undefined
 * © 2007 Ponomarev Dmitry
 * http://dmitry-ponomarev.ru
 */
function str_lencrlf(max, id_field, id_node) {
	if(document.getElementById) {
		var str = document.getElementById(id_field).value;
		// разбиваем строку в тех позициях, где встречается символы переноса строки,
		// а затем полученный массив преобразуем обратно в строку уже не содержащую символов переноса строки
		// если в строке нет символа "\r" - под Mozilla, возможно под LINUX
	    if (str.indexOf("\r") == -1)
			var arr = str.split("\n");
		else
			var arr = str.split("\r\n");
		str = arr.join('');
		var count = str.length;
		document.getElementById(id_node).firstChild.nodeValue = count;
		if(count > max) {
		    document.getElementById(id_node).style.color = 'red';
		    document.getElementById(id_node).style.fontWeight = 'bold';
		}
		else {
			document.getElementById(id_node).style.color = 'black';
			document.getElementById(id_node).style.fontWeight = 'normal';	 	    
		}
	}
	return;
}
//]]>
</script>
