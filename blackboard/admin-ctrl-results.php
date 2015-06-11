<?php global $amount; ?>
<div class="data-control">
	<span>Results per page</span>
	<select class="ctrl_results">
	<option value="30">30</option>
	<option value="50" <?php if ($amount == 50) { echo 'selected="selected"'; } ?>>50</option>
	<option value="100" <?php if ($amount == 100) { echo 'selected="selected"'; } ?>>100</option>
	<option value="150" <?php if ($amount == 150) { echo 'selected="selected"'; } ?>>150</option>
	<option value="200" <?php if ($amount == 200) { echo 'selected="selected"'; } ?>>200</option>
	</select>
	<button class="btn_results">Show</button>
</div>