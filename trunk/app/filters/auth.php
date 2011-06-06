<?php

/**
 * this filter just checks if the session value for the user authentication 
 * exists, or else redirects the user to the login page.
 */
class Auth_filter extends Filter
{

	/**
	 * loading the session class, and checking if the isAuthenticated attribute is true
	 */
	function before ()
	{
		$session = & load_class('Session');
		
		if (! $session->userdata('isAuthenticated'))
		{
			global $CFG;
			header('Location: ' . $CFG->slash_item('base_url') . 'admin/login');
		}
	}

	function after ()
	{
	}

}

?>