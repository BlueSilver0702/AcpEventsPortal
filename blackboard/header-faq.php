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
	
	<link href="<?php bloginfo('template_directory'); ?>/js/bubbletip/bubbletip.css" rel="stylesheet" type="text/css" />
	<!--[if IE]>
	<link href="<?php bloginfo('template_directory'); ?>/js/bubbletip/bubbletip-IE.css" rel="stylesheet" type="text/css" />
	<![endif]-->

	<!--include plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.openclose.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/plugins/jquery.placeholder.min.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.lightbox.js"></script>
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.bubbletip-1.0.6.js"></script>

	<!--init plugins-->
	<script src="<?php bloginfo('template_directory'); ?>/js/jquery.init-maps.js"></script>
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
	<meta name ="format-detection" content="telephone=no" />
</head>
<body>
<?php
global $no_wrap_bg, $post;
$style = (empty($no_wrap_bg) ? '' : 'style="background-image: none;"');
?>
	<!--wrapper starts-->
	<div id="wrapper" class="home-page faq-page" <?php echo $style; ?>>
		<!--header starts-->
		<header id="header">
			<div class="container">
				<div class="header-box">
					<h1 class="logo small align-left"><a href="<?php echo get_page_link(62); ?>">Advantage Care Physicians ADVANTAGECARE IN YOUR NEIGHBORHOOD</a></h1>
					<p>Call to make an ACP appointment: <span>877-212-5269</span></p>
					<nav id="nav" class="align-right">
						<h1 class="accessibility">Navigation</h1>
						<ul>
							<li><a href="<?php echo get_page_link(62); ?>">Home</a></li>
							<?php if ($post->ID != 80): ?>
							<li><a href="<?php echo get_page_link(80); ?>">ACP Difference</a></li>
							<?php endif; ?>
							<?php if ($post->ID != 75): ?>
							<li><a href="<?php echo get_page_link(75); ?>">Choosing a Doctor</a></li>
							<?php endif; ?>
							<?php if ($post->ID != 61): ?>
							<li><a href="<?php echo get_page_link(61); ?>">Medical Office Locator</a></li>
							<?php endif; ?>
						</ul>
					</nav>
					<a href="<?php echo get_page_link(48); ?>?acpredir=62" class="btn-more">Sign Up for ACP Physician Tips</a>
				</div>
			</div>
		</header>
		<!--header ends-->