<?php
/*
Template Name: ACP Admin Specialty Add Modal
*/

if (!$is_admin) {
	wp_redirect(site_url('/'));
	exit(0);
}

$sids = (empty($_REQUEST['sids']) || !is_array($_REQUEST['sids']) ? (empty($_SESSION['admin_spec_ids']) ? array() : $_SESSION['admin_spec_ids']) : $_REQUEST['sids']);
if (!empty($sids)) {
	$_SESSION['admin_spec_ids'] = $sids;
}

$specs = ACPManager::list_specialties();

?>

<div id="wrapper_spec_manager" class="wrapper_list_mgr">
	<div id="spec_tabs" class="list_tabs">
		<a href="#tab_spec_list" class="current">List Specialties</a>
		<a href="#tab_spec_add">Add New Specialty</a>
		<div class="clear"></div>
	</div>
	
	<div id="tab_spec_list" class="list_tab">
		<?php if (empty($specs)): ?>
		<p>No specialties have been added yet.</p>
		<?php else: ?>
		<h2>Includes Specialites <span>(click to remove)</span></h2>
		<ul id="inc_spec">
		<?php if (count($specs)): ?>
		<?php
		foreach ($specs as $k => $v) {
			$key = !empty($sids[$v->id]);
			$style = (!$key ? 'style="display: none;"' : '');
			echo "<li class=\"pos{$k}\" data-id=\"{$v->id}\" data-name=\"{$v->name}\" {$style}>";
			if ($key) {
				echo "<span>{$v->name}</span>";
			}
			echo '</li>';
		}
		?>
		<?php endif; ?>
		</ul>
		
		<h2>Available Specialites <span>(click to include)</span></h2>
		<ul id="avail_spec">
		<?php if (count($specs)): ?>
		<?php
		foreach ($specs as $k => $v) {
			$key = !empty($sids[$v->id]);
			$style = ($key ? 'style="display: none;"' : '');
			echo "<li class=\"pos{$k}\" data-id=\"{$v->id}\" data-name=\"{$v->name}\" {$style}>";
			if (empty($key)) {
				echo "<span>{$v->name}</span>";
			}
			echo '</li>';
		}
		?>
		<?php endif; ?>
		</ul>
		<?php endif; ?>
	</div>
	
	<div id="tab_spec_add" class="list_tab" style="display: none;">
		<p id="frm_spec_message" class="list_frm_msg"></p>
		<p id="frm_spec_error" class="list_frm_error"></p>
		<form id="frm_add_spec" action="" method="post">
		<input type="hidden" name="admin_action" value="add_specialty" />
		<div class="data-form">
			<div class="field">
				<label for="name">Specialty Name</label>
				<input type="text" name="sinfo[name]" id="name" value="" />
			</div>
			<div class="submit">
				<input type="submit" value="Add Specialty" />
			</div>
		</div>
		</form>
	</div>
</div>