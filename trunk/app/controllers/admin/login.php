<?php

class Login extends DF_Controller {
	
	function index() {
		$this->validation->set_rules ( array ('username' => 'required', 'password' => 'required' ) );
		$this->validation->set_fields ( array ('username' => 'Username', 'password' => 'Password' ) );
		
		if ($this->validation->run () == FALSE) {
			$this->load->view ( 'templates/header', array ('title' => 'DfGallery 2 : Login' ) );
			$this->do_status();
			$this->load->view ( 'admin/login/index' );
			$this->load->view ( 'templates/footer' );
		} else {
			$this->load->library ( 'Encrypt' );
			$this->load->model ( 'User_model', 'user' );
			$uid = $this->user->validate_credentials ( $this->input->post ( 'username' ), $this->encrypt->hash ( $this->input->post ( 'password' ) ) );
			if ($uid > - 1) {
				$this->session->set_userdata ( 'isAuthenticated', true );
				$this->session->set_userdata ( 'uid', $uid );
				redirect ( 'admin' );
			} else {
				$this->load->view ( 'templates/header', array ('title' => 'DfGallery 2 : Login' ) );
				$this->do_status();
				$this->load->view ( 'templates/message', array ('level' => 'red', 'message' => 'Unable to login with the give username and password.' ) );
				$this->load->view ( 'admin/login/index' );
				$this->load->view ( 'templates/footer' );
			}
		}
	}
	function logout() {
		$this->session->destroy ();
		redirect ( 'admin/login' );
	}
}

?>