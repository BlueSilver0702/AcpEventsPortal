<?php
/*
Template Name: ACP Admin Language
*/

if (!$is_lang_editor) {
	wp_redirect(get_page_link(51));
	exit(0);
}

$langs = ACPManager::list_langs();

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">Manage Languages</h1>

		<table class="data orange" style="max-width: 1000px;">
		<tr class="header">
			<!--td style="width: 5%; max-width: 40; min-width: 20px;">&#160;</td-->
			<td style="width: 65%;">Name</td>
			<td style="width: 25%;">Code</td>
			<td style="width: 10%;">Default</td>
		</tr>
		<?php if (empty($langs)): ?>
		<tr>
			<td colspan="3" class="txtcenter">No languages found</td>
		</tr>
		<?php else: ?>
		<?php foreach ($langs as $v): ?>
		<tr>
			<!--td class="txtcenter"><input type="checkbox" name="acpchecks[]" value="<?php echo $v->id; ?>" /></td-->
			<td><a href="<?php echo get_page_link(23) . "?lid={$v->id}"; ?>"><?php echo $v->name; ?></a></td>
			<td><?php echo $v->code; ?></td>
			<td><?php echo $v->is_default; ?></td>
		</tr>
		<?php endforeach; ?>
		<?php endif; ?>
		</table>
	</div>
</div>
<!--main ends-->

<?php
get_footer('admin');
?>