<?php

include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class Gallery extends DF_AdminController
{

	function index ()
	{
		$this->load->model('Gallery_CT_model', 'gallery_bean');
		
		$this->gallery_bean->uid = $this->uid;
		$galleries = $this->gallery_bean->load_all();
		
		$this->do_header('DfGallery 2 : Admin : Create a new Gallery');
		$this->load->view('/admin/gallery/index', array(
			'galleries' => $galleries 
		));
		
		$this->do_footer();
	}

	function add ()
	{
		$this->load->library('Theme_Manager');
		$themes = $this->theme_manager->get_themes();
		$this->if_redirect_message((sizeof($themes) == 0), 'red', 'No themes were found, please install atleast 1 theme to use the gallery');
		
		$this->validation->set_rules(array(
			'title' => 'required' 
		));
		$this->validation->set_fields(array(
			'title' => 'Gallery title' 
		));
		
		if ($this->validation->run() == FALSE) {
			$this->do_header('DfGallery 2 : Admin : Create a new Gallery', array(
				array(
					'title' => 'add' , 
					'url' => base_url() . 'admin/gallery/add' 
				) 
			));
			$this->load->view('admin/gallery/add', array(
				'themes' => $themes 
			));
			$this->do_footer();
		} else {
			$this->load->model('Gallery_CT_model', 'gallery_bean');
			$this->gallery_bean->uid = $this->uid;
			$this->gallery_bean->title = $this->input->post('title');
			$this->gallery_bean->save();
			$this->gallery_bean->properties->set('theme', $this->input->post('theme'));
			$this->gallery_bean->properties->set('skin', $this->input->post('skin'));
			$this->redirect_message('green', 'Gallery has been created.', 'admin/gallery');
		}
	}

	function edit ($id = -1)
	{
		$this->_load_gallery_bean($id);
		$this->load->library('Theme_Manager');
		$themes = $this->theme_manager->get_themes();
		$this->if_redirect_message((sizeof($themes) == 0), 'red', 'No themes were found, please install atleast 1 theme to use the gallery');
		
		$this->validation->set_rules(array(
			'title' => 'required' 
		));
		$this->validation->set_fields(array(
			'title' => 'Gallery title' 
		));
		
		if ($this->validation->run() == FALSE) {
			$this->do_header('DfGallery 2 : Admin : Edit Gallery', array(
				array(
					'title' => $this->gallery_bean->title , 
					'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
				) , 
				array(
					'title' => 'edit' , 
					'url' => base_url() . 'admin/gallery/edit/' . $this->gallery_bean->id 
				) 
			));
			$this->load->view('admin/gallery/edit', array(
				'themes' => $themes , 
				'gallery' => $this->gallery_bean 
			));
			$this->do_footer();
		} else {
			$this->gallery_bean->title = $this->input->post('title');
			$this->gallery_bean->update();
			$this->gallery_bean->properties->set('skin', $this->input->post('skin'));
			$theme_changed = ($this->input->post('theme') == $this->gallery_bean->properties->get_value('theme')) ? false : true;
			$executed = $this->gallery_bean->properties->set('theme', $this->input->post('theme'));
			if ($executed && $theme_changed) {
				$this->gallery_bean->properties->delete_all_like('config_theme_%');
			}
			$this->redirect_message('green', 'The gallery has been saved.', 'admin/gallery');
		}
	}

	function delete ($id = -1)
	{
		$this->_load_gallery_bean($id);
		$state = $this->gallery_bean->delete();
		$this->if_redirect_message($state, 'green', 'The gallery has been deleted', 'admin/gallery', 'red', 'Unable to delete the gallery.<br/>Please try again.');
	}

	function generate ($id = -1)
	{
		$this->_load_gallery_bean($id);
		$this->do_header('DfGallery 2 : Admin : generate embed code', array(
			array(
				'title' => $this->gallery_bean->title , 
				'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
			) , 
			array(
				'title' => 'generate code' , 
				'url' => base_url() . 'admin/gallery/generate/' . $this->gallery_bean->id
			) 
		));
		$this->load->view('admin/gallery/generate', array(
			'gallery' => $this->gallery_bean 
		));
		$this->do_footer();
	}

	function properties ($id = null)
	{
		$this->_load_gallery_bean($id);
		$this->load->library('Theme_Manager');
		$theme_name = $this->gallery_bean->properties->get_value('theme');
		
		$properties_sections = $this->theme_manager->get_theme_properties($theme_name);
		$properties = array( 
		);
		foreach ($properties_sections as $section) {
			foreach ($section['properties'] as $property) {
				array_push($properties, $property);
			}
		}
		if (sizeof($properties) == 0) {
			$this->do_header('DfGallery 2 : Admin : Edit Gallery Properties');
			$this->load->view('templates/message', array(
				'level' => 'red' , 
				'message' => 'No configurable properties where found for the selected theme.' 
			));
			$this->do_footer();
			return;
		}
		
		$db_properties = $this->gallery_bean->properties->get_all_config();
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
		
		if ($this->validation->run() == FALSE) {
			$this->do_header('DfGallery 2 : Admin : Gallery Settings ', array(
				array(
					'title' => $this->gallery_bean->title , 
					'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
				) , 
				array(
					'title' => 'config' , 
					'url' => base_url() . 'admin/gallery/properties/' . $this->gallery_bean->id 
				) 
			));
			$this->load->view('templates/heading', array(
				'title' => 'Gallery settings : ' . $this->gallery_bean->title 
			));
			$this->load->view('templates/admin/properties', array(
				'form_action' => '/admin/gallery/properties/' . $id , 
				'submit_label' => 'Save Config' , 
				'properties_sections' => $properties_sections 
			));
			$this->do_footer();
		} else {
			foreach ($properties_sections as $section) {
				foreach ($section['properties'] as $property) {
					$this->gallery_bean->properties->set($property['name'], $this->input->post($property['name']));
				}
			}
			$this->redirect_message('green', 'The gallery config been saved.','admin/gallery');
		}
	}

	function _load_gallery_bean ($id)
	{
		$this->load->model('Gallery_CT_model', 'gallery_bean');
		$this->gallery_bean->id = $id;
		$this->gallery_bean->uid = $this->uid;
		$loaded = $this->gallery_bean->load();
		$this->if_redirect_message(! $loaded, 'red', 'No gallery found with the specified ID');
	}
}

?>