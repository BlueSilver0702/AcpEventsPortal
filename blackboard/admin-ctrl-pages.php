<?php global $maxpage, $page, $prev, $next, $page_id, $total_found; ?>
<div class="data-control" style="float: right;">
	<i>Total results: <?php echo $total_found; ?></i>
	<?php if ($maxpage > 1): ?>
	<span>Jump to page</span>
	<select class="ctrl_page">
	<?php for ($i = 1; $i <= $maxpage; ++$i): ?>
	<option value="<?php echo $i; ?>" <?php if ($i == $page) { echo 'selected="selected"'; } ?>><?php echo $i; ?></option>
	<?php endfor; ?>
	</select>
	<button class="btn_page">Go</button>
	<?php endif; ?>
	<span>
	<?php if ($prev): ?>
	<a class="pglink" href="<?php echo get_page_link($page_id) . "?page={$prev}"; ?>">&laquo;</a>
	<?php endif; ?>
	Page <?php echo $page; ?> of <?php echo $maxpage; ?>
	<?php if ($next): ?>
	<a class="pglink" href="<?php echo get_page_link($page_id) . "?page={$next}"; ?>">&raquo;</a>
	<?php endif; ?>
	</span>
</div>