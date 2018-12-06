<?php
abstract class PLib_Layout_AbstractList {
    protected $res;
    protected $headers = array();
    protected $css_class = '';
    protected $num_cols;
    protected $footer_content = '';
    protected $footer_content_align = 'left';

    public function __construct(array $res, array $headers=null, $css_class = '') {
        $this->res = $res;
        if($headers) {
            $this->set_headers($headers);
        }
        $this->css_class = $css_class;
    }

    public function set_headers(array $headers) {
        $this->headers = $headers;
        $this->num_cols = count($this->headers);
    }
    
    public function set_footer($content, $align = 'left') {
        $this->footer_content = $content;
        $this->footer_content_align = $align;
    }
    
    public function __toString() {
        if($this->res) {
            ob_start();
            print '<table cellpadding="0" cellspacing="0" border="0" class="data-list ' . $this->css_class . '">';
            $this->render_headers();
            $this->rows();
            $this->render_footer();
            print '</table>';
            return ob_get_clean();
        }
        else {
            return '';
        }
    }
    
    abstract protected function rows();
    
    protected function render_headers() {
        if($this->headers) {
            print '<tr>';
            foreach ($this->headers as $th) {
                print '<th>' . plain_text($th) .  '</th>';
            }
            print '</tr>';
        }
    }
    
    protected function render_footer() {
        if($this->footer_content) {
            print "<tr><td colspan='{$this->num_cols}' align='{$this->footer_content_align}' class='footer'>{$this->footer_content}</td></tr>";
        }
    }
}
?>