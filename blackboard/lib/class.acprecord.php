<?php

class ACPRecord extends ACPEditable {
	const TYPE_GENERIC = 'Generic';
	const TYPE_EVENTREG = 'EventRegistration';
	const TYPE_SCREENBP ='ScreeningBP';
	const TYPE_SCREENBMI = 'ScreeningBMI';
	const TYPE_SCREENVISION = 'ScreeningVision';
	const TYPE_SCREENVITALS = 'ScreeningVitals';
	const TYPE_FAQDOCTOR = 'FAQDoctor';
	
	protected $rec_id;
	protected $rec_type;
	protected $email;
	
	public function __construct($data = null) {
		parent::__construct($data);
		$this->classname = 'ACPRecord'; //__CLASS__;
		$this->type = ACPManager::OBJ_RECORD;
		if (empty($this->rec_id) || empty($this->rec_type)) {
			$this->rec_type = self::TYPE_GENERIC;
		}
	}
	
	public function load_from_id($id) {
		$obj = ACPManager::sql_record_load($id);
		if ($obj) {
			$this->load_from_object($obj);
		}
		else {
			$this->error_id_not_found($id);
		}
	}
	
	public function load_from_object($obj) {
		if ($obj instanceof ACPRecord) {
			$this->clone_from_object($obj);
			return;
		}
		
		parent::load_from_object($obj);
		if (!isset($obj->rec_id, $obj->rec_type)) {
			$this->error_incomplete_data($obj);
		}
		
		//$rec = (empty($obj->rec_id) ? ACPManager::sql_record_load($obj->id) : $obj);
		$this->rec_id = $obj->rec_id;
		$this->rec_type = $obj->rec_type;
		$this->email = $obj->rec_email;
	}
	
	public function clone_from_object(ACPRecord $obj) {
		parent::clone_from_object($obj);
		$this->rec_id = $obj->rec_id;
		$this->rec_type = $obj->rec_type;
		$this->email = $obj->email;
	}
	
	public function insert() {
		if (empty($this->rec_type)) {
			ACPManager::error("Cannot insert record with empty \$rec_type property");
		}
		elseif (parent::insert()) {
			$this->rec_id = ACPManager::sql_record_insert($this->id, $this->rec_type, $this->email);
			return (bool)$this->rec_id;
		}
		return false;
	}
	
	public function update() {
		if (empty($this->rec_type)) {
			ACPManager::error("Cannot update record with empty \$rec_type property");
		}
		elseif (parent::update()) {
			return ACPManager::sql_record_update($this->id, $this->rec_type, $this->email, $this->rec_id);
		}
		return false;
	}
	
	public function delete() {
		if (parent::delete()) {
			return ACPManager::sql_record_delete($this->rec_id);
		}
		return false;
	}
	
	public function copy() {
		$obj = new ACPRecord($this);
		return $obj->insert();
	}
}

?>