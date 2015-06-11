<?php

class ACPSpecialty {
	protected $id;
	protected $name;
	
	public function __construct($data = null) {
		if (is_object($data)) {
			$this->load_from_object($data);
		}
		elseif (is_string($data)) {
			$this->load_from_string($data);
		}
		elseif (is_numeric($data)) {
			$this->load_from_id($data);
		}
	}
	
	public function __toString() {
		return $this->name;
	}
	
	public function __get($n) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			return $this->$n;
		}
	}
	
	public function __set($n, $v) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			$this->$n = $v;
			switch ($n) {
				case 'id':
					$this->id = (int)$v;
					break;
				default:
					$this->$n = $v;
					break;
			}
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_specialty_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			ACPManager::error("Unable to load from ID#{$id}. Not Found", 0, ACPManager::E_LEVEL_WARNING);
		}
	}
	
	public function load_from_string($name) {
		$obj = ACPManager::sql_specialty_load_from_name($name);
		if ($obj) {
			$this->load_from_object($obj);
		}
		/*else {
			ACPManager::error("Unable to load from Name: {$name}. Not Found", 0, ACPManager::E_LEVEL_WARNING);
		}*/
	}
	
	public function load_from_object($obj) {
		if (!is_object($obj)) {
			return;
		}
		if ($obj instanceof ACPSpecialty) {
			$this->clone_from_object($obj);
			return;
		}
		
		$this->id = $obj->spec_id;
		$this->name = $obj->spec_name;
	}
	
	public function clone_from_object(ACPSpecialty $obj) {
		$this->id = $obj->id;
		$this->name = $obj->name;
	}
	
	public function insert() {
		if (empty($this->name)) {
			ACPManager::error('Cannot insert specialty without a $name property');
		}
		else {
			$this->id = ACPManager::sql_specialty_insert($this->name);
			return (bool)$this->id;
		}
		return false;
	}
	
	public function update() {
		if (empty($this->id)) {
			ACPManager::error('Cannot update specialty without an $id property');
		}
		elseif (empty($this->name)) {
			ACPManager::error('Cannot update specialty without a $name property');
		}
		else {
			return ACPManager::sql_specialty_update($this->name, $this->id);
		}
		return false;
	}
	
	public function delete() {
		return ACPManager::sql_specialty_delete($this->id);
	}
}

?>