<?php
class Processor {
    private $project_id;
    private $products = array();
    private $product_id;
    private $tick = array(
    					'amount'    => 0,
    					'limit'     => 5,
    					'min_delay' => 2,
                        'max_delay' => 8
                    );
    private $progress = 0;
    private $url = 'http://market.yandex.ru';
    private $follow_depth = 0;
    private $follow_max_depth = 2;
    private $result;
    private $capture_time;
    private $prod_rec = array();
    private $properties = array();
    
    public function __construct($project_id) {
        $this->project_id = (int)$project_id;
    }
    
    public function action() {
        $this->set_action_progress();
        $this->products = PLib_Common::serialize(
				"SELECT * FROM gr_prod WHERE project_id={$this->project_id} AND capture_mode='' ORDER BY prod_id", "prod_id"
        );
        foreach($this->products as $id => $product) {
            $this->product_id = (int)$id;
            $this->follow_depth = 0;
            if($this->tick['amount'] >= $this->tick['limit']) { 
                $this->set_action_progress();
                throw new Exception();
            }
            $this->tick['amount']++;
            $this->request("{$this->url}/search.xml?text=" . urlencode(trim($product['title'])));
            $this->parse_result();
            $this->write();
            sleep(mt_rand($this->tick['min_delay'], $this->tick['max_delay']));
        }
    }
    
