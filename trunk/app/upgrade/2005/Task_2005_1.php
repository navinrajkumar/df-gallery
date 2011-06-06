<?php

/**
 * this upgrade creates the required content, content_prop and systemprop tables.
 */
class Task_2005_1
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
	function Task_2005_1()
	{
		$this->CI = & get_instance ();
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
		$this->CI->load->database ();
		
		$queries = array ( 
		);
		
		$errors = array ( 
		);
		
		$db_prefix = $this->CI->db->dbprefix;
		
		
		// ALTER THE FOREIGN KEYS FOR THE CONTENT TABLE
		$sql = "ALTER TABLE " . $this->CI->db->prep_tablename ( 'content' );
		$sql .= "    DROP FOREIGN KEY FK_" . $db_prefix . "_pid_id;";
		array_push ( $queries, array (
				$sql, 
				'Unable to delete foreign key constraint "_pid_id" on "content" table' 
		) );
		$sql = "ALTER TABLE " . $this->CI->db->prep_tablename ( 'content' );
		$sql .= "    DROP FOREIGN KEY FK_" . $db_prefix . "_uid_uid;";
		array_push ( $queries, array (
				$sql, 
				'Unable to delete foreign key constraint "_uid_uid" on "content"" table' 
		) );
		
		$sql = "ALTER TABLE " . $this->CI->db->prep_tablename ( 'content' );
		$sql .= "    ADD CONSTRAINT FK_" . $db_prefix . "_pid_id FOREIGN KEY FK_" . $db_prefix . "_pid_id (pid)";
		$sql .= "    REFERENCES " . $this->CI->db->prep_tablename ( 'content' ) . " (id)";
		$sql .= "    ON DELETE CASCADE";
		$sql .= "    ON UPDATE RESTRICT,";
		$sql .= "    ADD CONSTRAINT FK_" . $db_prefix . "_uid_uid FOREIGN KEY FK_" . $db_prefix . "_uid_uid (uid)";
		$sql .= "    REFERENCES " . $this->CI->db->prep_tablename ( 'users' ) . " (id)";
		$sql .= "    ON DELETE CASCADE";
		$sql .= "    ON UPDATE RESTRICT;";
		array_push ( $queries, array (
				$sql, 
				'Unable to add foreign key constraints to the "content" table' 
		) );
		

		//
		// EXECUTE ALL QUERIES
		//
		foreach ( $queries as $query ) {
			
			$res = $this->CI->db->query ( $query [0] );
			if (DEV_MODE){
				echo $query[0]."<br/>\n";
			}
			if ($res !== TRUE) {
				$log_error = true;
				if (sizeof($query)>1){
					$log_error = ($query[2] == true); 
				}
				if ($log_error){
					array_push ( $errors, $query [1] . $this->CI->db->_error_message () );
				}
			}
		}
		
		if (sizeof ( $errors ) > 0) {
			return array (
					'state' => FALSE, 
					'message' => implode ( "\n", $errors ) 
			);
		}
		
		return array (
				'state' => TRUE, 
				'message' => 'Updated the content table to cascade "delete" records.' 
		);
	}
}

?>