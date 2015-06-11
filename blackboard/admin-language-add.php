<?php
/*
Template Name: ACP Admin Language Add
*/

if (!$is_lang_editor) {
	wp_redirect(get_page_link(51));
	exit(0);
}

if (empty($_REQUEST['lid'])) {
	$lang = new ACPLang();
	$addedit = 'Add';
}
else {
	$lang = new ACPLang($_REQUEST['lid']);
	$addedit = (empty($lang) || empty($lang->id) ? 'Add' : 'Edit');
}

if (!empty($_POST)) {
	acp_stripslashes($_POST);
	$linfo = $_POST['linfo'];
	$lang->name = $linfo['name'];
	$lang->code = $linfo['code'];
	$lang->is_default = $linfo['default'];
	$lang->enabled = $linfo['enabled'];
	
	if (empty($lang->name)) {
		$errors[] = 'Language must have a name';
	}
	/*if (empty($lang->code)) {
		$errors[] = 'Language must have a code';
	}*/
	if (empty($errors)) {
		if ($lang->id) {
			$_SESSION['admin_saved_msg'] = 'Language updated';
			$lang->update();
		}
		else {
			$_SESSION['admin_saved_msg'] = 'New language saved';
			$lang->insert();
			wp_redirect(get_page_link(23) . "?lid={$lang->id}");
			exit(0);
		}
		$addedit = 'Edit';
	}
}

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<?php get_template_part('admin-form-message'); ?>
	<div class="container">
		<h1 class="title"><?php echo $addedit; ?> Language</h1>
		
		<?php if (!empty($errors)): ?>
		<p id="form-error"><?php echo implode('<br />', $errors); ?></p>
		<?php endif; ?>
		<form action="<?php echo get_page_link(23) . "?lid={$lang->id}"; ?>" method="post">
		<div class="data-form">
			<div class="field">
				<label for="name">Name</label>
				<input type="text" name="linfo[name]" id="name" value="<?php echo $lang->name; ?>" />
			</div>
			<div class="field">
				<label for="code">Code</label>
				<input type="text" name="linfo[code]" id="code" value="<?php echo $lang->code; ?>" />
			</div>
			<div class="field">
				<input type="checkbox" name="linfo[default]" id="default" value="1" <?php if ($lang->is_default == 'Yes') { echo 'checked="checked"'; } ?> /><label class="inline-label" for="default">Is Default</label>
				<input type="checkbox" name="linfo[enabled]" id="enabled" value="1" <?php if ($lang->enabled == 'Yes') { echo 'checked="checked"'; } ?> /><label class="inline-label" for="enabled">Enabled</label>
			</div>
			<div class="submit">
				<input type="submit" value="<?php echo $addedit; ?> Language" />
			</div>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>