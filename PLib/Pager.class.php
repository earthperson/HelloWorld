<?php
/**
 * Pager is a singleton
 *
 */
class PLib_Pager {
	private static $instance;
	private $recs = 0;			 // Всего записей 
	private $recs_on_page;       // Записей на странице
	private $max_recs = 150;     // Максимальное кол-во записей в режиме "все страницы"
	private $url;				 // Какой URL для ссылки на страницу (к нему добавляется номер страницы)
	private $pager_length;		 // Cколько ссылок на страницы выводить одновременно
	private $page_url_key = 'p'; // Ключ в массиве GET параметров для номера страницы
	private $page = 1; 			 // Номер текущей страницы
	private $count = 0; 		 // Общее кол-во страниц
	private $begin = 0;
	private $mode = null;
	private $inited = false;
	private $language = 'ru';
	
	private function __construct() {}
	
	private function __clone() {}
	
	public static function getInstance() {
		if(self::$instance === null) {
			self::$instance = new self;
		}
		return self::$instance;
	}
	
	public function init($recs, $url = '?page=', $recs_on_page = 15, $pager_length = 10) {
		$this->recs 		= (int)$recs;
		$this->url 			= (string)$url;
		$this->recs_on_page = (int)$recs_on_page;
		$this->pager_length = (int)$pager_length;
		if($this->recs > 0 && $this->recs_on_page > 0 && $this->pager_length > 0) {
	    	$this->inited = true;
	    }
	    else {
	    	return false;
	    }
	    $this->count = ceil($this->recs / $this->recs_on_page);
		if(isset($_GET[$this->page_url_key])) {
			if($_GET[$this->page_url_key] == 'all') {
				if($this->recs <= $this->max_recs) {
					$this->mode = 'all';
				}
			}
			else {
				$this->page = (int)abs($_GET[$this->page_url_key]);
			}
		}
		if($this->mode != 'all') {
			// Проверка на корректность номера текущей страницы
			if($this->page > ceil($this->recs / $this->recs_on_page) or $this->page < 1) {
		        $this->page = 1;
		    }
		    $this->begin = ($this->page - 1) * $this->recs_on_page;
		}
	}
	
	public function setLng($language) {
	    $this->language = $language;
	}
	
	public function getSqlLimit() {
		if($this->inited) {
			return ($this->mode == 'all') ? " LIMIT 0, {$this->max_recs}" : " LIMIT {$this->begin}, {$this->recs_on_page}";
		}
		else {
			return '';
		}
	}
	
	public function getHtml() {
	    global $TPLD;
		if(!$this->inited or $this->recs <= $this->recs_on_page) {
			return '';
		}
	    $begin_loop = 1; // Начальное значение в цикле
	    $end_loop = $this->count; // Конечное значение в цикле
	    $output = "<div class='pager'>" . PLib_Common::translate('PLib_Pager/pages', $this->language) . " ({$this->count}):&nbsp;";
	    // Далее в функции идёт сам вывод навигации, получено здесь всё опытным путём
	    if ($this->page > $this->pager_length) {
	        $output .= "&nbsp;<a href='{$this->url}";
	        $output .= $this->pager_length*(floor($this->page/$this->pager_length)-($this->page%$this->pager_length==0 ? 1: 0));
	        $output .= "' class='page'>(";
	        $output .= $this->pager_length*(floor($this->page/$this->pager_length)-1-($this->page%$this->pager_length==0 ? 1: 0))+1;
	        $output .= '-';
	        $output .= $this->pager_length*(floor($this->page/$this->pager_length)-($this->page%$this->pager_length==0 ? 1: 0));
	        $output .= ')</a>...&nbsp;';
	        $begin_loop=$this->pager_length*(floor($this->page/$this->pager_length)-($this->page%$this->pager_length==0 ? 1: 0))+1;
	    }
	    if ($this->count>$this->pager_length*(floor($this->page/$this->pager_length)-($this->page%$this->pager_length==0 ? 1: 0)+1)) {
	        $end_loop=$this->pager_length*ceil($this->page/$this->pager_length);
	    }
	    for ($i = $begin_loop; $i <= $end_loop;  $i++) {
	        if($i == $this->page && $this->mode != 'all') {
	        	$output .= "<b>&nbsp;{$i}&nbsp;</b>";
	        }
	        else {
	            $output .= "&nbsp;<a href='{$this->url}{$i}' class='page'>{$i}</a>&nbsp;";
	        }
	    }
	    if ($this->count>$this->pager_length*(floor($this->page/$this->pager_length)-($this->page%$this->pager_length==0 ? 1: 0)+1)) {
	        $output .= "&nbsp;...<a href='{$this->url}";
	        $output .= $this->pager_length*ceil($this->page/$this->pager_length)+1;
	        $output .= "' class='page'>(";
	        $output .= $this->pager_length*ceil($this->page/$this->pager_length)+1;
	        if ($this->pager_length*ceil($this->page/$this->pager_length)+1<$this->count) {
	            $output .= '-';
	            $output .= $this->count<=$this->pager_length*(ceil($this->page/$this->pager_length)+1) ? $this->count: $this->pager_length*(ceil($this->page/$this->pager_length)+1);
	        }
	        $output .= ')</a>';
	    }
	    if($this->recs <= $this->max_recs) {
			$output .= ($this->mode == 'all') ? '&nbsp;<b>&nbsp;' . PLib_Common::translate('PLib_Pager/show_all', $this->language) . '&nbsp;</b>' : "&nbsp;<a href='{$this->url}all' class='page'>" . PLib_Common::translate('PLib_Pager/show_all', $this->language) . "</a>";
	    }
    	$output .= '&nbsp;</div>';
    	return $output;
	}
}
?>