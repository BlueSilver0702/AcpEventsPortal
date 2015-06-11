<?php 

class ACPValidator {
	protected static $initialized = false;
	protected static $lang;
	
	public static function __callStatic($n, $params) {
		if (!self::$initialized) {
			self::init();
		}
		
		$n = strtolower($n);
		if (method_exists('ACPValidator', $n)) {
			return self::$n($params[0]);
		}
		else {
			ACPManager::error("Static method ACPValidator::{$n} does not exist");
			return true; //all validator functions return a !empty() value to indicate an error, so we return true here to account for this
		}
	}
	
	protected static function init() {
		global $acplang;
		self::$lang = &$acplang;
	}
	
	protected static function validate_screenbp($params) {
		$errors = array();
		if (empty($params['bp_sys'])) {
			$errors[] = self::$lang->get_text('bp_error_no_sys');
		}
		if (empty($params['bp_dia'])) {
			$errors[] = self::$lang->get_text('bp_error_no_dia');
		}
		return $errors;
	}
	
	protected static function validate_screenbmi($params) {
		$errors = array();
		if (empty($params['bmi'])) {
			$errors[] = self::$lang->get_text('bmi_error_blank');
		}
		return $errors;
	}
	
	protected static function validate_screenvision($params) {
		$errors = array();
		if (empty($params['left'])) {
			$errors[] = self::$lang->get_text('vision_error_no_left');
		}
		if (empty($params['right'])) {
			$errors[] = self::$lang->get_text('vision_error_no_right');
		}
		return $errors;
	}
	
	protected static function validate_screenvitals($params) {
		$errors = array();
		if (empty($params['temperature'])) {
			$errors[] = self::$lang->get_text('vitals_error_no_temperature');
		}
		if (empty($params['respiration'])) {
			$errors[] = self::$lang->get_text('vitals_error_no_respiration');
		}
		if (empty($params['pulse'])) {
			$errors[] = self::$lang->get_text('vitals_error_no_pulse');
		}
		if (empty($params['bp_sys'])) {
			$errors[] = self::$lang->get_text('bp_error_no_sys');
		}
		if (empty($params['bp_dia'])) {
			$errors[] = self::$lang->get_text('bp_error_no_dia');
		}
		return $errors;
	}
	
	protected static function validate_waiver($params) {
		$errors = array();
		if (empty($params['fname'])) {
			$errors[] = self::$lang->get_text('waiver_error_no_fname');
		}
		if (empty($params['lname'])) {
			$errors[] = self::$lang->get_text('waiver_error_no_lname');
		}
		if (empty($params['agree'])) {
			$errors[] = self::$lang->get_text('waiver_error_must_agree');
		}
		/*if (empty($params['witness_fname'])) {
			$errors[] = self::$lang->get_text('waiver_error_no_wit_fname');
		}
		if (empty($params['witness_lname'])) {
			$errors[] = self::$lang->get_text('waiver_error_no_wit_lname');
		}*/
		/*if (empty($params['rec_id'])) {
			$errors[] = self::$lang->get_text('waiver_error_no_record');
		}*/
		return $errors;
	}
	
	protected static function validate_event_register($params) {
		$errors = array();
		/*if (empty($params['fname'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_fname');
		}
		if (empty($params['lname'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_lname');
		}*/
		if (empty($params['email'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_email');
		}
		elseif (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = self::$lang->get_text('event_reg_error_invalid_email');
		}
		return $errors;
	}
	
	protected static function validate_faq_register($params) {
		$errors = array();
		if (empty($params['fname'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_fname');
		}
		if (empty($params['lname'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_lname');
		}
		if (empty($params['email'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_email');
		}
		elseif (!filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
			$errors[] = self::$lang->get_text('event_reg_error_invalid_email');
		}
		return $errors;
	}
	
	protected static function validate_add_staff($params) {
		$errors = array();
		if (empty($params['fname'])) {
			$errors[] = 'Staff must have a first name';
		}
		if (empty($params['lname'])) {
			$errors[] = 'Staff must have a last name';
		}
		/*if (empty($params['phone'])) {
			$errors[] = self::$lang->get_text('event_reg_error_no_zip');
		}*/
		return $errors;
	}
	
	protected static function validate_add_specialty($params) {
		$errors = array();
		if (empty($params['name'])) {
			$errors[] = 'Specialty must have a name';
		}
		return $errors;
	}
	
	protected static function validate_event_checkout($params) {
		$errors = array();
		if (empty($params['interactions'])) {
			$errors[] = 'Please enter an estimate of total interactions';
		}
		if (empty($params['attendance'])) {
			$errors[] = 'Please enter an estimate of the attendance';
		}
		if (strlen($params['comment']) < 5) {
			$errors[] = 'Please enter a comment of at least 5 characters about this event';
		}
		return $errors;
	}
	
	protected static function validate_event_details($params) {
		$errors = array();
		return $errors;
	}
}

?>