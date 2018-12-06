<?php
class PLib_Mail_Notification {
    private $template_path = '';
    private $values = array();
    private $tos = array();

    public function __construct($template_path) {
        if(file_exists($template_path)) {
            $this->template_path = $template_path;
        }
    }

    public function set_values($values) {
        if(is_array($values)) {
            $this->values = array_merge($this->values,$values);
        }
    }

    public function set_to($tos) {
        $this->tos = is_array($tos) ? $tos : array($tos);
    }

    public function send() {
        if(!(empty($this->template_path) || empty($this->values) || empty($this->tos))) {
            foreach ($this->tos as $to) {
                $this->values += array('to' => $to);
                $mail = $this->template($this->template_path, $this->values);
                $this->mailx($mail);
            }
        }
    }

    private function template($fname, $vars) {
        ob_start();
        extract($vars, EXTR_OVERWRITE);
        include($fname);
        return ob_get_clean();
    }

    /**
     * Функция отправляет письмо, полностью заданное в параметре $mail.
     * корректно обрабатываются заголовки To и Subject.
     *
     * @param string $mail
     */
    private function mailx($mail) {
        // Разделяем тело сообщения и заголовки.
        list ($head, $body) = preg_split("/\r?\n\r?\n/s", $mail, 2);
        // Выделяем заголовок To.
        $to = "";
        if (preg_match('/^To:\s*([^\r\n]*)[\r\n]*/m', $head, $p)) {
            $to = @$p[1]; // сохраняем
            $head = str_replace($p[0], "", $head); // удаляем из исходной строки
        }
        // Выделяем Subject.
        $subject = "";
        if (preg_match('/^Subject:\s*([^\r\n]*)[\r\n]*/m', $head, $p)) {
            $subject = '=?' . $GLOBALS['CONFIG']['general']['charset'] . '?B?' . base64_encode(@$p[1]) . '?=';
            $head = str_replace($p[0], "", $head);
        }
        // Удаляем комменарии
        $head = preg_replace("%^#(.*)$%m", "", $head);
        mail($to, $subject, $body, trim($head));
    }
}
?>