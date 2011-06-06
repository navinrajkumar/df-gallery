<?php

/**
 * If the product has not yet been installed this filter will redirect to the setup process.
 */
class Install_filter extends Filter
{

	/**
	 * redirects the user if the database config file has not been created.
	 */
	function before ()
	{
		global $CFG;
		
		//check if setup has to run
		if ($CFG->item('active_group', 'database') === FALSE)
		{
			header('Location: '.$CFG->slash_item('base_url'). 'admin/setup', TRUE, 302);
			exit();
		}
	}

	function after ()
	{
	}
}

?>