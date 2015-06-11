<?php
/*
Template Name: ACP Admin Errors
*/

if (!$is_developer) {
	wp_redirect(get_page_link(51));
	exit(0);
}

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">Errors</h1>
		
		
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>