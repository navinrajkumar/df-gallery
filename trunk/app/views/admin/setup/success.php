<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
?>
<p>
	Created admin account with credentials<br/>
	<p><strong>username:</strong> <?php echo $admin_username; ?></p>
	<p><strong>password:</strong> <?php echo $admin_password; ?></p>
</p>
<p class="message_grey">PLEASE CHANGE THE PASSWORD ONCE YOU LOGIN.<br/>
Please click <?php echo anchor('/admin/login', 'here'); ?> to login.</p>