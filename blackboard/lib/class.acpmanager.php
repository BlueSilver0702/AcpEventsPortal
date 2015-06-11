<?php

require_once 'class.acpsql.php';
require_once 'class.acperror.php';
//require_once 'class.acpfunctions.php';
require_once 'class.acpvalidator.php';
require_once 'class.acplist.php';
require_once 'class.acplang.php';
require_once 'class.acplangobject.php';
require_once 'class.acplangtext.php';
require_once 'class.acpwaiver.php';
require_once 'class.acpeditable.php';
require_once 'class.acprecord.php';
require_once 'class.acpeventstaff.php';
require_once 'class.acpevent.php';
require_once 'class.acpstaff.php';
require_once 'class.acpspecialty.php';
require_once 'class.acpoffice.php';
require_once 'class.acpscreeningbmi.php';
require_once 'class.acpscreeningbp.php';
require_once 'class.acpscreeningvision.php';
require_once 'class.acpscreeningvitals.php';
require_once 'class.acpeventregistration.php';

class ACPManager {
	//ACPManager::error() special $code parameters
	const E_CODE_BYPASS = 'ACPErrorBypass';	//Bypasses ACP's error logging
	const E_CODE_DOSAVE = 'ACPErrorDoSave';	//Save error on script end/termination
	const E_CODE_DOLOAD = 'ACPErrorDoLoad';	//Load error instances on creation
	const E_CODE_DOADD = 'ACPErrorDoAdd';	//Add error instance on creation
	
	//Error severities
	const E_LEVEL_CRITICAL = 1;	//Genuine error and requires script termination
	const E_LEVEL_RECOVERABLE = 2;	//Genuine error but recoverable 
	const E_LEVEL_WARNING = 3;	//Not an actual error, but perhaps a mistake by the developer, or a potentially dangerous improper way of doing something
	const E_LEVEL_NOTICE = 4;	//Not an error, but a possible problem, or something that needs to be noted
	const E_LEVEL_DEBUG = 5;	//Only used when getting debug info
	//Used for mysql selects
	const E_LEVEL_ANY = -1; 
	const E_LEVEL_LTRECOVERABLE = '< 1'; 
	const E_LEVEL_LTWARNING = '< 2';
	const E_LEVEL_LTNOTICE = '< 3';
	const E_LEVEL_LTDEBUG = '< 4';
	
	//Error statuses (these values must match the db enum values)
	const E_STATUS_OPEN = 'Open';
	const E_STATUS_FIXED = 'Fixed';
	const E_STATUS_ANY = 'Any'; //For selecting
	//const E_ASSIGNED = 'Assigned';
	
	//acp_objects 'type' values
	const OBJ_STAFF = 'Staff';
	const OBJ_EVENT = 'Event';
	const OBJ_RECORD = 'Record';
	const OBJ_OFFICE = 'Office';
	
	protected static $db;
	protected static $errors;
	protected static $events;
	protected static $records;
	protected static $staff;
	protected static $event_staff;
	protected static $offices;
	protected static $specialties;
	protected static $lang;
	
	//Calls to ACPSql methods are relayed here
	public static function __callStatic($n, $args) {
		if (self::$db) {
			if (method_exists(self::$db, $n)) {
				return self::$db->$n($args);
			}
		}
		else {
			self::error("Call to undefined or inaccessible static method ACPManager::{$n}");
			return false;
		}
	}
	
	//public static function init(&$db, $lang = null) {
	public static function init(&$db) {
		if ($db instanceof ACPSql) {
			self::$db = $db;
		}
		else {
			self::$db = new ACPSql('host=localhost', 'root', '');
		}
		set_error_handler('ACPManager::php_error');
		//self::load_lang($lang);
	}
	
	public static function &load_lang($lang = null) {
		$lang = new ACPLang((empty($lang) ? self::$db->lang_by_default() : $lang));
		self::$lang = &$lang;
		return $lang;
	}
	
	public static function validate($name, array $params) {
		$call = "validate_{$name}";
		if (method_exists('ACPValidator', $call)) {
			return ACPValidator::$call($params);
		}
		else {
			self::error("Static method ACPValidator::{$call} does not exist");
			return true; //all validator functions return a !empty() value to indicate an error, so we return true here to account for this
		}
	}
	
