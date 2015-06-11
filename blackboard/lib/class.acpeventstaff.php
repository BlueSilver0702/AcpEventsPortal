<?php

class ACPEventStaff extends ACPEditable {
	protected $staff;
	protected $event;
	protected $role;
	protected $checked_in;
	
	public function __construct($data = null, $event_id = null) {
		if (is_numeric($data) && is_numeric($event_id)) {
			$this->load_from_id($data, $event_id);
		}
		elseif (is_object($data)) {
			$this->load_from_object($data);
		}
		//parent::__construct($data);
		$this->classname = 'ACPEventStaff'; //__CLASS__;
	}
	
	public function __get($n) {
		$n = strtolower($n);
		switch ($n) {
			case 'event':
			case 'staff':
				if (is_numeric($this->$n)) {
					$this->{'load_' . $n}();
				}
				return $this->$n;
			default:
				return parent::__get($n);
		}
	}
	
	public function exists($id = 0, $event_id = 0) {
		if (empty($id) && !empty($this->staff)) {
			$id = (is_numeric($this->staff) ? $this->staff : ($this->staff instanceof ACPStaff ? $this->staff->staff_id : 0));
		}
		if (empty($event_id) && !empty($this->event)) {
			$event_id = (is_numeric($this->event) ? $this->event : ($this->event instanceof ACPEvent ? $this->event->event_id : 0));
		}
		if (is_numeric($id) && is_numeric($event_id) && ($obj = ACPManager::sql_eventstaff_load($id, $event_id))) {
			return $obj;
		}
		return false;
	}
	
	public function load_from_id($id, $event_id = null) {
		if (is_numeric($id) && is_numeric($event_id)) {
			$obj = $this->exists($id, $event_id);
			if ($obj) {
				$this->load_from_object($obj);
			}
			else {
				//$this->error_id_not_found($id);
				//parent::load_from_id($id);
				$this->id = $id;
				$this->staff = $id;
				$this->event = $event_id;
			}
		}
	}
	
	public function load_from_object($obj) {
		if ($obj instanceof ACPEventStaff) {
			$this->clone_from_object($obj);
			return;
		}
		
		//parent::load_from_id($obj->staff_id);
		if (!isset($obj->staff_id, $obj->event_id, $obj->staff_role)) {
			$this->error_incomplete_data($obj);
		}
		$this->id = $obj->staff_id;
		$this->staff = $obj->staff_id;
		$this->event = $obj->event_id;
		$this->role = $obj->staff_role;
		$this->checked_in = $obj->staff_checked_in;
		$this->exists();
	}
	
	public function clone_from_object(ACPEventStaff $obj) {
		//parent::clone_from_object($obj);
		$this->id = $obj->staff->staff_id;
		$this->staff = $obj->staff;
		$this->event = $obj->event;
		$this->role = $obj->role;
		$this->checked_in = $obj->checked_in;
		$this->exists();
	}
	
	public function insert() {
		$this->load_event();
		$this->load_staff();
		if (!($this->staff instanceof ACPStaff)) {
			ACPManager::error("Cannot insert {$this->classname} without a valid \$staff property");
		}
		elseif (!($this->event instanceof ACPEvent)) {
			ACPManager::error("Cannot insert {$this->classname} without a valid \$event property");
		}
		else {
			return ACPManager::sql_eventstaff_insert($this->staff->staff_id, $this->event->event_id, $this->role, $this->checked_in);
		}
		return false;
	}
	
	public function update() {
		$this->load_event();
		$this->load_staff();
		if (!($this->staff instanceof ACPStaff)) {
			ACPManager::error("Cannot update {$this->classname} without a valid \$staff property");
		}
		elseif (!($this->event instanceof ACPEvent)) {
			ACPManager::error("Cannot update {$this->classname} without a valid \$event property");
		}
		else {
			return ACPManager::sql_eventstaff_update($this->staff->staff_id, $this->event->event_id, $this->role, $this->checked_in);
		}
		return false;
	}
	
	public function save() {
		if ($this->exists()) {
			return $this->update();
		}
		return $this->insert();
	}
	
	public function delete() {
		$this->load_event();
		$this->load_staff();
		return ACPManager::sql_eventstaff_delete($this->staff->staff_id, $this->event->event_id);
	}
	
	public function copy() {
		$obj = new ACPEventStaff($this);
		return $obj->insert();
	}
	
	public function load_event() {
		if (!empty($this->event) && is_numeric($this->event)) {
			$this->event = ACPManager::get_event($this->event);
		}
	}
	
	public function load_staff() {
		if (!empty($this->staff) && is_numeric($this->staff)) {
			$this->staff = ACPManager::get_staff($this->staff);
		}
	}
}

?>