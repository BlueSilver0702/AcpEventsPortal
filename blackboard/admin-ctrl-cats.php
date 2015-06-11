<?php global $cats; ?>
<?php if (!empty($cats)): ?>
<select class="ctrl_ordercat">
<option value="">Category select</option>
<?php foreach ($cats as $v): ?>
<option value="<?php echo $v->id; ?>" <?php if ($ctrl['ordercat'] == $v->id) { echo 'selected="selected"'; } ?>><?php echo $v->name; ?></option>
<?php endforeach; ?>
</select>
<?php endif; ?>