	public static function address_to_latlng($address) {
		if (empty($address)) {
			return false;
		}
		$address = rawurlencode($address);
		$ch = curl_init("http://maps.google.com/maps/api/geocode/json?address={$address}&sensor=falase");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$resp = curl_exec($ch);
		if ($resp) {
			$resp = json_decode($resp);
			if ($resp) {
				return array($resp->results[0]->geometry->location->lat, $resp->results[0]->geometry->location->lng);
			}
			else {
				return self::error(json_last_error(), 0, self::E_LEVEL_WARNING);
			}
		}
		else {
			return self::error(curl_error($ch), curl_errno($ch), self::E_LEVEL_WARNING);
		}
	}
	
	public static function save_office_xml($savepath, $savefile = 'offices.xml') {
		$offices = self::list_offices(0, 0);
		if (!empty($offices)) {
			$doc = new DOMDocument('1.0');
			$doc->formatOutput = true;
			$markers = $doc->createElement('markers');
			$markers = $doc->appendChild($markers);
			
			foreach ($offices as $office) {
				if ($office && $office->name) {
					$mrk = $doc->createElement('marker');
					$mrk = $markers->appendChild($mrk);
					$mrk->setAttribute('name', $office->name);
					//$mrk->setAttribute('address', $office->address);
					$mrk->setAttribute('addrln1', $office->street);
					$mrk->setAttribute('addrln2', "{$office->county}, {$office->state} {$office->zip}");
					$mrk->setAttribute('phone', $office->phone);
					$mrk->setAttribute('lat', $office->lat);
					$mrk->setAttribute('lng', $office->lng);
					if (count($office->specialties)) {
						foreach ($office->specialties as $s) {
							$spec = $doc->createElement('specialty');
							$spec = $mrk->appendChild($spec);
							$spec->setAttribute('id', $s->id);
							$spec->setAttribute('name', $s->name);
						}
					}
				}
			}
			
			$xml = $doc->saveXML();
			
			file_put_contents($savepath . $savefile, $xml);
		}
	}
	
	public static function list_langs() {
		$langs = self::$db->lang_list();
		$ret = array();
		if (!empty($langs)) {
			foreach ($langs as $v) {
				$ret[] = new ACPLang($v, false);
			}
		}
		return $ret;
	}
	
	public static function list_lang_objects($lang_id = 0, $start = 0, $amount = 30, $filter = '', &$total_found = null) {
		$objs = ($lang_id ? self::$db->lang_list_untranslated($lang_id, $start, $amount, $filter, $total_found) : self::$db->lang_list_objects($start, $amount, $filter, $total_found));
		$ret = array();
		if (!empty($objs)) {
			foreach ($objs as $v) {
				$ret[] = new ACPLangObject($v, false);
			}
		}
		return $ret;
	}
	
	public static function office_multi_delete(array $oids) {
		self::$db->sql_office_multi_delete($oids);
		return;
	}
	
	public static function event_multi_delete(array $eids) {
		self::$db->sql_event_multi_delete($eids);
		return;
	}
	
	public static function staff_multi_delete(array $sids) {
		self::$db->sql_staff_multi_delete($sids);
		return;
	}
	
	public static function events_archive(array $eids, $archive = 1) {
		self::$db->sql_events_archive($eids, $archive);
		return;
	}
	
	public static function staff_archive(array $sids, $archive = 1) {
		self::$db->sql_staff_archive($sids, $archive);
		return;
	}
	
	public static function reg_archive(array $rids, $archive = 1) {
		self::$db->sql_reg_archive($rids, $archive);
	}
	
	public static function office_archive(array $oids, $archive = 1) {
		self::$db->sql_office_archive($oids, $archive);
		return;
	}
	
	public static function event_reg_multi_delete(array $rids) {
		self::$db->sql_event_reg_multi_delete($rids);
	}
	
	public static function screening_multi_delete(array $rids) {
		self::$db->sql_screening_multi_delete($rids);
	}
	
	public static function list_errors($status = self::E_STATUS_OPEN, $severity = ACPManager::E_LEVEL_ANY, $last_occurance = null, $amount = 30, $start = 0, $orderby = '', $orderdir = '', &$total_found = null) {
		if (!empty($last_occurance)) {
			$last_occurance = self::to_date($last_occurance);
		}
		if ($last_occurance instanceof DateTime) {
			$last_occurance = $last_occurance->format('Y-m-d H:i:s');
		}
		if ($last_occurance) {
			$errors = self::$db->sql_list_errors_by_date($status, $severity, $last_occurance, $start, $amount, $orderby, $orderdir, $total_found);
		}
		else {
			$errors = self::$db->sql_list_errors($status, $severity, $start, $amount, $orderby, $orderdir, $total_found);
		}
		$ret = array();
		if (is_array($errors)) {
			foreach ($errors as $v) {
				$ret[] = new ACPError($v);
			}
		}
		return $ret;
	}
	
