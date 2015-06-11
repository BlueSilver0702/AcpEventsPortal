<?php

class ACPStaff extends ACPEditable {
	protected $staff_id;
	protected $fname;
	protected $lname;
	protected $phone;
	protected $events;
	
	public function __construct($data = null) {
		$this->events = new ACPList('event');
		parent::__construct($data);
		$this->classname = 'ACPStaff'; //__CLASS__;
		$this->type = ACPManager::OBJ_STAFF;
	}
	
	public function __get($n) {
		$n = strtolower($n);
		switch ($n) {
			case 'full_name':
				return "{$this->fname} {$this->lname}";
			default:
				return parent::__get($n);
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_staff_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			$this->error_id_not_found($id);
		}
	}
	
	public function load_from_object($obj) {
		if ($obj instanceof ACPStaff) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->staff_id, $obj->staff_fname, $obj->staff_lname, $obj->staff_phone)) {
			$this->error_incomplete_data($obj);
		}
		$this->staff_id = $obj->staff_id;
		$this->fname = $obj->staff_fname;
		$this->lname = $obj->staff_lname;
		$this->phone = $obj->staff_phone;
	}
	
	public function clone_from_object(ACPStaff $obj) {
		parent::clone_from_object($obj);
		$this->staff_id = $obj->staff_id;
		$this->fname = $obj->fname;
		$this->lname = $obj->lname;
		$this->phone = $obj->phone;
	}
	
	public function insert() {
		if (empty($this->fname) && empty($this->lname)) {
			ACPManager::error('Cannot insert staff without a first or last name');
		}
		elseif (parent::insert()) {
			$this->staff_id = ACPManager::sql_staff_insert($this->id, $this->fname, $this->lname, $this->phone);
			return (bool)$this->staff_id;
		}
		return false;
	}
	
	public function update() {
		if (empty($this->staff_id)) {
			ACPManager::error('Cannot update staff without a staff ID');
		}
		elseif (empty($this->fname) && empty($this->lname)) {
			ACPManager::error('Cannot update staff without a first or last name');
		}
		elseif (parent::update()) {
			return ACPManager::sql_staff_update($this->id, $this->fname, $this->lname, $this->phone, $this->staff_id);
		}
	}
	
	public function delete() {
		if (empty($this->staff_id)) {
			ACPManager::error('Cannot delete staff without a staff ID');
		}
		elseif (parent::update()) {
			return ACPManager::sql_staff_delete($this->staff_id);
		}
	}
	
	public function copy() {
		$obj = new ACPScreeningBMI($this);
		return $obj->insert();
	}
}

?>