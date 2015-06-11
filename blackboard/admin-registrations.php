<?php
/*
Template Name: ACP Admin Registrations
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

global $page;

if (empty($_REQUEST['dctrl']) || !is_array($_REQUEST['dctrl'])) {
	$ctrl = array(
		'date' => null,
		'datetype' => '',
		'orderarch' => 0,
		'page' => (empty($page) ? (empty($_REQUEST['page']) ? 1 : (int)$_REQUEST['page']) : $page),
		'amount' => 30,
		'orderby' => (empty($_REQUEST['orderby']) ? 'created' : $_REQUEST['orderby']),
		'orderdir' => (empty($_REQUEST['orderdir']) ? 'desc' : $_REQUEST['orderdir']),
		'ordercat' => '',
		'action' => '',
		'event_id' => ''
	);
}
else {
	acp_stripslashes($_REQUEST);
	$ctrl = $_REQUEST['dctrl'];
	$ctrl['page'] = (empty($page) ? (empty($ctrl['page']) ? 1 : $ctrl['page']) : $page);
}


if (!empty($ctrl['action']) && !empty($ctrl['ids'])) {
	$num = count($ctrl['ids']);
	$s = ($num == 1 ? '' : 's');
	switch ($ctrl['action']) {
		case 'do_archive':
			$_SESSION['admin_saved_msg'] = "{$num} registration{$s} archived";
			ACPManager::reg_archive($ctrl['ids']);
			break;
		case 'do_unarchive':
			$_SESSION['admin_saved_msg'] = "{$num} registration{$s} unarchived";
			ACPManager::reg_archive($ctrl['ids'], 0);
			break;
		case 'do_delete':
			$_SESSION['admin_saved_msg'] = "{$num} registration{$s} deleted";
			ACPManager::event_reg_multi_delete($ctrl['ids']);
			break;
		default : break;
	}
}

$start_date = null;
$end_date = null;
$date = $ctrl['date'];
$datetype = $ctrl['datetype'];
$archived = ((int)$ctrl['orderarch'] == -1 ? null : (int)$ctrl['orderarch']);
$page = (int)$ctrl['page'];
$page = ($page < 1 ? 1 : $page);
$amount = (int)$ctrl['amount'];
$start = ($page - 1) * $amount;
$orderby = $ctrl['orderby'];
$orderdir = $ctrl['orderdir'];
$ordercat = $ctrl['ordercat'];
$event_id = (int)$ctrl['event_id'];
$event_dir = 'asc';
$created_dir = 'asc';
$dirvar = '';
switch ($orderby) {
	case 'event':
		$dirvar = 'event_dir';
		$orderby = 'e.`event_id`';
		break;
	case 'created':
	default:
		$dirvar = 'created_dir';
		$orderby = 'o.`obj_created`';
		break;
}
switch ($orderdir) {
	case 'asc':
		$$dirvar = 'desc';
		$orderdir = 'ASC';
		break;
	case 'desc':
	default:
		$orderdir = 'DESC';
		break;
}

$page_id = 8;
$total_found = 0;
if ($datetype == 'on') {
	$regs = ACPManager::list_event_regs_on_day($event_id, $date, $archived, $amount, $start, $orderby, $orderdir, $ordercat, $total_found);
}
else {
	$start_date = ($datetype == 'ona' ? $date : null);
	$end_date = ($datetype == 'onb' ? $date : null);
	$regs = ACPManager::list_event_regs($event_id, $start_date, $end_date, $archived, $amount, $start, $orderby, $orderdir, $ordercat, $total_found);
}
$maxpage = ceil($total_found / $amount);

$maxpage = ($maxpage == 0 ? 1 : $maxpage);
$next = ($page >= $maxpage ? 0 : $page + 1);
$prev = ($page <= 1 ? 0 : $page - 1);

$cats = ACPManager::list_cats(ACPManager::OBJ_RECORD);
$events = ACPManager::list_events();

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<?php get_template_part('admin-form-message'); ?>
	<form action="<?php echo get_page_link(8); ?>" method="post" id="frm_controls">
	<input type="hidden" name="dctrl[amount]" />
	<input type="hidden" name="dctrl[date]" />
	<input type="hidden" name="dctrl[datetype]" />
	<input type="hidden" name="dctrl[orderarch]" />
	<input type="hidden" name="dctrl[orderby]" />
	<input type="hidden" name="dctrl[orderdir]" />
	<input type="hidden" name="dctrl[ordercat]" />
	<input type="hidden" name="dctrl[page]" />
	<input type="hidden" name="dctrl[action]" />
	<input type="hidden" name="dctrl[ids]" />
	<input type="hidden" name="dctrl[event_id]" />
	</form>
	<div class="container">
		
		<div class="data-group" style="max-width: 1300px;">
		<!-- begin controls -->
		<div class="data-control-group">
			<?php get_template_part('admin-ctrl-actions'); ?>
			<div class="data-control">
				<!--span>Event</span-->
				<select class="ctrl_datetype">
				<option value="on">On</option>
				<option value="ona">On/After</option>
				<option value="onb">On/Before</option>
				</select>
				<input type="text" class="ctrl_date" value="<?php echo $ctrl['date']; ?>" placeholder="Date" />
				<select class="ctrl_orderarch">
				<option value="0" <?php if ($ctrl['orderarch'] == 0) { echo 'selected="selected"'; } ?>>Not archived</option>
				<option value="1" <?php if ($ctrl['orderarch'] == 1) { echo 'selected="selected"'; } ?>>Archived</option>
				<option value="-1" <?php if ($ctrl['orderarch'] == -1) { echo 'selected="selected"'; } ?>>Either</option>
				</select>
				<select class="ctrl_orderby">
				<option value="">Order By</option>
				<option value="date" <?php if ($ctrl['orderby'] == 'date') { echo 'selected="selected"'; } ?>>Date</option>
				<option value="event" <?php if ($ctrl['orderby'] == 'event') { echo 'selected="selected"'; } ?>>Event</option>
				</select>
				<select class="ctrl_orderdir">
				<option value="">Order Type</option>
				<option value="asc" <?php if ($ctrl['orderdir'] == 'asc') { echo 'selected="selected"'; } ?>>Ascending</option>
				<option value="desc" <?php if ($ctrl['orderdir'] == 'desc') { echo 'selected="selected"'; } ?>>Descending</option>
				</select>
				
				<?php if (!empty($events)): ?>
				<select class="ctrl_event">
				<option value="">Any Event</option>
				<?php foreach ($events as $v): ?>
				<option value="<?php echo $v->event_id; ?>" <?php if ($ctrl['event_id'] == $v->event_id) { echo 'selected="selected"'; } ?>><?php echo $v->name; ?></option>
				<?php endforeach; ?>
				</select>
				<?php endif; ?>
				
				<?php get_template_part('admin-ctrl-cats'); ?>
				
				<button class="btn_do_order">Sort</button>
			</div>
			<?php get_template_part('admin-ctrl-results'); ?>
			<div class="clear10"></div>
			<?php get_template_part('admin-ctrl-pages'); ?>
			<div class="clear5"></div>
		</div>
		<!-- end controls -->
		
		<!-- begin data table -->
		<table class="data orange">
		<tr class="header">
			<td style="width: 5%; max-width: 40; min-width: 20px;" class="txtcenter"><input type="checkbox" id="checkall" /></td>
			<td style="width: 30%;">Name</td>
			<td style="width: 25%;">Email</td>
			<td style="width: 20%;">
			<a class="sortable" href="<?php echo get_page_link(8) . "?orderby=event&orderdir={$event_dir}"; ?>">Event</a>
			</td>
			<td style="width: 5%;">Archived</td>
			<td style="width: 20%;">
			<a class="sortable" href="<?php echo get_page_link(8) . "?orderby=created&orderdir={$created_dir}"; ?>">Date</a>
			</td>
		</tr>
		<?php if (empty($regs)): ?>
		<tr>
			<td colspan="6" class="txtcenter">No registrations found</td>
		</tr>
		<?php else: ?>
		<?php foreach ($regs as $v): ?>
		<tr>
			<td class="txtcenter"><input type="checkbox" name="acpchecks[]" value="<?php echo $v->rec_id; ?>" /></td>
			<td><a href="<?php echo get_page_link(30) . "?rid={$v->rec_id}"; ?>"><?php echo (empty($v->fname) && empty($v->lname) ? '(No name)' : $v->full_name);; ?></a></td>
			<td><?php echo $v->email; ?></td>
			<td><?php echo $v->event->name; ?></td>
			<td><?php echo $v->archived; ?></td>
			<td><?php echo $v->created_format('M j, Y h:ia'); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</table>
		<!-- end data table -->
		
		<!-- begin controls -->
		<div class="data-control-group">
			<div class="clear5"></div>
			<?php get_template_part('admin-ctrl-pages'); ?>
			<div class="clear10"></div>
			<?php get_template_part('admin-ctrl-actions'); ?>
			<div class="data-control">
				<!--span>Event</span-->
				<select class="ctrl_datetype">
				<option value="on">On</option>
				<option value="ona">On/After</option>
				<option value="onb">On/Before</option>
				</select>
				<input type="text" class="ctrl_date" value="<?php echo $ctrl['date']; ?>" placeholder="Date" />
				<select class="ctrl_orderarch">
				<option value="0" <?php if ($ctrl['orderarch'] == 0) { echo 'selected="selected"'; } ?>>Not archived</option>
				<option value="1" <?php if ($ctrl['orderarch'] == 1) { echo 'selected="selected"'; } ?>>Archived</option>
				<option value="-1" <?php if ($ctrl['orderarch'] == -1) { echo 'selected="selected"'; } ?>>Either</option>
				</select>
				<select class="ctrl_orderby">
				<option value="">Order By</option>
				<option value="date" <?php if ($ctrl['orderby'] == 'date') { echo 'selected="selected"'; } ?>>Date</option>
				<option value="event" <?php if ($ctrl['orderby'] == 'event') { echo 'selected="selected"'; } ?>>Event</option>
				</select>
				<select class="ctrl_orderdir">
				<option value="">Order Type</option>
				<option value="asc" <?php if ($ctrl['orderdir'] == 'asc') { echo 'selected="selected"'; } ?>>Ascending</option>
				<option value="desc" <?php if ($ctrl['orderdir'] == 'desc') { echo 'selected="selected"'; } ?>>Descending</option>
				</select>
				
				<?php if (!empty($events)): ?>
				<select class="ctrl_event">
				<option value="">Any Event</option>
				<?php foreach ($events as $v): ?>
				<option value="<?php echo $v->event_id; ?>" <?php if ($ctrl['event_id'] == $v->event_id) { echo 'selected="selected"'; } ?>><?php echo $v->name; ?></option>
				<?php endforeach; ?>
				</select>
				<?php endif; ?>
				
				<?php get_template_part('admin-ctrl-cats'); ?>
				
				<button class="btn_do_order">Sort</button>
			</div>
			<?php get_template_part('admin-ctrl-results'); ?>
		</div>
		<!-- end controls -->
		</div>
	</div>
</div>
<!--main ends-->

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('#checkall').click(function() {
		jQuery('input[name=acpchecks\\[\\]]').prop('checked', jQuery(this).prop('checked'));
	});
	jQuery('button.btn_do_action').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var $this = jQuery(this);
		var grp = $this.closest('div.data-control-group');
		var frm = jQuery('#frm_controls');
		var checks = jQuery('input[name=acpchecks\\[\\]]:checked');
		if (checks.length) {
			checks.each(function() {
				frm.append('<input type="hidden" name="dctrl[ids][]" value="' + this.value + '" />');
			});
			frm.find('input[name=dctrl\\[action\\]]').val(grp.find('.ctrl_action').val());
			grp.find('.btn_do_order').trigger('click');
			//frm.submit();
		}
		return false;
	});
	jQuery('button.btn_do_order, button.btn_results, button.btn_page').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var $this = jQuery(this);
		var grp = $this.closest('div.data-control-group');
		var frm = jQuery('#frm_controls');
		var cats = grp.find('.ctrl_ordercat');
		var events = grp.find('.ctrl_event');
		frm.find('input[name=dctrl\\[amount\\]]').val(grp.find('.ctrl_results').val());
		frm.find('input[name=dctrl\\[page\\]]').val(grp.find('.ctrl_page').val());
		frm.find('input[name=dctrl\\[date\\]]').val(grp.find('.ctrl_date').val());
		frm.find('input[name=dctrl\\[datetype\\]]').val(grp.find('.ctrl_datetype').val());
		frm.find('input[name=dctrl\\[orderarch\\]]').val(grp.find('.ctrl_orderarch').val());
		frm.find('input[name=dctrl\\[orderby\\]]').val(grp.find('.ctrl_orderby').val());
		frm.find('input[name=dctrl\\[orderdir\\]]').val(grp.find('.ctrl_orderdir').val());
		if (cats.length) {
			frm.find('input[name=dctrl\\[ordercat\\]]').val(cats.val());
		}
		if (events.length) {
			frm.find('input[name=dctrl\\[event_id\\]]').val(events.val());
		}
		frm.submit();
		return false;
	});
	jQuery('.ctrl_date').datetimepicker({
		format: 'Y-m-d',
		timepicker: false
	});
});
</script>

<?php
get_footer('admin');
?>