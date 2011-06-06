<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
?>
<?php if ($upgrade_result){ ?>
	<p>
		Your gallery system has been upgraded successfully.
	</p>
<?php echo anchor('/admin','Gallery home.',array('class'=>'submit_button')); ?>
<?php }else{ ?>
	<p>
		We encountered errors during the upgrade.<br/>
		Please check the online wiki for more details on how to manually upgrade your gallery before you use the system.
	</p>
<?php echo anchor('http://wiki.dezinerfolio.com/dfgallery','Visit the Gallery Wiki for help.',array('class'=>'submit_button')); ?>
<?php } ?>
