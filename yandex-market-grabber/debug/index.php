<?php
$config = array(
                'add-xml-decl'  => true,
                'add-xml-space' => true,
    			'output-xml'    => true,
                'quote-nbsp'    => false,
                'doctype'		=> 'omit',
                'hide-comments' => true,
          );
$tidy = tidy_parse_string(file_get_contents('debug/result.html'), $config, 'utf8');
$tidy->CleanRepair();
$xml = @new SimpleXMLElement($tidy->value);
$res = $xml->xpath('//div[contains(@class,"b-offers")]');
print '<pre>';
$a = $res[0]->xpath('./div[1]/*//img/@src');
print_r((string)$a[0]);
exit;
?>