<?php

abstract class ACPEditable {
	protected $id;
	protected $type;
	protected $created_by;
	protected $created;
	protected $modified;
	protected $modified_by;
	protected $archived;
	protected $deleted;
	protected $classname;
	
	public abstract function copy();
	
	public function __construct($data = null) {
		$this->created = new DateTime();
		$this->modified = new DateTime();
		$this->classname = 'ACPEditable';
		$this->created_by = ACPManager::current_user_id();
		$this->modified_by = $this->created_by;
		$this->archived = 0;
		$this->deleted = 'No';
		
		if (is_numeric($data)) {
  			$this->load_from_id($data);
		}
		/*elseif ($data instanceof ACPEditable) {
			$this->clone_from_object($data);
		}*/
		elseif (is_object($data) && isset($data->obj_id)) {
			$this->load_from_object($data);
		}
		/*elseif (is_string($data) && strlen($data)) {
			$this->load_from_string($data);
		}*/
	}
	
	public function __get($n) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			switch ($n) {
				case 'archived':
					return ($this->archived ? 'Yes' : 'No');
					//return ($this->archived ? 1 : 0);
				default: return $this->$n;
			}
		}
		ACPManager::error("Attempt to read non-existing property {$this->classname}::{$n}");
		return null;
	}
	
	public function __set($n, $v) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			switch ($n) {
				case 'created':
				case 'modified':
					if (!empty($v)) {
						$this->$n = (empty($v) ? '' : ($v instanceof DateTime ? $v : ACPManager::to_date($v)));
					}
					break;
				case 'archived':
					$this->archived = (bool)$v;
					break;
				case 'deleted':
					$this->deleted = (empty($v) || (strtolower($v) == 'no') ? 'No' : 'Yes');
					break;
				default:
					$this->$n = $v;
			}
		}
		else {
			ACPManager::error("Attempt to access non-existing property {$this->classname}::{$n}");
		}
	}
	
	public function __isset($n) {
		$val = $this->$n;
		return !empty($val);
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_editable_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			$this->error_id_not_found($id);
		}
	}
	
	public function load_from_object($obj) {
		if (!is_object($obj)) {
			return;
		}
		if ($obj instanceof ACPEditable) {
			$this->clone_from_object($obj);
			return;
		}
		if (!isset($obj->obj_id, $obj->obj_type, $obj->obj_created, $obj->obj_created_by)) {
			$this->error_incomplete_data($obj);
		}
		
		$this->__set('id', $obj->obj_id);
		$this->__set('type', $obj->obj_type);
		$this->__set('created', $obj->obj_created);
		$this->__set('modified', $obj->obj_modified);
		$this->__set('created_by', $obj->obj_created_by);
		$this->__set('modified_by', $obj->obj_modified_by);
		$this->__set('archived', $obj->obj_archived);
	}
	
	public function clone_from_object(ACPEditable $obj) {
		$this->type = $obj->type;
		$this->created = new DateTime();
		$this->modified = new DateTime();
		$this->archived = $obj->archived;
		/*$this->created_by = $obj->created_by;
		$this->modified_by = $obj->modified_by;*/
	}
	
	public function save() {
		if (empty($this->id)) {
			return $this->insert();
		}
		else {
			return $this->update();
		}
	}
	
	public function insert() {
		if (!empty($this->id)) {
			ACPManager::error(__METHOD__ . ' called with non-null $id property. Use copy() for clarity', 0, ACPManager::E_LEVEL_WARNING);
			return $this->copy();
		}
		elseif (empty($this->type)) {
			ACPManager::error("Cannot insert {$this->classname} with empty \$type property");
			return false;
		}
		else {	
			$this->id = ACPManager::sql_editable_insert($this->type, $this->created_format('Y-m-d H:i:s'), $this->created_by, $this->modified_format('Y-m-d H:i:s'), $this->modified_by, $this->archived);
			return (bool)$this->id;
		}
	}
	
	public function update() {
		if (empty($this->id)) {
			ACPManager::error('Cannot update with empty $id property');
		}
		elseif (empty($this->type)) {
			ACPManager::error("Cannot update {$this->classname} with empty \$type property");
		}
		else {
			$this->modified = new DateTime();
			return ACPManager::sql_editable_update($this->type, $this->created_format('Y-m-d H:i:s'), $this->created_by, $this->modified_format('Y-m-d H:i:s'), $this->modified_by, $this->archived, $this->id);
		}
		return false;
	}
	
	public function delete() {
		if (empty($this->id)) {
			ACPManager::error('Cannot delete with empty $id property');
		}
		else {	
			return ACPManager::sql_editable_delete($this->id);
		}
		return false;
	}
	
	public function created_format($f) {
		return $this->date_format('created', $f);
	}
	
	public function modified_format($f) {
		return $this->date_format('modified', $f);
	}
	
	protected function error_incomplete_data($data = null) {
		$data = ($data ? ' Data: ' . var_export($data, true) : '');
		//ACPManager::error("Loading {$this->classname} object with incomplete data.{$data}", 0, ACPManager::E_LEVEL_WARNING);
		ACPManager::error("Loading {$this->classname} object with incomplete data.", 0, ACPManager::E_LEVEL_WARNING);
		//ACPManager::error("DATA: {$data}", 0, ACPManager::E_LEVEL_DEBUG);
	}
	
	protected function error_id_not_found($id) {
		$id = (int)$id;
		ACPManager::error("Unable to load from ID#{$id}. Not Found", 0, ACPManager::E_LEVEL_WARNING);
	}
	
	protected function date_format($n, $f) {
		$this->$n = ACPManager::to_date($this->$n);
		if ($this->$n instanceof DateTime) {
			return $this->$n->format($f);
		}
		return null;
	}
}

?>