<?php

wp_redirect(get_page_link(37));
exit(0);

get_header();
?>

<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title"><span class="align-right">Check in</span></h1>
		<!--feedback form starts-->
		<form action="#" class="feedback-form" novalidate>
			<fieldset>
				<div class="box">
					<div class="row">
						<input class="align-right small" id="attendance" type="text">
						<label class="align-right" for="attendance">estimated attendance</label>
					</div>
					<div class="row">
						<dl class="number align-right">
							<dt>number of screenings</dt>
							<dd class="text-right">52</dd>
						</dl>
					</div>
					<div class="row">
						<input class="align-right small" id="interactions" type="text">
						<label class="align-right" for="interactions">estimated field interactions</label>
					</div>
				</div>
				<div class="box inputs-list">
					<label for="comment">Enter 3 summary comments about today's event below:</label>
					<div class="row has-border">
						<input id="comment" type="text">
					</div>
					<div class="row has-border">
						<input type="text">
					</div>
					<div class="row">
						<input type="text">
					</div>
				</div>
			</fieldset>
			<div class="btn-holder text-right">
				<button class="btn" type="submit"><span>submit</span></button>
			</div>
		</form>
		<!--feedback form ends-->
	</div>
</div>
<!--main ends-->

<?php
get_footer();
?>