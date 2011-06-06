<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
?>
<p>
A newer version of the gallery has been installed, and has to undergo an upgrade process from version 
<?php echo $current_version; ?> to <?php echo $new_version; ?>.
</p>
<br/>
Below are the list of upgrade task that need to be executed
<br/><br/>
<table border="0" cellpadding="1" cellspacing="0">
  <tr height="25px">
    <td width="100px" ><strong>Version</strong> </td>
    <td ><strong>Description</strong> </td>
  </tr>
<?php 
foreach ($tasks as $task) { ?>
  <tr>
    <td bordercolor="#F00"><?php echo $task['version']; ?></td>
    <td><?php echo $task['description']; ?></td>
  </tr>
<?php }?>
</table>
<br/>
<?php echo anchor('/admin/upgrade/run','Run Upgrade',array('class'=>'submit_button')); ?>