<?php
/*
Template Name: Events Ajax Handler
*/

if (!$is_coordinator && (empty($_REQUEST['events_action']) || ($_REQUEST['events_action'] != 'login'))) {
	exit(0);
}

if (!empty($_REQUEST['events_action'])) {
	$act = $_REQUEST['events_action'];
	switch ($act) {
		case 'login':
			$uinfo = array(
				'user_login' => $_REQUEST['loginfo']['user'],
				'user_password' => $_REQUEST['loginfo']['pass'],
				'remember' => true
			);
			$result = wp_signon($uinfo, false);
			if (is_wp_error($result)) {
				die('CMD_ERRORS' . $result->get_error_message());
			}
			die('CMD_REDIR' . get_page_link(38));
		case 'sel_event':
			global $current_user;
			acp_stripslashes($_REQUEST);
			if (empty($_REQUEST['event_id'])) {
				die('CMD_ERRORS' . 'No event selected');
			}
			$_SESSION['cur_event_id'] = (int)$_REQUEST['event_id'];
			get_currentuserinfo();
			update_user_meta($current_user->ID, 'cur_event_id', $_SESSION['cur_event_id']);
			die('CMD_REDIR' . get_page_link(40)); //redirect back to main options
		/*case 'screenbp':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['scrinfo'];
			$errors = ACPManager::validate('screenbp', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			elseif (empty($_SESSION['cur_rec_id'])) {
				die('CMD_REDIR' . get_page_link(47) . '?scrtype=bp');
			}
			
			$scr = ACPManager::get_screening($_SESSION['cur_rec_id']);
			$scr->bp_sys = $data['bp_sys'];
			$scr->bp_dia = $data['bp_dia'];
			//$scr->email = $data['email'];
			$scr->event = $_SESSION['cur_event_id'];
			
			if ($scr->insert()) {
				unset($_SESSION['cur_rec_id']);
				die('CMD_MSG' . $acplang('bp_success') . 'CMD_REDIR' . get_page_link(42)); //display message and redirect back to screenings
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'screenbmi':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['scrinfo'];
			$errors = ACPManager::validate('screenbmi', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			elseif (empty($_SESSION['cur_rec_id'])) {
				die('CMD_REDIR' . get_page_link(47) . '?scrtype=bmi');
			}
			
			$scr = ACPManager::get_screening($_SESSION['cur_rec_id']);
			$scr->bmi = $data['bmi'];
			//$scr->email = $data['email'];
			$scr->event = $_SESSION['cur_event_id'];
			
			if ($scr->insert()) {
				unset($_SESSION['cur_rec_id']);
				die('CMD_MSG' . $acplang('bmi_success') . 'CMD_REDIR' . get_page_link(42)); //display message and redirect back to screenings
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'screenvision':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['scrinfo'];
			$errors = ACPManager::validate('screenvision', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			elseif (empty($_SESSION['cur_rec_id'])) {
				die('CMD_REDIR' . get_page_link(47) . '?scrtype=vision');
			}
			
			$scr = ACPManager::get_screening($_SESSION['cur_rec_id']);
			$scr->left = $data['left'];
			$scr->right = $data['right'];
			$scr->color_blind = $data['color_blind'];
			//$scr->email = $data['email'];
			$scr->event = $_SESSION['cur_event_id'];
			
			if ($scr->insert()) {
				unset($_SESSION['cur_rec_id']);
				die('CMD_MSG' . $acplang('vision_success') . 'CMD_REDIR' . get_page_link(42)); //display message and redirect back to screenings
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'screenvitals':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['scrinfo'];
			$errors = ACPManager::validate('screenvitals', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			elseif (empty($_SESSION['cur_rec_id'])) {
				die('CMD_REDIR' . get_page_link(47) . '?scrtype=vitals');
			}
			
			$scr = ACPManager::get_screening($_SESSION['cur_rec_id']);
			$scr->temperature = $data['temperature'];
			$scr->respiration = $data['respiration'];
			$scr->pulse = $data['pulse'];
			$scr->bp_sys = $data['bp_sys'];
			$scr->bp_dia = $data['bp_dia'];
			//$scr->email = $data['email'];
			$scr->event = $_SESSION['cur_event_id'];
			
			if ($scr->insert()) {
				unset($_SESSION['cur_rec_id']);
				die('CMD_MSG' . $acplang('vitals_success') . 'CMD_REDIR' . get_page_link(42)); //display message and redirect back to screenings
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}*/
		case 'waiver':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['wvrinfo'];
			$errors = ACPManager::validate('waiver', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			
			$wvr = new ACPWaiver();
			$wvr->fname = $data['fname'];
			$wvr->lname = $data['lname'];
			/*$wvr->witness_fname = $data['witness_fname'];
			$wvr->witness_lname = $data['witness_lname'];*/
			
			if (empty($_SESSION['cur_rec_id'])) {
				$reg = new ACPEventRegistration();
				switch ($data['scrtype']) {
					case 'bmi':
						$reg->rec_type = ACPRecord::TYPE_SCREENBMI;
						break;
					case 'vision':
						$reg->rec_type = ACPRecord::TYPE_SCREENVISION;
						break;
					case 'bp':
						$reg->rec_type = ACPRecord::TYPE_SCREENBP;
						break;
					case 'vitals':
						$reg->rec_type = ACPRecord::TYPE_SCREENVITALS;
						break;
					default:
						break;
				}
				if ($reg->insert()) {
					$_SESSION['cur_rec_id'] = $reg->rec_id;
				}
				else {
					die('CMD_ERRORS' . $acplang('error_unknown'));
				}
			}
			
			$wvr->rec_id = $_SESSION['cur_rec_id'];
			$wvr->event = $_SESSION['cur_event_id'];
			
			if ($wvr->save()) {
				$reg = ACPManager::get_event_registration($_SESSION['cur_rec_id']);
				$reg->fname = $wvr->fname;
				$reg->lname = $wvr->lname;
				$reg->update();
				$dietxt = 'CMD_MSG' . $acplang('waiver_success') . 'CMD_REDIR' . get_page_link(48);
				//unset($_SESSION['waiver_redir']);
				die($dietxt); //display message and redirect to the proper screening form
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'register':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['reginfo'];
			$data['patient'] = $_REQUEST['reginfo_patient'];
			$errors = ACPManager::validate('event_register', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			
			$reg = (empty($_SESSION['cur_rec_id']) ? new ACPEventRegistration() : ACPManager::get_event_registration($_SESSION['cur_rec_id']));
			$reg->fname = $data['fname'];
			$reg->lname = $data['lname'];
			$reg->phone = $data['phone'];
			$reg->email = $data['email'];
			$reg->zip = $data['zip'];
			$reg->patient = $data['patient'];
			$reg->event = $_SESSION['cur_event_id'];
			$reg->send_screen_info = (empty($data['screeninfo']) ? 'No' : 'Yes');
			
			//info_choices is set in events-waiver.php if the registration is coming from a screening, and excluded from the form in this case
			if (!empty($data['info_choices']) && is_array($data['info_choices'])) {
				$reg->info_choices = 0;
				foreach ($data['info_choices'] as $v) {
					$reg->info_choices |= (int)$v;
				}
			}
			
			$fn = ($reg->id ? 'update' : 'insert');
			
			if ($reg->send_screen_info == 'Yes') {
				if ($reg->rec_type == ACPRecord::TYPE_SCREENBP) {
					ACPManager::send_bp_email($reg->email, get_option('admin_email'));
				}
				elseif ($reg->rec_type == ACPRecord::TYPE_SCREENVISION) {
					ACPManager::send_vision_email($reg->email, get_option('admin_email'));
				}
				elseif ($reg->rec_type == ACPRecord::TYPE_SCREENBMI) {
					ACPManager::send_bmi_email($reg->email, get_option('admin_email'));
				}
				/*elseif ($reg->rec_type == ACPRecord::TYPE_SCREENVITALS) {
					ACPManager::send_vitals_email($reg->email, get_option('admin_email'));
				}*/
			}
			
			if ($reg->$fn()) {
				$redir = (empty($_REQUEST['acpredir']) ? (empty($_SESSION['cur_rec_id']) ? 41 : 42) : (int)$_REQUEST['acpredir']);
				unset($_SESSION['cur_rec_id']);
				die('CMD_MSG' . $acplang('event_reg_success') . 'CMD_REDIR' . get_page_link($redir)); //display message and redirect back to main options
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'faq_reg':
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['reginfo'];
			$errors = ACPManager::validate('faq_register', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			
			$reg = new ACPEventRegistration();
			$reg->fname = $data['fname'];
			$reg->lname = $data['lname'];
			$reg->email = $data['email'];
			$reg->event = $_SESSION['cur_event_id'];
			$reg->rec_type = ACPRecord::TYPE_FAQDOCTOR;
			
			ACPManager::send_faq_email($reg->email, get_option('admin_email'));
			
			if ($reg->insert()) {
				die('CMD_MSG' . 'Thank you. You will recieve an email with this information shortly.'); //display message and redirect back to main options
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'checkout':
			if (empty($_SESSION['cur_event_id'])) {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
			
			acp_stripslashes($_REQUEST);
			$data = $_REQUEST['checkout'];
			$errors = ACPManager::validate('event_checkout', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			
			$event = ACPManager::get_event($_SESSION['cur_event_id']);
			$event->interactions = (int)$data['interactions'];
			$event->attendance = (int)$data['attendance'];
			if (!empty($data['comment'])) {
				$event->add_comment($data['comment']);
			}
			
			if ($event->update()) {
				global $current_user;
				get_currentuserinfo();
				update_user_meta($current_user->ID, 'cur_event_id', '');
				unset($_SESSION['cur_event_id']);
				die('CMD_MSG' . 'Thank you for your input.' . 'CMD_REDIR' . get_page_link(41)); //display message and redirect back to main options
			}
			else {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
		case 'event_details':
			if (empty($_SESSION['cur_event_id'])) {
				die('CMD_ERRORS' . $acplang('error_unknown'));
			}
			
			acp_stripslashes($_REQUEST);
			if (empty($_REQUEST['edetails'])) {
				die('CMD_REDIR' . get_page_link(41));
			}
			$data = $_REQUEST['edetails'];
			/*$errors = ACPManager::validate('event_details', $data);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}*/
			
			$event = ACPManager::get_event($_SESSION['cur_event_id']);
			if (is_array($data['staff'])) {
				foreach ($event->staff as $k => $v) {
					$event->staff[$k]->checked_in = (int)!empty($data['staff'][$v->id]);
				}
				
				if ($event->save_staff()) {
					die('CMD_MSG' . 'Staff updated' . 'CMD_REDIR' . get_page_link(41));
				}
				else {
					die('CMD_ERRORS' . $acplang('error_unknown'));
				}
			}
			die('CMD_REDIR' . get_page_link(41));
		default:
			break;
	}
}

?>