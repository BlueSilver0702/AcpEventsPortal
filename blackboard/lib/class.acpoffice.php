<?php

class ACPOffice extends ACPEditable {
	protected $office_id;
	protected $name;
	protected $street;
	protected $city;
	protected $county;
	protected $state;
	protected $zip;
	protected $phone;
	protected $hours;
	protected $after_hours;
	protected $urgent_care;
	protected $specialties;
	protected $specialties_loaded;
	protected $lat;
	protected $lng;
	
	public function __construct($data = null) {
		$this->specialties = new ACPList('specialty');
		parent::__construct($data);
		$this->classname = 'ACPOffice'; //__CLASS__;
		$this->type = ACPManager::OBJ_OFFICE;
	}
	
	public function __get($n) {
		$n = strtolower($n);
		switch ($n) {
			case 'specialties':
				if (!$this->specialties_loaded) {
					$this->load_specialties();
				}
				return $this->specialties;
			case 'address':
				$addr = '';
				if (!empty($this->street) || !empty($this->city)) {
					$addr .= trim("{$this->street} {$this->city}") . ', ';
				}
				if (!empty($this->county)) {
					$addr .= "{$this->county}, ";
				}
				if (!empty($this->state) || !empty($this->zip)) {
					$addr .= "{$this->state} {$this->zip}";
				}
				return  trim($addr, ',');
			default:
				return parent::__get($n);
		}
	}
	
	public function __set($n, $v) {
		$n = strtolower($n);
		switch ($n) {
			case 'urgent_care':
				$v = strtolower($v);
				$this->urgent_care = ($v == 'yes' ? 'Yes' : ($v == 'no' ? 'No' : 'TBD'));
				break;
			default:
				parent::__set($n, $v);
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_office_load($id);
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
		if ($obj instanceof ACPOffice) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->office_id, $obj->office_name)) {
			$this->error_incomplete_data($obj);
		}
		
		$this->office_id = $obj->office_id;
		$this->name = $obj->office_name;
		$this->phone = $obj->office_phone;
		$this->street = $obj->office_street;
		$this->city = $obj->office_city;
		$this->county = $obj->office_county;
		$this->state = $obj->office_state;
		$this->zip = $obj->office_zip;
		$this->hours = $obj->office_hours;
		$this->after_hours = $obj->office_after_hours;
		$this->urgent_care = $obj->office_urgent_care;
		$this->lat = $obj->office_lat;
		$this->lng = $obj->office_lng;
	}
	
	public function clone_from_object(ACPOffice $obj) {
		parent::clone_from_object($obj);
		$this->office_id = $obj->office_id;
		$this->name = $obj->name;
		$this->phone = $obj->phone;
		$this->street = $obj->street;
		$this->city = $obj->city;
		$this->county = $obj->county;
		$this->state = $obj->state;
		$this->zip = $obj->zip;
		$this->hours = $obj->hours;
		$this->after_hours = $obj->after_hours;
		$this->urgent_care = $obj->urgent_care;
		$this->lat = $obj->lat;
		$this->lng = $obj->lng;
	}
	
	public function insert() {
		if (empty($this->name)) {
			ACPManager::error('Cannot insert office without a $name property');
		}
		elseif (parent::insert()) {
			$this->office_id = ACPManager::sql_office_insert($this->id, $this->name, $this->phone, $this->street, $this->city, $this->county, $this->state, $this->zip, $this->hours, $this->after_hours, $this->urgent_care, $this->lat, $this->lng);
			if ((bool)$this->office_id) {
				return $this->save_specialties();
			}
		}
		return false;
	}
	
	public function update() {
		if (empty($this->id)) {
			ACPManager::error('Cannot update office without an $id property');
		}
		elseif (empty($this->office_id)) {
			ACPManager::error('Cannot update office without an $office_id property');
		}
		elseif (empty($this->name)) {
			ACPManager::error('Cannot update office without a $name property');
		}
		elseif (parent::update()) {
			if (ACPManager::sql_office_update($this->id, $this->name, $this->phone, $this->street, $this->city, $this->county, $this->state, $this->zip, $this->hours, $this->after_hours, $this->urgent_care, $this->lat, $this->lng, $this->office_id)) {
				return $this->save_specialties();
			}
		}
		return false;
	}
	
	public function delete() {
		if (parent::delete()) {
			return ACPManager::sql_office_delete($this->office_id);
		}
		return false;
	}
	
	public function copy() {
		$obj = new ACPOffice($this);
		return $obj->insert();
	}
	
	public function load_specialties() {
		if ($this->office_id) {
			$this->specialties_loaded = true;
			$specs = ACPManager::sql_office_load_specialties($this->office_id);
			if (!empty($specs)) {
				foreach ($specs as $v) {
					$this->specialties[] = $v;
				}
			}
		}
	}
	
	public function delete_specialties() {
		return ACPManager::sql_office_delete_specialties($this->office_id);
	}
	
	public function save_specialties() {
		$this->load_specialties();
		$this->delete_specialties();
		return ACPManager::sql_office_save_specialties($this->office_id, $this->specialties->get_ids());
	}
	
	public function add_specialty($spec_id) {
		$this->load_specialties();
		$this->specialties[] = (int)$spec_id;
	}
	
	public function remove_specialty($spec_id) {
		$this->load_specialties();
		$key = $this->specialties->get_key_by_id((int)$spec_id);
		if (!is_null($key)) {
			unset($this->specialties[$key]);
		}
		return true;
	}
}

?>