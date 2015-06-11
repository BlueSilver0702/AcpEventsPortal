<?php

class ACPEvent extends ACPEditable {
	const SCREEN_VISION = 1;
	const SCREEN_BMI = 2;
	const SCREEN_VITALS = 4;
	const SCREEN_BP = 8;
	
	protected $event_id;
	protected $name;
	protected $img;
	protected $contact;
	protected $start;
	protected $end;
	protected $time;
	protected $set;
	protected $street;
	protected $city;
	protected $state;
	protected $zip;
	protected $uniform;
	protected $hours;
	protected $screen_types;
	protected $staff;
	protected $attendance;
	protected $screenings;
	protected $interactions;
	protected $comments;
	protected $staff_notes;
	protected $staff_loaded;
	protected $screenings_loaded;
	protected $comments_loaded;
	
	public function __construct($data = null) {
		$this->comments = array();
		$this->staff = new ACPList('event_staff');
		$this->screenings = new ACPList('screening');
		parent::__construct($data);
		$this->classname = 'ACPEvent'; //__CLASS__;
		$this->type = ACPManager::OBJ_EVENT;
	}
	
	public function __get($n) {
		$n = strtolower($n);
		switch ($n) {
			case 'staff':
			case 'screenings':
			case 'comments':
				$loaded = $n . '_loaded';
				if (!$this->$loaded) {
					$fn = 'load_' . $n;
					$this->$fn();
					$this->$loaded = true;
				}
				return $this->$n;
			case 'address':
				$addr = '';
				if (!empty($this->street) || !empty($this->city)) {
					$addr .= trim("{$this->street} {$this->city}") . ', ';
				}
				if (!empty($this->state) || !empty($this->zip)) {
					$addr .= "{$this->state} {$this->zip}";
				}
				return  trim($addr, ',');
			case 'total_screenings':
				$this->load_screenings();
				return count($this->screenings);
			case 'screen_types_text':
				return implode(', ', $this->list_screen_types());
			default:
				return parent::__get($n);
		}
	}
	
	public function __set($n, $v) {
		$n = strtolower($n);
		switch ($n) {
			case 'start':
			case 'end':
				$this->$n = (empty($v) ? '' : ($v instanceof DateTime ? $v : ACPManager::to_date($v)));
				break;
			case 'contact':
				if ($v instanceof ACPStaff) {
					$this->contact = $v;
				}
				elseif (is_numeric($v)) {
					$this->contact = ACPManager::get_staff($v);
				}
				elseif (!empty($v)) {
					ACPManager::error('Cannot set $contact property to type ' . gettype($v), 0, ACPManager::E_LEVEL_WARNING);
				}
				break;
			case 'screen_types':
			case 'set':
				if (is_numeric($v)) {
					$this->$n = (int)$v;
				}
				else {
					$this->$n = $v;
				}
				break;
			default:
				parent::__set($n, $v);
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_event_load($id);
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
		if ($obj instanceof ACPEvent) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->event_id, $obj->event_name, $obj->staff_id)) {
			$this->error_incomplete_data($obj);
		}
		//$event = (empty($obj->event_name) ? ACPManager::sql_event_load($obj->id) : $obj);
		
		/*$this->__set('name', $obj->event_name);
		$this->__set('event_id', $obj->event_id);
		$this->__set('contact', $obj->staff_id);
		$this->__set('start', $obj->event_start);
		$this->__set('end', $obj->event_end);
		$this->__set('address', $obj->event_address);
		$this->__set('uniform', $obj->event_uniform);
		$this->__set('hours', $obj->event_hours);
		$this->__set('screen_types', $obj->event_screen_types);
		$this->__set('attendance', $obj->event_attendance);
		$this->__set('interactions', $obj->event_interactions);*/
		
