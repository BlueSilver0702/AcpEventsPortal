<?php

date_default_timezone_set('America/New_York');

if (!session_id()) {
	session_start();
}

require_once 'lib/class.acpmanager.php';
$acpdb = new ACPSql('mysql:dbname=' . DB_NAME . ';host=' . DB_HOST, DB_USER, DB_PASSWORD);
$acplang = (empty($_SESSION['cur_lang']) ? null : $_SESSION['cur_lang']);
ACPManager::init($acpdb);
$acplang = ACPManager::load_lang($acplang);

function is_tree( $pid ) {
    global $post;
    if ( is_page($pid) )
        return true;  
    $anc = get_post_ancestors( $post->ID );
    foreach ( $anc as $ancestor ) {
        if( is_page() && $ancestor == $pid ) {
            return true;
        }
    }
    return false; 
}

function cstm_get_image_src($pid, $name) {
	$img = get_post_meta($pid, $name, true);
	if ($img) {
		$img = wp_get_attachment_image_src($img, 'full');
		$img = $img[0];
	}
	return $img;
}

/*function acp_login($loc, $req, $user){
	if (isset($user->roles) && is_array($user->roles)) {
		if (in_array('developer', $user->roles)) {
			return $loc; //site_url('/wp-admin/');
		}
		elseif (in_array('langeditor', $user->roles)) {
			return site_url('/acp-admin/');
		}
	}
	return site_url('/');
}*/
//add_filter('login_redirect', 'acp_login', 10, 3);

/*function acp_roles() {
	if (current_user_can('add_users') && !empty($_GET['debug2142'])) {
		/*$adm = get_role('administrator');
		$adm->add_cap('acp_lang_edit');
		$dev = add_role('acp_developer', 'ACP Developer', $adm->capabilities);
		if (is_null($dev)) {
			die('Failed to add acp_dev');
		}
		$dev->add_cap('acp_developer');
		$langeditor = add_role('acp_lang_editor', 'ACP Language Editor', array('acp_lang_edit'));
		if (is_null($langeditor)) {
			die('Failed to add acp_lang_editor');
		}
		$adm = get_role('administrator');
		$dev = get_role('acp_developer');
		$sub = get_role('subscriber');
		$event = add_role('acp_event_coordinator', 'ACP Event Coordinator', $sub->capabilities);
		if (is_null($event)) {
			die('Failed to add acp_event_coordinator');
		}
		$adm->add_cap('acp_event_coordinator');
		$dev->add_cap('acp_event_coordinator');
		$event->add_cap('acp_event_coordinator');
		/*$event = get_role('acp_event_coordinator');
		$event->add_cap('publish_posts');
		$event->add_cap('upload_files');
		$event->add_cap('edit_posts');
		$event->add_cap('edit_published_posts');
		die('chk21');
	}
}*/
//add_action('wp_loaded', 'acp_roles');

$is_developer = false;
$is_lang_editor = false;
$is_admin = false;
$is_coordinator = false;
function acp_user_check() {
	global $is_developer, $is_lang_editor, $is_admin, $is_coordinator;
	if (current_user_can('acp_developer')) {
		$is_developer = true;
		$is_admin = true;
		$is_lang_editor = true;
		$is_coordinator = true;
	}
	elseif (current_user_can('add_users')) {
		$is_admin = true;
		$is_lang_editor = true;
		$is_coordinator = true;
		//ACPManager::error('Testing error reporting', 0, ACPManager::E_LEVEL_DEBUG);
	}
	elseif (current_user_can('acp_lang_edit')) {
		$is_lang_editor = true;
	}
	elseif (current_user_can('acp_event_coordinator')) {
		$is_coordinator = true;
	}
}

add_action('wp_loaded', 'acp_user_check');

function acp_stripslashes(&$a) {
	if (!is_array($a)) {
		return;
	}
	foreach ($a as $k => $v) {
		if (is_array($v)) {
			acp_stripslashes($a[$k]);
		}
		else {
			$a[$k] = trim(stripslashes($v));
		}
	}
}

function cstm_remove_admin_pages() {
	global $is_developer;
	if (!$is_developer) {
		remove_menu_page('edit-comments.php');
		remove_menu_page('link-manager.php');
		//remove_menu_page('edit.php');
		//remove_menu_page('upload.php');
		remove_menu_page('themes.php');
		remove_menu_page('plugins.php');
		remove_menu_page('users.php');
		remove_menu_page('tools.php');
	}
}
add_action('admin_menu', 'cstm_remove_admin_pages');

/*function acp_add_event_img($meta) {
	//ACPManager::error(var_export($meta, true), 0, ACPManager::E_LEVEL_DEBUG);
	global $current_user;
	get_currentuserinfo();
	$eid = get_user_meta($current_user->ID, 'cur_event_id', true);
	if (!empty($eid)) {
		$img = wp_upload_dir();
		//$img = "{$img['baseurl']}/{$meta['file']}";
		$img = "{$img['baseurl']}/{$meta['sizes']['large']['file']}";
		$event = ACPManager::get_event($eid);
		$event->img = $img;
		$event->update();
	}
	return $meta;
}
add_filter('wp_generate_attachment_metadata', 'acp_add_event_img');*/

function acp_get_cur_event() {
	if (empty($_SESSION['cur_event_id']) && current_user_can('acp_event_coordinator')) {
		global $current_user;
		get_currentuserinfo();
		$_SESSION['cur_event_id'] = get_user_meta($current_user->ID, 'cur_event_id', true);
	}
}
add_action('wp_loaded', 'acp_get_cur_event');

?>