<?php
/*
Template Name: ACP Admin Ajax Handler
*/

if (!empty($_REQUEST['admin_action'])) {
	acp_stripslashes($_REQUEST);
	$act = $_REQUEST['admin_action'];
	//global $acplang;
	switch ($act) {
		case 'add_specialty':
			$sinfo = $_REQUEST['sinfo'];
			$errors = ACPManager::validate('add_specialty', $sinfo);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			
			$spec = new ACPSpecialty($sinfo['name']);
			if ($spec->id) {
				die('CMD_ERRORS' . 'A specialty with that name already exists');
			}
			$spec->name = $sinfo['name'];
			if ($spec->insert()) {
				echo 'CMD_MSG' . 'Specialty has been added' . 'CMD_HTML';
				include get_template_directory() . '/admin-specialty-add-modal.php';
				exit(0);
			}
			else {
				die('CMD_ERRORS' . 'An unknown error occurred.');
			}
		case 'add_staff':
			$sinfo = $_REQUEST['sinfo'];
			$errors = ACPManager::validate('add_staff', $sinfo);
			
			if (!empty($errors)) {
				die('CMD_ERRORS' . implode('<br />', $errors));
			}
			
			$staff = new ACPStaff();
			$staff->fname = $sinfo['fname'];
			$staff->lname = $sinfo['lname'];
			$staff->phone = $sinfo['phone'];
			if ($staff->insert()) {
				echo 'CMD_MSG' . 'Staff has been added' . 'CMD_HTML';
				include get_template_directory() . '/admin-staff-add-modal.php';
				exit(0);
			}
			else {
				die('CMD_ERRORS' . 'An unknown error occurred.');
			}
		default:
			break;
	}
}

?>