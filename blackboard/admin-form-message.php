<?php if (!empty($_SESSION['admin_saved_msg'])): ?>
<p id="form-message"><span><?php echo $_SESSION['admin_saved_msg']; ?></span></p>
<?php unset($_SESSION['admin_saved_msg']); ?>
<?php endif; ?>