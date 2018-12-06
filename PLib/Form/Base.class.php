<?php
require_once 'EM/emulator.lib.php';

/**
 * Class for convenience work with forms.
 * 
 * @author Idea Andrey Tushev http://tushev.com/
 * @author Implemented by Ponomarev Dmitry http://dmitry-ponomarev.ru
 * @todo disabled field did not added in sql - is it correct?
 * @todo Add 'tip' field like on support?
 *
 */
class PLib_Form_Base {
    
    /**
     * Enter description here...
     *
     * @var array
     */
    public $fieldsStruct = array();
    
    /**
     * Enter description here...
     *
     * @var string
     */
    protected $actionUrl = '';
    
    /**
     * Enter description here...
     *
     * @var array
     */
    protected $formAttributes = array(
        'method'  => 'post',
        'class'   => 'form',
        //'target'  => '',
        //'name'    => '',
        //'enctype' => ''
    );
    
    /**
     * Enter description here...
     *
     * @var array
     */
    protected $errorsInFill = array();
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $saveInput = true;
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $successMessages = array();
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $imageButtonFlag = false;
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $submitLabel = ' OK ';
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $regexpMessageDefault = 'Incorrect field: ';
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $wasUpdatedStructure = false;
    
    /**
     * Enter description here...
     *
     * @var mixed
     */
    protected $textFieldsWrapper = null;
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $submitButtonWrapper = null;
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $renderLayout = null;
    
    /**
     * Enter description here...
     *
     * @var unknown_type
     */
    protected $requiredFields = array();
    
    /**
     * Enter description here...
     *
     * @var string
     */
    protected $formWrapper = null;
    
    /**
     * @var boolean
     */
    protected $submitButton = true;
    
    /**
     * @var boolean
     */
    protected $trap = false;
    
    /*
     * 
     */
    protected $footer;

