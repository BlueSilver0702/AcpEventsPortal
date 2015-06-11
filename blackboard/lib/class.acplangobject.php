<?php

class ACPLangObject {
	protected $id;
	protected $name;
	protected $code;
	protected $text;
	
	public function __construct($data = null, $loadtext = true) {
		$this->text = array();
		if (is_numeric($data)) {
			$this->load_from_id($data, $loadtext);
		}
		elseif (is_string($data)) {
			$this->load_from_code($data, $loadtext);
		}
		elseif (is_object($data)) {
			$this->load_from_object($data, $loadtext);
		}
	}
	
	public function __get($n) {
		$n = strtolower($n);
		return (property_exists($this, $n) ? $this->$n : null);
	}
	public function __set($n, $v) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			switch ($n) {
				case 'id':
					$this->$n = (int)$v;
					break;
				case 'code':
					$this->code = ACPLang::to_code($v);
					break;
				case 'text':
					ACPManager::error('Cannot directly modify $text property');
					break;
				default:
					$this->$n = $v;
			}
		}
	}
	
	public function __isset($n) {
		$n = strtolower($n);
		return !empty($this->$n);
	}
	
	public function load_from_id($data, $loadtext = true) {
		$data = ACPManager::lang_obj_by_id($data, $loadtext);
		$this->load_from_object($data);
	}
	
	public function load_from_code($data, $loadtext = true) {
		$data = ACPManager::lang_obj_by_code($data, $loadtext);
		$this->load_from_object($data);
	}
	
	public function load_from_object($data, $loadtext = true) {
		if (!is_object($data)) {
			return;
		}
		$this->__set('id', $data->lobj_id);
		$this->__set('name', $data->lobj_name);
		$this->__set('code', $data->lobj_code);
		if ($loadtext) {
			$this->load_text();
		}
	}
	
	/*public function save() {
		if (empty($this->name)) {
			return ACPManager::error('Cannot save language with an empty $name property');
		}
		if (empty($this->code)) {
			//return ACPManager::error('Cannot save language with an empty $code property');
			$this->code = ACPLang::to_code($this->name);
		}
		
		if (empty($this->id)) {
			return $this->insert();
		}
		return $this->update();
	}*/
	
	public function insert() {
		if (empty($this->name)) {
			return ACPManager::error('Cannot insert language with an empty $name property');
		}
		if (empty($this->code)) {
			$this->code = ACPLang::to_code($this->name);
		}
		$this->id = ACPManager::lang_obj_insert($this->name, $this->code);
		return (bool)$this->id;
	}
	
	public function update() {
		if (empty($this->name)) {
			return ACPManager::error('Cannot update language with an empty $name property');
		}
		if (empty($this->code)) {
			$this->code = ACPLang::to_code($this->name);
		}
		return ACPManager::lang_obj_update($this->name, $this->code, $this->id);
	}
	
	public function load_text() {
		if (empty($this->id)) {
			return;
		}
		$text = ACPManager::lang_text_by_obj_id($this->id);
		if (!empty($text)) {
			foreach ($text as $v) {
				$this->text[$v->lang_id] = new ACPLangText($v);
			}
		}
	}
	
	public function add_text($lang_id, $data = null) {
		$this->text[$lang_id] = new ACPLangText($data);
	}
}

?>