	public static function list_cats($obj_type = null) {
		return self::$db->sql_list_cats($obj_type);
	}
	
	public static function current_user() {
		return wp_get_current_user();
	}
	
	public static function current_user_id() {
		return get_current_user_id();
	}
	
	public static function stats_event_reg($after = null, $before = null, $is_patient = null) {
		if ($after) {
			$after = self::to_date($after);
		}
		if ($before) {
			$before = self::to_date($before);
		}
		return self::$db->sql_stats_event_reg($after, $before, $is_patient);
	}
	
	public static function stats_screenings($after = null, $before = null, $type = 'Screen%', $registered = null) {
		if ($after) {
			$after = self::to_date($after);
		}
		if ($before) {
			$before = self::to_date($before);
		}
		return self::$db->sql_stats_screenings($after, $before, $type, $registered);
	}
	
	public static function list_specialties() {
		$specs = self::$db->sql_list_specialties();
		$ret = array();
		if (!empty($specs)) {
			foreach ($specs as $v) {
				$ret[] = self::get_specialty($v);
			}
		}
		return $ret;
	}
	
	public static function list_offices($archive = 0, $amount = 30, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$offices = self::$db->sql_list_offices($archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($offices)) {
			$ret = array();
			foreach ($offices as $v) {
				$ret[] = self::get_office($v);
			}
			return $ret;
		}
		elseif (is_array($offices)) {
			return array();
		}
		return false;
	}
	
	public static function list_events($after = null, $before = null, $archived = 0, $amount = 30, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		if ($after) {
			$after = self::to_date($after);
		}
		if ($before) {
			$before = self::to_date($before);
		}
		$events = self::$db->sql_list_events($after, $before, $archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($events)) {
			$ret = array();
			foreach ($events as $v) {
				$ret[] = self::get_event($v);
			}
			return $ret;
		}
		elseif (is_array($events)) {
			return array();
		}
		return false;
	}
	
	public static function list_events_on_day($date = null, $archived = 0, $amount = 30, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		if ($date) {
			$date = self::to_date($date);
		}
		
		$events = self::$db->sql_list_events_on_day($date, $archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($events)) {
			$ret = array();
			foreach ($events as $v) {
				$ret[] = self::get_event($v);
			}
			return $ret;
		}
		elseif (is_array($events)) {
			return array();
		}
		return false;
	}
	
	public static function list_staff($archived = 0, $amount = 30, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		$staff = self::$db->sql_list_staff($archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($staff)) {
			$ret = array();
			foreach ($staff as $v) {
				$ret[] = self::get_staff($v);
			}
			return $ret;
		}
		elseif (is_array($staff)) {
			return array();
		}
		return false;
	}
	
	public static function list_screenings($event = null, $scrtype = 'Screen%', $after = null, $before = null, $archived = 0, $amount = 0, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		if ($after) {
			$after = self::to_date($after);
		}
		if ($before) {
			$before = self::to_date($before);
		}
		$screens = self::$db->sql_list_screenings($event, $scrtype, $after, $before, $archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($screens)) {
			$ret = array();
			foreach ($screens as $v) {
				$ret[] = self::get_screening($v);
			}
			return $ret;
		}
		elseif (is_array($screens)) {
			return array();
		}
		return false;
	}
	
	public static function list_screenings_on_day($event = null, $scrtype = 'Screen%', $date = null, $archived = 0, $amount = 0, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		if ($date) {
			$date = self::to_date($date);
		}
		$screens = self::$db->sql_list_screenings_on_day($event, $scrtype, $date, $archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($screens)) {
			$ret = array();
			foreach ($screens as $v) {
				$ret[] = self::get_screening($v);
			}
			return $ret;
		}
		elseif (is_array($screens)) {
			return array();
		}
		return false;
	}
	
