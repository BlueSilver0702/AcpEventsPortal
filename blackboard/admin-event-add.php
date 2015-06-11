<?php
/*
Template Name: ACP Admin Event Add
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

$errors = array();

if (empty($_REQUEST['eid'])) {
	$event = new ACPEvent();
	$addedit = 'Add';
}
else {
	$event = ACPManager::get_event($_GET['eid']);
	if (empty($event) || empty($event->id)) {
		$addedit = 'Add';
	}
	else {
		$event->load_staff();
		$addedit = 'Edit';
	}
}

if (!empty($_POST)) {
	acp_stripslashes($_POST);
	$einfo = $_POST['einfo'];
	$event->name = $einfo['name'];
	$event->contact = $einfo['contact'];
	$event->start = $einfo['start'];
	$event->end = $einfo['end'];
	$event->set = ($einfo['set'] == 'Custom' ? $einfo['set_custom'] : $einfo['set']);
	$event->street = $einfo['street'];
	$event->city = $einfo['city'];
	$event->state = $einfo['state'];
	$event->zip = $einfo['zip'];
	$event->uniform = $einfo['uniform'];
	$event->hours = $einfo['hours'];
	$event->staff_notes = $einfo['notes'];
	$event->time = $einfo['time'];
	//$event->screen_types = $einfo['screen_types'];
	
	if (!empty($einfo['attendance'])) {
		$event->attendance = $einfo['attendance'];
	}
	if (!empty($einfo['interactions'])) {
		$event->interactions = $einfo['interactions'];
	}
	if (!empty($einfo['comment'])) {
		$event->add_comment($einfo['comment']);
	}
	
	if (empty($event->name)) {
		$errors[] = 'Event must have a name';
	}
	if (empty($event->contact->id)) {
		$errors[] = 'Event must have a staff contact';
	}
	if (empty($event->start)) {
		$errors[] = 'You must provide a set-up date';
	}
	if (empty($errors)) {
		if (!empty($einfo['screen_types']) && is_array($einfo['screen_types'])) {
			$event->screen_types = 0;
			foreach ($einfo['screen_types'] as $v) {
				$event->screen_types |= (int)$v;
			}
		}
		if (!empty($einfo['del']) && is_array($einfo['del'])) {
			foreach ($einfo['del'] as $v) {
				$event->remove_staff($v);
			}
		}
		if (!empty($einfo['staff']) && is_array($einfo['staff'])) {
			foreach ($einfo['staff'] as $k => $v) {
				$event->add_staff($v, $einfo['roles'][$k]);
			}
		}
		
		if ($event->id) {
			$_SESSION['admin_saved_msg'] = 'Sponsored event updated';
			$event->update();
		}
		else {
			$_SESSION['admin_saved_msg'] = 'New sponsored event saved';
			$event->insert();
			wp_redirect(get_page_link(14) . "?eid={$event->event_id}");
			exit(0);
		}
		$addedit = 'Edit';
	}
}

$tiers = array(
	'Tier 1' => 1, 'Tier 2' => 1, 'Tier 3' => 1
);
$is_custom_set = strlen($event->set) && !isset($tiers[$event->set]);

$staff = ACPManager::list_staff();

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<?php get_template_part('admin-form-message'); ?>
	<div class="container">
		<h1 class="title"><?php echo $addedit; ?> Event</h1>
		
		<?php if (!empty($errors)): ?>
		<p id="form-error"><?php echo implode('<br />', $errors); ?></p>
		<?php endif; ?>
		<form id="frm_event_add" action="<?php echo get_page_link(14) . "?eid={$event->event_id}"; ?>" method="post">
		<div class="data-form">
			<div class="field">
				<label for="name">Name</label>
				<input type="text" name="einfo[name]" id="name" value="<?php echo $event->name; ?>" />
			</div>
			
			<?php if (!empty($event->attendance)): ?>
			<div class="field">
				<label for="attendance">Estimated Attendance</label>
				<input type="text" name="einfo[attendance]" id="attendance" value="<?php echo $event->attendance; ?>" />
			</div>
			<?php endif; ?>
			
			<?php if (!empty($event->interactions)): ?>
			<div class="field">
				<label for="interactions">Estimated Interactions</label>
				<input type="text" name="einfo[interactions]" id="interactions" value="<?php echo $event->interactions; ?>" />
			</div>
			<?php endif; ?>
			
			<?php if (count($event->comments)): ?>
			<div class="field">
				<label for="comment">Comment</label>
				<textarea name="einfo[comment]" id="comment" class="small"><?php echo $event->comments[0]; ?></textarea>
			</div>
			<?php endif; ?>
			
			<div class="field">
				<label for="contact">Staff Contact <span>(Name and phone will be displayed with event info)</span></label>
				<select name="einfo[contact]" id="contact">
				<?php if (!empty($staff)): ?>
				<?php foreach ($staff as $v): ?>
				<option value="<?php echo $v->staff_id; ?>" <?php if (!empty($event->contact) && ($v->staff_id == $event->contact->staff_id)) { echo 'selected="selected"'; } ?>><?php echo "{$v->fname} {$v->lname}"; ?></option>
				<?php endforeach; ?>
				<?php endif; ?>
				</select>
			</div>
			<div class="field">
				<label for="start">Set-up Time</label>
				<input type="text" name="einfo[start]" id="start" value="<?php echo $event->start_format('Y-m-d H:i:s'); ?>" />
			</div>
			<div class="field">
				<label for="end">Pick-up Time</label>
				<input type="text" name="einfo[end]" id="end" value="<?php echo $event->end_format('Y-m-d H:i:s'); ?>" />
			</div>
			<div class="field">
				<label for="time">Event Time</label>
				<input type="text" name="einfo[time]" id="time" value="<?php echo $event->time; ?>" />
			</div>
			<div class="field">
				<label for="set">Set</label>
				<select name="einfo[set]" id="set">
				<option value="Tier 1" <?php if ($event->set == 'Tier 1') { echo 'selected="selected"'; } ?>>Tier 1</option>
				<option value="Tier 2" <?php if ($event->set == 'Tier 2') { echo 'selected="selected"'; } ?>>Tier 2</option>
				<option value="Tier 3" <?php if ($event->set == 'Tier 3') { echo 'selected="selected"'; } ?>>Tier 3</option>
				<option value="Custom" <?php if ($is_custom_set) { echo 'selected="selected"'; } ?>>Custom</option>
				</select>
				<input <?php if (!$is_custom_set) { echo 'disabled="disabled"'; } ?> type="text" name="einfo[set_custom]" id="set_custom" class="subtext" style="margin-left: 10px;" value="<?php if ($is_custom_set) { echo $event->set; } ?>" />
			</div>
			<div class="field">
				<label>Location</label>
				<input type="text" class="addr_street" name="einfo[street]" value="<?php echo $event->street; ?>" placeholder="Address" />
				<input type="text" class="addr_city" name="einfo[city]" value="<?php echo $event->city; ?>" placeholder="City" />
				<input type="text" class="addr_state" name="einfo[state]" value="<?php echo $event->state; ?>" placeholder="State" maxlength="2" />
				<input type="text" class="addr_zip" name="einfo[zip]" value="<?php echo $event->zip; ?>" placeholder="Zip Code" />
			</div>
			<div class="field">
				<label for="uniform">Uniform</label>
				<textarea name="einfo[uniform]" id="uniform" class="small"><?php echo $event->uniform; ?></textarea>
			</div>
			<div class="field">
				<label for="hours">Staff Hours</label>
				<textarea name="einfo[hours]" id="hours" class="small"><?php echo $event->hours; ?></textarea>
			</div>
			<div class="field">
				<label for="notes">Staff Notes</label>
				<textarea name="einfo[notes]" id="notes" class="small"><?php echo $event->staff_notes; ?></textarea>
			</div>
			<div class="field">
				<label for="screen_types">Screen Types</label>
				<!--textarea name="einfo[screen_types]" id="screen_types" class="small"><?php echo $event->screen_types; ?></textarea-->
				<input type="checkbox" name="einfo[screen_types][]" id="vision" value="1" <?php if ($event->screen_types & ACPEvent::SCREEN_VISION) { echo 'checked="checked"'; } ?> />
				<label class="sublabel" for="vision">Vision</label>
				<input type="checkbox" name="einfo[screen_types][]" id="bmi" value="2" <?php if ($event->screen_types & ACPEvent::SCREEN_BMI) { echo 'checked="checked"'; } ?> />
				<label class="sublabel" for="bmi">BMI</label>
				<input type="checkbox" name="einfo[screen_types][]" id="vitals" value="4" <?php if ($event->screen_types & ACPEvent::SCREEN_VITALS) { echo 'checked="checked"'; } ?> />
				<label class="sublabel" for="vitals">Vitals</label>
				<input type="checkbox" name="einfo[screen_types][]" id="bp" value="8" <?php if ($event->screen_types & ACPEvent::SCREEN_BP) { echo 'checked="checked"'; } ?> />
				<label class="sublabel" for="bp">BP</label>
			</div>
			<div class="field">
				<label>Staff</label>
				<select id="sel_staff" style="margin-right: 15px;">
				<option value="0">Select staff</option>
				<?php foreach ($staff as $v): ?>
				<option value="<?php echo $v->staff_id; ?>"><?php echo "{$v->fname} {$v->lname}"; ?></option>
				<?php endforeach; ?>
				</select>
				<select id="sel_role" style="margin-right: 15px;">
				<option value="0">Select role</option>
				<option value="Field Staff">Field Staff</option>
				<option value="Field Nurse">Field Nurse</option>
				<option value="Field Manager">Field Manager</option>
				<option value="ACP Volunteer">ACP Volunteer</option>
				</select>
				<button id="add_staff">Add</button>
				<table id="staff_list" class="data grey" style="max-width: 480px; margin-top: 20px;">
				<tr class="header">
					<td style="width: 50%;">Name</td>
					<td style="width: 35%;">Role</td>
					<td style="width: 15%;">Remove</td>
				</tr>
				<?php if (count($event->staff)): ?>
				<?php foreach ($event->staff as $v): ?>
				<tr>
					<td>
					<input type="hidden" name="einfo[staff][]" value="<?php echo $v->staff->staff_id; ?>" />
					<input type="hidden" name="einfo[roles][]" value="<?php echo $v->role; ?>" />
					<?php echo "{$v->staff->fname} {$v->staff->lname}"; ?>
					</td>
					<td><?php echo $v->role; ?></td>
					<td style="text-align: center;"><button data-sid="<?php echo $v->staff->staff_id; ?>" class="btn_staff">Del</button></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</table>
			</div>
			<div class="submit">
				<input type="submit" value="<?php echo $addedit; ?> Event" />
			</div>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<script type="text/javascript">
jQuery(document).ready(function() {
	var staffRowTpl = '<tr><td><input type="hidden" name="einfo[staff][]" value="{staff_id}" /><input type="hidden" name="einfo[roles][]" value="{role}" />{name}</td>';
	staffRowTpl += '<td>{role}</td><td style="text-align: center;"><button class="btn_staff">Del</button>';
	jQuery('#start').datetimepicker({
		format: 'Y-m-d H:i:00',
		formatTime: 'h:ia',
		step: '30'
	});
	jQuery('#end').datetimepicker({
		format: 'Y-m-d H:i:s',
		formatTime: 'h:ia',
		step: '30'
	});
	jQuery('#add_staff').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var staffId = jQuery('#sel_staff').val();
		var staffName = jQuery('#sel_staff option:selected').text();
		var staffRole = jQuery('#sel_role').val();
		if (staffId && staffRole) {
			jQuery('#staff_list').append(staffRowTpl.replace(/\{staff_id\}/g, staffId).replace(/\{role\}/g, staffRole).replace(/\{name\}/g, staffName));
		}
		return false;
	});
	jQuery('#set').change(function() {
		jQuery('#set_custom').prop('disabled', this.value != 'Custom');
	});
	jQuery(document).on('click', 'button.btn_staff', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var $this = jQuery(this);
		var sid = $this.attr('data-sid');
		var frm = jQuery('#frm_event_add');
		
		if (frm.find('input.einfodel[value=' + sid + ']').length == 0) {
			frm.append('<input type="hidden" class="einfodel" name="einfo[del][]" value="' + sid + '" />');
		}
		
		$this.closest('tr').remove();
		return false;
	});
	
});
</script>

<?php
get_footer('admin');
?>