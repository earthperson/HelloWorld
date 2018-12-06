<?php
class DbManager {
    public function import() {
        if(!empty($_FILES) && $_FILES['data']['error'] == UPLOAD_ERR_OK) {
            $input = trim(file_get_contents($_FILES['data']['tmp_name']));
            if(!$input = @iconv($_POST['charset'], 'utf-8', $input)) {
                throw new Exception();
            }
            if(!trim($_POST['new_project'])) {
                throw new Exception();
            }
            mysql_query("INSERT INTO gr_project SET name='" . mysql_real_escape_string(trim($_POST['new_project'])) . "'");
            $new_project_id = mysql_insert_id();
            $tok = strtok($input, "\r\n");
            $offset = 0;
            while($tok !== false) {
                if($offset >= (int)$_POST['offset']) {
                    mysql_query("INSERT INTO gr_prod SET project_id={$new_project_id}, title='" . mysql_real_escape_string(trim($tok)) . "'");
                }
                $offset++;
                $tok = strtok("\r\n");
            }
            setcookie('action-status', 'ok');
            header("Location: http://{$_SERVER['HTTP_HOST']}");
            exit();
        }
        else {
            throw new Exception();
        }
    }
    
    public function export($project_id) {
        global $CONFIG;
        $project_id = (int)$project_id;
        $name = "export{$project_id}";
        $separator = ' / ';
        $output = '';
        $headers = PLib_Common::serialize("
        	SELECT
        		CONCAT_WS('{$separator}', prop.title, prop.label) AS header
        	FROM
        		gr_properties AS prop
            	INNER JOIN gr_prod AS prod USING(prod_id)
        	WHERE
        		project_id={$project_id}
        	GROUP BY
        		prop.title, prop.label
        	ORDER BY
        		header
        ");
        if($headers) {
            foreach($headers as &$item) {
                $item = $item['header'];
            }
            array_unshift($headers, 'ИД продукта', 'Название продукта', 'Название продукта с маркета', 'Режим захвата');
            $output .= $this->to_string($headers, 'Header');
            $headers = array_flip($headers);
            $res = PLib_Common::serialize("
            	SELECT
            		prod.prod_id,
            		prod.title AS prod_title,
            		prod.market_title AS prod_market_title,
            		prod.capture_mode,
            		prop.*
            	FROM
            		gr_properties AS prop
                	LEFT JOIN gr_prod AS prod USING(prod_id)
                WHERE	
                	project_id={$project_id}
                ORDER BY
                	prod.prod_id
            ");
            $prod_id = -1;
            $columns = array_fill(0, count($headers), '');
            foreach($res as $rec) {
                if($prod_id < 0) {
                    $prod_id = (int)$rec['prod_id'];
                }
                if($index = @$headers["{$rec['title']}{$separator}{$rec['label']}"]) {
                    $columns[$index] = $rec['value'];
                }
                $next_rec = next($res);
                if($prod_id !== (int)@$next_rec['prod_id']) { // prod_id will be changed or end of loop
                    $prod_id = (int)@$next_rec['prod_id'];
                    $i = 0;
                    $columns[$i++] = $rec['prod_id'];
                    $columns[$i++] = $rec['prod_title'];
                    $columns[$i++] = $rec['prod_market_title'];
                    $columns[$i++] = $rec['capture_mode'];
                    $output .= $this->to_string($columns);
                    $columns = array_fill(0, count($headers), '');
                }
            }
            if($output) {
                $output = '<?xml version="1.0" encoding="' . $CONFIG['general']['charset'] . '"?>
                           <?mso-application progid="Excel.Sheet"?>
                           <Workbook
                                xmlns="urn:schemas-microsoft-com:office:spreadsheet"
                                xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
                           <ss:Styles>
                             <ss:Style ss:ID="Default" ss:Name="Normal">
                                 <Font ss:Bold="0"/>
                             </ss:Style>
                             <ss:Style ss:ID="Header">
                                 <Font ss:Bold="1"/>
                             </ss:Style>
                           </ss:Styles>
                		   <Worksheet ss:Name="'. $name . '">
                		     <ss:Table>
                            	"' . $output . '"
                		     </ss:Table>
                		   </Worksheet>
                		   </Workbook>';
            }
        }
        
        header("HTTP/1.0 200 OK");
        header("Status: 200 OK");
        header("Accept-Ranges: bytes");
        header("Content-Length: " . strlen($output));
        header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT"); // Date in the past
        header("Content-Type: application/vnd.ms-excel; charset={$CONFIG['general']['charset']}");
        header("Content-Disposition: attachment; filename={$name}.xls");
        
        print $output;
    }
    
    private function to_string($input, $style_id = 'Default') {
        ksort($input);
        $output = '';
        foreach($input as $item) {
            $output .= '<ss:Cell ss:StyleID="' . $style_id . '">
                          <ss:Data ss:Type="String"><![CDATA[' . $item . ']]></ss:Data>
                        </ss:Cell>'."\n";
        }
        return "<ss:Row>{$output}</ss:Row>\n";
    }
}
?>