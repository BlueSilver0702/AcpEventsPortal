<?php
/*
Template Name: ACP Admin Staff
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

global $page;

if (empty($_REQUEST['dctrl']) || !is_array($_REQUEST['dctrl'])) {
	$ctrl = array(
		'orderarch' => 0,
		'page' => (empty($page) ? (empty($_REQUEST['page']) ? 1 : (int)$_REQUEST['page']) : $page),
		'amount' => 30,
		'orderby' => (empty($_REQUEST['orderby']) ? 'created' : $_REQUEST['orderby']),
		'orderdir' => (empty($_REQUEST['orderdir']) ? 'desc' : $_REQUEST['orderdir']),
		'ordercat' => '',
		'action' => ''
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
			$_SESSION['admin_saved_msg'] = "{$num} staff member{$s} archived";
			ACPManager::staff_archive($ctrl['ids']);
			break;
		case 'do_unarchive':
			$_SESSION['admin_saved_msg'] = "{$num} staff member{$s} unarchived";
			ACPManager::staff_archive($ctrl['ids'], 0);
			break;
		case 'do_delete':
			$_SESSION['admin_saved_msg'] = "{$num} staff member{$s} deleted";
			ACPManager::staff_multi_delete($ctrl['ids']);
			break;
		default : break;
	}
}
$archived = ((int)$ctrl['orderarch'] == -1 ? null : (int)$ctrl['orderarch']);
$page = (int)$ctrl['page'];
$page = ($page < 1 ? 1 : $page);
$amount = (int)$ctrl['amount'];
$start = ($page - 1) * $amount;
$orderby = $ctrl['orderby'];
$orderdir = $ctrl['orderdir'];
$ordercat = $ctrl['ordercat'];
$fname_dir = 'asc';
$lname_dir = 'asc';
$created_dir = 'asc';
switch ($orderby) {
	case 'fname':
		$dirvar = 'fname_dir';
		$orderby = 's.`staff_fname`';
		break;
	case 'lname':
		$dirvar = 'lname_dir';
		$orderby = 's.`staff_lname`';
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

$page_id = 7;
$total_found = 0;
$staff = ACPManager::list_staff($archived, $amount, $start, $orderby, $orderdir, $ordercat, $total_found);
$maxpage = ceil($total_found / $amount);

$maxpage = ($maxpage == 0 ? 1 : $maxpage);
$next = ($page >= $maxpage ? 0 : $page + 1);
$prev = ($page <= 1 ? 0 : $page - 1);

$cats = ACPManager::list_cats(ACPManager::OBJ_STAFF);

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<?php get_template_part('admin-form-message'); ?>
	<form action="<?php echo get_page_link(7); ?>" method="post" id="frm_controls">
	<input type="hidden" name="dctrl[amount]" />
	<input type="hidden" name="dctrl[orderarch]" />
	<input type="hidden" name="dctrl[orderby]" />
	<input type="hidden" name="dctrl[orderdir]" />
	<input type="hidden" name="dctrl[ordercat]" />
	<input type="hidden" name="dctrl[page]" />
	<input type="hidden" name="dctrl[action]" />
	<input type="hidden" name="dctrl[ids]" />
	</form>
	<div class="container">
		
		<div class="data-group" style="max-width: 1150px;">
		<!-- begin controls -->
		<div class="data-control-group">
			<?php get_template_part('admin-ctrl-actions'); ?>
			<div class="data-control">
				<select class="ctrl_orderarch">
				<option value="0" <?php if ($ctrl['orderarch'] == 0) { echo 'selected="selected"'; } ?>>Not archived</option>
				<option value="1" <?php if ($ctrl['orderarch'] == 1) { echo 'selected="selected"'; } ?>>Archived</option>
				<option value="-1" <?php if ($ctrl['orderarch'] == -1) { echo 'selected="selected"'; } ?>>Either</option>
				</select>
				<select class="ctrl_orderby">
				<option value="">Order By</option>
				<option value="name" <?php if ($ctrl['orderby'] == 'name') { echo 'selected="selected"'; } ?>>Name</option>
				<option value="event" <?php if ($ctrl['orderby'] == 'event') { echo 'selected="selected"'; } ?>>Event Date</option>
				<option value="date" <?php if ($ctrl['orderby'] == 'date') { echo 'selected="selected"'; } ?>>Date Created</option>
				</select>
				<select class="ctrl_orderdir">
				<option value="">Order Type</option>
				<option value="asc" <?php if ($ctrl['orderdir'] == 'asc') { echo 'selected="selected"'; } ?>>Ascending</option>
				<option value="desc" <?php if ($ctrl['orderdir'] == 'desc') { echo 'selected="selected"'; } ?>>Descending</option>
				</select>
				
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
			<td style="width: 5%;" class="txtcenter"><input type="checkbox" id="checkall" /></td>
			<td style="width: 26%;">
			<a class="sortable" href="<?php echo get_page_link(7) . "?orderby=fname&orderdir={$fname_dir}"; ?>">First Name</a>
			</td>
			<td style="width: 26%;">
			<a class="sortable" href="<?php echo get_page_link(7) . "?orderby=lname&orderdir={$lname_dir}"; ?>">Last Name</a>
			</td>
			<td style="width: 20%;">Phone</td>
			<td style="width: 7%;">Archived</td>
			<td style="width: 21%;">
			<a class="sortable" href="<?php echo get_page_link(7) . "?orderby=created&orderdir={$created_dir}"; ?>">Created</a>
			</td>
		</tr>
		<?php if (empty($staff)): ?>
		<tr>
			<td colspan="6" style="text-align: center;">No staff found</td>
		</tr>
		<?php else: ?>
		<?php foreach ($staff as $v): ?>
		<tr>
			<td class="txtcenter"><input type="checkbox" name="acpchecks[]" value="<?php echo $v->staff_id; ?>" /></td>
			<td><a href="<?php echo get_page_link(15) . "?sid={$v->staff_id}"; ?>"><?php echo $v->fname; ?></a></td>
			<td><?php echo $v->lname; ?></td>
			<td><?php echo $v->phone; ?></td>
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
				<select class="ctrl_orderarch">
				<option value="0" <?php if ($ctrl['orderarch'] == 0) { echo 'selected="selected"'; } ?>>Not archived</option>
				<option value="1" <?php if ($ctrl['orderarch'] == 1) { echo 'selected="selected"'; } ?>>Archived</option>
				<option value="-1" <?php if ($ctrl['orderarch'] == -1) { echo 'selected="selected"'; } ?>>Either</option>
				</select>
				<select class="ctrl_orderby">
				<option value="">Order By</option>
				<option value="name" <?php if ($ctrl['orderby'] == 'name') { echo 'selected="selected"'; } ?>>Name</option>
				<option value="event" <?php if ($ctrl['orderby'] == 'event') { echo 'selected="selected"'; } ?>>Event Date</option>
				<option value="date" <?php if ($ctrl['orderby'] == 'date') { echo 'selected="selected"'; } ?>>Date Created</option>
				</select>
				<select class="ctrl_orderdir">
				<option value="">Order Type</option>
				<option value="asc" <?php if ($ctrl['orderdir'] == 'asc') { echo 'selected="selected"'; } ?>>Ascending</option>
				<option value="desc" <?php if ($ctrl['orderdir'] == 'desc') { echo 'selected="selected"'; } ?>>Descending</option>
				</select>
				
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
		frm.find('input[name=dctrl\\[amount\\]]').val(grp.find('.ctrl_results').val());
		frm.find('input[name=dctrl\\[page\\]]').val(grp.find('.ctrl_page').val());
		frm.find('input[name=dctrl\\[orderarch\\]]').val(grp.find('.ctrl_orderarch').val());
		frm.find('input[name=dctrl\\[orderby\\]]').val(grp.find('.ctrl_orderby').val());
		frm.find('input[name=dctrl\\[orderdir\\]]').val(grp.find('.ctrl_orderdir').val());
		if (cats.length) {
			frm.find('input[name=dctrl\\[ordercat\\]]').val(cats.val());
		}
		frm.submit();
		return false;
	});
});
</script>

<?php
get_footer('admin');
?>