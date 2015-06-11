<?php
/*
Template Name: ACP Admin Language Texts
*/

if (!$is_lang_editor) {
	wp_redirect(get_page_link(51));
	exit(0);
}

$ctrl = (empty($_REQUEST['dctrl']) ? array() : $_REQUEST['dctrl']);
$ctrl['lid'] = (empty($ctrl['lid']) ? (empty($_REQUEST['langid']) ? '' : (int)$_REQUEST['langid']) : $ctrl['lid']);

$lang_id = (empty($ctrl['lid']) ? null : (int)$ctrl['lid']);
$untranslated = (isset($ctrl['trans']) ? (int)$ctrl['trans'] : ($lang_id ? 1 : 0));

global $page;
$page = (empty($ctrl['page']) ? (empty($page) ? 1 : $page) : (int)$ctrl['page']);
$amount = (empty($ctrl['amount']) ? 30 : (int)$ctrl['amount']);
$start = ($page - 1) * $amount;
$filter = (empty($ctrl['filter']) ? '' : "%{$ctrl['filter']}%");
$filter_text = (empty($ctrl['filter']) ? '' : $ctrl['filter']);
$total_found = 0;

$lobjs = ACPManager::list_lang_objects(($untranslated ? $lang_id : 0), $start, $amount, $filter, $total_found);
$langs = ACPManager::list_langs();

$maxpage = ceil($total_found / $amount);
$maxpage = ($maxpage == 0 ? 1 : $maxpage);
$next = ($page >= $maxpage ? 0 : $page + 1);
$prev = ($page <= 1 ? 0 : $page - 1);

$curlang = null;
if ($lang_id) {
	$curlang = new ACPLang($lang_id);
}

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<form action="<?php echo get_page_link(35); ?>" method="post" id="frm_controls">
	<input type="hidden" name="dctrl[lid]" id="ctrl_lid" />
	<input type="hidden" name="dctrl[trans]" id="ctrl_trans" />
	<input type="hidden" name="dctrl[amount]" id="ctrl_amount" />
	<input type="hidden" name="dctrl[page]" id="ctrl_page" />
	<input type="hidden" name="dctrl[filter]" id="ctrl_filter" />
	</form>
	<div class="container">
		<h1 class="title"><?php echo ($curlang ? $curlang->name : 'Manage'); ?><?php echo ($untranslated ? ' Untranslated' : ''); ?> Texts</h1>
		
		<div class="data-group" style="max-width: 1000px;">
		<div class="data-control-group">
			<div class="data-control">
				<?php if (!empty($langs)): ?>
				<select class="ctrl_lang">
				<option value="">Any Language</option>
				<?php foreach ($langs as $v): ?>
				<option value="<?php echo $v->id; ?>" <?php if ($lang_id == $v->id) { echo 'selected="selected"'; } ?>><?php echo $v->name; ?></option>
				<?php endforeach; ?>
				</select>
				
				<select class="ctrl_trans" <?php if (empty($lang_id)) { echo 'disabled="disabled"'; } ?>>
				<option value="0">All Texts</option>
				<option value="1" <?php if ($untranslated) { echo 'selected="selected"'; } ?>>Untranslated</option>
				</select>
				
				<input type="text" class="ctrl_filter" placeholder="Name filter" value="<?php echo $filter_text; ?>" style="width: 225px;" />
				
				<button class="btn_do_order">Show</button>
				<?php endif; ?>
			</div>
			
			<?php get_template_part('admin-ctrl-results'); ?>
			<div class="clear10"></div>
			<?php get_template_part('admin-ctrl-pages'); ?>
			<div class="clear5"></div>
		</div>
		
		<div class="clear10"></div>

		<table class="data orange">
		<tr class="header">
			<!--td style="width: 5%; max-width: 40; min-width: 20px;">&#160;</td-->
			<td style="width: 70%;">Name</td>
			<td style="width: 30%;">Code</td>
		</tr>
		<?php if (empty($lobjs)): ?>
		<tr>
			<td colspan="2" class="txtcenter">No <?php echo ($untranslated ? 'untranslated ' : ''); ?>texts found</td>
		</tr>
		<?php else: ?>
		<?php foreach ($lobjs as $v): ?>
		<tr>
			<td><a href="<?php echo get_page_link(36) . "?lid={$v->id}"; ?>"><?php echo $v->name; ?></a></td>
			<td><?php echo $v->code; ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</table>
		
		<div class="clear10"></div>
		
		<div class="data-control-group">
			<div class="clear5"></div>
			<?php get_template_part('admin-ctrl-pages'); ?>
			<div class="clear10"></div>
			
			<div class="data-control">
				<?php if (!empty($langs)): ?>
				<select class="ctrl_lang">
				<option value="">Any Language</option>
				<?php foreach ($langs as $v): ?>
				<option value="<?php echo $v->id; ?>" <?php if ($lang_id == $v->id) { echo 'selected="selected"'; } ?>><?php echo $v->name; ?></option>
				<?php endforeach; ?>
				</select>
				
				<select class="ctrl_trans" <?php if (empty($lang_id)) { echo 'disabled="disabled"'; } ?>>
				<option value="0">All Texts</option>
				<option value="1" <?php if ($untranslated) { echo 'selected="selected"'; } ?>>Untranslated</option>
				</select>
				
				<input type="text" name="ctrl[filter]" placeholder="Name filter" value="<?php echo $filter_text; ?>" style="width: 225px;" />
				
				<button class="btn_do_order">Show</button>
				<?php endif; ?>
			</div>
			
			<?php get_template_part('admin-ctrl-results'); ?>
		</div>
		</div>
	</div>
</div>
<!--main ends-->

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('select.ctrl_lang').change(function() {
		if (this.value) {
			jQuery('select.ctrl_trans').prop('disabled', false);
		}
		else {
			jQuery('select.ctrl_trans').prop('disabled', true);
		}
	});
	jQuery('button.btn_do_order, button.btn_results, button.btn_page').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var $this = jQuery(this);
		var grp = $this.closest('div.data-control-group');
		var frm = jQuery('#frm_controls');
		frm.find('#ctrl_lid').val(grp.find('select.ctrl_lang').val());
		frm.find('#ctrl_trans').val(grp.find('select.ctrl_trans').val());
		frm.find('#ctrl_amount').val(grp.find('select.ctrl_results').val());
		frm.find('#ctrl_page').val(grp.find('select.ctrl_page').val());
		frm.find('#ctrl_filter').val(grp.find('input.ctrl_filter').val());
		frm.submit();
		return false;
	});
});
</script>

<?php
get_footer('admin');
?>