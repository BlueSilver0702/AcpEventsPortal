<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<title><?php $title = wp_title('', false); echo (empty($title) ? '' : "{$title} | "); bloginfo( 'name' ); ?></title>
	<?php //wp_head(); ?>

	<!--reset styles-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/reset.css">
	<!--ui styles-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ui.css">
	<!--common styles-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/common.css">
	<!--custom fonts-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/fontface.css">
	<!--jQuery library-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>

	<!--include plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.custom-radio.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.custom-select.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.img-stretch.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/jquery.placeholder.min.js"></script>

	<!--init plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.init-plugins.js"></script>
	<!--common javascript-->
	<script src="<?php bloginfo('template_directory'); ?>/js/common.js"></script>
	
	<!--[if IE 9]>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie9.css">
	<![endif]-->
	<!--[if lt IE 9]>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/respond.src.js"></script>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/style.css">
</head>
<body>
	<!--wrapper starts-->
	<div id="wrapper" class="home-page full-height">
		<div class="item-table">
			<div class="item-cell" style="vertical-align: top;">
			<div class="header-box" style="border-style: none; padding-left: 60px;">
				<!--header starts-->
				<header id="header">
					<div class="container" style="padding: 8px 0px 0px 0px;">
						<h1 class="logo small align-left"><a href="#">Advantage Care Physicians ADVANTAGECARE IN YOUR NEIGHBORHOOD</a></h1>
					</div>
				</header>
			</div>
				<!--header ends-->