		$this->name = $obj->event_name;
		$this->img = $obj->event_img;
		$this->event_id = $obj->event_id;
		$this->contact = ACPManager::get_staff($obj->staff_id);
		$this->start = ACPManager::to_date($obj->event_start);
		$this->end = ACPManager::to_date($obj->event_end);
		$this->set = $obj->event_set;
		//$this->address = $obj->event_address;
		$this->street = $obj->event_street;
		$this->city = $obj->event_city;
		$this->state = $obj->event_state;
		$this->zip = $obj->event_zip;
		$this->uniform = $obj->event_uniform;
		$this->hours = $obj->event_hours;
		$this->screen_types = $obj->event_screen_types_num;
		$this->attendance = $obj->event_attendance;
		$this->interactions = $obj->event_interactions;
		$this->staff_notes = $obj->event_staff_notes;
		$this->time = $obj->event_time;
	}
	
	public function clone_from_object(ACPEvent $obj) {
		parent::clone_from_object($obj);
		$this->name = $obj->name;
		$this->img = $obj->img;
		$this->event_id = $obj->event_id;
		$this->contact = $obj->contact;
		$this->start = $obj->start;
		$this->end = $obj->end;
		$this->set = $obj->set;
		//$this->address = $obj->address;
		$this->street = $obj->street;
		$this->city = $obj->city;
		$this->state = $obj->state;
		$this->zip = $obj->zip;
		$this->uniform = $obj->uniform;
		$this->hours = $obj->hours;
		$this->screen_types = $obj->screen_types;
		$this->attendance = $obj->attendance;
		$this->interactions = $obj->interactions;
		$this->staff_notes = $obj->staff_notes;
		$this->time = $obj->time;
	}
	
	public function insert() {
		if (empty($this->contact) || empty($this->contact->staff_id)) {
			ACPManager::error("Cannot insert event without an staff contact property");
		}
		elseif (parent::insert()) {
			return (($this->event_id = ACPManager::sql_event_insert($this->id, $this->contact->staff_id, $this->name, $this->start_format('Y-m-d H:i:s'), $this->end_format('Y-m-d H:i:s'), $this->set,
								$this->street, $this->city, $this->state, $this->zip, $this->uniform, $this->hours, $this->screen_types, $this->attendance, $this->interactions, $this->img, $this->staff_notes, $this->time)) ? 
				($this->save_staff() ? $this->save_comments() : false) : false);
		}
		return false;
	}
	
	public function update() {
		if (empty($this->id)) {
			ACPManager::error("Cannot update event without an \$id property");
		}
		elseif (empty($this->event_id)) {
			ACPManager::error("Cannot update event without an \$event_id property");
		}
		elseif (empty($this->contact) || empty($this->contact->staff_id)) {
			ACPManager::error("Cannot update event without a staff contact property");
		}
		elseif (parent::update()) {
			return (ACPManager::sql_event_update($this->id, $this->contact->staff_id, $this->name, $this->start_format('Y-m-d H:i:s'), $this->end_format('Y-m-d H:i:s'), $this->set,
								$this->street, $this->city, $this->state, $this->zip, $this->uniform, $this->hours, $this->screen_types, $this->attendance, $this->interactions, $this->img, $this->staff_notes, $this->time,
								$this->event_id) ? 
				($this->save_staff() ? $this->save_comments() : false) : false);
		}
		return false;
	}
	
	public function delete() {
		if (parent::delete()) {
			return (ACPManager::sql_event_delete($this->event_id) ? ($this->delete_screenings() ? $this->delete_comments() : false) : false);
		}
		return false;
	}
	
	public function copy() {
		$obj = new ACPEvent($this);
		return $obj->insert();
	}
	
	public function add_staff($v, $role) {
		$eventstaff = ACPManager::get_event_staff($v, $this->event_id);
		if ($eventstaff) {
			if (empty($eventstaff->id)) {
				$eventstaff->id = $v;
			}
			if (empty($eventstaff->staff)) {
				$eventstaff->staff = $v;
			}
			$this->load_staff();
			$eventstaff->role = $role;
			$this->staff[] = $eventstaff;
			return true;
		}
		else {
			return ACPManager::error('Failed to add event staff', 0, ACPManager::E_LEVEL_WARNING);
		}
	}
	
	public function remove_staff($v) {
		$eventstaff = ACPManager::get_event_staff($v, $this->event_id);
		if ($eventstaff) {
			$this->load_staff();
			$key = $this->staff->get_key_by_id($eventstaff->staff->staff_id);
			if (!is_null($key)) {
				$this->staff[$key]->delete();
				unset($this->staff[$key]);
			}
			return true;
		}
		else {
			return ACPManager::error('Failed to remove event staff', 0, ACPManager::E_LEVEL_WARNING);
		}
	}
	
	public function add_comment($v) {
		$this->comments[] = (string)$v;
	}
	
	public function add_screening($v) {
		$this->screenings[] = $v;
	}
	
	public function load_screenings() {
		if (empty($this->event_id)) {
			//return ACPManager::error('Cannot load screenings without an $event_id property', 0, ACPManager::E_LEVEL_NOTICE);
			return false;
		}
		if ($this->screenings_loaded) {
			return true;
		}
		$this->screenings_loaded = true;
		//$this->screenings = new ACPList('screening');
		$screens = ACPManager::sql_event_load_screenings($this->event_id);
		if (is_array($screens)) {
			foreach ($screens as $v) {
				$this->screenings[] = $v;
			}
		}
		return true;
	}
	
	public function load_staff() {
		if (empty($this->event_id)) {
			//return ACPManager::error('Cannot load staff without an $event_id property', 0, ACPManager::E_LEVEL_NOTICE);
			return false;
		}
		if ($this->staff_loaded) {
			return true;
		}
		$this->staff_loaded = true;
		//$this->staff = new ACPList('staff');
		$staff = ACPManager::sql_event_load_staff($this->event_id);
		if (is_array($staff)) {
			foreach ($staff as $v) {
				$this->staff[] = ACPManager::get_event_staff($v);
			}
			return true;
		}
		return ACPManager::error("ACPSql::sql_event_load_staff() failed to return an array", 0, ACPManager::E_LEVEL_NOTICE);
	}
	
	public function load_comments() {
		if (empty($this->event_id)) {
			//return ACPManager::error('Cannot load comments without an $event_id property');
			return false;
		}
		if ($this->comments_loaded) {
			return true;
		}
		$this->comments_loaded = true;
		//$this->comments = array();
		$comments = ACPManager::sql_event_load_comments($this->event_id);
		if (is_array($comments)) {
			$this->comments = $comments;
			return true;
		}
		return ACPManager::error("ACPSql::sql_event_load_comments() failed to return an array", 0, ACPManager::E_LEVEL_NOTICE);
	}
	
	public function save_staff() {
		if (empty($this->event_id)) {
			return ACPManager::error('Cannot save staff without an $event_id property');
		}
		if (count($this->staff)) {
			$chk = 1;
			//$this->delete_staff();
			foreach ($this->staff as $v) {
				if (empty($v->event)) {
					$v->event = $this->event_id;
				}
				$chk &= $v->save();
			}
			return (bool)$chk;
		}
		return true;
	}
	
	public function save_comments() {
		if (empty($this->event_id)) {
			return ACPManager::error('Cannot save comments without an $event_id property');
		}
		if (count($this->comments)) {
			return ACPManager::sql_event_save_comments($this->event_id, $this->comments);
		}
		return true;
	}
	
	public function delete_staff() {
		if (empty($this->event_id)) {
			return ACPManager::error('Cannot delete staff without an $event_id property');
		}
		return ACPManager::sql_event_delete_staff($this->event_id);
	}
	
	public function delete_comments() {
		if (empty($this->event_id)) {
			return ACPManager::error('Cannot delete comments without an $event_id property');
		}
		return ACPManager::sql_event_delete_comments($this->event_id);
	}
	
	public function start_format($f) {
		return $this->date_format('start', $f);
	}
	
	public function end_format($f) {
		return $this->date_format('end', $f);
	}
	
	public function count_registrations() {
		if (empty($this->event_id)) {
			return ACPManager::error('Cannot count registrations without an $event_id property');
		}
		return ACPManager::sql_event_count_reg($this->event_id);
	}
	
	public function list_screen_types() {
		$types = array();
		if ($this->screen_types & self::SCREEN_VISION) {
			$types[] = 'Vision';
		}
		if ($this->screen_types & self::SCREEN_BMI) {
			$types[] = 'BMI';
		}
		if ($this->screen_types & self::SCREEN_VITALS) {
			$types[] = 'Vitals';
		}
		if ($this->screen_types & self::SCREEN_BP) {
			$types[] = 'Blood Pressure';
		}
		
		return $types;
	}
	
	public function get_staff_by_role($role) {
		$this->load_staff();
		$ret = array();
		foreach ($this->staff as $v) {
			if ($v->role == $role) {
				$ret[] = $v;
			}
		}
		return $ret;
	}
}

?>