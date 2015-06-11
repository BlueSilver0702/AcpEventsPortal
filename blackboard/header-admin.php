<!doctype html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<title><?php $title = wp_title('', false); echo (empty($title) ? '' : "{$title}"); ?></title>
	<?php //wp_head(); ?>

	<!--reset styles-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/admin-reset.css">
	<!--ui styles-->
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/admin-ui.css">
	<!--custom fonts -->
	<link href='http://fonts.googleapis.com/css?family=Open+Sans:300' rel='stylesheet' type='text/css'>
	<!--jQuery library-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.min.js"></script>

	<!--include plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.img-stretch.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/jquery.placeholder.js"></script>

	<!--init plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.init-plugins.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/datetimepicker/jquery.datetimepicker.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/fancybox/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/jqueryui/jquery-ui.min.js"></script>
	<!--common javascript-->
	<!--script src="<?php bloginfo('template_directory'); ?>/js/common.js"></script-->
	<script src="<?php bloginfo('template_directory'); ?>/js/admin.js"></script>
	
	<!--[if IE 9]>
		<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie9.css">
	<![endif]-->
	<!--[if lt IE 9]>
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/css/ie.css">
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/respond.src.js"></script>
	<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/fancybox/jquery.fancybox.css" type="text/css" media="screen" />
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/datetimepicker/jquery.datetimepicker.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/jqueryui/jquery-ui.min.css">
	<link rel="stylesheet" href="<?php bloginfo('template_directory'); ?>/admin.css">
</head>
<body>
	<!--wrapper starts-->
	<div id="wrapper">
		<!--header starts-->
		<header id="header">
			<div class="container">
				<div class="header-box">
					<strong class="logo align-left">Advantage Care Physicians</strong>
				</div>
			</div>
			<div class="border"></div>
		</header>
		<!--header ends-->
		<?php get_sidebar('admin'); ?>