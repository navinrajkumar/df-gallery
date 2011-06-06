<?php

/**
 * Once the user is authenticated and the has logged in, we will trigger the upgrade filter.
 * This filter will call the upgrade manager and if there are any upgrades to perform, will
 * redirect the user to the upgrade controller of the application.
 */
class Upgrade_filter extends Filter
{

	/**
	 * load the upgrade manager, and check if there are any upgrades to be executed.
	 */
	function before ()
	{
		$upgrade_manager = & load_class('Upgrade_Manager');
		
		if ($upgrade_manager->has_upgrades())
		{
			$config = & load_class('Config');
			
			header('Location: ' . $config->slash_item('base_url') . 'admin/upgrade');
		}
	}

	function after ()
	{
	}
}

?>