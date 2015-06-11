<?php
/*
Template Name: Events Details
Page ID: 40
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}

$event = ACPManager::get_event($_SESSION['cur_event_id']);
$msg = false;
$redir = false;
$error = false;

if (!empty($_POST)) {
	acp_stripslashes($_POST);
	
	if (empty($_POST['edetails']) && empty($_FILES['edetails_img'])) {
		wp_redirect(get_page_link(41));
		exit(0);
	}
	$data = $_POST['edetails'];
	
	if (!empty($data['staff'])) {
		foreach ($event->staff as $k => $v) {
			$event->staff[$k]->checked_in = (int)!empty($data['staff'][$v->id]);
		}
	}
	
	if (!empty($_FILES['edetails_img'])) {
		if (! function_exists( 'wp_handle_upload' )) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');
		}
		$fdata = wp_handle_upload($_FILES['edetails_img'], array('test_form' => false));
		if ($fdata && !empty($fdata['url'])) {
			$event->img = $fdata['url'];
		}
	}
	
	if ($event->update()) {
		$msg = 'Event updated';
		$redir = get_page_link(41);
	}
	else {
		$error = $acplang('error_unknown');
	}
}
elseif (!empty($_GET['delimg'])) {
	$event->img = '';
	$event->update();
	wp_redirect(get_page_link(40));
	exit(0);
}

$managers = $event->get_staff_by_role('Field Manager');
$nurses = $event->get_staff_by_role('Field Nurse');
$staff = $event->get_staff_by_role('Field Staff');
$volunteers = $event->get_staff_by_role('ACP Volunteer');

get_header('details');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<div class="heading-box text-right">
			<h2><span>check in</span></h2>
			<h3><span>details</span></h3>
		</div>
		<?php if ($msg): ?>
		<p id="form-msg" style="color: #FFFFFF;"><?php echo $msg; ?></p>
		<?php else: ?>
		
		<?php if ($error): ?>
		<p id="error-msg" style="color: #CC0029;"></p>
		<?php endif; ?>
		<!--details form starts-->
		<form action="<?php echo get_page_link(40); ?>" novalidate class="details-form" method="post" enctype="multipart/form-data">
		<input type="hidden" name="events_action" value="event_details" />
			<header class="event-heading">
				<h4 class="align-left"><?php echo $event->name; ?></h4>
				<span class="date align-right text-right"><strong><?php echo $event->start_format('F d'); ?>,</strong> <?php echo $event->start_format('Y'); ?></span>
			</header>
			<?php if (!empty($managers)): ?>
			<fieldset>
				<header class="list-heading">
					<h5 class="align-left">Field Manager</h5>
					<strong class="note align-right">check in</strong>
				</header>
				<ul class="list">
					<?php foreach ($managers as $k => $v): ?>
					<li>
						<label for="check-<?php echo $k; ?>"><?php echo $v->staff->full_name; ?></label>
						<input class="align-right" id="check-<?php echo $k; ?>" name="edetails[staff][<?php echo $v->id; ?>]" value="1" type="checkbox" <?php if ($v->checked_in) { echo 'checked="checked"'; } ?> />
					</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
			<?php endif; ?>
			<?php if (!empty($nurses)): ?>
			<fieldset>
				<header class="list-heading">
					<h5 class="align-left">Nurse Staff</h5>
					<strong class="note align-right">check in</strong>
				</header>
				<ul class="list">
					<?php foreach ($nurses as $k => $v): ?>
					<li>
						<label for="check-<?php echo $k; ?>"><?php echo $v->staff->full_name; ?></label>
						<input class="align-right" id="check-<?php echo $k; ?>" name="edetails[staff][<?php echo $v->id; ?>]" value="1" type="checkbox" <?php if ($v->checked_in) { echo 'checked="checked"'; } ?> />
					</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
			<?php endif; ?>
			<?php if (!empty($staff)): ?>
			<fieldset>
				<header class="list-heading">
					<h5 class="align-left">Field Staff</h5>
					<strong class="note align-right">check in</strong>
				</header>
				<ul class="list">
					<?php foreach ($staff as $k => $v): ?>
					<li>
						<label for="check-<?php echo $k; ?>"><?php echo $v->staff->full_name; ?></label>
						<input class="align-right" id="check-<?php echo $k; ?>" name="edetails[staff][<?php echo $v->id; ?>]" value="1" type="checkbox" <?php if ($v->checked_in) { echo 'checked="checked"'; } ?> />
					</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
			<?php endif; ?>
			<?php if (!empty($volunteers)): ?>
			<fieldset>
				<header class="list-heading">
					<h5 class="align-left">ACP Volunteer</h5>
					<strong class="note align-right">check in</strong>
				</header>
				<ul class="list">
					<?php foreach ($volunteers as $k => $v): ?>
					<li>
						<label for="check-<?php echo $k; ?>"><?php echo $v->staff->full_name; ?></label>
						<input class="align-right" id="check-<?php echo $k; ?>" name="edetails[staff][<?php echo $v->id; ?>]" value="1" type="checkbox" <?php if ($v->checked_in) { echo 'checked="checked"'; } ?> />
					</li>
					<?php endforeach; ?>
				</ul>
			</fieldset>
			<?php endif; ?>
			
			<div class="upload-box">
				<?php if (empty($event->img)): ?>
				<input id="file-input" name="edetails_img" type="file" accept="image/*;capture=camera" capture="camera">
				<label for="file-input"><span>click here</span>to upload a photo</label>
				<?php else: ?>
				<img src="<?php echo $event->img; ?>" height="150" />
				<a href="<?php echo get_page_link(40); ?>?delimg=1">Delete</a>
				<?php endif; ?>
			</div>
			<div class="text-info">
				<h5>Event Overview</h5>
				<dl>
					<dt>contact</dt>
					<dd><?php echo $event->contact->full_name; ?> / <?php echo $event->contact->phone; ?></dd>
					<dt>date</dt>
					<dd><?php echo $event->start_format('F d, Y'); ?></dd>
					<dt>set-up time</dt>
					<dd><?php echo $event->start_format('g:ia'); ?></dd>
					
					<?php if ($event->end): ?>
					<dt>pick-up time</dt>
					<dd><?php echo $event->end_format('g:ia'); ?></dd>
					<?php endif; ?>
					
					<?php if ($event->time): ?>
					<dt>event time</dt>
					<dd><?php echo $event->time; ?></dd>
					<?php endif; ?>
					
					<dt>staff hours</dt>
					<dd class="time-box"><?php echo $event->hours; ?><!--span>11<sup>AM</sup></span><span> - 6<sup>PM</sup></span--></dd>
					
					
					<dt>set</dt>
					<dd><?php echo $event->set; ?></dd>
					<dt>address</dt>
					<dd><?php echo $event->address; ?></dd>
					
					<dt>uniform</dt>
					<dd><?php echo $event->uniform; ?></dd>
					<dt>screenings</dt>
					<dd><?php echo $event->screen_types_text; ?></dd>
					
					<?php if ($event->staff_notes): ?>
					<dt>notes</dt>
					<dd><?php echo $event->staff_notes; ?></dd>
					<?php endif; ?>
				</dl>
			</div>
			<div class="btn-holder text-right">
				<button type="submit"><span class="text-right">go to</span> main options</button>
			</div>
		</form>
		<!--details form ends-->
		<?php endif; ?>
	</div>
</div>
<!--main ends-->

<?php if ($redir): ?>
<script type="text/javascript">
jQuery(document).ready(function() {
	window.setTimeout(function() {
		location.href = '<?php echo $redir; ?>';
	}, 2000);
});
</script>
<?php endif; ?>

<?php
get_footer('details');
?>