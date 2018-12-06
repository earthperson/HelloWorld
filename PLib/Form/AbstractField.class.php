<?php
abstract class PLib_Form_AbstractField {
	public $name;
	public $value;
	public $id;
	public $max_file_size;
	public $rec;
    
    abstract protected function getFieldHTML();
    abstract protected function getSqlFromPost();
}
?>