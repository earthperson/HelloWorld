myRssParse() Возвращает содержимое Rss файла в виде ассоциативного массива
<?php
/**
 * Возвращает содержимое Rss файла в виде ассоциативного массива из 4 элементов,
 * PUBDATE, TITLE, LINK, DESCRIPTION, но кол-во и тип возвращаемых элементов легко
 * модифицируется, использует функции библиотеки expat
 *
 * @param string $source
 * @return array
 * @copyright http://dmitry-ponomarev.ru
 */
function myRssParse($source) {
	// Создать программу обработки
	if(!($parser = @xml_parser_create('UTF-8'))) {
		print 'Программу обработки создать невозможно!';
		exit();
	}
	// Принять целый файл
	if($data = file_get_contents($source)) {
		// Проанализировать файл и поместить результат в массив
		xml_parse_into_struct($parser, $data, $structure, $index);
		// Удалить программу обработки
		xml_parser_free($parser);
	}
	reset($structure);
	$matches = array();
	while (list(, $xml_elem) = each($structure)) {
		if ($xml_elem['type'] == 'complete' && $xml_elem['level'] == 4) {
			switch ($xml_elem['tag']) {
				case 'PUBDATE':
					if(isset($xml_elem['value'])) {
						$rss['date'][] =  trim($xml_elem['value']);
					}
					else {
						$rss['date'][] = '';
					}
					break;
				case 'TITLE':
					if(isset($xml_elem['value'])) {
						$rss['title'][] = trim(iconv('UTF-8', 'windows-1251', $xml_elem['value']));
					}
					else {
						$rss['title'][] = '';
					}
					break;
				case 'LINK':
					if(isset($xml_elem['value'])) {
						$rss['link'][] = trim($xml_elem['value']);
					}
					else {
						$rss['link'][] = '';
					}
					break;
				case 'DESCRIPTION':
					if(isset($xml_elem['value'])) {
						$rss['description'][] = trim(iconv('UTF-8', 'windows-1251', $xml_elem['value']));
					}
					else {
						$rss['description'][] = '';
					}
					break;
			}
		}
	}
	return $rss;
}
?>