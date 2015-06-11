<?php
/*
Template Name: Events Register
Page ID: 48
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}
$reg = (empty($_SESSION['cur_rec_id']) ? new ACPEventRegistration() : ACPManager::get_event_registration($_SESSION['cur_rec_id']));

get_header();
?>
<!--main starts-->
<div id="main">
	<!--sign up starts-->
	<section class="sign-up">
		<h2 class="accessibility">Sign up section</h2>
		<div class="container">
			<p><strong>AdvantageCare Physicians</strong> offer the very best health care in your neighborhood. Sign up today.</p>
			<p id="form-msg"></p>
			<p id="error-msg"></p>
			<!--main form starts-->
			<form action="#" class="main-form ajax" novalidate>
			<input type="hidden" name="events_action" value="register" />
			<input type="hidden" name="acpredir" value="<?php echo (empty($_REQUEST['acpredir']) ? '' : $_REQUEST['acpredir']); ?>" />
				<div class="row">
					<input class="tall" placeholder="First Name" type="text" name="reginfo[fname]" value="<?php echo $reg->fname; ?>">
				</div>
				<div class="row">
					<input class="tall" placeholder="Last Name" type="text" name="reginfo[lname]" value="<?php echo $reg->lname; ?>">
				</div>
				<div class="row">
					<input class="tall" placeholder="Email Address *" type="email" name="reginfo[email]">
				</div>
				<div class="row">
					<input class="tall" placeholder="Phone Number" type="tel" name="reginfo[phone]">
				</div>
				<div class="row">
					<input class="tall" placeholder="Zip Code" type="text" name="reginfo[zip]">
				</div>
				
				<?php if ($reg->rec_id): ?>
				<div class="row" id="send-scrn-info">
					<label><input type="checkbox" name="reginfo[screeninfo]" value="1" checked="checked" />
					I would like to receive information about my screening.</label>
				</div>
				<?php endif; ?>
				
				<span class="caption">Are you currently an ACP patient?</span>
				<div class="radio-box">
					<div class="box align-left">
						<input id="positive" type="radio" name="reginfo_patient" value="1">
						<label for="positive">YES</label>
					</div>
					<div class="box align-left">
						<input id="negative" type="radio" name="reginfo_patient" value="2" checked="checked">
						<label for="negative">NO</label>
					</div>
				</div>
				
				<div class="row" id="acp-tips">
					<p>I would like to receive ACP tips related to:</p>
					<label><input type="checkbox" name="reginfo[info_choices][]" value="<?php echo ACPEventRegistration::CHOICE_DIABETES; ?>" checked="checked" /> Diabetes Management</label>
					<label><input type="checkbox" name="reginfo[info_choices][]" value="<?php echo ACPEventRegistration::CHOICE_FITNESS; ?>" /> Fitness</label><br />
					
					<label><input type="checkbox" name="reginfo[info_choices][]" value="<?php echo ACPEventRegistration::CHOICE_EATING; ?>" /> Healthy Eating</label>
					<label><input type="checkbox" name="reginfo[info_choices][]" value="<?php echo ACPEventRegistration::CHOICE_CHILD; ?>" /> Child Health</label><br />
					<label><input type="checkbox" name="reginfo[info_choices][]" value="<?php echo ACPEventRegistration::CHOICE_WOMEN; ?>" /> Women's Health Issues</label>
				</div>
				
				<div class="btn-holder text-right">
					<button class="btn" type="submit"><span>submit</span></button>
				</div>
			</form>
			<!--main form ends-->
		</div>
	</section>
	<!--sign up ends-->
</div>
<!--main ends-->

<?php
$no_wrap_bg = true;
get_footer();
?>