    public function reset() {
        mysql_query("UPDATE gr_prod SET
        				market_title=DEFAULT, image=DEFAULT, min_price=DEFAULT, avg_price=DEFAULT, max_price=DEFAULT, capture_time=DEFAULT, capture_mode=DEFAULT
        			 WHERE project_id={$this->project_id}");
        mysql_query("DELETE FROM gr_properties WHERE prod_id IN((SELECT prod_id FROM gr_prod WHERE project_id={$this->project_id}))");
        $this->progress = 100;
    }
    
    public function progress() {
        return (int)$this->progress;
    }
    
    private function request($url) {
        $opts = array(
          'http'=>array(
            'method' => "GET",
        	'header' =>
            	"User-Agent: Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.1.2) Gecko/20090729 AdCentriaIM/1.7 Firefox/3.5.2\r\n".
            	"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8\r\n".
                "Accept-Language: ru,en-us;q=0.7,en;q=0.3\r\n".
                "Accept-Charset: windows-1251,utf-8;q=0.7,*;q=0.7\r\n".
                "Cookie: Yandex-CS-Captcha-Evidence=84.52.99.15:1252085811:5aec1964e1c172e569f19911b57e9e87\r\n".
                "Connection: close\r\n"
          )
        );
        $this->result = file_get_contents($url, 0, stream_context_create($opts));
        $this->capture_time = date('Y-m-d H:i:s');
    }
    
    private function parse_result() {
        if($this->follow_depth++ > $this->follow_max_depth) {
            return null;
        }
        if($this->result) {
            $config = array(
                            'add-xml-decl'  => true,
                            'add-xml-space' => true,
                			'output-xml'    => true,
                            'quote-nbsp'    => false,
                            'doctype'		=> 'omit',
                            'hide-comments' => true,
                      );
            $tidy = tidy_parse_string($this->result, $config, 'utf8');
            $tidy->CleanRepair();
            $xml = @new SimpleXMLElement($tidy->value);
            $this->prod_rec = array();
            if($res = $xml->xpath('//h1[@id="global-model-name"]/text()')) {
                // capture mode detail
                $this->prod_rec['capture_mode'] = 'detail';
                // market title
                $this->prod_rec['market_title'] = (string)$res[0];
                // image
                if($res = $xml->xpath('//table[@id="model-pictures"]/*//a/@href')) { // big
                    $this->prod_rec['image'] = (string)$res[0];
                }
                else { // thumb
                    $res = $xml->xpath('//table[@id="model-pictures"]/*//img/@src');
                    $this->prod_rec['image'] = (string)$res[0];
                }
                // avg price
                $res = $xml->xpath('//div[@class="price"]/text()');
                $price = $this->fetch_numbers((string)$res[0]);
                $this->prod_rec['avg_price'] = @$price[0];
                // min price and max price
                $res = $xml->xpath('//div[@class="prices"]/div[3]/text()');
                $prices = $this->fetch_numbers((string)$res[0]);
                $this->prod_rec['min_price'] = @$prices[0];
                $this->prod_rec['max_price'] = @$prices[1];
                // properties
                $this->properties = array();
                if($res = $xml->xpath('//div[@id="full-spec-cont"]/table/*//tr')) {
                    $title = '';
                    foreach($res as $tr) {
                        if($tr->td['class'] == 'title') {
                            $t = (string)@$tr->td[0]->b;
                            if($t != $title) {
                                $title = $t;
                            }
                        }
                        if($tr->td['class'] == 'label') {
                            $this->properties[] = array(
                                                        'title'=>$title,
                            							'label'=>(string)@$tr->td[0]->span,
                            							'value'=>(string)@$tr->td[1]
                                                  );
                        }
                    }
                }
                
            }
            elseif($res = $xml->xpath('//div[contains(@class,"b-offers")]')) {
                if($href = $res[0]->xpath('./div[2]/h3/a/@href') and preg_match("%model\.xml\?%si", (string)$href[0])) {
                    // temporary capture mode follow
                    $this->prod_rec['capture_mode'] = 'follow';
                    $this->request("{$this->url}/" . ltrim((string)$href[0],'/'));
                    $this->parse_result();
                    return null;
                }
                else {
                    // capture mode list
                    $this->prod_rec['capture_mode'] = 'list';
                    // image
                    $res = $res[0]->xpath('./div[1]/*//img/@src');
                    $this->prod_rec['image'] = (string)$res[0];
                }
            }
            else {
                // capture mode blank
                $this->prod_rec['capture_mode'] = 'blank';
            }
        }
    }
    
    private function write() {
        if(in_array($this->prod_rec['capture_mode'], array('blank', 'list', 'detail'))) {
            $this->prod_rec['capture_time'] = $this->capture_time;
            mysql_query("UPDATE gr_prod SET " . $this->get_set_expr($this->prod_rec) . " WHERE prod_id={$this->product_id} LIMIT 1");
            foreach ($this->properties as $property) {
                mysql_query("INSERT INTO gr_properties SET prod_id={$this->product_id}, " . $this->get_set_expr($property));
            }
            $this->save_image();
        }
    }
    
    private function fetch_numbers($str) {
        $output = array();
        $buffer = preg_replace("%[[:blank:]]+%su", '', $str);
        foreach(preg_split("%(\d+)%su", $buffer, -1, PREG_SPLIT_DELIM_CAPTURE) as $item) {
            if(is_numeric($item)) {
                $output[] = (int)$item;
            }
        }
        return $output;
    }
    
    private function save_image() {
        if($info = @getimagesize($this->prod_rec['image'])) {
            $ext = '';
            if($info[2] == IMAGETYPE_GIF) {
                $ext = 'gif';
            }
            elseif($info[2] == IMAGETYPE_JPEG) {
                $ext = 'jpg';
            }
            elseif($info[2] == IMAGETYPE_PNG) {
                $ext = 'png';
            }
            if(!is_dir("var/{$this->project_id}")) {
                mkdir("var/{$this->project_id}");
            }
            file_put_contents(
            	"var/{$this->project_id}/{$this->product_id}.{$ext}",
                file_get_contents($this->prod_rec['image'])
            );
        }
    }
    
    private function get_set_expr($params) {
        $sql = '';
        foreach($params as $field=>$value) {
            if(!empty($sql)) { $sql .= ','; }
            $sql .= "{$field}='" . mysql_real_escape_string(trim($value)) . "'";
        }
        return $sql;
    }
    
    private function set_action_progress() {
        $total = PLib_Common::serialize("SELECT COUNT(*) AS cnt FROM gr_prod WHERE project_id={$this->project_id} LIMIT 1");
        $total_processed = PLib_Common::serialize("SELECT COUNT(*) AS cnt FROM gr_prod WHERE project_id={$this->project_id} AND capture_mode <> '' LIMIT 1");
        $this->progress = floor((100 * (int)$total_processed['cnt']) / (int)$total['cnt']);
    }
}
?>