<?php if (! defined ( 'BASEPATH' )) exit ( 'No direct script access allowed' ); ?>
<p class="message_<?php echo (isset ( $level )) ? $level : 'white'; ?>"><?php echo nl2br ( $message ); ?></p>