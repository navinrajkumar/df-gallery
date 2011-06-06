<?php

/**
 * this upgrade creates the required content, content_prop and systemprop tables.
 */
class Task_2001_1
{

	/**
	 * Instance of the current controller.
	 *
	 * @var Controller
	 */
	var $CI;

	/**
	 * return a simple task that can be executed by Upgrade manager.
	 *
	 * @return Task_0
	 */
	function Task_2001_1()
	{
	}

	/**
	 * the task execution that will be invoked by the Upgrade mangaer.
	 * We will write all the db queries, and data manuplation within the run.
	 *
	 * @param array $params
	 * @return array a status array.
	 */
	function run()
	{
		return array (
				'state' => TRUE, 
				'message' => 'Installed the test module successfully.' 
		);
	}
}

?>