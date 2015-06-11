<?php
/*
Template Name: ACP Admin Dashboard
Page ID: 4
*/

if (!is_user_logged_in() || !$is_lang_editor) {
	wp_redirect(get_page_link(51));
	exit(0);
}
/*elseif (!$is_lang_editor) {
	wp_redirect(site_url('/'));
	exit(0);
}*/
elseif (!$is_admin) {
	//take translators to the language page since there's nothing to see on the dashboard
	wp_redirect(get_page_link(11));
}

$errors = ACPManager::list_errors(ACPManager::E_STATUS_OPEN, ACPManager::E_LEVEL_ANY, time() - (86400 * 7), 5);
$events = ACPManager::list_events(mktime(0, 0, 0, date('n'), date('j'), date('Y')), null, 0, 5);

$thisweek = ACPManager::get_relative_time('this week');
$thismonth = ACPManager::get_relative_time('this month');
$lastweek = ACPManager::get_relative_time('last week');
$lastmonth = ACPManager::get_relative_time('last month');

$stats_event_reg = array(
	'thisweek_nopat' => ACPManager::stats_event_reg($thisweek, null, 'No'),
	'thisweek_pat' => ACPManager::stats_event_reg($thisweek, null, 'Yes'),
	'lastweek_nopat' => ACPManager::stats_event_reg($lastweek, $thisweek, 'No'),
	'lastweek_pat' => ACPManager::stats_event_reg($lastweek, $thisweek, 'Yes'),
	'thismonth_nopat' => ACPManager::stats_event_reg($thismonth, null, 'No'),
	'thismonth_pat' => ACPManager::stats_event_reg($thismonth, null, 'Yes'),
	'lastmonth_nopat' => ACPManager::stats_event_reg($lastmonth, $thismonth, 'No'),
	'lastmonth_pat' => ACPManager::stats_event_reg($lastmonth, $thismonth, 'Yes')
);
$stats_event_reg['thisweek_total'] = $stats_event_reg['thisweek_nopat'] + $stats_event_reg['thisweek_pat'];
$stats_event_reg['lastweek_total'] = $stats_event_reg['lastweek_nopat'] + $stats_event_reg['lastweek_pat'];
$stats_event_reg['thismonth_total'] = $stats_event_reg['thismonth_nopat'] + $stats_event_reg['thismonth_pat'];
$stats_event_reg['lastmonth_total'] = $stats_event_reg['lastmonth_nopat'] + $stats_event_reg['lastmonth_pat'];

$stats_screenings = array(
	'vision' => array(
		'thisweek' => ACPManager::stats_screenings($thisweek, null, 'ScreeningVision'),
		'lastweek' => ACPManager::stats_screenings($lastweek, $thisweek, 'ScreeningVision'),
		'thismonth' => ACPManager::stats_screenings($thismonth, null, 'ScreeningVision'),
		'lastmonth' => ACPManager::stats_screenings($lastmonth, $thismonth, 'ScreeningVision')
	),
	'vitals' => array(
		'thisweek' => ACPManager::stats_screenings($thisweek, null, 'ScreeningVitals'),
		'lastweek' => ACPManager::stats_screenings($lastweek, $thisweek, 'ScreeningVitals'),
		'thismonth' => ACPManager::stats_screenings($thismonth, null, 'ScreeningVitals'),
		'lastmonth' => ACPManager::stats_screenings($lastmonth, $thismonth, 'ScreeningVitals')
	),
	'bmi' => array(
		'thisweek' => ACPManager::stats_screenings($thisweek, null, 'ScreeningBMI'),
		'lastweek' => ACPManager::stats_screenings($lastweek, $thisweek, 'ScreeningBMI'),
		'thismonth' => ACPManager::stats_screenings($thismonth, null, 'ScreeningBMI'),
		'lastmonth' => ACPManager::stats_screenings($lastmonth, $thismonth, 'ScreeningBMI')
	),
	'bp' => array(
		'thisweek' => ACPManager::stats_screenings($thisweek, null, 'ScreeningBP'),
		'lastweek' => ACPManager::stats_screenings($lastweek, $thisweek, 'ScreeningBP'),
		'thismonth' => ACPManager::stats_screenings($thismonth, null, 'ScreeningBP'),
		'lastmonth' => ACPManager::stats_screenings($lastmonth, $thismonth, 'ScreeningBP')
	)
);