	public static function list_event_regs($event = null, $after = null, $before = null, $archived = 0, $amount = 0, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		if ($after) {
			$after = self::to_date($after);
		}
		if ($before) {
			$before = self::to_date($before);
		}
		$regs = self::$db->sql_list_event_regs($event, $after, $before, $archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($regs)) {
			$ret = array();
			foreach ($regs as $v) {
				$ret[] = self::get_event_registration($v);
			}
			return $ret;
		}
		elseif (is_array($regs)) {
			return array();
		}
		return false;
	}
	
	public static function list_event_regs_on_day($event = null, $date = null, $archived = 0, $amount = 0, $start = 0, $orderby = '', $orderdir = '', $cat = null, &$total_found = null) {
		if ($date) {
			$date = self::to_date($date);
		}
		$regs = self::$db->sql_list_event_regs_on_day($event, $date, $archived, $start, $amount, $orderby, $orderdir, $cat, $total_found);
		if (!empty($regs)) {
			$ret = array();
			foreach ($regs as $v) {
				$ret[] = self::get_event_registration($v);
			}
			return $ret;
		}
		elseif (is_array($regs)) {
			return array();
		}
		return false;
	}
	
	public static function to_date($v, $tz = null) {
		if (!empty($v)) {
			if ($v instanceof DateTime) {
				return $v;
			}
			if (is_numeric($v)) { //In case time() was used for the value
				$v = '@' . $v;
			}
			if (is_string($v)) {
				try {
					$date = new DateTime($v, $tz);
				}
				catch (Exception $e) {
					return self::error($e->getMessage());
				}
				$chk = DateTime::getLastErrors();
				/*if ($chk['error_count']) {
					self::error("Invalid date format \"{$v}\"");
					return null;
				}*/
				if ($chk['warning_count']) {
					self::error("Date format \"{$v}\" produced warnings. Possible incorrect date generated", 0, self::E_LEVEL_WARNING);
					//return null;
				}
				return $date;
			}
			self::error('Parameter 1 must be DateTime or string in ' . __METHOD__ . '. ' . gettype($v) . ' given', 0, self::E_LEVEL_WARNING);
		}
		return null;
	}
	
	public static function get_relative_time($type) {
		list($dow, $day, $month, $year) = explode('|', date('w|j|n|Y'));
		$today = mktime(0, 0, 0, $month, $day, $year);
		$type = strtolower($type);
		switch ($type) {
			case 'last week':
				return new DateTime('@' . ($today - (($dow + 7) * 86400)));
			case 'last month':
				if ($month == 1) {
					return new DateTime('@' . mktime(0, 0, 0, 12, 1, $year - 1));
				}
				return new DateTime('@' . mktime(0, 0, 0, $month - 1, 1, $year));
			case 'this week':
				return new DateTime('@' . ($today - ($dow * 86400)));
			case 'this month':
				return new DateTime('@' . mktime(0, 0, 0, $month, 1, $year));
			default:
				return new DateTime();
		}
	}
	
	public static function send_faq_email($email, $from) {
		$msg = file_get_contents(get_template_directory() . '/email_faq.html');
		self::send_email($email, 'AdvantageCare Physicians - Choosing a Physician', $msg, $from);
	}
	
	public static function send_bp_email($email, $from) {
		$msg = file_get_contents(get_template_directory() . '/email_bp.html');
		self::send_email($email, 'Your AdvantageCare Physicians Blood Pressure Screening', $msg, $from);
	}
	
	public static function send_vision_email($email, $from) {
		$msg = file_get_contents(get_template_directory() . '/email_vision.html');
		self::send_email($email, 'Your AdvantageCare Physicians Vision Screening', $msg, $from);
	}
	
	public static function send_bmi_email($email, $from) {
		$msg = file_get_contents(get_template_directory() . '/email_bmi.html');
		self::send_email($email, 'Your AdvantageCare Physicians BMI & Body Fat Screening', $msg, $from);
	}
	
	public static function send_vitals_email($email, $from) {
		$msg = file_get_contents(get_template_directory() . '/email_vitals.html');
		self::send_email($email, 'AdvantageCare Physicians - More Information', $msg, $from);
	}
	
	public static function send_email($email, $subject, $msg, $from) {
		$headers = 'From: "AdvantageCare Physicians" <' . $from . '>' . "\r\n";
		$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
		mail($email, $subject, $msg, $headers, '-f ' . $from);
	}
	
