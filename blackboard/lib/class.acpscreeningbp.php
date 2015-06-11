<?php

class ACPScreeningBP extends ACPRecord {
	protected $bp_sys;
	protected $bp_dia;
	protected $event;
	protected $display_type;
	
	public function __construct($data = null) {
		parent::__construct($data);
		$this->classname = 'ACPScreeningBP'; //__CLASS__;
		if (empty($this->rec_id) || empty($this->rec_type)) {
			$this->rec_type = ACPRecord::TYPE_SCREENBP;
		}
		$this->display_type = 'BP';
	}
	
	public function __get($n) {
		$n = strtolower($n);
		switch ($n) {
			case 'event':
				if ($this->event instanceof ACPEvent) {
					return $this->event;
				}
				$this->load_event();
				return $this->event;
			case 'event_id':
				if ($this->event instanceof ACPEvent) {
					return $this->event->event_id;
				}
				elseif (is_numeric($this->event)) {
					return $this->event;
				}
				return $this->event;
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
			case 'event_id':
				$this->load_event($v);
				break;
			default:
				parent::__set($n, $v);
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_screeningbp_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			$this->error_id_not_found($id);
		}
	}
	
	public function load_from_object($obj) {
		if ($obj instanceof ACPScreeningBP) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->screen_bp_sys, $obj->screen_bp_dia, $obj->event_id)) {
			$this->error_incomplete_data($obj);
		}
		//$scr = (empty($obj->screen_bp_sys) ? ACPManager::sql_screeningbp_load($obj->rec_id) : $obj);
		$this->event = $obj->event_id;
		$this->bp_sys = $obj->screen_bp_sys;
		$this->bp_dia = $obj->screen_bp_dia;
	}
	
	public function clone_from_object(ACPScreeningBP $obj) {
		parent::clone_from_object($obj);
		$this->bp_sys = $scr->bp_sys;
		$this->bp_dia = $scr->bp_dia;
		$this->event = $obj->event;
	}
	
	public function insert() {
		$this->load_event();
		if (empty($this->event_id)) {
			ACPManager::error('Cannot insert screening without an $event_id property');
		}
		elseif (parent::insert()) {
			if (ACPManager::sql_screeningbp_insert($this->rec_id, $this->bp_sys, $this->bp_dia)) {
				return ACPManager::sql_set_event_record($this->rec_id, $this->event_id);
			}
		}
		return false;
	}
	
	public function update() {
		if (parent::update()) {
			return ACPManager::sql_screeningbp_update($this->rec_id, $this->bp_sys, $this->bp_dia);
		}
		return false;
	}
	
	public function delete() {
		if (parent::delete()) {
			return ACPManager::sql_screeningbp_delete($this->rec_id);
		}
		return false;
	}
	
	public function copy() {
		$obj = new ACPScreeningBP($this);
		return $obj->insert();
	}
	
	public function load_event($event_id = null) {
		$event_id = (int)$event_id;
		if (($this->event instanceof ACPEvent) && empty($event_id)) {
			return;
		}
		if (empty($event_id) && is_numeric($this->event)) {
			$event_id = $this->event;
		}
		if (!empty($event_id)) {
			$this->event = ACPManager::get_event($event_id);
		}
	}
}

?>