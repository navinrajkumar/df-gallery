<?php
include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class User extends DF_AdminController
{

	function edit ()
	{
		
		$properties_sections['change_password'] = array(
			'name' => 'Change Password' , 
			'description' => 'You could change the admin password below.' 
		);
		
		$properties_sections['change_password']['properties'][] = array(
			'display_name' => 'old password' , 
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
			$this->do_header('DfGallery 2 : Admin : Settings ', array(
				array(
					'title' => 'profile' , 
					'url' => base_url() . 'admin/user/edit' 
				) 
			));
			$this->load->view('templates/heading', array(
				'title' => 'Profile settings' 
			));
			$this->load->view('templates/admin/properties', array(
				'form_action' => '/admin/user/edit' , 
				'submit_label' => 'Update password' , 
				'properties_sections' => $properties_sections 
			));
			$this->do_footer();
		} else {
			$this->load->model('User_model', 'user');
			$this->load->library('Encrypt');
			$this->user->id = $this->uid;
			$this->user->load();
			$state = $this->user->update_password($this->encrypt->hash($this->input->post('current_password')), $this->encrypt->hash($this->input->post('new_password')));
			$this->if_redirect_message($state, 'green', 'Password has been successfully changed.','admin/user/edit/success', 'red', 'Unable to update password.<br/>current user password doesn\'t match with the database.');
		}
	
	}
}

?>