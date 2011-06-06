<?php

/**
 * the most core upgrade task that creates the user table with the requires user fields.
 * it also creates the first 'admin' user account with the params passed to it.
 */
class Task_2000_1
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
	function Task_2000_1()
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
	function run($params)
	{
		$this->CI->load->database ();
		
		$queries = array ( 
		);
		
		$errors = array ( 
		);
		
		// create user table
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->CI->db->prep_tablename ( 'users' ) . "(";
		$sql .= "  id int(10) unsigned NOT NULL auto_increment,";
		$sql .= "  username varchar(60) NOT NULL,";
		$sql .= "  password varchar(64) NOT NULL,";
		$sql .= "  mail varchar(100) default NULL,";
		$sql .= "  created int(10) unsigned default NULL,";
		$sql .= "  PRIMARY KEY  (id)";
		$sql .= ");";
		
		array_push ( $queries, array (
				$sql, 
				'Unable to create the users table' 
		) );
		
		// create admin user
		$this->CI->load->library ( 'Encrypt' );
		
		$pssword_hash = $this->CI->encrypt->hash ( $params [1] );
		
		$res = $this->CI->db->query ( "SELECT id from " . $this->CI->db->prep_tablename ( 'users' ) . " WHERE username='admin';" );

		if (! $res) {
			$sql = $this->CI->db->insert_string ( 'users', array (
					'username' => $params [0], 
					'password' => $pssword_hash, 
					'created' => time () 
			) );
			
			array_push ( $queries, array (
					$sql, 
					'Unable to create the ' . $params [0] . ' user.' 
			) );
		} else {
			$sql = $this->CI->db->update_string ( 'users', array (
					'password' => $pssword_hash, 
					'created' => time () 
			), array (
					'username' => $params [0] 
			) );
			
			array_push ( $queries, array (
					$sql, 
					'Unable to update the ' . $params [0] . ' user.' 
			) );
		}
		
		// create content table
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->CI->db->prep_tablename ( 'content' ) . "(";
		$sql .= "  id int(10) unsigned NOT NULL auto_increment,";
		$sql .= "    uid int(10) unsigned NOT NULL,";
		$sql .= "    pid int(10) unsigned default NULL,";
		$sql .= "    type varchar(32) NOT NULL,";
		$sql .= "    title varchar(128) NOT NULL,";
		$sql .= "    PRIMARY KEY  (id)";
		$sql .= "  );";
		
		array_push ( $queries, array (
				$sql, 
				'Unable to create the content table' 
		) );
		
		// create contentprop table
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->CI->db->prep_tablename ( 'contentprop' ) . "(";
		$sql .= "    id int(10) unsigned NOT NULL auto_increment,";
		$sql .= "    cid int(10) unsigned NOT NULL,";
		$sql .= "    name varchar(100) NOT NULL,";
		$sql .= "    value longtext,";
		$sql .= "    PRIMARY KEY  (id)";
		$sql .= "  );";
		
		array_push ( $queries, array (
				$sql, 
				'Unable to create the content properties table' 
		) );
		
		// create system properties table
		$sql = "CREATE TABLE IF NOT EXISTS " . $this->CI->db->prep_tablename ( 'systemprop' ) . "(";
		$sql .= "  name VARCHAR(100) NOT NULL,";
		$sql .= "  value LONGTEXT,";
		$sql .= "  PRIMARY KEY  (name)";
		$sql .= ");";
		array_push ( $queries, array (
				$sql, 
				'Unable to create the system properties table' 
		) );
		
		// add key constraints...
		

		$db_prefix = $this->CI->db->dbprefix;
		
		$sql = "ALTER TABLE " . $this->CI->db->prep_tablename ( 'content' );
		$sql .= "    ADD CONSTRAINT FK_" . $db_prefix . "_pid_id FOREIGN KEY FK_" . $db_prefix . "_pid_id (pid)";
		$sql .= "    REFERENCES " . $this->CI->db->prep_tablename ( 'content' ) . " (id)";
		$sql .= "    ON DELETE RESTRICT";
		$sql .= "    ON UPDATE RESTRICT,";
		$sql .= "    ADD CONSTRAINT FK_" . $db_prefix . "_uid_uid FOREIGN KEY FK_" . $db_prefix . "_uid_uid (uid)";
		$sql .= "    REFERENCES " . $this->CI->db->prep_tablename ( 'users' ) . " (id)";
		$sql .= "    ON DELETE CASCADE";
		$sql .= "    ON UPDATE RESTRICT;";
		array_push ( $queries, array (
				$sql, 
				'Unable to add foreign key constraints to content table' 
		) );
		
		$sql = "ALTER TABLE " . $this->CI->db->prep_tablename ( 'contentprop' );
		$sql .= "    ADD CONSTRAINT FK_" . $db_prefix . "_cid_id FOREIGN KEY FK_" . $db_prefix . "_cid_id (cid)";
		$sql .= "    REFERENCES " . $this->CI->db->prep_tablename ( 'content' ) . " (id)";
		$sql .= "    ON DELETE CASCADE";
		$sql .= "    ON UPDATE RESTRICT;";
		array_push ( $queries, array (
				$sql, 
				'Unable to add foreign key constraints to contentprop table' 
		) );
		
		// add required system properties
		$sql = $this->CI->db->insert_string ( 'systemprop', array (
				'name' => 'flickr_api_key', 
				'value' => 'dc123ae6ab78886c452b7ad44ec171c6' 
		) );
		
		array_push ( $queries, array (
				$sql, 
				'Unable set the flick API key to be used. you can configure this in the gallery settings later.',false
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
				'message' => 'Installed the core gallery module successfully.' 
		);
	}
}

?>