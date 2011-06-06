<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
	
?>
	    <div class="sidenav">
	    		<div align="center">
					<?php echo anchor('/admin/gallery','<span class="icon ico_photos">&nbsp;</span>Galleries',array('class'=>'sidenav_btn')); // 'title'=>'header=[Gallery] body=[view all the galleries for this account.]')); ?>
					<?php echo anchor('/admin/settings/edit','<span class="icon ico_options">&nbsp;</span>Settings',array('class'=>'sidenav_btn'));//, 'title'=>'header=[System Settings] body=[Change your gallery options.]')); ?>
					<?php echo anchor('/admin/user/edit','<span class="icon ico_user">&nbsp;</span>Profile',array('class'=>'sidenav_btn'));//, 'title'=>'header=[User profile] body=[Change your passowrd and other profile settings.]')); ?>
					<?php echo anchor('/admin/login/logout','<span class="icon ico_logout">&nbsp;</span>Logout',array('class'=>'sidenav_btn')); ?>
				</div>
		</div>
		<div class="content">
		