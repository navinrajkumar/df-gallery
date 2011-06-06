<?php
if (! defined ( 'BASEPATH' ))
	exit ( 'No direct script access allowed' );
?>
<p>The latest version of the gallery has been installed and upgraded on
your system.</p>
<?php
echo anchor ( '/admin', 'Gallery Home.', array ('class' => 'submit_button' ) );
?>
