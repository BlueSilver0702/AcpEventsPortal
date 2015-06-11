<?php
/*
Template Name: Events Check Out
Page ID: 50
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}

$event = ACPManager::get_event($_SESSION['cur_event_id']);
if (empty($event) || empty($event->event_id)) {
	wp_redirect(get_page_link(38));
	exit(0);
}

get_header();
?>
<!--main starts-->
<div id="main">
	<!--check out starts-->
	<section class="check-out">
		<div class="container">
			<h1 class="title text-right">check out</h1>
			<!--main form starts-->
			<p id="form-msg"></p>
			<p id="error-msg"></p>
			<form action="#" class="main-form ajax" novalidate>
			<input type="hidden" name="events_action" value="checkout" />
				<fieldset>
					<div class="box">
						<div class="row">
							<input class="align-right small" id="attendance" type="text" name="checkout[attendance]">
							<label class="align-right" for="attendance">estimated attendance</label>
						</div>
						<div class="row">
							<dl class="number align-right">
								<dt>number of screenings</dt>
								<dd class="text-right"><?php echo $event->total_screenings; ?></dd>
							</dl>
						</div>
						<div class="row">
							<input class="align-right small" id="interactions" type="text" name="checkout[interactions]">
							<label class="align-right" for="interactions">estimated field interactions</label>
						</div>
					</div>
					<div class="box inputs-list">
						<label for="comment">Enter a summary comment about today's event below:</label>
						<div class="row has-border">
							<textarea name="checkout[comment]" style="width: 100%; height: 220px; border: 1px solid #E0E0E0;"></textarea>
						</div>
						<!--div class="row has-border">
							<input type="text">
						</div>
						<div class="row">
							<input type="text">
						</div-->
					</div>
				</fieldset>
				<div class="btn-holder text-right">
					<button class="btn" type="submit"><span>submit</span></button>
				</div>
			</form>
			<!--main form ends-->
		</div>
	</section>
	<!--check out ends-->
</div>
<!--main ends-->

<?php
get_footer();
?>