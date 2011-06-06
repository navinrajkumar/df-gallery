<?php
/**
 * global includes.
 */
include_once BASEPATH . 'helpers/file_helper' . EXT;

/**
 * the upgrade manager will validate the current install, look for new 
 * upgrades and will execute updates accordingly.
 */
class CI_Upgrade_Manager
{

	/**
	 * stores an instance of the CI_Config class
	 *
	 * @var CI_Config
	 */
	var $config;

	/**
	 * constructor that initializes the config class.
	 *
	 * @return CI_Upgrade_Manager
	 */
	function CI_Upgrade_Manager ()
	{
		$this->config = & load_class('Config');
	}

	/**
	 * execute an upgrade task.
	 *
	 * @param string $task
	 * @param array $args
	 * @return array - status array
	 */
	function execute ($task, $args = array())
	{
		if (is_file(UPGRADE_FOLDER . $task . EXT))
		{
			// use reflection and run the task.
			$clazz = substr($task, strrpos($task, '/') + 1);
			include_once UPGRADE_FOLDER . $task . EXT;
			
			if (class_exists($clazz))
			{
				$task = new $clazz();
				return $task->run($args);
			} else
			{
				return array(
					'state' => FALSE , 
					'message' => "Unable to locate class $clazz within with filename '" . $task . EXT . "'" 
				);
			}
		} else
		{
			return array(
				'state' => FALSE , 
				'message' => "Unable to locate the upgrade task with filename '$task'" 
			);
		}
	}

	/**
	 * get the latest tasks installed for upgrade.
	 *
	 * @return float
	 */
	function upgrade_version ()
	{
		$upgrades = $this->config->item('upgrade_tasks', 'dfg/upgrade');
		if(is_array($upgrades)){
			$latest_task = array_pop($upgrades);
			return $latest_task['version'];
		}
		return 0;
	}

	/**
	 * write the upgrade state into a dfg_config file
	 *
	 * @param boolean $written
	 */
	function save_upgrade_state ($version)
	{
		// write the config file here.
		$config_data_str = '<?php ' . "\n";
		
		$config_data_str .= '// Dezinerfolio.com - DfGallery Auto generated upgrade state config file' . "\n\n";
		
		$config_data_str .= '$config = ' . var_export(array(
			'upgrade_version' => $version 
		), TRUE) . ";\n\n";
		
		$config_data_str .= '?>';
		
		return write_file(UPGRADE_CONFIG_STATE_FILE, $config_data_str);
	}

	/**
	 * get all the pending upgrade tasks in an array for execution.
	 *
	 * @return array [string]
	 */
	function pending_upgrade_tasks ()
	{
		$upgrades = $this->config->item('upgrade_tasks', 'dfg/upgrade');
		
		$tasks = array( 
		);
		
		$current_version = $this->current_version();
		
		foreach ($upgrades as $task)
		{
			if ($task['version'] > $current_version)
			{
				array_push($tasks, $task);
			}
		}
		return $tasks;
	}

	/**
	 * return the current upgrade version.
	 *
	 * @return float
	 */
	function current_version ()
	{
		return $this->config->item('upgrade_version', 'dfg/upgrade_state');
	}

	/**
	 * returns true if any pending tasks are awaiting upgrade
	 *
	 * @return boolean
	 */
	function has_upgrades ()
	{
		return ($this->upgrade_version() > $this->current_version()) ? TRUE : FALSE;
	}
}
?>