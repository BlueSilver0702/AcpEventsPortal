<?php

class ACPSql {
	protected $pdo;
	protected $return_type;
	
	public function __construct($dsn, $user, $pass) {
		$this->pdo = new PDO($dsn, $user, $pass);
		$this->return_type = PDO::FETCH_OBJ;
	}
	
	public function __set($n, $v) {
		if (isset($this->$n)) {
			switch ($n) {
				case 'return_type':
					$this->set_return_type($v);
					break;
				default:
					$this->$n = $v;
			}
		}
		else {
			ACPManager::error("Attempt to set non-existing property ACPSql::\${$n}");
		}
	}
	
	public function __call($n, $args) {
		if (method_exists($this->pdo, $n)) {
			switch (count($args)) {
				case 0:
					return $this->pdo->$n();
				case 1:
					return $this->pdo->$n($args[0]);
				case 2:
					return $this->pdo->$n($args[0], $args[1]);
				case 3:
					return $this->pdo->$n($args[0], $args[1], $args[2]);
				case 4:
					return $this->pdo->$n($args[0], $args[1], $args[2], $args[3]);
				case 5:
					return $this->pdo->$n($args[0], $args[1], $args[2], $args[3], $args[4]);
				case 6:
					return $this->pdo->$n($args[0], $args[1], $args[2], $args[3], $args[4], $args[5]);
				case 7:
					return $this->pdo->$n($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6]);
				case 8:
					return $this->pdo->$n($args[0], $args[1], $args[2], $args[3], $args[4], $args[5], $args[6], $args[7]);
				default:
					return call_user_func_array(array($this->pdo, $n), $args);
			}
		}
		else {
			ACPManager::error("Call to undefined method ACPSql::{$n}");
		}
	}
	
	public function set_return_type($type) {
		if (is_string($type)) {
			$type = strtolower($type);
		}
		switch ($type) {
			case 'assoc':
			case PDO::FETCH_ASSOC:
				$this->return_type = PDO::FETCH_ASSOC;
				break;
			case 'both':
			case PDO::FETCH_BOTH:
				$this->return_type = PDO::FETCH_BOTH;
				break;
			case 'num':
			case PDO::FETCH_NUM:
				$this->return_type = PDO::FETCH_NUM;
				break;
			case 'obj':
			case 'object':
			case PDO::FETCH_OBJ:
			default:
				$this->return_type = PDO::FETCH_OBJ;
				break;
		}
	}
	
	//Returns true if there was an error
	protected static function param_count_error($fn, $params, $param_need, $code = 0) {
		$param_given = count($params);
		if ($param_given != $param_need) {
			$s = ($param_need == 1 ? '' : 's');
			ACPManager::error("{$fn} takes {$param_need} parameter{$s}, {$param_given} given.", $code);
			return true;
		}
		return false;
	}
	
	//Returns true if there was an error
	protected static function param_numeric_error($fn, $params, $id, $code = 0) {
		if (is_array($id)) {
			do {
				$tmpid = current($id);
				if (!is_numeric($params[$tmpid])) {
					++$tmpid;
					ACPManager::error("{$fn} expects parameter {$tmpid} to be a numeric value", $code);
					return true;
				}
			}
			while (next($id));
		}
		else {
			if (!is_numeric($params[$id])) {
				++$id;
				ACPManager::error("{$fn} expects parameter {$id} to be a numeric value", $code);
				return true;
			}
		}
		return false;
	}
	
	/*protected static function param_error($fn, $param_need, $param_given, $code = 0) {
		$s = ($param_need == 1 ? '' : 's');
		return ACPManager::error("{$fn} takes {$param_need} parameter{$s}, {$param_given} given.", $code);
	}*/
	
	protected static function statement_error(PDOStatement $stmt, $code = 0) {
		$e = $stmt->errorInfo();
		return ACPManager::error("({$e[0]}, {$e[1]}) {$e[2]}", $code);
	}
	
	//Executes $sql, then returns true or false for success or failure
	protected function query_bool($sql, $params = null, $error_code = 0) {
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($params)) {
			return true;
		}
		return self::statement_error($stmt, $error_code);
	}
	
	//Executes $sql, then returns PDO::lastInsertId() or false for error
	protected function query_insert($sql, $params = null, $error_code = 0) {
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($params)) {
			return $this->pdo->lastInsertId();
		}
		return self::statement_error($stmt, $error_code);
	}
	
	//Executes $sql, then returns PDO::fetch() or false for error
	protected function query_fetch($sql, $params = null, $error_code = 0) {
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($params)) {
			return $stmt->fetch($this->return_type);
		}
		return self::statement_error($stmt, $error_code);
	}
	
	//Executes $sql, then returns PDO::fetchAll() or false for error
	protected function query_fetch_all($sql, $params = null, $error_code = 0) {
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($params)) {
			return $stmt->fetchAll($this->return_type);
		}
		return self::statement_error($stmt, $error_code);
	}
	
	//Executes $sql, then returns PDO::fetchColumn() for all rows or false for error
	protected function query_fetch_col($sql, $params = null, $col = 0, $error_code = 0) {
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($params)) {
			$data = array();
			while ($v = $stmt->fetchColumn($col)) {
				$data[] = $v;
			}
			return $data;
		}
		return self::statement_error($stmt, $error_code);
	}
	
	//Executes $sql, then returns PDO::fetchColumn() -- a single value -- or false for error
	protected function query_single($sql, $params = null, $col = 0, $error_code = 0) {
		$stmt = $this->pdo->prepare($sql);
		if ($stmt->execute($params)) {
			return $stmt->fetchColumn($col);
		}
		return self::statement_error($stmt, $error_code);
	}
	
	/*
		ACPError SQL functions
	*/
	public function sql_error_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 3, ACPManager::E_CODE_BYPASS)) {
			return false;
		}
		
		$sql = "SELECT *
			FROM `acp_errors`
			WHERE `error_message` = ?
			AND `error_file` = ?
			AND `error_line` = ?";
		return $this->query_fetch($sql, $params, ACPManager::E_CODE_BYPASS);
	}
	
	public function sql_error_load_by_id(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1, ACPManager::E_CODE_BYPASS) || self::param_numeric_error(__METHOD__, $params, 0, ACPManager::E_CODE_BYPASS)) {
			return false;
		}
		
		$sql = "SELECT *
			FROM `acp_errors`
			WHERE `error_id` = ?";
		return $this->query_fetch($sql, $params, ACPManager::E_CODE_BYPASS);
	}
	
	public function sql_error_load_instances(array $params) {
		if (self::param_count_error(__METHOD__, $params, 3, ACPManager::E_CODE_BYPASS) || self::param_numeric_error(__METHOD__, $params, 0, ACPManager::E_CODE_BYPASS)) {
			return false;
		}
		
		$sql = "SELECT `einst_time`, `einst_trace`
			FROM `acp_error_instances`
			WHERE `error_id` = ?
			ORDER BY `einst_time` DESC";
		if (is_numeric($params[2]) && $params[1]) {
			$sql .= " LIMIT {$params[2]}, {$params[1]}";
		}
		elseif ($params[1]) {
			$sql .= " LIMIT {$params[1]}";
		}
		
		return $this->query_fetch_all($sql, array($params[0]), ACPManager::E_CODE_BYPASS);
	}
	
	public function sql_error_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 7, ACPManager::E_CODE_BYPASS)) {
			return false;
		}
		$sql = "INSERT INTO `acp_errors`
			SET `error_message` = ?
			, `error_code` = ?
			, `error_file` = ?
			, `error_line` = ?
			, `error_severity` = ?
			, `error_status` = ?
			, `error_assigned` = ?";
		return $this->query_insert($sql, $params, ACPManager::E_CODE_BYPASS);
	}
	
	public function sql_error_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 8, ACPManager::E_CODE_BYPASS) || self::param_numeric_error(__METHOD__, $params, 7, ACPManager::E_CODE_BYPASS)) {
			return false;
		}
		
		$sql = "UPDATE `acp_errors`
			SET `error_message` = ?
			, `error_code` = ?
			, `error_file` = ?
			, `error_line` = ?
			, `error_severity` = ?
			, `error_status` = ?
			, `error_assigned` = ?
			WHERE `error_id` = ?";
		return $this->query_bool($sql, $params, ACPManager::E_CODE_BYPASS);
	}
	
	public function sql_error_save_instances(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2, ACPManager::E_CODE_BYPASS) || self::param_numeric_error(__METHOD__, $params, 0, ACPManager::E_CODE_BYPASS)) {
			return false;
		}
		if (!is_array($params[1])) {
			return ACPManager::error(__METHOD__ . ' requires parameter 2 to be an array, ' . gettype($params[1]) . ' given', ACPManager::E_CODE_BYPASS);
		}
		
		$sql = "INSERT INTO `acp_error_instances`
			(`error_id`, `einst_time`, `einst_trace`)
			VALUES ";
		$add_inst = array();
		$values = array();
		foreach ($params[1] as $v) {
			$add_inst[] = "('{$params[0]}', ?, ?)";
			$values[] = $v[0]->format('Y-m-d H:i:s');
			$values[] = $v[1];
		}
		$sql .= implode(',', $add_inst);
		
		return $this->query_bool($sql, $values, ACPManager::E_CODE_BYPASS);
	}
	
	/*
		ACPLang SQL functions
	*/
	public function sql_lang_all() {
		$sql = "SELECT `lang_id`, `lang_slug`
			FROM `acp_lang`";
		return $this->query_fetch_all($sql);
	}
	
	public function sql_lang_texts() {
		$sql = "SELECT `lang_id`, `text_code`, `text_string`
			FROM `acp_lang_text`";
		return $this->query_fetch_all($sql);
	}
	
	/*
		ACPEditable SQL functions
	*/
	public function sql_editable_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT *
			FROM `acp_objects`
			WHERE `obj_id`  = ?";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_editable_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 6)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_objects`
			SET `obj_type` = ?
			, `obj_created` = ?
			, `obj_created_by` = ?
			, `obj_modified` = ?
			, `obj_modified_by` = ?
			, `obj_archived` = ?";
		return $this->query_insert($sql, $params);
	}
	
	public function sql_editable_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 7) || self::param_numeric_error(__METHOD__, $params, 6)) {
			return false;
		}
		
		$sql = "UPDATE `acp_objects`
			SET `obj_type` = ?
			, `obj_created` = ?
			, `obj_created_by` = ?
			, `obj_modified` = ?
			, `obj_modified_by` = ?
			, `obj_archived` = ?
			WHERE `obj_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_editable_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_objects`
			WHERE `obj_id` = ?";*/
		$sql = "UPDATE `acp_objects`
			SET `obj_deleted` = 'Yes'
			WHERE `obj_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	/*
		ACPRecord SQL functions
	*/
	public function sql_record_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		$sql = "SELECT r.*, o.*
			FROM `acp_records` r, `acp_objects` o
			WHERE r.`rec_id` = ?
			AND o.`obj_id` = r.`obj_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_record_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 3) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_records`
			SET `obj_id` = ?
			, `rec_type` = ?
			, `rec_email` = ?";
		return $this->query_insert($sql, $params);
	}
	
	public function sql_record_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 4) || self::param_numeric_error(__METHOD__, $params, array(0, 3))) {
			return false;
		}
		
		$sql = "UPDATE `acp_records`
			SET `obj_id` = ?
			, `rec_type` = ?
			, `rec_email` = ?
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_record_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_records`
			WHERE `rec_id` = ?";*/
		$sql = "UPDATE `acp_objects` o, `acp_records` r
			SET o.`obj_deleted` = 'Yes'
			WHERE r.`rec_id` = ?
			AND o.`obj_id` = r.`obj_id`";
		return $this->query_bool($sql, $params);
	}
	
	/*
		ACPEventRegistration SQL functions
	*/
	public function sql_event_reg_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT e.*, e.`reg_info_choices` + 0 AS 'reg_info_choices_num', r.*, o.*
			FROM `acp_event_registrations` e, `acp_records` r, `acp_objects` o
			WHERE e.`rec_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND o.`obj_id` = r.`obj_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_event_reg_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 10) || self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_event_registrations`
			SET `rec_id` = ?
			, `event_id` = ?
			, `reg_fname` = ?
			, `reg_lname` = ?
			, `reg_phone` = ?
			, `reg_zip` = ?
			, `reg_appointment` = ?
			, `reg_patient` = ?
			, `reg_info_choices` = ?
			, `reg_send_screen_info` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_event_reg_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 10) || self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		
		$params[10] = $params[0];
		$sql = "UPDATE `acp_event_registrations`
			SET `rec_id` = ?
			, `event_id` = ?
			, `reg_fname` = ?
			, `reg_lname` = ?
			, `reg_phone` = ?
			, `reg_zip` = ?
			, `reg_appointment` = ?
			, `reg_patient` = ?
			, `reg_info_choices` = ?
			, `reg_send_screen_info` = ?
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_event_reg_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_event_registrations`
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);*/
		return true;
	}
	
	/*
		ACPScreeningBMI SQL functions
	*/
	public function sql_screeningbmi_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT s.*, r.*, o.*, e.`event_id`
			FROM `acp_screen_bmi` s, `acp_records` r, `acp_objects` o, `acp_event_records` e
			WHERE s.`rec_id` = ?
			AND r.`rec_id` = s.`rec_id`
			AND o.`obj_id` = r.`obj_id`
			AND e.`rec_id` = r.`rec_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_screeningbmi_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_screen_bmi`
			SET `rec_id` = ?
			, `screen_bmi` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningbmi_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$params[2] = $params[0];
		$sql = "UPDATE `acp_screen_bmi`
			SET `rec_id` = ?
			, `screen_bmi` = ?
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningbmi_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_screen_bmi`
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);*/
		return true;
	}
	
	/*
		ACPScreeningVitals SQL functions
	*/
	public function sql_screeningvitals_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT s.*, r.*, o.*, e.`event_id`
			FROM `acp_screen_vitals` s, `acp_records` r, `acp_objects` o, `acp_event_records` e
			WHERE s.`rec_id` = ?
			AND r.`rec_id` = s.`rec_id`
			AND o.`obj_id` = r.`obj_id`
			AND e.`rec_id` = r.`rec_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_screeningvitals_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 6) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_screen_vitals`
			SET `rec_id` = ?
			, `screen_temperature` = ?
			, `screen_respiration` = ?
			, `screen_pulse` = ?
			, `screen_bp_sys` = ?
			, `screen_bp_dia` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningvitals_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 6) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$params[6] = $params[0];
		$sql = "UPDATE `acp_screen_vitals`
			SET `rec_id` = ?
			, `screen_temperature` = ?
			, `screen_respiration` = ?
			, `screen_pulse` = ?
			, `screen_bp_sys` = ?
			, `screen_bp_dia` = ?
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningvitals_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_screen_vitals`
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);*/
		return true;
	}
	
	
	/*
		ACPScreeningVision SQL functions
	*/
	public function sql_screeningvision_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT s.*, r.*, o.*, e.`event_id`
			FROM `acp_screen_vision` s, `acp_records` r, `acp_objects` o, `acp_event_records` e
			WHERE s.`rec_id` = ?
			AND r.`rec_id` = s.`rec_id`
			AND o.`obj_id` = r.`obj_id`
			AND e.`rec_id` = r.`rec_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_screeningvision_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 4) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_screen_vision`
			SET `rec_id` = ?
			, `screen_left` = ?
			, `screen_right` = ?
			, `screen_color_blind` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningvision_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 4) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$params[4] = $params[0];
		$sql = "UPDATE `acp_screen_vision`
			SET `rec_id` = ?
			, `screen_left` = ?
			, `screen_right` = ?
			, `screen_color_blind` = ?
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningvision_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_screen_vision`
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);*/
		return true;
	}
	
	/*
		ACPScreeningBP SQL functions
	*/
	public function sql_screeningbp_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT s.*, r.*, o.*, e.`event_id`
			FROM `acp_screen_bp` s, `acp_records` r, `acp_objects` o, `acp_event_records` e
			WHERE s.`rec_id` = ?
			AND r.`rec_id` = s.`rec_id`
			AND o.`obj_id` = r.`obj_id`
			AND e.`rec_id` = r.`rec_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_screeningbp_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 3) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_screen_bp`
			SET `rec_id` = ?
			, `screen_bp_sys` = ?
			, `screen_bp_dia` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningbp_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 3) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$params[3] = $params[0];
		$sql = "UPDATE `acp_screen_bp`
			SET `rec_id` = ?
			, `screen_bp_sys` = ?
			, `screen_bp_dia` = ?
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_screeningbp_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_screen_bp`
			WHERE `rec_id` = ?";
		return $this->query_bool($sql, $params);*/
		return true;
	}
	
	/*
		ACPStaff SQL functions
	*/
	public function sql_staff_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT s.*, o.*
			FROM `acp_staff` s, `acp_objects` o
			WHERE s.`staff_id` = ?
			AND o.`obj_id` = s.`obj_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_staff_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 4) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		$sql = "INSERT INTO `acp_staff`
			SET `obj_id` = ?
			, `staff_fname` = ?
			, `staff_lname` = ?
			, `staff_phone` = ?";
		return $this->query_insert($sql, $params);
	}
	
	public function sql_staff_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 5) || self::param_numeric_error(__METHOD__, $params, array(0, 4))) {
			return false;
		}
		
		$sql = "UPDATE `acp_staff`
			SET `obj_id` = ?
			, `staff_fname` = ?
			, `staff_lname` = ?
			, `staff_phone` = ?
			WHERE `staff_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_staff_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_staff`
			WHERE `staff_id` = ?";
		return $this->query_bool($sql, $params);*/
		$sql = "UPDATE `acp_objects` o, `acp_staff` s
			SET o.`obj_deleted` = 'Yes'
			WHERE s.`staff_id` = ?
			AND o.`obj_id` = s.`obj_id`";
		return $this->query_bool($sql, $params);
	}
	
	/*
		ACPEvent SQL functions
	*/
	public function sql_event_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT e.*, e.`event_screen_types` + 0 AS 'event_screen_types_num', o.*
			FROM `acp_events` e, `acp_objects` o
			WHERE e.`event_id` = ?
			AND o.`obj_id` = e.`obj_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_event_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 18) || self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_events`
			SET `obj_id` = ?
			, `staff_id` = ?
			, `event_name` = ?
			, `event_start` = ?
			, `event_end` = ?
			, `event_set` = ?
			, `event_street` = ?
			, `event_city` = ?
			, `event_state` = ?
			, `event_zip` = ?
			, `event_uniform` = ?
			, `event_hours` = ?
			, `event_screen_types` = ?
			, `event_attendance` = ?
			, `event_interactions` = ?
			, `event_img` = ?
			, `event_staff_notes` = ?
			, `event_time` = ?";
		if (function_exists('wp_insert_post')) {
			if ($eid = $this->query_insert($sql, $params)) {
				$post = array(
					'post_title' => $params[2],
					'post_status' => 'publish',
					'post_type' => 'acp_event'
				);
				$ret = wp_insert_post($post);
				if (($ret instanceof WP_Error) || ($ret === false)) {
					return ACPManager::error($ret->get_error_message(), $ret->get_error_code());
				}
				update_post_meta($ret, 'acp_obj_id', $params[0]);
				update_post_meta($ret, 'acp_event_id', $eid);
				return $eid;
			}
		}
		else {
			return $this->query_insert($sql, $params);
		}
	}
	
	public function sql_event_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 19) || self::param_numeric_error(__METHOD__, $params, array(0, 1, 18))) {
			return false;
		}
		
		$sql = "UPDATE `acp_events`
			SET `obj_id` = ?
			, `staff_id` = ?
			, `event_name` = ?
			, `event_start` = ?
			, `event_end` = ?
			, `event_set` = ?
			, `event_street` = ?
			, `event_city` = ?
			, `event_state` = ?
			, `event_zip` = ?
			, `event_uniform` = ?
			, `event_hours` = ?
			, `event_screen_types` = ?
			, `event_attendance` = ?
			, `event_interactions` = ?
			, `event_img` = ?
			, `event_staff_notes` = ?
			, `event_time` = ?
			WHERE `event_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_event_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		/*$sql = "DELETE FROM `acp_events`
			WHERE `event_id` = ?";
		if (function_exists('wp_delete_post')) {
			if ($this->query_bool($sql, $params)) {
				$args = array(
					'meta_key' => 'acp_obj_id',
					'meta_value' => $params[0]
				);
				$post = get_posts($args);
				if (count($post)) {
					$post = $post[0];
					$ret = wp_delete_post($post->ID, true);
					if ($ret === false) {
						return ACPManager::error('Unable to delete matching WP post', 0, ACPManager::E_LEVEL_WARNING);
					}
					return true;
				}
				return ACPManager::error('Could not find matching WP post', 0, ACPManager::E_LEVEL_WARNING);
			}
		}
		else {
			return $this->query_bool($sql, $params);
		}*/
		
		$sql = "UPDATE `acp_objects` o, `acp_events` e
			SET o.`obj_deleted` = 'Yes'
			WHERE e.`event_id` = ?
			AND o.`obj_id` = e.`obj_id`";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_event_load_screenings(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT r.`rec_id`
			FROM `acp_records` r, `acp_event_records` e
			WHERE e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND r.`rec_type` LIKE 'Screen%'";
		return $this->query_fetch_col($sql, $params);
	}
	
	public function sql_event_load_staff(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT *
			FROM `acp_event_staff`
			WHERE `event_id` = ?";
		return $this->query_fetch_all($sql, $params);
	}
	
	public function sql_event_load_comments(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT `comment_text`
			FROM `acp_event_comments`
			WHERE `event_id` = ?";
		return $this->query_fetch_col($sql, $params);
	}
	
	public function sql_event_save_staff(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		if (!($params[1] instanceof ACPList)) {
			return ACPManager::error(__METHOD__ . ' requires parameter 2 to be an instance of ACPList, ' . gettype($params[1]) . ' given');
		}
		
		$values = array();
		$data = array();
		foreach ($params[1] as $v) {
			$values[] = "('{$params[0]}', ?)";
			$data[] = $v;
		}
		$values = implode(',', $values);
		
		$sql = "DELETE FROM `acp_event_staff`
			WHERE `event_id` = ?";
		if ($this->query_bool($sql, array($params[0]))) {
			$sql = "INSERT INTO `acp_event_staff`
				(`event_id`, `staff_id`)
				VALUES ";
			
			return (count($params[1]) ? $this->query_bool($sql . $values, $data) : true);
		}
		return false;
	}
	
	public function sql_event_save_comments(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		if (!is_array($params[1])) {
			return ACPManager::error(__METHOD__ . ' requires parameter 2 to be an array, ' . gettype($params[1]) . ' given');
		}
		
		$values = array();
		foreach ($params[1] as $v) {
			$values[] = "('{$params[0]}', ?)";
		}
		$values = implode(',', $values);
		
		$sql = "DELETE FROM `acp_event_comments`
			WHERE `event_id` = ?";
		if ($this->query_bool($sql, array($params[0]))) {
			$sql = "INSERT INTO `acp_event_comments`
				(`event_id`, `comment_text`)
				VALUES ";
			return (count($params[1]) ? $this->query_bool($sql . $values, $params[1]) : true);
		}
		return false;
	}
	
	public function sql_event_delete_staff(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		$sql = "DELETE FROM `acp_event_staff`
			WHERE `event_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_event_delete_comments(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		$sql = "DELETE FROM `acp_event_comments`
			WHERE `event_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_event_count_reg(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations`
			WHERE `event_id` = ?";
		return $this->query_single($sql, $params);
	}
	
	/*
		ACPEventStaff functions
	*/
	public function sql_eventstaff_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		$sql = "SELECT e.*, s.*, o.*
			FROM `acp_event_staff` e, `acp_staff` s, `acp_objects` o
			WHERE e.`staff_id` = ?
			AND e.`event_id` = ?
			AND s.`staff_id` = e.`staff_id`
			AND o.`obj_id` = s.`obj_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_eventstaff_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 4) || self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_event_staff`
			SET `staff_id` = ?
			, `event_id` = ?
			, `staff_role` = ?
			, `staff_checked_in` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_eventstaff_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 4) || self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		
		$params[4] = $params[0];
		$params[5] = $params[1];
		$sql = "UPDATE `acp_event_staff`
			SET `staff_id` = ?
			, `event_id` = ?
			, `staff_role` = ?
			, `staff_checked_in` = ?
			WHERE `staff_id` = ?
			AND `event_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_eventstaff_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "DELETE FROM `acp_event_staff`
			WHERE `staff_id` = ?
			AND `event_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	/*
		Language functions
	*/
	
	public function lang_list() {
		$sql = "SELECT *
			FROM `acp_lang`";
		return $this->query_fetch_all($sql);
	}
	
	public function lang_list_objects($start = 0, $amount = 30, $filter = '', &$total_found = null) {
		$sql = "SELECT SQL_CALC_FOUND_ROWS *
			FROM `acp_lang_objects`";
		$start = (int)$start;
		$amount = (int)$amount;
		$params = array();
		
		if (!empty($filter)) {
			$sql .= " WHERE `lobj_name` LIKE ?";
			$params[] = $filter;
		}
		
		if ($start && $amount) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif ($amount) {
			$sql .= " LIMIT {$amount}";
		}
		
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
		}
		return $ret;
	}
	
	public function lang_list_untranslated($lang_id, $start = 0, $amount = 30, $filter = '', &$total_found = null) {
		$lang_id = (int)$lang_id;
		$params = array($lang_id);
		$sql = "SELECT SQL_CALC_FOUND_ROWS *
			FROM `acp_lang_objects`
			WHERE `lobj_id` NOT IN (
				SELECT `lobj_id`
				FROM `acp_lang_text`
				WHERE `lang_id` = ?
			)";
		
		if (!empty($filter)) {
			$sql .= " AND `lobj_name` LIKE ?";
			$params[] = $filter;
		}
		
		if ($start && $amount) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif ($amount) {
			$sql .= " LIMIT {$amount}";
		}
		
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
		}
		return $ret;
	}
	
	public function lang_by_default() {
		$sql = "SELECT *
			FROM `acp_lang`
			WHERE `lang_is_default` = 'Yes'";
		return $this->query_fetch($sql);
	}
	
	public function lang_by_code(array $params) {
		$code = ACPLang::to_code($params[0]);
		$sql = "SELECT *
			FROM `acp_lang`
			WHERE `lang_code` = ?";
		return $this->query_fetch($sql, array($code));
	}
	
	public function lang_by_id(array $params) {
		$id = (int)$params[0];
		$sql = "SELECT *
			FROM `acp_lang`
			WHERE `lang_id` = ?";
		return $this->query_fetch($sql, array($id));
	}
	
	public function lang_obj_by_id(array $params) {
		$lobj_id = (int)$params[0];
		$sql = "SELECT *
			FROM `acp_lang_objects`
			WHERE `lobj_id` = ?";
		return $this->query_fetch($sql, array($lobj_id));
	}
	
	public function lang_obj_by_code(array $params) {
		$code = ACPLang::to_code($params[0]);
		$sql = "SELECT *
			FROM `acp_lang_objects`
			WHERE `lobj_code` = ?";
		return $this->query_fetch($sql, array($code));
	}
	
	public function lang_text_by_id(array $params) {
		$text_id = (int)$params[0];
		$sql = "SELECT *
			FROM `acp_lang_text`
			WHERE `text_id` = ?";
		return $this->query_fetch($sql, array($text_id));
	}
	
	//Returns multiple
	public function lang_text_by_lang_id(array $params) {
		$lang_id = (int)$params[0];
		$sql = "SELECT t.*, o.`lobj_code`
			FROM `acp_lang_text` t, `acp_lang_objects` o
			WHERE t.`lang_id` = ?
			AND o.`lobj_id` = t.`lobj_id`";
		return $this->query_fetch_all($sql, array($lang_id));
	}
	
	//Returns multiple
	public function lang_text_by_obj_id(array $params) {
		$lobj_id = (int)$params[0];
		$sql = "SELECT *
			FROM `acp_lang_text`
			WHERE `lobj_id` = ?";
		return $this->query_fetch_all($sql, array($lobj_id));
	}
	
	public function lang_insert(array $params) {
		$name = $params[0];
		$code = ACPLang::to_code($params[1]);
		$is_default = (strtolower($params[2] == 'no') || empty($params[2]) ? 'No' : 'Yes');
		$enabled = (strtolower($params[3] == 'no') || empty($params[3]) ? 'No' : 'Yes');
		
		if ($is_default == 'Yes') {
			$sql = "UPDATE `acp_lang`
				SET `lang_is_default` = 'No'";
			$this->query_bool($sql);
		}
		
		$sql = "INSERT INTO `acp_lang`
			SET `lang_name` = ?
			, `lang_code` = ?
			, `lang_is_default` = ?
			, `lang_enabled` = ?";
		return $this->query_insert($sql, array($name, $code, $is_default, $enabled));
	}
	
	public function lang_update(array $params) {
		$lang_id = (int)$params[0];
		$name = $params[1];
		$code = ACPLang::to_code($params[2]);
		$is_default = (strtolower($params[3] == 'no') || empty($params[3]) ? 'No' : 'Yes');
		$enabled = (strtolower($params[4] == 'no') || empty($params[4]) ? 'No' : 'Yes');
		
		if ($is_default == 'Yes') {
			$sql = "UPDATE `acp_lang`
				SET `lang_is_default` = 'No'";
			$this->query_bool($sql);
		}
		
		$sql = "UPDATE `acp_lang`
			SET `lang_name` = ?
			, `lang_code` = ?
			, `lang_is_default` = ?
			, `lang_enabled` = ?
			WHERE `lang_id` = ?";
		return $this->query_bool($sql, array($name, $code, $is_default, $enabled, $lang_id));
	}
	
	public function lang_obj_insert(array $params) {
		$name = $params[0];
		$code = ACPLang::to_code($params[1]);
		
		$sql = "INSERT INTO `acp_lang_objects`
			SET `lobj_name` = ?
			, `lobj_code` = ?";
		return $this->query_insert($sql, array($name, $code));
	}
	
	public function lang_obj_update(array $params) {
		$name = $params[0];
		$lobj_id = (int)$params[2];
		$code = ACPLang::to_code($params[1]);
		
		$sql = "UPDATE `acp_lang_objects`
			SET `lobj_name` = ?
			, `lobj_code` = ?
			WHERE `lobj_id` = ?";
		return $this->query_bool($sql, array($name, $code));
	}
	
	public function lang_text_insert(array $params) {
		$lang_id = (int)$params[0];
		$lobj_id = (int)$params[1];
		$string = $params[2];
		
		$sql = "INSERT INTO `acp_lang_text`
			SET `lang_id` = ?
			, `lobj_id` = ?
			, `text_string` = ?";
		return $this->query_insert($sql, array($lang_id, $lobj_id, $string));
	}
	
	public function lang_text_update(array $params) {
		$text_id = (int)$params[0];
		$lang_id = (int)$params[1];
		$lobj_id = (int)$params[2];
		$string = $params[3];
		
		$sql = "UPDATE `acp_lang_text`
			SET `lang_id` = ?
			, `lobj_id` = ?
			, `text_string` = ?
			WHERE `text_id` = ?";
		return $this->query_bool($sql, array($lang_id, $lobj_id, $string, $text_id));
	}
	
	/*
		Waiver functions
	*/
	public function sql_waiver_by_id(array $params) {
		$params[0] = (int)$params[0];
		$sql = "SELECT *
			FROM `acp_waivers`
			WHERE `waiver_id` = ?";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_waiver_insert(array $params) {
		$params[0] = (int)$params[0]; //rec_id
		
		$sql = "INSERT INTO `acp_waivers`
			SET `rec_id` = ?
			, `waiver_fname` = ?
			, `waiver_lname` = ?
			, `waiver_wit_fname` = ?
			, `waiver_wit_lname` = ?";
		return $this->query_insert($sql, $params);
	}
	
	public function sql_waiver_update(array $params) {
		$params[0] = (int)$params[0]; //rec_id
		$params[5] = (int)$params[5]; //waiver_id
		
		$sql = "UPDATE `acp_waivers`
			SET `rec_id` = ?
			, `waiver_fname` = ?
			, `waiver_lname` = ?
			, `waiver_wit_fname` = ?
			, `waiver_wit_lname` = ?
			WHERE `waiver_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	/*
		ACPOffice functions
	*/
	public function sql_office_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT off.*, obj.*
			FROM `acp_offices` off, `acp_objects` obj
			WHERE off.`office_id` = ?
			AND obj.`obj_id` = off.`obj_id`";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_office_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 13) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_offices`
			SET `obj_id` = ?
			, `office_name` = ?
			, `office_phone` = ?
			, `office_street` = ?
			, `office_city` = ?
			, `office_county` = ?
			, `office_state` = ?
			, `office_zip` = ?
			, `office_hours` = ?
			, `office_after_hours` = ?
			, `office_urgent_care` = ?
			, `office_lat` = ?
			, `office_lng` = ?";
		return $this->query_insert($sql, $params);
	}
	
	public function sql_office_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 14) || self::param_numeric_error(__METHOD__, $params, array(0, 13))) {
			return false;
		}
		
		$sql = "UPDATE `acp_offices`
			SET `obj_id` = ?
			, `office_name` = ?
			, `office_phone` = ?
			, `office_street` = ?
			, `office_city` = ?
			, `office_county` = ?
			, `office_state` = ?
			, `office_zip` = ?
			, `office_hours` = ?
			, `office_after_hours` = ?
			, `office_urgent_care` = ?
			, `office_lat` = ?
			, `office_lng` = ?
			WHERE `office_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_office_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "UPDATE `acp_objects` obj, `acp_offices` off
			SET obj.`obj_deleted` = 'Yes'
			WHERE off.`office_id` = ?
			AND obj.`obj_id` = off.`obj_id`";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_office_load_specialties(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT `spec_id`
			FROM `acp_office_specialties`
			WHERE `office_id` = ?";
		return $this->query_fetch_col($sql, $params);
	}
	
	public function sql_office_delete_specialties(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "DELETE FROM `acp_office_specialties`
			WHERE `office_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_office_save_specialties(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		if (empty($params[1])) {
			return true;
		}
		
		$specids = $params[1];
		$sql = "INSERT INTO `acp_office_specialties`
			(`office_id`, `spec_id`)
			VALUES ";
		$vals = array();
		foreach ($params[1] as $v) {
			$v = (int)$v;
			$vals[] = "('{$params[0]}', '{$v}')";
		}
		$sql .= implode(',', $vals);
		
		return $this->query_bool($sql);
	}
	
	/*
		ACPSpecialty functions
	*/
	public function sql_specialty_load(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT *
			FROM `acp_specialties`
			WHERE `spec_id` = ?";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_specialty_load_from_name(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1)) {
			return false;
		}
		
		$sql = "SELECT *
			FROM `acp_specialties`
			WHERE `spec_name` LIKE ?
			LIMIT 1";
		return $this->query_fetch($sql, $params);
	}
	
	public function sql_specialty_insert(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1)) {
			return false;
		}
		
		$sql = "INSERT INTO `acp_specialties`
			SET `spec_name` = ?";
		return $this->query_insert($sql, $params);
	}
	
	public function sql_specialty_update(array $params) {
		if (self::param_count_error(__METHOD__, $params, 2) || self::param_numeric_error(__METHOD__, $params, 1)) {
			return false;
		}
		
		$sql = "UPDATE `acp_specialties`
			SET `spec_name` = ?
			WHERE `spec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_specialty_delete(array $params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "DELETE FROM `acp_specialties`
			WHERE `spec_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_list_specialties() {
		$sql = "SELECT *
			FROM `acp_specialties`
			ORDER BY `spec_name`";
		return $this->query_fetch_all($sql);
	}
	
	/*
		Statistics functions
	*/
	
	public function sql_stats_event_reg($after = null, $before = null, $is_patient = null) {
		$is_patient = (is_null($is_patient) ? null : (strtolower($is_patient) == 'yes' ? 'Yes' : 'No'));
		$params = array();
		
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations` e, `acp_records` r, `acp_objects` o
			WHERE r.`rec_id` = e.`rec_id`
			AND o.`obj_id` = r.`obj_id`";
		
		if ($after instanceof DateTime) {
			$sql .= " AND o.`obj_created` >= ?";
			$params[] = $after->format('Y-m-d H:i:s');
		}
		if ($before instanceof DateTime) {
			$sql .= " AND o.`obj_created` <= ?";
			$params[] = $before->format('Y-m-d H:i:s');
		}
		if (!empty($is_patient)) {
			$sql .= " AND e.`reg_patient` = ?";
			$params[] = $is_patient;
		}
		
		return $this->query_single($sql, $params);
	}
	
	public function sql_stats_screenings($after = null, $before = null, $type = 'Screen%', $registered = null) {
		$registered = (is_null($registered) ? null : (bool)$registered);
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_objects` o, `acp_waivers` w
			WHERE r.`rec_type` LIKE ?
			AND o.`obj_id` = r.`obj_id`
			AND w.`rec_id` = r.`rec_id`";
		$params = array($type);
		
		if ($after instanceof DateTime) {
			$sql .= " AND o.`obj_created` >= ?";
			$params[] = $after->format('Y-m-d H:i:s');
		}
		if ($before instanceof DateTime) {
			$sql .= " AND o.`obj_created` <= ?";
			$params[] = $before->format('Y-m-d H:i:s');
		}
		if ($registered === true) {
			$sql .= " AND (r.`rec_email` IS NOT NULL";
			$sql .= " OR r.`rec_email` != '')";
		}
		elseif ($registered === false) {
			$sql .= " AND (r.`rec_email` IS NULL";
			$sql .= " OR r.`rec_email` == '')";
		}
		
		return $this->query_single($sql, $params);
	}
	
	public function sql_stats_event($params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		$result = array();
		
		//Totals
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_waivers` w
			WHERE r.`rec_type` = 'ScreeningVision'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND w.`rec_id` = r.`rec_id`";
		$result['vision'] = array(
			'total' => $this->query_single($sql, $params)
		);
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_waivers` w
			WHERE r.`rec_type` = 'ScreeningBMI'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND w.`rec_id` = r.`rec_id`";
		$result['bmi'] = array(
			'total' => $this->query_single($sql, $params)
		);
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_waivers` w
			WHERE r.`rec_type` = 'ScreeningBP'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND w.`rec_id` = r.`rec_id`";
		$result['bp'] = array(
			'total' => $this->query_single($sql, $params)
		);
		
		//Registered
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e
			WHERE r.`rec_type` = 'ScreeningVision'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND r.`rec_email` != ''
			AND r.`rec_email` IS NOT NULL";
		$result['vision']['registered'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['vision']['total'] == 0 ? 0 : round($result['vision']['registered'] / $result['vision']['total'], 3));
		$result['vision']['registered'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e
			WHERE r.`rec_type` = 'ScreeningBMI'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND r.`rec_email` != ''
			AND r.`rec_email` IS NOT NULL";
		$result['bmi']['registered'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bmi']['total'] == 0 ? 0 : round($result['bmi']['registered'] / $result['bmi']['total'], 3));
		$result['bmi']['registered'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e
			WHERE r.`rec_type` = 'ScreeningBP'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND r.`rec_email` != ''
			AND r.`rec_email` IS NOT NULL";
		$result['bp']['registered'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bp']['total'] == 0 ? 0 : round($result['bp']['registered'] / $result['bp']['total'], 3));
		$result['bp']['registered'] .= " ({$perc}%)";
		
		//Is Patient
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningVision'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_patient` = 'Yes'";
		$result['vision']['patient'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['vision']['total'] == 0 ? 0 : round($result['vision']['patient'] / $result['vision']['total'], 3));
		$result['vision']['patient'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningBMI'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_patient` = 'Yes'";
		$result['bmi']['patient'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bmi']['total'] == 0 ? 0 : round($result['bmi']['patient'] / $result['bmi']['total'], 3));
		$result['bmi']['patient'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningBP'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_patient` = 'Yes'";
		$result['bp']['patient'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bp']['total'] == 0 ? 0 : round($result['bp']['patient'] / $result['bp']['total'], 3));
		$result['bp']['patient'] .= " ({$perc}%)";
		
		//More Info
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningVision'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_send_screen_info` = 'Yes'";
		$result['vision']['moreinfo'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['vision']['total'] == 0 ? 0 : round($result['vision']['moreinfo'] / $result['vision']['total'], 3));
		$result['vision']['moreinfo'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningBMI'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_send_screen_info` = 'Yes'";
		$result['bmi']['moreinfo'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bmi']['total'] == 0 ? 0 : round($result['bmi']['moreinfo'] / $result['bmi']['total'], 3));
		$result['bmi']['moreinfo'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningBP'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_send_screen_info` = 'Yes'";
		$result['bp']['moreinfo'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bp']['total'] == 0 ? 0 : round($result['bp']['moreinfo'] / $result['bp']['total'], 3));
		$result['bp']['moreinfo'] .= " ({$perc}%)";
		
		//Tips
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningVision'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_info_choices` != ''
			AND er.`reg_info_choices` IS NOT NULL";
		$result['vision']['tips'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['vision']['total'] == 0 ? 0 : round($result['vision']['tips'] / $result['vision']['total'], 3));
		$result['vision']['tips'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningBMI'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_info_choices` != ''
			AND er.`reg_info_choices` IS NOT NULL";
		$result['bmi']['tips'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bmi']['total'] == 0 ? 0 : round($result['bmi']['tips'] / $result['bmi']['total'], 3));
		$result['bmi']['tips'] .= " ({$perc}%)";
		
		$sql = "SELECT COUNT(*)
			FROM `acp_records` r, `acp_event_records` e, `acp_event_registrations` er
			WHERE r.`rec_type` = 'ScreeningBP'
			AND e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND er.`rec_id` = e.`rec_id`
			AND er.`reg_info_choices` != ''
			AND er.`reg_info_choices` IS NOT NULL";
		$result['bp']['tips'] = $this->query_single($sql, $params);
		$perc = 100 * ($result['bp']['total'] == 0 ? 0 : round($result['bp']['tips'] / $result['bp']['total'], 3));
		$result['bp']['tips'] .= " ({$perc}%)";
		
		$result['totals'] = array(
			'total' => $result['vision']['total'] + $result['bmi']['total'] + $result['bp']['total'],
			'registered' => $result['vision']['registered'] + $result['bmi']['registered'] + $result['bp']['registered'],
			'moreinfo' => $result['vision']['moreinfo'] + $result['bmi']['moreinfo'] + $result['bp']['moreinfo'],
			'patient' => $result['vision']['patient'] + $result['bmi']['patient'] + $result['bp']['patient'],
			'tips' => $result['vision']['tips'] + $result['bmi']['tips'] + $result['bp']['tips']
		);
		if ($result['totals']['total'] == 0) {
			$result['totals']['registered'] .= ' (0%)';
			$result['totals']['moreinfo'] .= ' (0%)';
			$result['totals']['patient'] .= ' (0%)';
			$result['totals']['tips'] .= ' (0%)';
		}
		else {
			$perc = 100 * round($result['totals']['registered'] / $result['totals']['total'], 3);
			$result['totals']['registered'] .= " ({$perc}%)";
			$perc = 100 * round($result['totals']['moreinfo'] / $result['totals']['total'], 3);
			$result['totals']['moreinfo'] .= " ({$perc}%)";
			$perc = 100 * round($result['totals']['patient'] / $result['totals']['total'], 3);
			$result['totals']['patient'] .= " ({$perc}%)";
			$perc = 100 * round($result['totals']['tips'] / $result['totals']['total'], 3);
			$result['totals']['tips'] .= " ({$perc}%)";
		}
		
		//Tips Breakdown
		$result['tips_breakdown'] = array(
			'total' => 0
		);
		
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations`
			WHERE `reg_info_choices` & " . ACPEventRegistration::CHOICE_DIABETES;
		$result['tips_breakdown']['diabetes'] = $this->query_single($sql);
		$result['tips_breakdown']['total'] += $result['tips_breakdown']['diabetes'];
		
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations`
			WHERE `reg_info_choices` & " . ACPEventRegistration::CHOICE_EATING;
		$result['tips_breakdown']['eating'] = $this->query_single($sql);
		$result['tips_breakdown']['total'] += $result['tips_breakdown']['eating'];
		
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations`
			WHERE `reg_info_choices` & " . ACPEventRegistration::CHOICE_FITNESS;
		$result['tips_breakdown']['fitness'] = $this->query_single($sql);
		$result['tips_breakdown']['total'] += $result['tips_breakdown']['fitness'];
		
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations`
			WHERE `reg_info_choices` & " . ACPEventRegistration::CHOICE_CHILD;
		$result['tips_breakdown']['child'] = $this->query_single($sql);
		$result['tips_breakdown']['total'] += $result['tips_breakdown']['child'];
		
		$sql = "SELECT COUNT(*)
			FROM `acp_event_registrations`
			WHERE `reg_info_choices` & " . ACPEventRegistration::CHOICE_WOMEN;
		$result['tips_breakdown']['women'] = $this->query_single($sql);
		$result['tips_breakdown']['total'] += $result['tips_breakdown']['women'];
		
		return $result;
	}
	
	public function sql_stats_event_data($params) {
		if (self::param_count_error(__METHOD__, $params, 1) || self::param_numeric_error(__METHOD__, $params, 0)) {
			return false;
		}
		
		$sql = "SELECT r.`rec_email`, r.`rec_type`, e.`reg_fname`, e.`reg_lname`, e.`reg_phone`, e.`reg_zip`, e.`reg_patient`, e.`reg_info_choices`, e.`reg_send_screen_info`, event.`event_name`,
				DATE_FORMAT(o.`obj_created`, '%b %e, %Y %l:%i%p') AS 'date'
			FROM `acp_event_registrations` e, `acp_records` r, `acp_events` event, `acp_objects` o, `acp_waivers` w
			WHERE e.`event_id` = ?
			AND r.`rec_id` = e.`rec_id`
			AND event.`event_id` = e.`event_id`
			AND o.`obj_id` = r.`obj_id`
			AND w.`rec_id` = r.`rec_id`";
		return $this->query_fetch_all($sql, $params);
	}
	
	/*
		Miscellanious functions
	*/
	public function sql_get_screening($id) {
		if (self::param_numeric_error(__METHOD__, array($id), 0)) {
			return false;
		}
		
		$sql = "SELECT `rec_type`
			FROM `acp_records`
			WHERE `rec_id` = ?";
		$row = $this->query_fetch($sql);
		return ($row && !empty($row->rec_type) ? $row->rec_type : false);
	}
	
	//$archived = 1 for archived events, $archived = 0 for non-archived, $archived = null for all
	public function sql_list_events($after = null, $before = null, $archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$orderby = (empty($orderby) ? 'e.`event_start`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, e.`event_screen_types` + 0 AS 'event_screen_types_num', o.*
				FROM `acp_events` e, `acp_objects` o, `acp_object_cats` c
				WHERE o.`obj_id` = e.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, e.`event_screen_types` + 0 AS 'event_screen_types_num', o.*
				FROM `acp_events` e, `acp_objects` o
				WHERE o.`obj_id` = e.`obj_id`
				AND o.`obj_deleted` = 'No'";
		}
		
		if ($after instanceof DateTime) {
			$sql .= " AND e.`event_start` >= ?";
			$params[] = $after->format('Y-m-d H:i:s');
		}
		if ($before instanceof DateTime) {
			$sql .= " AND e.`event_start` <= ?";
			$params[] = $before->format('Y-m-d H:i:s');
		}
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
		return false;
	}
	
	public function sql_list_events_on_day($date = null, $archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$orderby = (empty($orderby) ? 'e.`event_start`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, o.*, e.`event_screen_types` + 0 AS 'event_screen_types_num'
				FROM `acp_events` e, `acp_objects` o, `acp_object_cats` c
				WHERE o.`obj_id` = e.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, o.*, e.`event_screen_types` + 0 AS 'event_screen_types_num'
				FROM `acp_events` e, `acp_objects` o
				WHERE o.`obj_id` = e.`obj_id`
				AND o.`obj_deleted` = 'No'";
		}
		if ($date instanceof DateTime) {
			$sql .= " AND e.`event_start` >= ? AND e.`event_start` <= ?";
			$params[] = $date->format('Y-m-d 00:00:00');
			$params[] = $date->format('Y-m-d 23:59:59');
		}
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
		return false;
	}
	
	public function sql_list_offices($archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$orderby = (empty($orderby) ? 'off.`office_name`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'asc') ? 'ASC' : 'DESC');
		$cat = (int)$cat;
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS off.*, o.*
				FROM `acp_offices` off, `acp_objects` o, `acp_object_cats` c
				WHERE o.`obj_id` = off.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS off.*, o.*
				FROM `acp_offices` off, `acp_objects` o
				WHERE o.`obj_id` = off.`obj_id`
				AND o.`obj_deleted` = 'No'";
		}
		
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
	}
	
	public function sql_list_staff($archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$orderby = (empty($orderby) ? 'o.`obj_created`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS s.*, o.*
				FROM `acp_staff` s, `acp_objects` o, `acp_object_cats` c
				WHERE o.`obj_id` = s.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS s.*, o.*
				FROM `acp_staff` s, `acp_objects` o
				WHERE o.`obj_id` = s.`obj_id`
				AND o.`obj_deleted` = 'No'";
		}
		
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
	}
	
	public function sql_list_screenings($event = null, $scrtype = 'Screen%', $after = null, $before = null, $archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$event = (int)$event;
		$orderby = (empty($orderby) ? 'o.`obj_created`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		$scrtype = (empty($scrtype) ? 'Screen%' : (string)$scrtype);
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS r.`rec_id`, r.`rec_type`, o.`obj_id`, e.`event_id`
				FROM `acp_records` r, `acp_objects` o, `acp_object_cats` c, `acp_event_records` e
				WHERE r.`rec_type` LIKE '{$scrtype}'
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND e.`rec_id` = r.`rec_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS r.`rec_id`, r.`rec_type`, o.`obj_id`, e.`event_id`
				FROM `acp_records` r, `acp_objects` o, `acp_event_records` e
				WHERE r.`rec_type` LIKE '{$scrtype}'
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND e.`rec_id` = r.`rec_id`";
		}
		
		if ($after instanceof DateTime) {
			$sql .= " AND o.`obj_created` >= ?";
			$params[] = $after->format('Y-m-d H:i:s');
		}
		if ($before instanceof DateTime) {
			$sql .= " AND o.`obj_created` <= ?";
			$params[] = $before->format('Y-m-d H:i:s');
		}
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($event)) {
			$sql .= " AND e.`event_id` = ? AND r.`rec_id` = e.`rec_id`";
			$params[] = $event;
		}
		
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
	}
	
	public function sql_list_screenings_on_day($event = null, $scrtype = 'Screen%', $date = null, $archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$event = (int)$event;
		$orderby = (empty($orderby) ? 'o.`obj_created`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		$scrtype = (empty($scrtype) ? 'Screen%' : (string)$scrtype);
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS r.`rec_id`, r.`rec_type`, o.`obj_id`, e.`event_id`
				FROM `acp_records` r, `acp_objects` o, `acp_object_cats` c, `acp_event_records` e
				WHERE r.`rec_type` LIKE '{$scrtype}'
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND e.`rec_id` = r.`rec_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS r.`rec_id`, r.`rec_type`, o.`obj_id`, e.`event_id`
				FROM `acp_records` r, `acp_objects` o, `acp_event_records` e
				WHERE r.`rec_type` LIKE '{$scrtype}'
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND e.`rec_id` = r.`rec_id`";
		}
		
		if ($date instanceof DateTime) {
			$sql .= " AND o.`obj_created` >= ? AND o.`obj_created` <= ?";
			$params[] = $date->format('Y-m-d 00:00:00');
			$params[] = $date->format('Y-m-d 23:59:59');
		}
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($event)) {
			$sql .= " AND e.`event_id` = ? AND r.`rec_id` = e.`rec_id`";
			$params[] = $event;
		}
		
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
	}
	
	public function sql_list_event_regs($event = null, $after = null, $before = null, $archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$event = (int)$event;
		$orderby = (empty($orderby) ? 'o.`obj_created`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, r.*, o.*, e.`reg_info_choices` + 0 AS 'reg_info_choices_num'
				FROM `acp_event_registrations` e, `acp_records` r, `acp_objects` o, `acp_object_cats` c
				WHERE r.`rec_id` = e.`rec_id`
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, r.*, o.*, e.`reg_info_choices` + 0 AS 'reg_info_choices_num'
				FROM `acp_event_registrations` e, `acp_records` r, `acp_objects` o
				WHERE r.`rec_id` = e.`rec_id`
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'";
		}
		
		if ($after instanceof DateTime) {
			$sql .= " AND o.`obj_created` >= ?";
			$params[] = $after->format('Y-m-d H:i:s');
		}
		if ($before instanceof DateTime) {
			$sql .= " AND o.`obj_created` <= ?";
			$params[] = $before->format('Y-m-d H:i:s');
		}
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($event)) {
			$sql .= " AND e.`event_id` = ? AND r.`rec_id` = e.`rec_id`";
			$params[] = $event;
		}
		
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
		return false;
	}
	
	public function sql_list_event_regs_on_day($event = null, $date = null, $archived = 0, $start = 0, $amount = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$params = array();
		$start = (int)$start;
		$amount = (int)$amount;
		$event = (int)$event;
		$orderby = (empty($orderby) ? 'o.`obj_created`' : $orderby);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$cat = (int)$cat;
		
		if ($cat) {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, r.*, o.*, e.`reg_info_choices` + 0 AS 'reg_info_choices_num'
				FROM `acp_event_registrations` e, `acp_records` r, `acp_objects` o, `acp_object_cats` c
				WHERE r.`rec_id` = e.`rec_id`
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'
				AND c.`obj_id` = o.`obj_id`
				AND c.`cat_id` = ?";
			$params[] = $cat;
		}
		else {
			$sql = "SELECT SQL_CALC_FOUND_ROWS e.*, r.*, o.*, e.`reg_info_choices` + 0 AS 'reg_info_choices_num'
				FROM `acp_event_registrations` e, `acp_records` r, `acp_objects` o
				WHERE r.`rec_id` = e.`rec_id`
				AND o.`obj_id` = r.`obj_id`
				AND o.`obj_deleted` = 'No'";
		}
		
		if ($date instanceof DateTime) {
			$sql .= " AND o.`obj_created` >= ? AND o.`obj_created` <= ?";
			$params[] = $date->format('Y-m-d 00:00:00');
			$params[] = $date->format('Y-m-d 23:59:59');
		}
		if (!is_null($archived)) {
			$sql .= " AND o.`obj_archived` = ?";
			$params[] = (int)$archived;
		}
		if (!empty($event)) {
			$sql .= " AND e.`event_id` = ? AND r.`rec_id` = e.`rec_id`";
			$params[] = $event;
		}
		
		if (!empty($orderby)) {
			$sql .= " ORDER BY {$orderby} " . (empty($orderdir) ? '' : $orderdir);
		}
		if (!empty($start) && !empty($amount)) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif (!empty($amount)) {
			$sql .= " LIMIT {$amount}";
		}
		if ($ret = $this->query_fetch_all($sql, $params)) {
			$total_found = $this->query_single("SELECT FOUND_ROWS()");
			return $ret;
		}
		return false;
	}
	
	public function sql_set_event_record(array $params) {
		if (self::param_numeric_error(__METHOD__, $params, array(0, 1))) {
			return false;
		}
		$sql = "INSERT INTO `acp_event_records`
			SET `rec_id` = ?
			, `event_id` = ?";
		return $this->query_bool($sql, $params);
	}
	
	public function sql_list_cats($obj_type = null) {
		$params = array();
		$sql = "SELECT `cat_id`, `cat_name`
			FROM `acp_categories`";
		if ($obj_type) {
			$sql .= " WHERE `cat_type` = ? OR `cat_type` = NULL";
			$params[] = $obj_type;
		}
		return $this->query_fetch_all($sql, $params);
	}
	
	public function sql_event_multi_delete(array $eids) {
		if (count($eids)) {
			//array_walk($eids, 'intval');
			$eids = implode(',', $eids);
			
			$sql = "SELECT `obj_id`
				FROM `acp_events`
				WHERE `event_id` IN ({$eids})";
			$oids = $this->query_fetch_col($sql);
			$oids = implode(',', $oids);
			
			$sql = "UPDATE `acp_objects`
				SET `obj_deleted` = 'Yes'
				WHERE `obj_id` IN ({$oids})";
			return $this->query_bool($sql);
			
			/*$sql = "SELECT `rec_id`
				FROM `acp_event_records`
				WHERE `event_id` IN ({$eids})";
			$rids = $this->query_fetch_col($sql);
			$rids = implode(',', $rids);
			
			$asql = array();
			$asql[] = "DELETE FROM `acp_screen_bmi` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_screen_bp` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_screen_vision` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_screen_vitals` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_event_registrations` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_records` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_event_records` WHERE `rec_id` IN ({$eids})";
			$asql[] = "DELETE FROM `acp_events` WHERE `event_id` IN ({$eids})";
			$asql[] = "DELETE FROM `acp_objects` WHERE `obj_id` IN ({$oids})";
			foreach ($asql as $sql) {
				$this->query_bool($sql);
			}*/
		}
	}
	
	public function sql_staff_multi_delete(array $sids) {
		if (count($sids)) {
			//array_walk($sids, 'intval');
			$sids = implode(',', $sids);
			
			$sql = "SELECT `obj_id`
				FROM `acp_staff`
				WHERE `staff_id` IN ({$sids})";
			$oids = $this->query_fetch_col($sql);
			$oids = implode(',', $oids);
			
			$sql = "UPDATE `acp_objects`
				SET `obj_deleted` = 'Yes'
				WHERE `obj_id` IN ({$oids})";
			return $this->query_bool($sql);
			
			/*$asql = array();
			$asql[] = "DELETE FROM `acp_staff` WHERE `staff_id` IN ({$sids})";
			$asql[] = "DELETE FROM `acp_objects` WHERE `obj_id` IN ({$oids})";
			foreach ($asql as $sql) {
				$this->query_bool($sql);
			}*/
		}
	}
	
	public function sql_office_multi_delete(array $offids) {
		if (count($offids)) {
			$offids = implode(',', $offids);
			
			$sql = "UPDATE `acp_objects` obj, `acp_offices` off
				SET obj.`obj_deleted` = 'Yes'
				WHERE off.`office_id` IN ({$offids})
				AND obj.`obj_id` = off.`obj_id`";
			return $this->query_bool($sql);
		}
	}
	
	public function sql_events_archive(array $eids, $archive = 1) {
		if (count($eids)) {
			//array_walk($eids, 'intval');
			$eids = implode(',', $eids);
			$archive = (bool)$archive;
			$archive = (int)$archive;
			
			$sql = "UPDATE `acp_objects` o, `acp_events` e
				SET o.`obj_archived` = '{$archive}'
				WHERE e.`event_id` IN ({$eids})
				AND o.`obj_id` = e.`obj_id`";
			$this->query_bool($sql);
		}
	}
	
	public function sql_staff_archive(array $sids, $archive = 1) {
		if (count($sids)) {
			//array_walk($sids, 'intval');
			$sids = implode(',', $sids);
			$archive = (bool)$archive;
			$archive = (int)$archive;
			
			$sql = "UPDATE `acp_objects` o, `acp_staff` s
				SET o.`obj_archived` = '{$archive}'
				WHERE s.`staff_id` IN ({$sids})
				AND o.`obj_id` = s.`obj_id`";
			$this->query_bool($sql);
		}
	}
	
	public function sql_reg_archive(array $rids, $archive = 1) {
		if (count($rids)) {
			$rids = implode(',', $rids);
			
			$sql = "UPDATE `acp_objects` o, `acp_records` r
				SET o.`obj_archived` = '{$archive}'
				WHERE r.`rec_id` IN ({$rids})
				AND o.`obj_id` = r.`obj_id`";
			$this->query_bool($sql);
		}
	}
	
	public function sql_office_archive(array $offids, $archive = 1) {
		if (count($offids)) {
			$offids = implode(',', $offids);
			
			$sql = "UPDATE `acp_objects` obj, `acp_offices` off
				SET obj.`obj_archived` = '{$archive}'
				WHERE off.`office_id` IN ({$offids})
				AND obj.`obj_id` = off.`obj_id`";
			return $this->query_bool($sql);
		}
	}
	
	public function sql_event_reg_multi_delete(array $rids, $archive = 1) {
		if (count($rids)) {
			$rids = implode(',', $rids);
			
			$sql = "SELECT `obj_id`
				FROM `acp_records`
				WHERE `rec_id` IN ({$rids})";
			$oids = $this->query_fetch_col($sql);
			$oids = implode(',', $oids);
			
			$sql = "UPDATE `acp_objects`
				SET `obj_deleted` = 'Yes'
				WHERE `obj_id` IN ({$oids})";
			return $this->query_bool($sql);
			
			/*$asql = array();
			$asql[] = "DELETE FROM `acp_event_registrations` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_records` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_event_records` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_objects` WHERE `obj_id` IN ({$oids})";
			foreach ($asql as $sql) {
				$this->query_bool($sql);
			}*/
		}
	}
	
	public function sql_screening_multi_delete(array $rids, $archive = 1) {
		if (count($rids)) {
			$rids = implode(',', $rids);
			
			$sql = "SELECT `obj_id`
				FROM `acp_records`
				WHERE `rec_id` IN ({$rids})";
			$oids = $this->query_fetch_col($sql);
			$oids = implode(',', $oids);
			
			$sql = "UPDATE `acp_objects`
				SET `obj_deleted` = 'Yes'
				WHERE `obj_id` IN ({$oids})";
			return $this->query_bool($sql);
			
			/*$asql = array();
			$asql[] = "DELETE FROM `acp_screen_bmi` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_screen_bp` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_screen_vision` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_screen_vitals` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_records` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_event_records` WHERE `rec_id` IN ({$rids})";
			$asql[] = "DELETE FROM `acp_objects` WHERE `obj_id` IN ({$oids})";
			foreach ($asql as $sql) {
				$this->query_bool($sql);
			}*/
		}
	}
	
	public function sql_list_errors($status = ACPManager::E_STATUS_OPEN, $severity = ACPManager::E_LEVEL_ANY, $start = 0, $amount = 30, $orderby = '', $orderdir = '', &$total_found = null) {
		$orderby = (empty($orderby) ? 'einst_time' : $einst_time);
		$orderdir = (empty($orderdir) || (strtolower($orderdir) == 'desc') ? 'DESC' : 'ASC');
		$start = (int)$start;
		$amount = (int)$amount;
		$sql = "SELECT *
			FROM `acp_errors`
			ORDER BY `{$orderby}` {$orderdir}";
		if ($start) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif ($amount) {
			$sql .= " LIMIT {$amount}";
		}
		return $this->query_fetch_col($sql);
	}
	
	public function sql_list_errors_by_date($status = ACPManager::E_STATUS_OPEN, $severity = ACPManager::E_LEVEL_ANY, $last_occurance = null, $start = 0, $amount = 30, $orderby = '', $orderdir = '', &$total_found = null) {
		$severity = (int)$severity;
		$sql = "SELECT `error_id`
			FROM `acp_errors`";
		$where = false;
		if ($status != ACPManager::E_STATUS_ANY) {
			$sql .= " WHERE `error_status` = '{$status}'";
			$where = true;
		}
		if ($severity != ACPManager::E_LEVEL_ANY) {
			$sql .= ($where ? ' AND ' : ' WHERE ');
			$where = true;
			$comp = (is_string($severity) ? '' : '=');
			$sql .= " WHERE `error_severity` {$comp} {$severity}";
		}
		
		$eids = $this->query_fetch_col($sql);
		if (empty($eids)) {
			return false;
		}
		$eids = implode(',', $eids);
		
		$orderby = (empty($orderby) ? 'einst_time' : $einst_time);
		$orderdir = (strtolower($orderdir) == 'asc' ? 'ASC' : 'DESC');
		$start = (int)$start;
		$amount = (int)$amount;
		$sql = "SELECT `error_id`
			FROM `acp_error_instances`
			WHERE `error_id` IN ({$eids})";
		if ($last_occurance) {
			$sql .= " AND `einst_time` >= '{$last_occurance}'";
		}
		
		$sql .= " GROUP BY `error_id`
			ORDER BY `{$orderby}` {$orderdir}";
		
		if ($start) {
			$sql .= " LIMIT {$start}, {$amount}";
		}
		elseif ($amount) {
			$sql .= " LIMIT {$amount}";
		}
		return $this->query_fetch_col($sql);
	}
}

?>