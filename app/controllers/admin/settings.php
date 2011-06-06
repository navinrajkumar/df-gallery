<?php
include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class Settings extends DF_AdminController
{

	function edit ()
	{
		
		$properties_sections = $this->config->item('system_properties', 'dfg/properties');
		$properties = array( 
		);
		foreach ($properties_sections as $section) {
			foreach ($section['properties'] as $property) {
				array_push($properties, $property);
			}
		}
		$this->do_header('DfGallery 2 : Admin : Settings', array(
			array(
				'title' => 'settings' , 
				'url' => base_url() . 'admin/settings/edit/' 
			) 
		));
		
		if (sizeof($properties) == 0) {
			
			$this->load->view('templates/message', array(
				'level' => 'red' , 
				'message' => 'No configurable settings where found for this installation.' 
			));
			$this->do_footer();
			return;
		}
		$this->load->model('SystemSettings_model', 'system_settings');
		$db_properties = $this->system_settings->get_all();
		foreach ($db_properties as $db_property) {
			$prop_name = $db_property->name;
			$this->validation->$prop_name = $db_property->value;
		}
		$rules = array( 
		);
		foreach ($properties as $property) {
			$prop_name = $property['name'];
			if (isset($property['rule'])) {
				$rules[$prop_name] = $property['rule'];
			}
			$value = $this->input->post($prop_name);
			if ($value) {
				$this->validation->$prop_name = $value;
			} else {
				if (! isset($this->validation->$prop_name)) {
					$this->validation->$prop_name = $property['default_value'];
				}
			}
		}
		
		$this->validation->set_rules($rules);
		$this->validation->set_message('required', 'this field cannot be left blank, currently setting default value.');
		$this->validation->set_message('min_length', 'this field must be atleast <span class="hide">%s</span> %s characters.');
		
		if ($this->validation->run() == FALSE) {
			$this->load->view('templates/heading', array(
				'title' => 'Settings' 
			));
			$this->load->view('templates/admin/properties', array(
				'form_action' => '/admin/settings/edit' , 
				'submit_label' => 'Update Settings' , 
				'properties_sections' => $properties_sections 
			));
			$this->do_footer();
		} else {
			foreach ($properties_sections as $section) {
				foreach ($section['properties'] as $property) {
					$this->system_settings->set($property['name'], $this->input->post($property['name']));
				}
			}
			$this->redirect_message('green', 'All the settings been saved.', 'admin/gallery');
		}
	
	}
}

?>