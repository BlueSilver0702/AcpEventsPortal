<?php
/*
Template Name: ACP Admin Office Add
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

$errors = array();

if (empty($_REQUEST['oid'])) {
	$office = new ACPOffice();
	$addedit = 'Add';
}
else {
	$office = ACPManager::get_office($_GET['oid']);
	if (empty($office) || empty($office->id)) {
		$addedit = 'Add';
	}
	else {
		$addedit = 'Edit';
	}
}

if (!empty($_POST)) {
	acp_stripslashes($_POST);
	$oinfo = $_POST['oinfo'];
	$office->name = $oinfo['name'];
	$office->phone = $oinfo['phone'];
	$office->street = $oinfo['street'];
	$office->city = $oinfo['city'];
	$office->state = $oinfo['state'];
	$office->zip = $oinfo['zip'];
	$office->county = $oinfo['county'];
	$office->hours = $oinfo['hours'];
	$office->after_hours = $oinfo['after_hours'];
	$office->urgent_care = $oinfo['urgent_care'];
	if (empty($office->name)) {
		$errors[] = 'Office must have a name';
	}
	if (empty($office->zip)) {
		$errors[] = 'Office must have a zip code';
	}
	if (empty($errors)) {
		if (!empty($oinfo['specs']) && is_array($oinfo['specs'])) {
			foreach ($oinfo['specs'] as $v) {
				$office->add_specialty($v);
			}
		}
		if (!empty($oinfo['del']) && is_array($oinfo['del'])) {
			foreach ($oinfo['del'] as $v) {
				$office->remove_specialty($v);
			}
		}
		
		$latlng = ACPManager::address_to_latlng($office->address);
		if ($latlng) {
			$office->lat = $latlng[0];
			$office->lng = $latlng[1];
		}
		
		if ($office->id) {
			$_SESSION['admin_saved_msg'] = 'Office updated';
			if ($office->update()) {
				ACPManager::save_office_xml(get_template_directory() . '/', 'locations.xml');
			}
		}
		else {
			$_SESSION['admin_saved_msg'] = 'New office saved';
			if ($office->insert()) {
				ACPManager::save_office_xml(get_template_directory() . '/', 'locations.xml');
			}
			wp_redirect(get_page_link(56) . "?oid={$office->office_id}");
			exit(0);
		}
		$addedit = 'Edit';
	}
}

unset($_SESSION['admin_spec_ids']);
$specs = ACPManager::list_specialties();

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<?php get_template_part('admin-form-message'); ?>
	<div class="container">
		<h1 class="title"><?php echo $addedit; ?> Office</h1>
		
		<?php if (!empty($errors)): ?>
		<p id="form-error"><?php echo implode('<br />', $errors); ?></p>
		<?php endif; ?>
		<form id="frm_office_add" action="<?php echo get_page_link(56) . "?oid={$office->office_id}"; ?>" method="post">
		<div class="data-form">
			<div class="field">
				<label for="name">Name</label>
				<input type="text" name="oinfo[name]" id="name" value="<?php echo $office->name; ?>" />
			</div>
			<div class="field">
				<label for="phone">Phone</label>
				<input type="text" name="oinfo[phone]" id="phone" value="<?php echo $office->phone; ?>" />
			</div>
			<div class="field">
				<label>Location</label>
				<input type="text" class="addr_street" name="oinfo[street]" value="<?php echo $office->street; ?>" placeholder="Address" />
				<input type="text" class="addr_city" name="oinfo[city]" value="<?php echo $office->city; ?>" placeholder="City" />
				<input type="text" class="addr_county" name="oinfo[county]" value="<?php echo $office->county; ?>" placeholder="County" />
				<input type="text" class="addr_state" name="oinfo[state]" value="<?php echo $office->state; ?>" placeholder="State" maxlength="2" />
				<input type="text" class="addr_zip" name="oinfo[zip]" value="<?php echo $office->zip; ?>" placeholder="Zip Code" />
			</div>
			<div class="field">
				<label for="uniform">Hours</label>
				<textarea name="oinfo[hours]" id="hours" class="medium"><?php echo $office->hours; ?></textarea>
			</div>
			<div class="field">
				<label for="after_hours">After Hours Care</label>
				<textarea name="oinfo[after_hours]" id="after_hours" class="medium"><?php echo $office->after_hours; ?></textarea>
			</div>
			<div class="field">
				<label for="urgent_care">Urgent Care</label>
				<select name="oinfo[urgent_care]" id="urgent_care">
				<option value="TBD">TBD</option>
				<option value="Yes" <?php if ($office->urgent_care == 'Yes') { echo 'selected="selected"'; } ?>>Yes</option>
				<option value="No" <?php if ($office->urgent_care == 'No') { echo 'selected="selected"'; } ?>>No</option>
				</select>
			</div>
			<div class="field">
				<label>Specialties</label>
				<div class="clear10"></div>
				
				<?php if (!empty($specs)): ?>
				<input type="text" class="subtext" id="spec_search" placeholder="Find speciality" />
				<input type="hidden" id="find_spec_id" />
				<input type="hidden" id="find_spec_name" />
				<button id="add_spec" style="margin-right: 25px;">Add</button>
				<?php endif; ?>
				
				<a href="<?php echo get_page_link(59); ?>" data-orig="<?php echo get_page_link(59); ?>" id="manage_specs" class="lnkButton" data-fancybox-type="ajax">Manage Specialties</a>
				<div class="clear10"></div>
				
				<div style="width: 350px; max-height: 400px; overflow: auto;">
				<table id="spec_list" class="data grey" style="max-width: 300px; margin-top: 20px;">
				<!--tr class="header">
					<td>Current Specialties</td>
					<td style="width: 15%;">Remove</td>
				</tr-->
				<?php if (count($office->specialties)): ?>
				<?php foreach ($office->specialties as $v): ?>
				<tr>
					<td>
					<input type="hidden" name="oinfo[specs][]" class="oinfoadd" value="<?php echo $v->id; ?>" />
					<?php echo $v->name; ?>
					</td>
					<td style="text-align: center;"><button data-sid="<?php echo $v->id; ?>" class="btn_spec">Del</button></td>
				</tr>
				<?php endforeach; ?>
				<?php endif; ?>
				</table>
				</div>
			</div>
			<div class="submit">
				<input type="submit" value="<?php echo $addedit; ?> Office" />
			</div>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<script type="text/javascript">
var reqSent = false;
var specRowTpl = '<tr><td><input type="hidden" name="oinfo[specs][]" value="{spec_id}" />{name}</td>';
specRowTpl += '<td style="text-align: center;"><button class="btn_spec">Del</button>';

function addSpec(sid, name) {
	var frm = jQuery('#frm_office_add');
	if (frm.find('input[name=oinfo\\[specs\\]\\[\\]][value=' + sid + ']').length == 0) {
		jQuery('#spec_list').append(specRowTpl.replace(/\{spec_id\}/g, sid).replace(/\{name\}/g, name));
		frm.find('input[name=oinfo\\[del\\]\\[\\]][value=' + sid + ']').remove();
	}
	updateManageLink();
}
function removeSpec(sid) {
	var frm = jQuery('#frm_office_add');
	var inp = frm.find('input[name=oinfo\\[specs\\]\\[\\]][value=' + sid + ']');
	if (inp.length != 0) {
		frm.append('<input type="hidden" class="oinfodel" name="oinfo[del][]" value="' + sid + '" />');
		inp.closest('tr').remove();
	}
	updateManageLink();
}
function handleNewSpec(data) {
	if (data.match('CMD_HTML') && data.match('CMD_MSG')) {
		var htmlpos = data.indexOf('CMD_HTML');
		var msgpos = data.indexOf('CMD_MSG');
		if (htmlpos < msgpos) {
			var html = data.substring(htmlpos + 8, msgpos);
			var msg = data.substr(msgpos + 7);
		}
		else {
			var msg = data.substring(msgpos + 7, htmlpos);
			var html = data.substr(htmlpos + 8);
		}
		jQuery('#frm_add_spec').remove();
		jQuery('#frm_spec_message').text(msg);
		jQuery('#frm_spec_message').show();
		window.setTimeout(function() {
			jQuery('#wrapper_spec_manager').empty().append(html);
		}, 1700);
	}
	else if (data.match('CMD_ERRORS')) {
		jQuery('#frm_spec_error').text(data.substr(10));
		jQuery('#frm_spec_error').show();
	}
	reqSent = false;
}
function getCurSpecIds() {
	var sids = '?';
	jQuery('#spec_list input[name=oinfo\\[specs\\]\\[\\]]').each(function() {
		sids += encodeURIComponent('sids[' + jQuery(this).val() + ']') + '=1&';
	});
	return sids;
}
function updateManageLink() {
	jQuery('#manage_specs').attr('href', jQuery('#manage_specs').attr('data-orig') + getCurSpecIds());
}
jQuery(document).ready(function() {
	updateManageLink();
	
	jQuery('#manage_specs').fancybox({
		autoSize: false,
		width: 700
	});
	
	jQuery('#add_spec').click(function(e) {
		e.preventDefault();
		e.stopPropagation();
		var specId = jQuery('#find_spec_id').val();
		var specName = jQuery('#find_spec_name').val();
		if (specId && specName) {
			addSpec(specId, specName);
			jQuery('#spec_search').val('');
		}
		return false;
	});
	jQuery(document).on('click', 'button.btn_spec', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var $this = jQuery(this);
		var sid = $this.attr('data-sid');
		
		removeSpec(sid);
		
		$this.closest('tr').remove();
		return false;
	});
	jQuery(document).on('click', '#avail_spec li', function() {
		var $this = jQuery(this);
		var pos = $this.attr('class');
		var contents = $this.contents();
		jQuery('#inc_spec li.' + pos).show().append(contents);
		$this.hide().empty();
		addSpec($this.attr('data-id'), $this.attr('data-name'));
	});
	jQuery(document).on('click', '#inc_spec li', function() {
		var $this = jQuery(this);
		var pos = $this.attr('class');
		var contents = $this.contents();
		jQuery('#avail_spec li.' + pos).show().append(contents);
		$this.hide().empty();
		removeSpec($this.attr('data-id'));
	});
	jQuery(document).on('submit', '#frm_add_spec', function(e) {
		e.preventDefault();
		e.stopPropagation();
		
		if (reqSent) {
			return false;
		}
		reqSent = true;
		jQuery('#frm_spec_message').hide();
		jQuery('#frm_spec_error').hide();
		jQuery('#frm_spec_message').empty();
		jQuery('#frm_spec_error').empty();
		
		var data = jQuery(this).serialize();
		
		jQuery.ajax({
			type: 'POST',
			url: '<?php echo get_page_link(60); ?>' + getCurSpecIds(),
			data: data,
			success: handleNewSpec,
			error: function() { reqSent = false; }
		});
		return false;
	});
	jQuery(document).on('click', 'div.list_tabs a', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var $this = jQuery(this);
		if ($this.hasClass('current')) {
			return false;
		}
		var prev = $this.parent().find('a.current');
		prev.removeClass('current');
		$this.addClass('current');
		jQuery(prev.attr('href')).hide();
		jQuery($this.attr('href')).show()
		
		return false;
	});

	<?php if (!empty($specs)): $specend = count($specs) - 1; ?>
	var specAutocomplete = [
	<?php foreach ($specs as $k => $v): ?>
	{ value: '<?php echo addslashes($v->name); ?>', id: '<?php echo $v->id; ?>'}<?php if ($k != $specend) { echo ','; } ?>

	<?php endforeach; ?>
	];
	
	jQuery('#spec_search').autocomplete({
		source: specAutocomplete,
		select: function(e, ui) {
			jQuery('#find_spec_id').val(ui.item.id);
			jQuery('#find_spec_name').val(ui.item.value);
			jQuery('#spec_search').val(ui.item.value);
			return false;
		},
		_renderItem: function(ul, item) {
			return jQuery('<li>').append('<a>' + item.value + '</a>').appendTo(ul);
		}
	});
	<?php endif; ?>
});
</script>

<?php
get_footer('admin');
?>