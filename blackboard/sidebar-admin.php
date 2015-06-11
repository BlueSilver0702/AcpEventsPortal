<?php

global $is_developer, $is_admin, $is_lang_editor, $post;

$scr_vit = false;
$scr_vis = false;
$scr_bp = false;
$scr_bmi = false;
if (!empty($_GET['scrtype'])) {
	switch($_GET['scrtype']) {
		case 'vitals':
			$scr_vit = true;
			break;
		case 'vision':
			$scr_vis = true;
			break;
		case 'bp':
			$scr_bp = true;
			break;
		case 'bmi':
			$scr_bmi = true;
	}
}

$lang_id = (empty($_REQUEST['langid']) ? 0 : (int)$_REQUEST['langid']);

$langs = ACPManager::list_langs();

?>
<div id="sidebar">
	<ul>
	<?php if ($is_admin): ?>
	<li <?php if ($post->ID == 4) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(4); ?>">Dashboard</a>
	</li>
	<li <?php if (is_tree(6)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(6); ?>">Sponsored Events</a>
		<ul>
		<li <?php if (is_tree(14)) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(14); ?>">Add Event</a></li>
		</ul>
	</li>
	<li <?php if (is_tree(7)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(7); ?>">Staff</a>
		<ul>
		<li <?php if (is_tree(15)) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(15); ?>">Add Staff</a></li>
		</ul>
	</li>
	<li <?php if (is_tree(55)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(55); ?>">Offices</a>
		<ul>
		<li <?php if (is_tree(56)) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(56); ?>">Add Office</a></li>
		</ul>
	</li>
	<!--li <?php if (is_tree(9)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(9); ?>">Screenings</a>
		<ul>
		<li <?php if ($scr_vit) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(9); ?>?scrtype=vitals">Vitals</a></li>
		<li <?php if ($scr_vis) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(9); ?>?scrtype=vision">Vision</a></li>
		<li <?php if ($scr_bmi) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(9); ?>?scrtype=bmi">BMI</a></li>
		<li <?php if ($scr_bp) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(9); ?>?scrtype=bp">BP</a></li>
		</ul>
	</li-->
	<li <?php if (is_tree(8)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(8); ?>">Registrations</a>
	</li>
	<li <?php if (is_tree(12)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(85); ?>">Statistics</a>
		<ul>
		<li><a href="#">Events</a></li>
		<li <?php if (is_tree(85)) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(85); ?>">Registrations</a></li>
		<li><a href="#">Screenings</a></li>
		<li><a href="#">Make Report</a></li>
		</ul>
	</li>
	<li <?php if (is_tree(21)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(21); ?>">Categories</a>
		<ul>
		<li <?php if (is_tree(22)) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(22); ?>">Add Category</a></li>
		</ul>
	</li>
	<?php endif; ?>
	
	<?php if ($is_lang_editor): ?>
	<li <?php if (is_tree(11)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(11); ?>">Language</a>
		<ul>
		<li><a href="<?php echo get_page_link(23); ?>">Add Language</a></li>
		<li><a href="<?php echo get_page_link(36); ?>">Add Text</a></li>
		<li><a href="<?php echo get_page_link(35); ?>">All Text</a></li>
		<?php if (count($langs)): ?>
		<?php foreach ($langs as $v): ?>
		<li <?php if ($lang_id == $v->id) { echo 'class="current"'; } ?>><a href="<?php echo get_page_link(35) . "?langid={$v->id}"; ?>"><?php echo $v->name; ?></a></li>
		<?php endforeach; ?>
		<?php endif; ?>
		</ul>
	</li>
	<?php endif; ?>
	
	<?php if ($is_developer): ?>
	<li <?php if (is_tree(10)) { echo 'class="current"'; } ?>>
		<a href="<?php echo get_page_link(10); ?>">Errors</a>
	</li>
	<li>
		<a href="<?php echo site_url('/wp-admin/'); ?>" target="_blank">WP Admin</a>
	</li>
	<?php endif; ?>
	
	<li class="spacer"></li>
	
	<li>
		<a href="<?php echo wp_logout_url(get_page_link(51)); ?>">Logout</a>
	</li>
	
	</ul>
</div>