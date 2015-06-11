<?php
/*
Template Name: Events Select
Page ID: 38
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

//get events
//$events = ACPManager::list_events(time() - 86400, null, 0, 0, 0, 'e.`event_name`', 'ASC');
$events = ACPManager::list_events(time() - 86400 * 30, null, 0, 0, 0, 'e.`event_name`', 'ASC');

get_header();
?>
<!--main starts-->
<div id="main">
	<!--select section starts-->
	<section class="select-section select-event">
		<div class="container">
			<h1 class="title text-right">event portal</h1>
			<p id="form-msg"></p>
			<p id="error-msg"></p>
			<form action="#" class="main-form ajax" novalidate>
			<input type="hidden" name="events_action" value="sel_event" />
				<div class="select-box">
					<select class="section-select" data-placeholder="<?php echo $acplang('select_event'); ?>" name="event_id">
						<?php foreach ($events as $v): ?>
						<option value="<?php echo $v->event_id; ?>"><?php echo $v->name; ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="btn-holder text-right">
					<button class="btn" type="submit"><span>submit</span></button>
				</div>
			</form>
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