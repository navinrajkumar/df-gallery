<?php

class Setup extends DF_Controller
{

	function index()
	{
		if ($this->config->item ( 'active_group', 'database' )) {
			$this->config_exists ();
		} else {
			$this->_create_config_form ();
		}
	}

	function _create_config_form()
	{
		$this->validation->set_rules ( array (
				'hostname' => 'required', 
				'username' => 'required', 
				'password' => 'required', 
				'database' => 'required', 
				//'dbprefix' => 'required' 
		) );
		$this->validation->set_fields ( array (
				'hostname' => 'Hostname', 
				'username' => 'Username', 
				'password' => 'Password', 
				'database' => 'Database', 
				//'dbprefix' => 'Database prefix' 
		) );
		if ($this->validation->run () == FALSE) {
			$this->do_header ( 'DfGallery 2 Admin Setup' );
			$this->load->view ( 'admin/setup/index', array (
					'error' => '' 
			) );
			$this->do_footer ();
		} else {
			$this->create_config_file ();
		}
	}

	function config_exists()
	{
		$this->do_header ( 'DfGallery 2 : Config Exists' );
		$this->load->view ( 'admin/setup/config_exists' );
		$this->do_footer ();
	}

	function create_config_file()
	{
		
		$db ['dfg'] ['hostname'] = $this->input->post ( 'hostname' );
		$db ['dfg'] ['username'] = $this->input->post ( 'username' );
		$db ['dfg'] ['password'] = $this->input->post ( 'password' );
		$db ['dfg'] ['database'] = $this->input->post ( 'database' );
		$db ['dfg'] ['dbdriver'] = $this->input->post ( 'dbdriver' );
		$db ['dfg'] ['dbprefix'] = $this->input->post ( 'dbprefix' );
		$db ['dfg'] ['pconnect'] = FALSE;
		$db ['dfg'] ['db_debug'] = FALSE;
		$db ['dfg'] ['cache_on'] = FALSE;
		$db ['dfg'] ['cachedir'] = "";
		$db ['dfg'] ['char_set'] = "utf8";
		$db ['dfg'] ['dbcollat'] = "utf8_general_ci";
		
		$dbo = $this->load->database ( $db ['dfg'], TRUE, FALSE );

		if (! $dbo->conn_id) {
			$this->do_header ( 'DfGallery 2 : Admin Setup' );
			$this->load->view ( 'templates/message', array (
					'level' => 'red', 
					'message' => 'Unable to connect to the database with the given credentials, or the installed ' . $db ['dfg'] ['dbdriver'] . ' Connector does not support authentication protocol.' 
			) );
			$this->load->view ( 'admin/setup/index' );
			$this->do_footer ();
			return;
		}else if($dbo->db_select() === FALSE){
			$this->do_header ( 'DfGallery 2 : Admin Setup' );
			$this->load->view ( 'templates/message', array (
					'level' => 'red', 
					'message' => 'Unable to select database "'. $db ['dfg'] ['database'] .'"'
			) );
			$this->load->view ( 'admin/setup/index' );
			$this->do_footer ();
			return;
		}
		
		if (file_exists ( DATABASE_CONFIG_FILE ) && $this->config->item ( 'active_group', 'database' ) !== FALSE) {
			$this->config_exists ();
			return;
		}
		
		$this->load->helpers ( array (
				'string', 
				'file' 
		) );
		
		$admin_username = 'admin';
		$admin_password = 'admin';//random_string ();
		
		// write the config file here.
		$config_data_str = '<?php ' . "\n";
		$config_data_str .= '// Dezinerfolio.com - DfGallery Auto generated config file' . "\n\n";
		$config_data_str .= '$db[\'dfg\'] = ' . var_export ( $db ['dfg'], TRUE ) . ";\n\n";
		$config_data_str .= '$active_group = "dfg"' . ";\n";
		$config_data_str .= '$active_record = TRUE' . ";\n";
		$config_data_str .= "\$config['active_group'] = 'dfg';\n";
		$config_data_str .= '?>';
		$file_written = write_file ( DATABASE_CONFIG_FILE, $config_data_str );
		if ($file_written) {
			chmod ( DATABASE_CONFIG_FILE, 0644 );
			$this->do_header ( 'DfGallery 2 : Created Config ' );
			$this->load->view ( 'templates/message', array (
					'level' => 'green', 
					'message' => 'Created database.php successfully.' 
			) );
			$this->_run_initial_upgrade ( $admin_username, $admin_password );
			$this->load->view ( 'admin/setup/success', array (
					'admin_username' => $admin_username, 
					'admin_password' => $admin_password 
			) );
			$this->do_footer ();
		} else {
			// unable to write
			$this->do_header ( 'DfGallery 2 : Setup Error' );
			$this->load->view ( 'admin/setup/index', array (
					'error' => 'Unable to write config file into "' . DATABASE_CONFIG_FILE . '"<br/>Please set the write permissions for the database.php file' 
			) );
			$this->do_footer ();
		}
	}

	function _run_initial_upgrade()
	{
		$this->load->library ( 'Upgrade_Manager' );
		$args = func_get_args ();
		$result = $this->upgrade_manager->execute ( '2000/Task_2000_1', $args );
		$this->upgrade_manager->save_upgrade_state ( 2.000 );
		if ($result ['state'] == TRUE) {
			$this->load->view ( 'templates/message', array (
					'level' => 'green', 
					'message' => $result ['message'] 
			) );
		} else {
			$this->load->view ( 'templates/message', array (
					'level' => 'red', 
					'message' => $result ['message'] 
			) );
		}
	}
}

?>