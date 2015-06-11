<?php
/*
Template Name: ACP Admin Categories
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
		<h1 class="title">Categories</h1>
		
		
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>