    /**
     * @param array $fieldsStruct
     * @param string $actionUrl
     * @param array $attributes
     */
    public function __construct($fieldsStruct = null, $actionUrl = '', $attributes = null) {
        if(is_array($fieldsStruct)) { // it can be null
            $this->fieldsStruct = $fieldsStruct;
        }
        $this->actionUrl = trim($actionUrl);
        if(is_array($attributes)) {
            $this->formAttributes = array_merge($this->formAttributes, $attributes);
        }
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @param array $field
     */
    public function setField($name, $field) {
        $this->fieldsStruct[$name] = $field;        
    }
    
	/**
     * Enter description here...
     *
     * @param string $url
     */
    public function setActionUrl($url) {
        $this->actionUrl = trim($url);      
    }
    
	/**
     * Enter description here...
     *
     * @param string $url
     */
    public function setAttributes($attributes = null) {
    	if(is_array($attributes)) {
            $this->formAttributes = array_merge($this->formAttributes, $attributes);
        }  
    }
    
    /**
     * Enter description here...
     * 
     */
    public function moveFieldAfter($moved_name, $after_name) {
    	
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     */
    public function unsetField($name) {
        unset($this->fieldsStruct[$name]);   
    }
    
    /**
     * Test http input fields
     *
     * @return boolean
     */
    public function testFields() {
    	if($this->trap) {
	    	if(!empty($_POST['from_to_email'])) {
	    		return false;
	    	}
    	}
        foreach($this->fieldsStruct as $name => $field) {
            if((bool)@$field['disabled']) { continue; }
            if(@$field['type'] == 'read_only_text') { continue; }
            if(!is_object(@$field['type'])) {
                if(isset($field['regexp'])) {
                    if(!preg_match($field['regexp'], @$_POST[$name])) {
                        $this->errorsInFill[$name] = isset($field['regexp_message'])
                        ? $field['regexp_message'] : $this->regexpMessageDefault . @$field['label'] . '.';
                    }
                }
            }
        }
        return empty($this->errorsInFill);
    }
    
 	/**
     * Enter description here...
     *
     * @param unknown_type $names
     */
    public function setRequiredFields($names) {
        if(!is_array($names)) { $names = array($names); }
        $this->requiredFields = array_merge($this->requiredFields,$names);
    }
    
	/**
     * Enter description here...
     *
     */
    public function setFormWrapper($wrapper) {
        $this->formWrapper = $wrapper;
    }
    
    /**
     * @param mixed $wrapper
     *
     */
    public function setTextFieldsWrapper($wrapper) {
        $this->textFieldsWrapper = $wrapper;
    }
    
    /**
     * Enter description here...
     *
     */
    public function setSubmitButtonWrapper($wrapper) {
        $this->submitButtonWrapper = $wrapper;
    }
    
    /**
     * Enter description here...
     * 
     */
    public function setRenderLayout($layout) {
    	$this->renderLayout = $layout;
    }
    
/**
     * Enter description here...
     * 
     */
    public function setFooter(AbstractFormFooter $obj) {
    	$this->footer = $obj->getContent();
    }
    
    /**
     * @todo update when type='checkbox' and etc
     *
     * @param resorce $res
     */
    public function updateFieldsStructFromSqlResource($res) {
        if(is_resource($res) AND mysql_num_rows($res)) {
            mysql_data_seek($res,0);
            $rec = mysql_fetch_assoc($res);
            foreach ($this->fieldsStruct as $name=>&$field) {
                if(!in_array(@$field['type'], array('separator', 'hidden', 'file', 'image'))) {
                    if(@$field['type'] == 'radio') {
                        $field['checked'] = @$rec[$name];
                    }
                    elseif(@$field['type'] == 'checkbox') {
                        $field['checked'] = @$rec[$name] == 'Y'  ? true : false;
                    }
                    elseif(@$field['type'] == 'list') {
                        $field['selected'] = @$rec[$name];
                    }
                    elseif(@$field['type'] == 'password') {
                        $field['value'] = str_repeat('*', 15);
                    }
                	elseif(isset($field['type']) && is_object($field['type'])) {
                        $field['type']->rec = $rec;
                    }
                    else {
                        $field['value'] = @$rec[$name];
                    }
                }
            }
            mysql_data_seek($res,0);
            $this->wasUpdatedStructure = true;
        }
    }

	/**
     * Get HTML form
     * 
     * @param string $layout
     * @return string
     */
    public function __toString() {
    	ob_start();
    	// Split
        if(is_array($this->renderLayout) && $this->renderLayout['method'] == 'split') {
        	$output = array('','','');
        	$cell = 0;
       		foreach($this->fieldsStruct as $name => $field)  {
                $id = $this->getId();
                if($this->renderLayout['separator'] == $name) {
                	$cell = 1;
                }
                if(@$field['type'] == 'separator') {
                    $output[$cell] .=  '<tr><td ' . ($this->hasLabel() ? 'colspan="2" ' : '') . $this->makeAttributes(@$field['attributes']) . ' class="separator"><h3>' . plain_text(@$field['text']) . '</h3></td></tr>';
                }
                else {
                    $output[$cell] .=  '<tr>';
                    if(!is_null(@$field['label'])) {
                        $output[$cell] .=  '<td class="label in-horizontal" valign="top"><label for="' . $id . '"' . (isset($this->errorsInFill[$name]) ? ' class="errors-in-fill"' : '') . '>' . plain_text(@$field['label']) . '</label>' . (in_array($name, $this->requiredFields) ? '<sup>*</sup>' : '') . '</td>';
                    }
                    $output[$cell] .=  '<td>';
                    if(!is_null($this->textFieldsWrapper)) {
                        if(!in_array(@$field['type'], array('separator', 'hidden', 'file', 'image', 'radio', 'read_only_text', 'list', 'checkbox'))) {
                            if(is_array($this->textFieldsWrapper)) {
                                if(isset($this->textFieldsWrapper[$name])) {
                                    $output[$cell] .= srintf($this->textFieldsWrapper[$name],$this->getFieldHTML($name, $field, $id));
                                }
                                else {
                                    $output[$cell] .=  $this->getFieldHTML($name, $field, $id);
                                }
                            }
                            else {
                                $output[$cell] .= srintf($this->textFieldsWrapper,$this->getFieldHTML($name, $field, $id));
                            }
                        }
                        else {
                            $output[$cell] .=  $this->getFieldHTML($name, $field, $id);
                        }
                    }
                    else {
                        $output[$cell] .=  $this->getFieldHTML($name, $field, $id);
                    }
                    $output[$cell] .=  '</td></tr>';
                }
            }
            if(!$this->imageButtonFlag && $this->submitButton) {
                $output[2] .= '<tr>';
                if($this->hasLabel()) {
                    $output[2] .= '<td>&nbsp;</td>';
                }
                $output[2] .= '<td>';
                if(!is_null($this->submitButtonWrapper)) {
                    $output[2] .= srintf($this->submitButtonWrapper,$this->getSubmitButtonHTML());
                }
                else {
                	$output[2] .= $this->getSubmitButtonHTML();
                }
                $output[2] .= '</td></tr>';
            }
            else {
	            if(!empty($this->footer)) {
	            	$output[2] .= $this->footer;
	            }
            }
            if(isset($this->renderLayout['wrapper'])) {
            	printf($this->renderLayout['wrapper'], $output[0], $output[1], $output[2]);
            }
        }
    	// Vertical
        elseif($this->renderLayout == 'vertical') {
            foreach($this->fieldsStruct as $name => $field)  {
                $id = $this->getId();
                if(@$field['type'] == 'separator') {
                    print '<tr><td' . $this->makeAttributes(@$field['attributes']) . ' class="separator"><h3>' . plain_text(@$field['text']) . '</h3></td></tr>';
                }
                else {
                    if(!is_null(@$field['label'])) {
                        print '<tr><td class="label in-horizontal" valign="top"><label for="' . $id . '"' . (isset($this->errorsInFill[$name]) ? ' class="errors-in-fill"' : '') . '>' . plain_text(@$field['label']) . '</label>' . (in_array($name, $this->requiredFields) ? '<sup>*</sup>' : '') . '</td></tr>';
                    }
                    print '<tr><td>';
                    if(!is_null($this->textFieldsWrapper)) {
                        if(!in_array(@$field['type'], array('separator', 'hidden', 'file', 'image', 'radio', 'read_only_text', 'list', 'checkbox'))) {
                            if(is_array($this->textFieldsWrapper)) {
                                if(isset($this->textFieldsWrapper[$name])) {
                                    printf($this->textFieldsWrapper[$name],$this->getFieldHTML($name, $field, $id));
                                }
                                else {
                                    print $this->getFieldHTML($name, $field, $id);
                                }
                            }
                            else {
                                printf($this->textFieldsWrapper,$this->getFieldHTML($name, $field, $id));
                            }
                        }
                        else {
                            print $this->getFieldHTML($name, $field, $id);
                        }
                    }
                    else {
                        print $this->getFieldHTML($name, $field, $id);
                    }
                    print '</td></tr>';
                }
            }
            if(!$this->imageButtonFlag && $this->submitButton) {
                print '<tr><td>';
                if(!is_null($this->submitButtonWrapper)) {
                    printf($this->submitButtonWrapper,$this->getSubmitButtonHTML());
                }
                else {
                	print $this->getSubmitButtonHTML();
                }
                print '</td></tr>';
            }
        	if(!empty($this->footer)) {
        		$footer = "<tr><td>{$this->footer}</td></tr>";
            }
        }
        // Chain
        /*elseif($layout == 'chain') {
            $sep = $fields = '';
            foreach($this->fieldsStruct as $name=>$field)  {
                $id = $this->getId();
                if(@$field['type'] == 'separator') {
                    $sep = '<tr><td colspan="' . count($this->fieldsStruct) . '" ' . $this->makeAttributes(@$field['attributes']) . ' class="separator">' . html_output(@$field['text']) . '</td></tr>';
                }
                else {
                    $fields .= '<td class="label in-chain"><label for="' . $id . '">' . html_output(@$field['label']) . @$field['tip'] . '</label> ' . $this->getFieldHTML($name, $field, $id) . '</td>';
                }
            }
            if(!$this->imageButtonFlag) {
                $fields .= '<td>' . $this->getSubmitButtonHTML() . '</td>';
            }
            print "{$sep}<tr>{$fields}</tr>";
        }*/
        // Horizontal
        else {
            foreach($this->fieldsStruct as $name => $field)  {
                $id = $this->getId();
                if(@$field['type'] == 'separator') {
                    print '<tr><td ' . ($this->hasLabel() ? 'colspan="2" ' : '') . $this->makeAttributes(@$field['attributes']) . ' class="separator"><h3>' . plain_text(@$field['text']) . '</h3></td></tr>';
                }
                else {
                    print '<tr>';
                    if(!is_null(@$field['label'])) {
                        print '<td class="label in-horizontal" valign="top"><label for="' . $id . '"' . (isset($this->errorsInFill[$name]) ? ' class="errors-in-fill"' : '') . '>' . plain_text(@$field['label']) . '</label>' . (in_array($name, $this->requiredFields) ? '<sup>*</sup>' : '') . '</td>';
                    }
                    print '<td>';
                    if(!is_null($this->textFieldsWrapper)) {
                        if(!in_array(@$field['type'], array('separator', 'hidden', 'file', 'image', 'radio', 'read_only_text', 'list', 'checkbox'))) {
                            if(is_array($this->textFieldsWrapper)) {
                                if(isset($this->textFieldsWrapper[$name])) {
                                    printf($this->textFieldsWrapper[$name],$this->getFieldHTML($name, $field, $id));
                                }
                                else {
                                    print $this->getFieldHTML($name, $field, $id);
                                }
                            }
                            else {
                                printf($this->textFieldsWrapper,$this->getFieldHTML($name, $field, $id));
                            }
                        }
                        else {
                            print $this->getFieldHTML($name, $field, $id);
                        }
                    }
                    else {
                        print $this->getFieldHTML($name, $field, $id);
                    }
                    print '</td></tr>';
                }
            }
            if(!$this->imageButtonFlag && $this->submitButton) {
                print '<tr>';
                if($this->hasLabel()) {
                    print '<td>&nbsp;</td>';
                }
                print '<td>';
                if(!is_null($this->submitButtonWrapper)) {
                    printf($this->submitButtonWrapper,$this->getSubmitButtonHTML());
                }
                else {
                	print $this->getSubmitButtonHTML();
                }
                print '</td></tr>';
            }
        	$footer = !empty($this->footer) ? "<tr><td colspan='2'>{$this->footer}</td></tr>" : '';
        }
        $fields = ob_get_clean();
        ob_start();
        print '<form action="' . $this->actionUrl . '" ' . $this->makeAttributes($this->formAttributes) . '>';
        if($this->trap) {
        	print '<input type="text" name="from_to_email" value="" style="display: none;" />';
        }
        print '<input type="hidden" name="uniqueFormNumber" value="' . $this->getId() . '" />';
        if(!empty($this->errorsInFill)) {
        	$this->errorsInFill = array_unique($this->errorsInFill);
        	print "<div class='error'>";
            foreach ($this->errorsInFill as $error) {
            	print '<div style="padding: 1px 0px;">' . plain_text($error) . '</div>';
            }
            print '</div>';
        }
        if(!empty($this->successMessages)) {
        	$this->successMessages = array_unique($this->successMessages);
        	print "<div class='success'>";
            foreach ($this->successMessages as $success) {
            	print '<div style="padding: 1px 0px;">' . plain_text($success) . '</div>';
            }
            print '</div>';
        }
        if(is_array($this->renderLayout) && $this->renderLayout['method'] == 'split') {
            print $fields;
        }
        else {
            print '<table cellspacing="0" cellpadding="2" border="0" width="100%">';
            print $fields;
            print $footer;
            print '</table>';
        }
        print '</form>';
        if(!is_null($this->formWrapper)) {
            return sprintf($this->formWrapper,ob_get_clean());
        }
        else {
        	return ob_get_clean();
        }
    }
    
    /**
     * Gets part of SQL from Post like "field_a='a', field_b='b'"
     * 
     * @return string
     */
    public function getSqlFromPost() {
        $sql = '';
        foreach($this->fieldsStruct as $name=>$field) {
            if(!is_object(@$field['type'])) {
                $val = trim(@$_POST[$name]);
                if(!get_magic_quotes_gpc()) {
                    $val = mysql_real_escape_string($val);
                }
                if(!in_array(@$field['type'], array('separator', 'hidden', 'file', 'image', 'read_only_text')) && !(bool)@$field['disabled']) {
                    if(@$field['type'] == 'password') {
                        if((strpos($val, str_repeat('*', 5)) === 0)) {continue;}
                        $sql .= "{$name}=MD5('{$val}'),";
                    }
                    elseif(@$field['type'] == 'checkbox') {
                        $sql .= "{$name}='" . (isset($_POST[$name]) ? 'Y' : 'N') . "',";
                    }
                    else {
                        $sql .= "{$name}='{$val}',";
                    }
                }
            }
            else {
            	$field['type']->name = $name;
                $sql .= @$field['type']->getSqlFromPost();
            }
        }
        return rtrim($sql, ',');
    }

    /**
     * Enter description here...
     *
     * @param unknown_type $errors
     */
    public function addErrorMessage($msg) {
        $this->errorsInFill[] = $msg;
    }
    
    /**
     * Enter description here...
     *
     * @param unknown_type $errors
     */
    public function addSuccessMessage($msg) {
        $this->successMessages[] = $msg;
    }

    /**
     * Set value on submit button
     *
     * @param string $label
     */
    public function setSubmitButtonLabel($label) {
        $this->submitLabel = $label;
    }
    
    /**
     * 
     * @return unknown_type
     */
	public function unsetSubmitButton() {
    	$this->submitButton = false;
    }
    
    /**
     * Enter description here...
     *
     */
    public function saveInput($option = true) {
        $this->saveInput = (bool)$option;
    }
    
	/**
     * Enter description here...
     *
     */
    public function useTrap($option = true) {
        $this->trap = (bool)$option;
    }
    
    /**
     * Enter description here...
     *
     * @param array $attributes
     * @return string
     */
    protected function makeAttributes($attributes) {
        $attributesStr = '';
        if(is_array($attributes)) {
            foreach ($attributes as $attribute=>$value) {
                $attributesStr .= $attribute . '="' . addslashes($value) . '" ';
            }
        }
        return trim($attributesStr);
    }
    
    /**
     * 
     * @return boolean
     */
    protected function hasLabel() {
        foreach ($this->fieldsStruct as $field) {
            if(isset($field['label'])) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Get uniqid identifier
     *
     * @return string
     */
    protected function getId() {
        return md5(uniqid(rand(), true));
    }
    
    /**
     * Enter description here...
     *
     * @param string $name
     * @param array $field
     * @param string $id
     * @return string
     */
    protected function getFieldHTML($name, $field, $id) {
        $name = plain_text($name);
        // If fields was filled incorrect
        if((!empty($this->errorsInFill) || ($this->saveInput && isset($_POST[$name]))) && !is_object(@$field['type'])) {
            if(!in_array(@$field['type'], array('separator', 'hidden', 'file', 'image', 'password' ,'read_only_text')) && !(bool)@$field['disabled'] && !$this->wasUpdatedStructure) {
                $value = plain_text( get_magic_quotes_gpc() ? stripslashes(@$_POST[$name]) : @$_POST[$name] );
            }
            else {
                $value = plain_text(@$field['value']);
            }
        }
        else {
            $value = plain_text(@$field['value']);
        }
        $attributes = isset($field['attributes']) ? $this->makeAttributes($field['attributes']) : '';
        $disabled = (bool)@$field['disabled'] ? ' disabled="disabled"' : '';

        // Textarea
        if(@$field['type'] == 'textarea') {
            return "<textarea name='{$name}' cols='45' rows='10' class='text-control' {$attributes} id='{$id}'{$disabled}>{$value}</textarea>";
        }
        // Password
        elseif(@$field['type'] == 'password') {
            return "<input type='password' name='{$name}' value='{$value}' class='text-control' {$attributes} id='{$id}'{$disabled} />";
        }
        // File
        elseif(@$field['type'] == 'file') {
            $this->formAttributes['enctype'] = 'multipart/form-data';
            return (@$field['max_file_size'] ? "<input type='hidden' name='MAX_FILE_SIZE' value='" . (int)$field['max_file_size'] . "' />" : '') 
            . "<input type='file' name='{$name}' {$attributes} id='{$id}'{$disabled} /> max"
            . ' ' . ini_get('upload_max_filesize');
        }
        // Hidden
        elseif(@$field['type'] == 'hidden') {
            return "<input type='hidden' name='{$name}' value='{$value}' />";
        }
        // List
        elseif(@$field['type'] == 'list') {
            if(is_array(@$field['values'])) {
                $o = '';
                foreach ($field['values'] as $v=>$l) {
                    if((!empty($this->errorsInFill) || $this->saveInput) && !is_object(@$field['type']) && !$this->wasUpdatedStructure) {
                        if(isset($_POST[$name]) && $_POST[$name] == (string)$v) { $field['selected'] = $v; }
                    }
                    $o .= '<option value="' . plain_text($v) . '"' . (@$field['selected'] === $v ? ' selected="selected"' : '') . '>' . plain_text($l) . '</option>';
                }
            }
            else {
                $o = '<option value="">&nbsp;</option>';
            }
            return "<select name='{$name}' class='text-control' {$attributes} id='{$id}'{$disabled}>{$o}</select>";
        }
        // Checkbox
        elseif(@$field['type'] == 'checkbox') {
            if((!empty($this->errorsInFill) || $this->saveInput) && !is_object(@$field['type'])) {
                if(isset($_POST[$name])) { $field['checked'] = true; }
            }
            return "<input type='checkbox' name='{$name}'" . (@$field['checked'] ? ' checked="checked"' : '') . " {$attributes} id='{$id}'{$disabled} />";
        }
        // Radio
        elseif(@$field['type'] == 'radio') {
            $s = '';
            if(is_array(@$field['values'])) {
                foreach ($field['values'] as $v=>$l) {
                    if((!empty($this->errorsInFill) || $this->saveInput) && !is_object(@$field['type']) && !$this->wasUpdatedStructure) {
                        if(isset($_POST[$name]) && $_POST[$name] == (string)$v) { $field['checked'] = $v; }
                    }
                    $i = $this->getId();
                    $s .= "<input type='radio' name='{$name}' value='" . plain_text($v) . "'" . (@$field['checked'] === $v ? ' checked="checked"' : '') . " {$attributes} id='{$i}'{$disabled} /><label for='{$i}'>" . plain_text($l) . '</label>';
                }
            }
            return '<div style="padding-bottom: 8px;">' . $s . '</div>';
        }
        // Image
        elseif(@$field['type'] == 'image') {
            $this->imageButtonFlag = true;
            return "<input type='image' {$attributes} src='{$field['src']}'{$disabled} class='button' />";
        }
        // Object
        elseif(is_object(@$field['type'])) {
            $field['type']->name = $name;
            $field['type']->value =& $value;
            $field['type']->id = $id;
            $field['type']->max_file_size = (int)@$field['max_file_size'];
            return $field['type']->getFieldHTML();
        }
        // Read only text
        elseif(@$field['type'] == 'read_only_text') {
            return "<div class='text-control-read-only'>{$value}</div>";
        }
        // Text
        else {
            return "<input type='text' name='{$name}' value='{$value}' class='text-control' {$attributes} id='{$id}'{$disabled} />";
        }
    }
    
    /**
     * Return submit button for the form. If in fields structure will detected field
     * with type=image this method never will called 
     *
     * @return string
     */
    protected function getSubmitButtonHTML() {
        return '<input type="submit" value="' . plain_text($this->submitLabel) . '" class="button" />';
    }
    
}
?>