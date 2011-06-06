<?php

include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class Upgrade extends DF_AdminController  {
	
	function Upgrade() {
		parent::DF_Controller ();
		$this->load->library ( 'Upgrade_Manager' );
	}
	
	function index() {
		$this->_check_if_latest ();
		$this->do_header ( 'DfGallery 2 :Upgrade' );
		$current_version = $this->upgrade_manager->current_version ();
		$new_version = $this->upgrade_manager->upgrade_version ();
		$tasks = $this->upgrade_manager->pending_upgrade_tasks ();
		$this->load->view ( 'admin/upgrade/index', array ('current_version' => $current_version, 'new_version' => $new_version, 'tasks' => $tasks ) );
		$this->do_footer ();
	}
	
	function _check_if_latest() {
		if (! $this->upgrade_manager->has_upgrades ()) {
			$this->redirect_message ( 'green', 'The latest version of the gallery has been installed and upgraded on your system.' , 'admin/' );
		}
	}
	
	function run() {
		$this->_check_if_latest ();
		$this->do_header ( 'DfGallery 2 :Upgrade Execution' );
		$this->load->view ( 'templates/message', array ('level' => 'grey', 'message' => 'Executing Upgrade Tasks' ) );
		
		$tasks = $this->upgrade_manager->pending_upgrade_tasks ();
		$upgrade_result = TRUE;
		foreach ( $tasks as $task ) {
			$result = $this->upgrade_manager->execute ( $task ['file'] );
			if ($result ['state'] == FALSE) {
				$upgrade_result = FALSE;
				$this->load->view ( 'templates/message', array ('level' => 'red', 'message' => $result ['message'] ) );
				break;
			}
			$this->upgrade_manager->save_upgrade_state ( $task ['version'] );
			$this->load->view ( 'templates/message', array ('level' => ($result ['state'] == TRUE) ? 'green' : 'red', 'message' => $result ['message'] ) );
		}
		
		$this->load->view ( 'admin/upgrade/run', array ('upgrade_result' => $upgrade_result ) );
		$this->do_footer ();
	}
}

?>