<?php
/*
Template Name: ACP Admin Registration View
*/

if (!$is_admin) {
	wp_redirect(get_page_link(51));
	exit(0);
}

if (empty($_GET['rid'])) {
	wp_redirect(get_page_link(8));
	exit(0);
}

$reg = ACPManager::get_event_registration((int)$_GET['rid']);
switch ($reg->rec_type) {
	case ACPRecord::TYPE_SCREENBP:
		$screening = 'BP';
		break;
	case ACPRecord::TYPE_SCREENBMI:
		$screening = 'BMI';
		break;
	case ACPRecord::TYPE_SCREENVISION:
		$screening = 'Vision';
		break;
	case ACPRecord::TYPE_SCREENVITALS:
		$screening = 'Vitals';
		break;
	case ACPRecord::TYPE_EVENTREG:
	case ACPRecord::TYPE_GENERIC:
	default:
		$screening = 'None';
		break;
}

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">View Registration</h1>
		
		<form method="post" action="<?php echo get_page_link(30); ?>">
		<div class="data-view">
			<div class="detail">
			<span>Date:</span>
			<?php echo $reg->created_format('M j, Y h:ia'); ?>
			</div>
			
			<div class="detail">
			<span>Event:</span>
			<?php echo $reg->event->name; ?>
			</div>
			
			<div class="detail">
			<span>Patient:</span>
			<?php echo $reg->patient; ?>
			</div>
			
			<div class="clear5"></div>
			
			<h2>Name</h2>
			<span><?php echo "{$reg->fname} {$reg->lname}"; ?></span>
			
			<h2>Email</h2>
			<span><?php echo $reg->email; ?></span>
			
			<h2>Phone</h2>
			<span><?php echo $reg->phone; ?></span>
			
			<h2>Zip</h2>
			<span><?php echo $reg->zip; ?></span>
			
			<h2>More Info</h2>
			<span><?php echo (empty($reg->info_choices) ? 'None' : implode(', ', $reg->list_info_choices())); ?></span>
			
			<h2>Screening</h2>
			<span><?php echo $screening; ?></span>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>