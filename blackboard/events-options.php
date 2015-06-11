<?php
/*
Template Name: Events Options
Page ID: 41
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}

get_header();
?>
<!--main starts-->
<div id="main">
	<!--select section starts-->
	<section class="select-section">
		<div class="container">
			<!--h1 class="title text-right">select mode</h1-->
			<ul class="link-list alt">
				<li class="details"><a href="<?php echo get_page_link(40); ?>">event details</a></li>
				<li class="screenings"><a href="<?php echo get_page_link(42); ?>">health screenings</a></li>
				<!--li class="capture"><a href="<?php echo get_page_link(48); ?>">data capture</a></li-->
				<!--li class="registration"><a href="<?php echo get_page_link(48); ?>">event registration</a></li-->
				<li class="kiosk"><a href="<?php echo get_page_link(62); ?>">kiosk</a></li>
				<li class="check"><a href="<?php echo get_page_link(50); ?>">check out</a></li>
				<!--li class="select"><a href="<?php echo get_page_link(38); ?>">select event</a></li-->
				<!--li class="select"><a href="<?php echo get_page_link(81); ?>">logout</a></li-->
			</ul>
		</div>
	</section>
	<!--select section ends-->
</div>
<!--main ends-->

<?php
get_footer();
?>