$stats_screenings['total'] = array(
	'thisweek' => $stats_screenings['vision']['thisweek'] + $stats_screenings['vitals']['thisweek'] + $stats_screenings['bmi']['thisweek'] + $stats_screenings['bp']['thisweek'],
	'lastweek' => $stats_screenings['vision']['lastweek'] + $stats_screenings['vitals']['lastweek'] + $stats_screenings['bmi']['lastweek'] + $stats_screenings['bp']['lastweek'],
	'thismonth' => $stats_screenings['vision']['thismonth'] + $stats_screenings['vitals']['thismonth'] + $stats_screenings['bmi']['thismonth'] + $stats_screenings['bp']['thismonth'],
	'lastmonth' => $stats_screenings['vision']['lastmonth'] + $stats_screenings['vitals']['lastmonth'] + $stats_screenings['bmi']['lastmonth'] + $stats_screenings['bp']['lastmonth']
);

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">Dashboard</h1>
		
		<?php if ($is_developer): ?>
		<div id="open-errors" class="dashboard-block">
		<h2>Open Errors</h2>
		<table class="data orange" style="max-width: 1240px;">
		<tr class="header">
			<td style="width: 65%;">Message</td>
			<td style="width: 15%; min-width: 140px;">Last Occurance</td>
			<td style="width: 10%;">Severity</td>
			<td style="width: 10%;">Assigned</td>
		</tr>
		<?php if (empty($errors)): ?>
		<tr>
			<td colspan="4" class="txtcenter">No recent open errors</td>
		</tr>
		<?php else: ?>
		<?php foreach ($errors as $v): ?>
		<?php $v->load_instances(1); ?>
		<tr>
			<td><a href="<?php echo get_page_link(24) . "?eid={$v->id}"; ?>"><?php echo $v->message; ?></a></td>
			<td><?php echo $v->instances[0][0]->format('M j h:ia'); ?></td>
			<td><?php echo $v->severity; ?></td>
			<td><?php echo ($v->assigned ? $v->assigned : 'No'); ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</table>
		</div>
		<?php endif; ?>
		
		<?php if ($is_admin): ?>
		<div id="upcoming-events" class="dashboard-block">
		<h2>Upcoming Events</h2>
		<table class="data orange" style="max-width: 1160px;">
		<tr class="header">
			<td style="width: 55%;">Name</td>
			<td style="width: 15%;">Date</td>
			<td style="width: 12%;">Registrations</td>
			<td style="width: 18%;">Contact</td>
		</tr>
		<?php if (empty($events)): ?>
		<tr>
			<td colspan="4" class="txtcenter">No upcoming events</td>
		</tr>
		<?php else: ?>
		<?php foreach ($events as $v): ?>
		<tr>
			<td><a href="<?php echo get_page_link(14) . "?eid={$v->event_id}"; ?>"><?php echo $v->name; ?></a></td>
			<td><?php echo $v->start_format('M j h:ia'); ?></td>
			<td><?php echo $v->count_registrations(); ?></td>
			<td><a href="<?php echo get_page_link(15) . "?sid={$v->contact->staff_id}"; ?>"><?php echo "{$v->contact->fname} {$v->contact->lname}"; ?></a></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</table>
		</div>
		
		<div id="reg-stats" class="dashboard-block">
		<h2>Latest Registration Statistics</h2>
		<table class="data orange" style="max-width: 1110px;">
		<tr class="header">
			<td style="width: 12%;">&#160;</td>
			<td>Event<br />(patient)</td>
			<td>Website<br />(patient)</td>
			<td>Event<br />(not patient)</td>
			<td>Website<br />(not patient)</td>
			<td>Event<br />(total)</td>
			<td>Website<br />(total)</td>
			<td>Total</td>
		</tr>
		<tr>
			<th>This Week</th>
			<td><?php echo $stats_event_reg['thisweek_pat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['thisweek_nopat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['thisweek_total']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['thisweek_total']; ?></td>
		</tr>
		<tr>
			<th>Last Week</th>
			<td><?php echo $stats_event_reg['lastweek_pat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['lastweek_nopat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['lastweek_total']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['lastweek_total']; ?></td>
		</tr>
		<tr>
			<th>This Month</th>
			<td><?php echo $stats_event_reg['thismonth_pat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['thismonth_nopat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['thismonth_total']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['thismonth_total']; ?></td>
		</tr>
		<tr>
			<th>Last Month</th>
			<td><?php echo $stats_event_reg['lastmonth_pat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['lastmonth_nopat']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['lastmonth_total']; ?></td>
			<td>0</td>
			<td><?php echo $stats_event_reg['lastmonth_total']; ?></td>
		</tr>
		</table>
		</div>
		
		<div id="screen-stats" class="dashboard-block">
		<h2>Latest Screening Statistics</h2>
		<table class="data orange" style="max-width: 610px;">
		<tr class="header">
			<td style="width: 20%;">&#160;</td>
			<td>Vision</td>
			<td>Vitals</td>
			<td>BMI</td>
			<td>BP</td>
			<td>Total</td>
		</tr>
		<tr>
			<th>This Week</th>
			<td><?php echo $stats_screenings['vision']['thisweek']; ?></td>
			<td><?php echo $stats_screenings['vitals']['thisweek']; ?></td>
			<td><?php echo $stats_screenings['bmi']['thisweek']; ?></td>
			<td><?php echo $stats_screenings['bp']['thisweek']; ?></td>
			<td><?php echo $stats_screenings['total']['thisweek']; ?></td>
		</tr>
		<tr>
			<th>Last Week</th>
			<td><?php echo $stats_screenings['vision']['lastweek']; ?></td>
			<td><?php echo $stats_screenings['vitals']['lastweek']; ?></td>
			<td><?php echo $stats_screenings['bmi']['lastweek']; ?></td>
			<td><?php echo $stats_screenings['bp']['lastweek']; ?></td>
			<td><?php echo $stats_screenings['total']['lastweek']; ?></td>
		</tr>
		<tr>
			<th>This Month</th>
			<td><?php echo $stats_screenings['vision']['thismonth']; ?></td>
			<td><?php echo $stats_screenings['vitals']['thismonth']; ?></td>
			<td><?php echo $stats_screenings['bmi']['thismonth']; ?></td>
			<td><?php echo $stats_screenings['bp']['thismonth']; ?></td>
			<td><?php echo $stats_screenings['total']['thismonth']; ?></td>
		</tr>
		<tr>
			<th>Last Month</th>
			<td><?php echo $stats_screenings['vision']['lastmonth']; ?></td>
			<td><?php echo $stats_screenings['vitals']['lastmonth']; ?></td>
			<td><?php echo $stats_screenings['bmi']['lastmonth']; ?></td>
			<td><?php echo $stats_screenings['bp']['lastmonth']; ?></td>
			<td><?php echo $stats_screenings['total']['lastmonth']; ?></td>
		</tr>
		</table>
		</div>
		<?php endif; ?>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>