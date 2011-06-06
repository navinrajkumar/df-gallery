<?php

class CI_Cache_Manager extends CI_Output
{

	function display_cache()
	{
		global $RTR;
		global $CFG;
		global $URI;
		global $OUT;
		if ($RTR->fetch_class () == 'api' && $RTR->fetch_method () == 'rest') {
			header ( 'Content-Type: text/xml;' );
			header ( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
			header ( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
			if ($OUT->_display_cache ( $CFG, $URI ) == TRUE) {
				exit ();
			}
		}
		return FALSE;
	}
}

?>