<?php
/*
Template Name: ACP Admin Category Add
*/

if (!$is_admin) {
	wp_redirect(site_url('/'));
	exit(0);
}

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">Add Category</h1>
		
		
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>