<?php
/*
Template Name: Events Check In
Page ID: 37
*/

if ($is_coordinator) {
	wp_redirect(get_page_link(38));
	exit(0);
}

get_header();
?>
<!--main starts-->
<div id="main">
	<!--select section starts-->
	<section class="select-section check-in">
		<div class="container">
			<h1 class="title text-right">event portal</h1>
			<p id="form-msg"></p>
			<p id="error-msg"></p>
			<!--main form starts-->
			<form action="#" class="main-form ajax" novalidate>
			<input type="hidden" name="events_action" value="login" />
				<div class="row">
					<input class="tall" placeholder="Username" type="text" name="loginfo[user]" />
				</div>
				<div class="row">
					<input class="tall" placeholder="Password" type="password" name="loginfo[pass]" />
				</div>
				<div class="btn-holder text-right">
					<button class="btn" type="submit"><span>login</span></button>
				</div>
			</form>
			<!--main form ends-->
		</div>
	</section>
	<!--select section ends-->
</div>
<!--main ends-->

<?php
get_footer();
?>