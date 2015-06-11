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
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.img-stretch.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.custom-checkbox.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/jquery.placeholder.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/jquery.nicefileinput.min.js"></script>

	<!--init plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.init-plugins.js"></script>
	<!--common javascript-->
	<script type="text/javascript">
	var wpRoot = '<?php echo site_url('/'); ?>';
	var wpThemeRoot = '<?php bloginfo('template_directory'); ?>/';
	var wpAjaxHandler = '<?php echo get_page_link(57); ?>';
	</script>
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
	<div id="wrapper" class="details-page">
		<!--header starts-->
		<header id="header" class="no-border">
			<div class="container">
				<div class="header-box">
					<h1 class="logo align-left"><a href="<?php echo get_page_link(41); ?>">Advantage Care Physicians ADVANTAGECARE IN YOUR NEIGHBORHOOD</a></h1>
				</div>
			</div>
		</header>
		<!--header ends-->