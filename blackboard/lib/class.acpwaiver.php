<?php

class ACPWaiver {
	protected $id;
	protected $rec_id;
	protected $fname;
	protected $lname;
	protected $witness_fname;
	protected $witness_lname;
	
	public function __construct($data = null) {
		if (is_numeric($data)) {
			$this->load_from_id($data);
		}
		elseif (is_object($data)) {
			$this->load_from_object($data);
		}
	}
	
	public function __get($n) {
		$n = strtolower($n);
		return (property_exists($this, $n) ? $this->$n : null);
	}
	public function __set($n, $v) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			switch ($n) {
				case 'rec_id':
					$this->$n = (int)$v;
					break;
				default:
					$this->$n = $v;
			}
		}
	}
	
	public function __isset($n) {
		$n = strtolower($n);
		return !empty($this->$n);
	}
	
	public function load_from_id($data) {
		$data = ACPManager::sql_waiver_by_id($data);
		$this->load_from_object($data);
	}
	
	public function load_from_object($data) {
		if (!is_object($data)) {
			return;
		}
		$this->__set('rec_id', $data->rec_id);
		$this->__set('fname', $data->waiver_fname);
		$this->__set('lname', $data->waiver_lname);
		$this->__set('witness_fname', $data->waiver_wit_fname);
		$this->__set('witness_lname', $data->waiver_wit_lname);
	}
	
	public function save() {
		if (empty($this->rec_id)) {
			return ACPManager::error('Cannot save waiver with an empty $rec_id property');
		}
		if (empty($this->fname)) {
			return ACPManager::error('Cannot save waiver with an empty $fname property');
		}
		if (empty($this->lname)) {
			return ACPManager::error('Cannot save waiver with an empty $lname property');
		}
		/*if (empty($this->witness_fname)) {
			return ACPManager::error('Cannot save waiver with an empty $witness_fname property');
		}
		if (empty($this->witness_lname)) {
			return ACPManager::error('Cannot save waiver with an empty $witness_lname property');
		}*/
		if (empty($this->id)) {
			return $this->insert();
		}
		return $this->update();
	}
	
	protected function insert() {
		$this->id = ACPManager::sql_waiver_insert($this->rec_id, $this->fname, $this->lname, $this->witness_fname, $this->witness_lname);
		return (bool)$this->id;
	}
	
	protected function update() {
		return ACPManager::sql_waiver_update($this->rec_id, $this->fname, $this->lname, $this->witness_fname, $this->witness_lname, $this->id);
	}
}

?>