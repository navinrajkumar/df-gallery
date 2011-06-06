<?php

class ResetPassword extends DF_Controller {
	
	function index() {
		$configured_password = $this->config->item('configured_password', 'dfg/resetpassword');
		if ($configured_password !== FALSE) {
				
			$properties_sections['change_password'] = array(
				'name' => 'Change Password' , 
				'description' => 'You could change the admin password below.' 
			);
			
			$properties_sections['change_password']['properties'][] = array(
				'display_name' => 'old password as in the config file' , 
				'type' => 'password' , 
				'name' => 'current_password' , 
				'default_value' => '' , 
				'rule' => 'required|min_length[5]' 
			);
			
			$properties_sections['change_password']['properties'][] = array(
				'display_name' => 'new password' , 
				'type' => 'password' , 
				'name' => 'new_password' , 
				'default_value' => '' , 
				'rule' => 'required|min_length[5]' 
			);
			
			$properties_sections['change_password']['properties'][] = array(
				'display_name' => 'confirm password' , 
				'type' => 'password' , 
				'name' => 'confirm_password' , 
				'default_value' => '' , 
				'rule' => 'required|min_length[5]|matches[new_password]' 
			);
			
			$properties = array( 
			);
			foreach ($properties_sections as $section) {
				foreach ($section['properties'] as $property) {
					array_push($properties, $property);
				}
			}
			$rules = array( 
			);
			foreach ($properties as $property) {
				$prop_name = $property['name'];
				if (isset($property['rule'])) {
					$rules[$prop_name] = $property['rule'];
				}
			}
			
			$this->validation->set_rules($rules);
			$this->validation->set_message('required', 'this field cannot be left blank, currently setting default value.');
			$this->validation->set_message('min_length', 'this field must be atleast <span class="hide">%s</span> %s characters.');
			
			if ($this->validation->run() == FALSE) {
				$this->do_header('DfGallery 2 : Admin : Reset Password ', array(
					array(
						'title' => 'profile' , 
						'url' => base_url() . 'admin/resetpassword' 
					) 
				));
				$this->load->view('templates/heading', array(
					'title' => 'Admin Password Reset' 
				));
				$this->load->view('templates/admin/properties', array(
					'form_action' => '/admin/resetpassword' , 
					'submit_label' => 'Update password' , 
					'properties_sections' => $properties_sections 
				));
				$this->do_footer();
			} else {
				$this->load->model('User_model', 'user');
				$this->user->load_where(array('username'=>'admin'));
				$state = $configured_password == $this->input->post('current_password');
				if($state){
					$this->load->library('Encrypt');
					$this->user->password = $this->encrypt->hash($this->input->post('new_password'));
					$state = $this->user->save();
				}else{
				}
				$this->if_redirect_message($state !== FALSE, 'green', 'Password has been successfully changed.','admin/login', 'red', 'Unable to update password.<br/>current user password doesn\'t match with the configured password.');
			}
		}else{
			$this->do_header('DfGallery 2 : Admin : Reset Password ', array(
				array(
					'title' => 'profile' , 
					'url' => base_url() . 'admin/resetpassword' 
				) 
			));
			$this->load->view('templates/heading', array(
				'title' => 'Admin Password Reset' 
			));
			
			$this->load->view('templates/message', array(
				'level' => 'red',
				'message' => 'Reset password has been disabled in the configuration, please enable the required configuration settings in ' . 
				'the config folder if you wish to to reset the admin password.<br/><br/>'.
				' For more details please visit <a href="http://dezinerfolio.com">Dezinerfolio.com</a>' 
			));
			$this->do_footer();
		}
	}
}

?>