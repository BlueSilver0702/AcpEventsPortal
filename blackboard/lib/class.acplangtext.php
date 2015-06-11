<?php

class ACPLangText {
	protected $id;
	protected $lobj_id;
	protected $lang_id;
	protected $string;
	
	public function __construct($data = null) {
		if (is_numeric($data)) {
			$this->load_from_id($data);
		}
		elseif (is_object($data)) {
			$this->load_from_object($data);
		}
	}
	
	public function __toString() {
		return $this->string;
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
				case 'lobj_id':
				case 'lang_id':
					$this->$n = (int)$v;
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
	
	public function load_from_id($data) {
		$data = ACPManager::lang_text_by_id($data);
		$this->load_from_object($data);
	}
	
	public function load_from_object($data) {
		if (!is_object($data)) {
			return;
		}
		$this->__set('id', $data->text_id);
		$this->__set('lobj_id', $data->lobj_id);
		$this->__set('lang_id', $data->lang_id);
		$this->__set('string', $data->text_string);
	}
	
	public function save() {
		if (empty($this->id)) {
			return $this->insert();
		}
		return $this->update();
	}
	
	public function insert() {
		if (empty($this->lang_id)) {
			return ACPManager::error('Cannot insert language with an empty $lang_id property');
		}
		if (empty($this->lobj_id)) {
			return ACPManager::error('Cannot insert language with an empty $lobj_id property');
		}
		$this->id = ACPManager::lang_text_insert($this->lang_id, $this->lobj_id, $this->string);
		return (bool)$this->id;
	}
	
	public function update() {
		if (empty($this->id)) {
			return ACPManager::error('Cannot update language with an empty $id property');
		}
		if (empty($this->lang_id)) {
			return ACPManager::error('Cannot update language with an empty $lang_id property');
		}
		if (empty($this->lobj_id)) {
			return ACPManager::error('Cannot update language with an empty $lobj_id property');
		}
		return ACPManager::lang_text_update($this->id, $this->lang_id, $this->lobj_id, $this->string);
	}
}

?>