<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); 
echo '<?xml version="1.0"?>' ?>
<resp>
	<error code="<?php echo $code; ?>">
		<?php echo $message; ?>
	</error>
</resp>