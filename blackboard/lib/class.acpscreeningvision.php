<?php

class ACPScreeningVision extends ACPRecord {
	protected $left;
	protected $right;
	protected $color_blind;
	protected $event;
	protected $display_type;
	
	public function __construct($data = null) {
		parent::__construct($data);
		$this->classname = 'ACPScreeningVision'; //__CLASS__;
		$this->rec_type = ACPRecord::TYPE_SCREENVISION;
		$this->display_type = 'Vision';
	}
	
	public function __get($n) {
		$n = strtolower($n);
		switch ($n) {
			case 'color_blind':
				return (empty($this->color_blind) ? 'No' : $this->color_blind);
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
			case 'color_blind':
				$this->color_blind = ((empty($v) || (strtolower($v) == 'no') || ($v == 2)) ? 'No' : 'Yes');
				break;
			default:
				parent::__set($n, $v);
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_screeningvision_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			$this->error_id_not_found($id);
		}
	}
	
	public function load_from_object($obj) {
		if ($obj instanceof ACPScreeningVision) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->screen_left, $obj->screen_right, $obj->screen_color_blind, $obj->event_id)) {
			$this->error_incomplete_data($obj);
		}
		//$scr = (empty($obj->screen_left) ? ACPManager::sql_screeningvision_load($obj->rec_id) : $obj);
		$this->event = $obj->event_id;
		$this->left = $obj->screen_left;
		$this->right = $obj->screen_right;
		$this->color_blind = $obj->screen_color_blind;
	}
	
	public function clone_from_object(ACPScreeningVision $obj) {
		parent::clone_from_object($obj);
		$this->left = $obj->left;
		$this->right = $obj->right;
		$this->color_blind = $obj->color_blind;
		$this->event = $obj->event;
	}
	
	public function insert() {
		$this->load_event();
		if (empty($this->event_id)) {
			ACPManager::error('Cannot insert screening without an $event_id property');
		}
		elseif (parent::insert()) {
			if (ACPManager::sql_screeningvision_insert($this->rec_id, $this->left, $this->right, $this->color_blind)) {
				return ACPManager::sql_set_event_record($this->rec_id, $this->event_id);
			}
		}
		return false;
	}
	
	public function update() {
		if (parent::update()) {
			return ACPManager::sql_screeningvision_update($this->rec_id, $this->left, $this->right, $this->color_blind);
		}
		return false;
	}
	
	public function delete() {
		if (parent::delete()) {
			return ACPManager::sql_screeningvision_delete($this->rec_id);
		}
		return false;
	}
	
	public function copy() {
		$obj = new ACPScreeningVision($this);
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