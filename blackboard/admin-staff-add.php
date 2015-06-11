<?php
/*
Template Name: ACP Admin Staff Add
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

$errors = array();
if (empty($_REQUEST['sid'])) {
	$staff = new ACPStaff();
	$addedit = 'Add';
}
else {
	$staff = ACPManager::get_staff($_REQUEST['sid']);
	$addedit = (empty($staff) || empty($staff->id) ? 'Add' : 'Edit');
}

if (!empty($_POST)) {
	acp_stripslashes($_POST);
	$sinfo = $_POST['sinfo'];
	$staff->fname = $sinfo['fname'];
	$staff->lname = $sinfo['lname'];
	$staff->phone = $sinfo['phone'];
	if (empty($staff->fname) && empty($staff->lname)) {
		$errors[] = 'Staff must have a name';
	}
	if (empty($errors)) {
		if ($staff->id) {
			$_SESSION['admin_saved_msg'] = 'Staff member updated';
			$staff->update();
		}
		else {
			$_SESSION['admin_saved_msg'] = 'New staff member saved';
			$staff->insert();
			wp_redirect(get_page_link(15) . "?sid={$staff->staff_id}");
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
		<h1 class="title"><?php echo $addedit; ?> Staff</h1>
		
		<?php if (!empty($errors)): ?>
		<p id="form-error"><?php echo implode('<br />', $errors); ?></p>
		<?php endif; ?>
		<form action="<?php echo get_page_link(15) . "?sid={$staff->staff_id}"; ?>" method="post">
		<div class="data-form">
			<div class="field">
				<label for="fname">First Name</label>
				<input type="text" name="sinfo[fname]" id="fname" value="<?php echo $staff->fname; ?>" />
			</div>
			<div class="field">
				<label for="fname">Last Name</label>
				<input type="text" name="sinfo[lname]" id="lname" value="<?php echo $staff->lname; ?>" />
			</div>
			<div class="field">
				<label for="start">Phone</label>
				<input type="text" name="sinfo[phone]" id="phone" value="<?php echo $staff->phone; ?>" />
			</div>
			<div class="submit">
				<input type="submit" value="<?php echo $addedit; ?> Staff" />
			</div>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>