	public static function php_error($num, $str, $file, $line) {
		switch ($num) {
			case E_ERROR:
				$severity = self::E_LEVEL_CRITICAL;
				break;
			case E_WARNING:
			case E_USER_WARNING:
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$severity = self::E_LEVEL_WARNING;
				break;
			case E_NOTICE:
			case E_USER_NOTICE:
			case E_STRICT:
				$severity = self::E_LEVEL_NOTICE;
				break;
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
			default:
				$severity = self::E_LEVEL_RECOVERABLE;
				
		}
		self::error($str, $num, $severity, $file, $line);
	}
	
	public static function error($msg = '', $code = 0, $severity = self::E_LEVEL_RECOVERABLE, $filename = __FILE__, $lineno = __LINE__, Exception $previous = null) {
		if (self::$db && ($code !== self::E_CODE_BYPASS)) {
			$error = self::get_error($msg, $code, $severity, $filename, $lineno, $previous);
			$error->add_instance();
			$error->save_on_end = true;
			//if ((count($error->new_instances) > 1000) && ($error->severity < self::E_LEVEL_NOTICE)) {
			if (count($error->new_instances) > 1000) {
				$error_infinite = new ACPError("Possible infinite loop caused by error ID#{$error->id}. Ending script execution", 0, self::E_LEVEL_CRITICAL);
				$error_infinite->add_instance();
				$error_infinite->save_on_end = true;
				$error->save_on_end = false;
				exit(0);
			}
			if ($severity ==  self::E_LEVEL_CRITICAL) {
				exit(0);
			}
		}
		else {
			throw new ErrorException($msg, 0, E_USER_WARNING, $filename, $lineno, $previous);
		}
		return false;
	}
	
	public static function &get_error($msg, $code= 0, $severity = self::E_LEVEL_RECOVERABLE, $file = null, $line = null, $previous = null) {
		return self::get_object_ref('errors', new ACPError($msg, $code, $severity, $file, $line, $previous));
	}
	
	public static function &get_office($data) {
		return self::get_object_ref('offices', new ACPOffice($data));
	}
	
	public static function &get_event($data) {
		return self::get_object_ref('events', new ACPEvent($data));
	}
	
	public static function &get_staff($data) {
		return self::get_object_ref('staff', new ACPStaff($data));
	}
	
	public static function &get_event_staff($data, $event_id = null) {
		return self::get_object_ref('event_staff', new ACPEventStaff($data, $event_id));
	}
	
	public static function &get_screening($data) {
		if (is_object($data) && isset($data->rec_id, $data->rec_type)) {
			$type = $data->rec_type;
			$data = $data->rec_id;
		}
		else {
			$type = self::$db->sql_get_screening($data);
		}
		switch ($type) {
			case 'ScreeningBMI':
				return self::get_object_ref('records', new ACPScreeningBMI($data));
			case 'ScreeningBP':
				return self::get_object_ref('records', new ACPScreeningBP($data));
			case 'ScreeningVitals':
				return self::get_object_ref('records', new ACPScreeningVitals($data));
			case 'ScreeningVision':
				return self::get_object_ref('records', new ACPScreeningVision($data));
			default:
				return null;
		}
	}
	
	public static function &get_specialty($data) {
		return self::get_object_ref('specialties', new ACPSpecialty($data));
	}
	
	public static function &get_screeningbmi($data) {
		return self::get_object_ref('records', new ACPScreeningBMI($data));
	}
	
	public static function &get_screeningbp($data) {
		return self::get_object_ref('records', new ACPScreeningBP($data));
	}
	
	public static function &get_screeningvision($data) {
		return self::get_object_ref('records', new ACPScreeningVision($data));
	}
	
	public static function &get_screeningvitals($data) {
		return self::get_object_ref('records', new ACPScreeningVitals($data));
	}
	
	public static function &get_event_registration($data) {
		return self::get_object_ref('records', new ACPEventRegistration($data));
	}
	
	protected static function &get_object_ref($name, $obj) {
		if (empty($obj->id)) {
			return $obj;
		}
		if (!empty($obj->id) && empty(self::${$name}[$obj->id])) {
			self::${$name}[$obj->id] = $obj;
		}
		/*if (!empty($obj->slug) && empty(self::${$name}[$obj->slug])) {
			self::${$name}[$obj->slug] = $obj;
		}*/
		$obj = self::${$name}[$obj->id];
		return $obj;
	}
}

?>