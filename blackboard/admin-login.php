<?php
/*
Template Name: ACP Admin Login
Page ID: 51
*/

if (is_user_logged_in()) {
	if ($is_lang_editor) {
		wp_redirect(get_page_link(4));
		exit(0);
	}
	else {
		wp_logout();
	}
}

$error = '';
if (!empty($_POST)) {
	//acp_stripslashes($_POST);
	$uinfo = array(
		'user_login' => $_POST['acpuser'],
		'user_password' => $_POST['acppass'],
		'remember' => true
	);
	$result = wp_signon($uinfo, false);
	if (is_wp_error($result)) {
		$error = $result->get_error_message();
	}
	else {
		wp_redirect(get_page_link(4));
		exit(0);
	}
}

get_header('admin-login');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<form action="<?php echo get_page_link(51); ?>" method="post">
		<div id="login">
			<input type="text" name="acpuser" placeholder="Username" />
			<input type="password" name="acppass" placeholder="Password" />
			<input type="submit" value="Login" />
			<div class="clear"></div>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>