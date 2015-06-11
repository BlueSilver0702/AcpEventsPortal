<?php
/*
Template Name: ACP Admin Language Add Text
*/

if (!$is_lang_editor) {
	wp_redirect(get_page_link(51));
	exit(0);
}

if (empty($_REQUEST['lid'])) {
	$lobj = new ACPLangObject();
	$addedit = 'Add';
}
else {
	$lobj = new ACPLangObject($_REQUEST['lid']);
	$addedit = (empty($lobj) || empty($lobj->id) ? 'Add' : 'Edit');
}

if (!empty($_POST)) {
	acp_stripslashes($_POST);
	$linfo = $_POST['linfo'];
	$lobj->name = $linfo['name'];
	$lobj->code = $linfo['code'];
	
	if (empty($lobj->name)) {
		$errors[] = 'Language must have a name';
	}
	/*if (empty($lobj->code)) {
		$errors[] = 'Language must have a code';
	}*/
	if (empty($errors)) {
		if ($lobj->id) {
			$_SESSION['admin_saved_msg'] = 'Text updated';
			$lobj->update();
			$redir = false;
		}
		else {
			$_SESSION['admin_saved_msg'] = 'New text saved';
			$lobj->insert();
			$redir = true;
		}
		if (!empty($linfo['text']) && is_array($linfo['text'])) {
			foreach ($linfo['text'] as $lang_id => $txt) {
				$txt = trim($txt);
				if (empty($txt)) {
					continue;
				}
				if (empty($lobj->text[$lang_id]) || !($lobj->text[$lang_id] instanceof ACPLangText)) {
					$lobj->add_text($lang_id);
					$lobj->text[$lang_id]->lobj_id = $lobj->id;
					$lobj->text[$lang_id]->lang_id = $lang_id;
					$lobj->text[$lang_id]->string = $txt;
					$lobj->text[$lang_id]->save();
				}
				else {
					$lobj->text[$lang_id]->string = $txt;
					$lobj->text[$lang_id]->save();
				}
			}
		}
		if ($redir) {
			wp_redirect(get_page_link(36) . "?lid={$lobj->id}");
			exit(0);
		}
	}
}

$langs = ACPManager::list_langs();

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<?php get_template_part('admin-form-message'); ?>
	<div class="container">
		<h1 class="title"><?php echo $addedit; ?> Text</h1>
		
		<?php if (!empty($errors)): ?>
		<p id="form-error"><?php echo implode('<br />', $errors); ?></p>
		<?php endif; ?>
		<form action="<?php echo get_page_link(36) . "?lid={$lobj->id}"; ?>" method="post">
		<div class="data-form">
			<div class="field">
				<label for="name">Name</label>
				<input type="text" name="linfo[name]" id="name" value="<?php echo $lobj->name; ?>" />
			</div>
			<div class="field">
				<label for="code">Code</label>
				<input type="text" name="linfo[code]" id="code" value="<?php echo $lobj->code; ?>" />
			</div>
			<?php if (!empty($langs)): ?>
			<?php foreach ($langs as $v): ?>
			<div class="field">
				<label for="text<?php echo $v->id; ?>"><?php echo $v->name; ?> Text</label>
				<textarea class="medium" name="linfo[text][<?php echo $v->id; ?>]" id="text<?php echo $v->id; ?>"><?php echo (empty($lobj->text[$v->id]) ? '' : $lobj->text[$v->id]); ?></textarea>
			</div>
			<?php endforeach; ?>
			<?php endif; ?>
			<div class="submit">
				<input type="submit" value="<?php echo $addedit; ?> Text" />
			</div>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>