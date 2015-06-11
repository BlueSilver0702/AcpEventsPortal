<?php

class ACPError extends ErrorException {
	protected $id;
	protected $instances;
	protected $new_instances;
	protected $status;
	protected $assigned;
	protected $save_on_end;
	
	//See ACPError::check_code() for special values for the $code parameter
	public function __construct($message = '', $code = 0, $severity = ACPManager::E_LEVEL_RECOVERABLE, $filename = __FILE__, $lineno = __LINE__, Exception $previous = null) {
		$this->new_instances = array();
		$this->instances = array();
		$code_v = (is_array($code) ? 0 : $code);
		
		if (is_object($message)) {
			parent::__construct($message->error_message, $message->error_code, $message->error_severity, $message->error_filename, $message->error_lineno, null);
			$this->id = $message->error_id;
			$this->status = $message->error_status;
			return;
		}
		else {
			parent::__construct($message, $code_v, $severity, $filename, $lineno, $previous);
			$this->load();
		}
		if (empty($this->status)) {
			$this->status = ACPManager::E_STATUS_OPEN;
		}
		
		$this->check_code($code);
	}
	
	public function __destruct() {
		if ($this->save_on_end) {
			$this->save();
		}
	}
	
	public function __get($n) {
		$n = strtolower($n);
		if (property_exists($this, $n)) {
			return $this->$n;
		}
		ACPManager::error("Attempt to read non-existing property ACPError::\${$n}", ACPManager::E_CODE_BYPASS);
		return null;
	}
	
	public function __set($n, $v) {
		$n = strtolower($n);
		if (isset($this->$n)) {
			switch ($n) {
				case 'instances':
				case 'new_instances':
					if (!is_array($v)) {
						ACPManager::error("Cannot set property ACPError::\${$n} to non-array value", ACPManager::E_CODE_BYPASS);
						return;
					}
				default:
					$this->$n = $v;
					return;
			}
		}
		ACPManager::error("Attempt to access non-existing property ACPError::\${$n}", ACPManager::E_CODE_BYPASS);
	}
	
	protected function check_code($code) {
		if (!is_array($code)) {
			$code = array($code);
		}
		$save_on_end = false;
		$load_instances = false;
		$add_instance = false;
		foreach ($code as $v) {
			switch ($v) {
				case ACPManager::E_CODE_DOSAVE:
					$save_on_end = true;
					break;
				case ACPManager::E_CODE_DOLOAD:
					$load_instances = true;
					break;
				case ACPManager::E_CODE_DOADD:
					$add_instance = true;
					break;
			}
		}
		
		$this->save_on_end = $save_on_end;
		if ($load_instances) {
			$this->load_instances();
		}
		if ($add_instance) {
			$this->add_instance();
		}
	}
	
	public function load($msg = null, $file = null, $line = null) {
		if (empty($msg)) {
			$msg = $this->message;
			$file = $this->file;
			$line = $this->line;
		}
		
		if (is_numeric($msg)) {
			$row = ACPManager::sql_error_load_by_id($msg);
		}
		else {
			$row = ACPManager::sql_error_load($msg, $file, $line);
		}
		
		if (!empty($row)) {
			$this->id = $row->error_id;
			$this->message = $row->error_message;
			$this->severity = $row->error_severity;
			$this->file = $row->error_file;
			$this->line = $row->error_line;
			$this->code = $row->error_code;
			$this->status = $row->error_status;
			$this->assigned = $row->error_assigned;
		}
	}
	
	public function load_instances($amount = 0, $start = 0) {
		if (!empty($this->id)) {
			$res = ACPManager::sql_error_load_instances($this->id, $amount, $start);
			if (is_array($res)) {
				foreach ($res as $v) {
					$this->instances[] = array(ACPManager::to_date($v->einst_time), $v->einst_trace);
				}
			}
		}
	}
	
	public function add_instance() {
		if ($this->status == ACPManager::E_STATUS_FIXED) {
			$this->status = ACPManager::E_STATUS_OPEN;
			$this->assigned = '';
		}
		$this->new_instances[] = array(new DateTime(), $this->getTraceAsString());
	}
	
	public function save() {
		if (empty($this->id)) {
			$this->id = ACPManager::sql_error_insert($this->message, $this->code, $this->file, $this->line, $this->severity, $this->status, $this->assigned);
		}
		else {
			ACPManager::sql_error_update($this->message, $this->code, $this->file, $this->line, $this->severity, $this->status, $this->assigned, $this->id);
		}
		
		$this->save_instances();
	}
	
	public function save_instances() {
		if (empty($this->id) || !count($this->new_instances)) {
			return false;
		}
		
		return ACPManager::sql_error_save_instances($this->id, $this->new_instances);
	}
}

?>