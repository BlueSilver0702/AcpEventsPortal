<?php
/*
Template Name: Events Screening Options
Page ID: 42
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}

if (!empty($_SESSION['cur_rec_id'])) {
	$chk = ACPManager::get_event_registration($_SESSION['cur_rec_id']);
	if ($chk->fname) {
		unset($_SESSION['cur_rec_id']);
	}
	unset($chk);
}

$event = ACPManager::get_event($_SESSION['cur_event_id']);

get_header();
?>
<!--main starts-->
<div id="main">
	<!--select section starts-->
	<section class="select-section">
		<div class="container">
			<h1 class="title text-right">select screening</h1>
			<ul class="link-list">
				<?php if ($event->screen_types & ACPEvent::SCREEN_BP): ?>
				<li class="pressure"><a href="<?php echo get_page_link(47) . '?scrtype=bp'; ?>">Blood Pressure</a></li>
				<?php endif; ?>
				
				<?php if ($event->screen_types & ACPEvent::SCREEN_VISION): ?>
				<li class="vision"><a href="<?php echo get_page_link(47) . '?scrtype=vision'; ?>">Vision</a></li>
				<?php endif; ?>
				
				<?php if ($event->screen_types & ACPEvent::SCREEN_VITALS): ?>
				<li class="vitals"><a href="<?php echo get_page_link(47) . '?scrtype=vitals'; ?>">Vitals</a></li>
				<?php endif; ?>
				
				<?php if ($event->screen_types & ACPEvent::SCREEN_BMI): ?>
				<li class="bmi"><a href="<?php echo get_page_link(47) . '?scrtype=bmi'; ?>">BMI</a></li>
				<?php endif; ?>
			</ul>
		</div>
	</section>
	<!--select section ends-->
</div>
<!--main ends-->

<script type="text/javascript">
jQuery(document).ready(function() {
	
});
</script>

<?php
get_footer();
?>