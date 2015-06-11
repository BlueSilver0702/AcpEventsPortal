<?php

class ACPEventRegistration extends ACPRecord {
	const CHOICE_DIABETES = 1;
	const CHOICE_EATING = 2;
	const CHOICE_FITNESS = 4;
	const CHOICE_CHILD = 8;
	const CHOICE_WOMEN = 16;
	
	protected $event;
	protected $fname;
	protected $lname;
	protected $phone;
	protected $zip;
	protected $appointment;
	protected $patient;
	protected $info_choices;
	protected $send_screen_info;
	
	public function __construct($data = null) {
		$this->fname = '';
		$this->lname = '';
		$this->phone = '';
		$this->zip = '';
		$this->appointment = 'No';
		$this->patient = 'No';
		$this->info_choices = 0;
		parent::__construct($data);
		$this->classname = 'ACPEventRegistration'; //__CLASS__;
		if (empty($this->rec_id) || empty($this->rec_type)) {
			$this->rec_type = ACPRecord::TYPE_EVENTREG;
		}
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
	
	public function __set($n, $v) {
		$n = strtolower($n);
		switch ($n) {
			case 'event':
				$this->event = ($v instanceof ACPEvent ? $v : ACPManager::get_event($v));
				break;
			case 'appointment':
			case 'patient':
			case 'send_screen_info':
				$this->$n = ((empty($v) || (strtolower($v) == 'no') || ($v == 2)) ? 'No' : 'Yes');
				break;
			case 'info_choices':
				if (is_numeric($v)) {
					$this->info_choices = (int)$v;
				}
				else {
					$this->info_choices = $v;
				}
				break;
			default:
				parent::__set($n, $v);
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_event_reg_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			$this->error_id_not_found($id);
		}
	}
	
	public function load_from_object($obj) {
		if ($obj instanceof ACPEventRegistration) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->event_id)) {
			$this->error_incomplete_data($obj);
		}
		//$reg = (empty($obj->event_id) ? ACPManager::sql_event_reg_load($obj->rec_id) : $obj);
		$this->event = ACPManager::get_event($obj->event_id);
		$this->appointment = $obj->reg_appointment;
		$this->patient = $obj->reg_patient;
		$this->send_screen_info = $obj->reg_send_screen_info;
		$this->fname = $obj->reg_fname;
		$this->lname = $obj->reg_lname;
		$this->phone = $obj->reg_phone;
		$this->zip = $obj->reg_zip;
		$this->info_choices = $obj->reg_info_choices_num;
	}
	
	public function clone_from_object(ACPEventRegistration $obj) {
		parent::clone_from_object($obj);
		$this->event = $obj->event;
		$this->appointment = $obj->appointment;
		$this->patient = $obj->patient;
		$this->send_screen_info = $obj->send_screen_info;
		$this->fname = $obj->fname;
		$this->lname = $obj->lname;
		$this->phone = $obj->phone;
		$this->zip = $obj->zip;
		$this->info_choices = $obj->info_choices;
	}
	
	public function insert() {
		if (empty($this->event) || empty($this->event->id)) {
			ACPManager::error("Cannot insert event registration without an event");
		}
		elseif (parent::insert()) {
			if (ACPManager::sql_event_reg_insert($this->rec_id, $this->event->event_id, $this->fname, $this->lname, $this->phone, $this->zip, $this->appointment, $this->patient, $this->info_choices, $this->send_screen_info)) {
				return ACPManager::sql_set_event_record($this->rec_id, $this->event->event_id);
			}
		}
		return false;
	}
	
	public function update() {
		if (empty($this->id)) {
			ACPManager::error("Cannot update event registration without an ID");
		}
		elseif (empty($this->event) || empty($this->event->id)) {
			ACPManager::error("Cannot update event registration without an event");
		}
		elseif (parent::update()) {
			return ACPManager::sql_event_reg_update($this->rec_id, $this->event->event_id, $this->fname, $this->lname, $this->phone, $this->zip, $this->appointment, $this->patient, $this->info_choices, $this->send_screen_info);
		}
		return false;
	}
	
	public function delete() {
		if (empty($this->id)) {
			ACPManager::error("Cannot delete event registration without an ID");
		}
		elseif (empty($this->rec_id)) {
			ACPManager::error("Cannot delete event registration without a record ID");
		}
		elseif (parent::delete()) {
			return ACPManager::sql_event_reg_delete($this->rec_id);
		}
		return false;
	}
	
	public function copy() {
		$obj = new ACPEventRegistration($this);
		return $obj->insert();
	}
	
	public function list_info_choices() {
		$choices = array();
		if ($this->info_choices & self::CHOICE_DIABETES) {
			$choices[] = 'Diabetes Management';
		}
		if ($this->info_choices & self::CHOICE_EATING) {
			$choices[] = 'Healthy Eating';
		}
		if ($this->info_choices & self::CHOICE_FITNESS) {
			$choices[] = 'Fitness';
		}
		if ($this->info_choices & self::CHOICE_CHILD) {
			$choices[] = 'Child Health';
		}
		if ($this->info_choices & self::CHOICE_WOMEN) {
			$choices[] = 'Women\'s Health Issues';
		}
		
		return $choices;
	}
}

?>