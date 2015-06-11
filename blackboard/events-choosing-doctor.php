<?php
/*
Template Name: Events Choosing Doctor
Page ID: 75
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}

get_header('faq');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<!--faq starts-->
		<section class="faq">
			<p><?php echo $acplang('choose_doctor_intro'); ?></p>
			<h2><?php echo $acplang('choose_doctor_top_questions'); ?></h2>
			<ul class="list">
				<li>
					<a href="#" class="opener">
						<span class="number">1</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q1'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a1'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">2</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q2'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a2'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">3</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q3'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a3'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">4</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q4'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a4'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">5</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q5'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a5'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">6</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q6'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a6'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">7</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q7'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a7'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">8</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q8'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a8'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">9</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q9'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a9'); ?></p>
							</div>
						</div>
					</a>
				</li>
				<li>
					<a href="#" class="opener">
						<span class="number">10</span>
						<div class="text">
							<h3><?php echo $acplang('choose_doctor_q10'); ?></h3>
							<div class="slide">
								<p><?php echo $acplang('choose_doctor_a10'); ?></p>
							</div>
						</div>
					</a>
				</li>
			</ul>
			<div class="btn-holder text-center" style="padding-bottom:25px"><a class="btn-mail open-lightbox" data-href="#email-form"" href="#"><?php echo $acplang('choose_doctor_email_me'); ?></a></div>
			<p><?php echo $acplang('choose_doctor_more_info'); ?></p>

		</section>
		<!--faq ends-->
	</div>
</div>
<!--main ends-->

<div id="email-form" class="lightbox">
<p id="form-msg"></p>
<p id="error-msg"></p>
<!--main form starts-->
<form action="#" class="main-form ajax" novalidate>
<input type="hidden" name="events_action" value="faq_reg" />
	<div class="row">
		<input class="tall" placeholder="First Name" type="text" name="reginfo[fname]" value="">
	</div>
	<div class="row">
		<input class="tall" placeholder="Last Name" type="text" name="reginfo[lname]" value="">
	</div>
	<div class="row">
		<input class="tall" placeholder="Email Address" type="email" name="reginfo[email]">
	</div>
	<div class="btn-holder text-right">
		<button class="btn nograd" type="submit"><span>submit</span></button>
	</div>
</form>
</div>
<a href="#" class="close-lightbox">Close</a>

<?php
get_footer('faq');
?>