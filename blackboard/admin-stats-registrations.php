<?php
/*
Template Name: ACP Admin Statistics (Registrations)
Page ID: 85
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

$events = ACPManager::list_events(null, null, null, 0, 0, 'e.`event_name`', 'ASC');

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">Registration Statistics</h1>
		
		<div class="stats-form">
		<form name="frm-stats" method="post" action="<?php echo get_page_link(87); ?>">
			<h2>Filters</h2>
			<div class="text">
				<label for="event">Event:</label>
				<select name="statinfo[filters][event]" id="event">
				<!--option value="">All Events</option-->
				<?php foreach ($events as $v): ?>
				<option value="<?php echo $v->event_id; ?>"><?php echo $v->name; ?></option>
				<?php endforeach; ?>
				</select>
			</div>
			<!--div class="text">
				<label for="datestart">Date:</label>
				<select name="statinfo[filters][datedir]" id="datedir">
				<option value="on">On</option>
				<option value="after">On/After</option>
				<option value="before">On/Before</option>
				<option value="between">Between</option>
				</select>
				<input type="text" name="statinfo[filters][datestart]" id="datestart" value="" />
				<input type="text" disabled="disabled" name="statinfo[filters][dateend]" id="dateend" value="" />
			</div>
			<div class="checkboxes">
				<h3>Type</h3>
				<label>
					<input type="checkbox" name="statinfo[filters][type][bmi]" value="1" /> BMI Screening
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][type][vision]" value="1" /> Vision Screening
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][type][bp]" value="1" /> BP Screening
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][type][kiosk]" value="1" /> Kiosk Registration
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][type][faq]" value="1" /> Physician FAQ Email
				</label>
				<div class="clear"></div>
			</div>
			<div class="checkboxes">
				<h3>ACP Tips</h3>
				<label>
					<input type="checkbox" name="statinfo[filters][tips][diabetes]" value="1" /> Diabetes Management
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][tips][fitness]" value="1" /> Fitness
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][tips][eating]" value="1" /> Healthy Eating
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][tips][child]" value="1" /> Child Health
				</label>
				<label>
					<input type="checkbox" name="statinfo[filters][tips][women]" value="1" /> Women's Health Issues
				</label>
				<div class="clear"></div>
			</div-->
			
			<h2>Data<br /><span>Leave these options unchecked if you only want the statistic numbers</span></h2>
			<div class="checkboxes">
				<label data-info="The name of the registrant">
					<input type="checkbox" name="statinfo[data][name]" value="1" /> Name
				</label>
				<label data-info="The email given">
					<input type="checkbox" name="statinfo[data][email]" value="1" /> Email
				</label>
				<label data-info="The phone number given">
					<input type="checkbox" name="statinfo[data][phone]" value="1" /> Phone
				</label>
				<label data-info="The zip code given">
					<input type="checkbox" name="statinfo[data][zip]" value="1" /> Zip
				</label>
				<label data-info="The event the registration took place at">
					<input type="checkbox" name="statinfo[data][event]" value="1" /> Event
				</label>
				<label data-info="The registration type(e.g. BMI, Vision, More Info)">
					<input type="checkbox" name="statinfo[data][type]" value="1" /> Type
				</label>
				<label data-info="The date and time of the registration">
					<input type="checkbox" name="statinfo[data][date]" value="1" /> Date/Time
				</label>
				<label data-info="The choices the registrant selected to recieve more information on">
					<input type="checkbox" name="statinfo[data][moreinfo]" value="1" /> ACP Tips
				</label>
				<label data-info="Whether or not the registrant is a current ACP patient">
					<input type="checkbox" name="statinfo[data][patient]" value="1" /> Patient
				</label>
				<label data-info="For screening registrations, whether or not the person wanted to have more info about their screening emailed">
					<input type="checkbox" name="statinfo[data][scrninfo]" value="1" /> Screening Info
				</label>
				<div class="clear"></div>
			</div>
			
			<div class="submit">
				<input type="submit" value="Submit" />
			</div>
		</form>
		</div>
	</div>
</div>
<!--main ends-->

<script type="text/javascript">
/*jQuery(document).ready(function() {
	jQuery('#datestart').datetimepicker({
		format: 'Y-m-d',
		timepicker: false
	});
	jQuery('#dateend').datetimepicker({
		format: 'Y-m-d',
		timepicker: false
	});
	jQuery('#datedir').change(function() {
		jQuery('#dateend').prop('disabled', this.value != 'between');
	});
});*/
</script>

<?php
get_footer('admin');
?>