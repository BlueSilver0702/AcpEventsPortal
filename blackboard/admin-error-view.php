<?php
/*
Template Name: ACP Admin Error View
*/

if (!$is_developer) {
	wp_redirect(get_page_link(51));
	exit(0);
}

if (empty($_GET['eid'])) {
	wp_redirect(get_page_link(10));
	exit(0);
}

$error = ACPManager::get_error((int)$_GET['eid']);
$error->load_instances(15);

get_header('admin');
?>
<!--main starts-->
<div id="main">
	<div class="container">
		<h1 class="title">View Error</h1>
		
		<form method="post" action="<?php echo get_page_link(24); ?>">
		<div class="error-view">
			<div class="detail">
			<span>Status:</span>
			<select name="einfo[status]" id="status">
			<option value="<?php echo ACPManager::E_STATUS_OPEN; ?>" <?php if ($error->status == ACPManager::E_STATUS_OPEN) { echo 'selected="selected"'; } ?>>Open</option>
			<option value="<?php echo ACPManager::E_STATUS_FIXED; ?>" <?php if ($error->status == ACPManager::E_STATUS_FIXED) { echo 'selected="selected"'; } ?>>Fixed</option>
			</select>
			</div>
			
			<div class="detail">
			<span>Severity:</span>
			<?php echo $error->severity; ?>
			</div>
			
			<div class="detail">
			<span>Assigned:</span>
			<input type="text" name="einfo[assigned]" id="assigned" value="<?php echo ($error->assigned ? $error->assigned : ''); ?>" />
			</div>
			
			<div class="detail">
			<span>Line:</span>
			<?php echo $error->line; ?>
			</div>
			
			<div class="detail">
			<span>Code:</span>
			<?php echo $error->code; ?>
			</div>
			
			<div class="detail">
			<span>Last Occured:</span>
			<?php echo $error->instances[0][0]->format('M d, Y H:i:s'); ?>
			</div>
			
			<div class="clear15"></div>
			
			<h2>Message</h2>
			<pre><?php echo $error->message; ?></pre>
			
			<h2>File</h2>
			<pre><?php echo $error->file; ?></pre>
			
			<h2>Instances</h2>
			<?php foreach ($error->instances as $k => $v): ?>
			<div class="instance">
			<h3><?php echo $v[0]->format('M d, Y H:i:s'); ?> <span class="toggle" data-eid="<?php echo $k; ?>">Show</span></h3>
			<pre id="inst<?php echo $k; ?>"><?php echo $v[1]; ?></pre>
			</div>
			<?php endforeach; ?>
		</div>
		</form>
	</div>
</div>
<!--main ends-->

<script type="text/javascript">
jQuery(document).ready(function() {
	jQuery('h3 span.toggle').click(function(e) {
		var $this = jQuery(this);
		var eid = $this.attr('data-eid');
		
		if ($this.text() == 'Show') {
			jQuery('#inst' + eid).toggle();
			$this.text('Hide');
		}
		else if ($this.text() == 'Hide') {
			jQuery('#inst' + eid).toggle();
			$this.text('Show');
		}
	});
});
</script>

<?php
get_footer('admin');
?>