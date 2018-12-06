myRssParse() ���������� ���������� Rss ����� � ���� �������������� �������
<?php
/**
 * ���������� ���������� Rss ����� � ���� �������������� ������� �� 4 ���������,
 * PUBDATE, TITLE, LINK, DESCRIPTION, �� ���-�� � ��� ������������ ��������� �����
 * ��������������, ���������� ������� ���������� expat
 *
 * @param string $source
 * @return array
 * @copyright http://dmitry-ponomarev.ru
 */
function myRssParse($source) {
	// ������� ��������� ���������
	if(!($parser = @xml_parser_create('UTF-8'))) {
		print '��������� ��������� ������� ����������!';
		exit();
	}
	// ������� ����� ����
	if($data = file_get_contents($source)) {
		// ���������������� ���� � ��������� ��������� � ������
		xml_parse_into_struct($parser, $data, $structure, $index);
		// ������� ��������� ���������
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