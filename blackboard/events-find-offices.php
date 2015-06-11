<?php
/*
Template Name: Events Find Offices
Page ID: 61
*/

if (!$is_coordinator) {
	wp_redirect(get_page_link(37));
	exit(0);
}

if (empty($_SESSION['cur_event_id'])) {
	wp_redirect(get_page_link(38));
	exit(0);
}

//$offices = ACPManager::list_offices(0, 0);
$specs = ACPManager::list_specialties();

get_header('faq');
?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
<script src="<?php bloginfo('template_directory'); ?>/js/jquery.search-ajax.js"></script>
<!--main starts-->
<div id="main">
	<div class="container">
		<!--contacts section starts-->
		<section class="contacts">
			<h1>Medical Office Locations</h1>
			<div id="office-gmap" class="map">&nbsp;</div>
			<em class="caption">Enter your zip code or search by specialty to find your nearest ACP medical office</em>
			<form action="" class="search" id="frm-office-search">
				<input type="text" name="zip-code" placeholder="Zip Code" id="fld-location">
				<span class="type open-lightbox" data-href="#speciality">
					<span class="text">Specialty</span>
					<i class="ico-plus"></i>
					<input type="hidden" name="speciality" id="fld-specialty">
				</span>
				<input type="submit" value="Search">
			</form>
			<div class="list-holder">
				<ul class="list" id="office-list-left">
					
				</ul>
				<ul class="list" id="office-list-right">
					
				</ul>
				<span id="loading-text" class="loader">Loading ... </span>
			</div>
		</section>
		<div id="list-item-tpl" class="tpl">
		<li data-mid="{mid}">
			<span class="number">{num}</span>
			<h2>{name}</h2>
			<address>{addrln1}<br />{addrln2}<br />{phone}</address>
			<a href="#">Specialties +</a>
			<div class="specialty-bubble" style="display: none;" id="bubble{num}">{specs}</div>
		</li>
		</div>
		<!--contacts section ends-->
	</div>
</div>
<!--main ends-->
</div>
<div id="speciality" class="lightbox">
<ul class="list">
	<li><a class="close" data-search href="#" data-sid="">All Specialties</a></li>
	<?php foreach ($specs as $v): ?>
	<li><a class="close" data-search href="#" data-sid="<?php echo $v->id; ?>"><?php echo $v->name; ?></a></li>
	<?php endforeach; ?>
</ul>
</div>
<a href="#" class="close-lightbox">Close</a>

<?php
get_footer('faq');
?>