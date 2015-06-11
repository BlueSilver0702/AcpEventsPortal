<?php
/*
Template Name: Events Waiver
Page ID: 47
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}
elseif (empty($_REQUEST['scrtype'])) {
	wp_redirect(get_page_link(42));
	exit(0);
}


if (empty($_SESSION['cur_rec_id'])) {
	$reg = new ACPEventRegistration();
	$fn = 'insert';
}
else {
	$reg = ACPManager::get_event_registration($_SESSION['cur_rec_id']);
	if ($reg->fname) {
		$reg = new ACPEventRegistration();
		$fn = 'insert';
	}
	else {
		$fn = 'update';
	}
}

switch ($_REQUEST['scrtype']) {
	case 'bmi':
		$reg->rec_type = ACPRecord::TYPE_SCREENBMI;
		//$reg->info_choices = ACPEventRegistration::CHOICE_BMI;
		//$scr = new ACPScreeningBMI();
		//$_SESSION['waiver_redir'] = get_page_link(43);
		break;
	case 'vision':
		$reg->rec_type = ACPRecord::TYPE_SCREENVISION;
		//$reg->info_choices = ACPEventRegistration::CHOICE_VISION;
		//$scr = new ACPScreeningVision();
		//$_SESSION['waiver_redir'] = get_page_link(45);
		break;
	case 'bp':
		$reg->rec_type = ACPRecord::TYPE_SCREENBP;
		//$reg->info_choices = ACPEventRegistration::CHOICE_BP;
		//$scr = new ACPScreeningBP();
		//$_SESSION['waiver_redir'] = get_page_link(44);
		break;
	case 'vitals':
		$reg->rec_type = ACPRecord::TYPE_SCREENVITALS;
		//$reg->info_choices = ACPEventRegistration::CHOICE_VITALS;
		//$scr = new ACPScreeningVitals();
		//$_SESSION['waiver_redir'] = get_page_link(46);
		break;
	default:
		wp_redirect(get_page_link(42));
		exit(0);
}

$reg->event = $_SESSION['cur_event_id'];
if ($reg->$fn()) {
	$_SESSION['cur_rec_id'] = $reg->rec_id;
}
else {
	wp_redirect(get_page_link(42));
}

$no_wrap_bg = true;
get_header();
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<p id="form-msg"></p>
		<form class="agreement-form ajax" action="#" novalidate>
		<input type="hidden" name="events_action" value="waiver" />
		<input type="hidden" name="wvrinfo[scrtype]" value="<?php echo $_REQUEST['scrtype']; ?>" />
			<h2><?php echo $acplang('waiver_title'); ?></h2>
			<p><?php echo $acplang('waiver_p1'); ?></p>
			<p><?php echo $acplang('waiver_p2'); ?></p>
			<p><?php echo $acplang('waiver_p3'); ?></p>
			<p><?php echo $acplang('waiver_p4'); ?></p>
			<p><?php echo $acplang('waiver_p5'); ?></p>
			<p><?php echo $acplang('waiver_p6'); ?></p>
			<fieldset>
				<p id="error-msg"></p>
				<div class="checkbox-holder">
					<label for="agreement-checkbox">
					<input type="checkbox" name="wvrinfo[agree]" value="1" id="agreement-checkbox" />
					<?php echo $acplang('waiver_agree'); ?>
					</label>
				</div>
				<div class="input-holder">
					<div class="box align-left">
						<input type="text" name="wvrinfo[fname]" placeholder="<Patient First Name>">
					</div>
					<div class="box align-left">
						<input type="text" name="wvrinfo[lname]" placeholder="<Patient Last Name>">
					</div>
				</div>
			</fieldset>
			<div class="btn-holder">
				<button class="btn" type="submit"><span>submit</span></button>
			</div>
		</form>
	</div>
</div>
<!--main ends-->

<?php
get_footer();
?>