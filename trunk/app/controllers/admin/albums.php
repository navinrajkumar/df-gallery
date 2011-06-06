<?php

include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class Albums extends DF_AdminController
{

	function index ($gid = null)
	{
		$this->_load_gallery_bean($gid);
		
		$this->load->model('Album_CT_model', 'album_bean');
		$this->album_bean->uid = $this->uid;
		$albums = $this->album_bean->load_all(array(
			'pid' => $gid 
		));
		
		foreach ($albums as $album) {
			$album->properties->load('album_type');
		}
		
		$header_breadcrumbs = array(
			array(
				'title' => $this->gallery_bean->title , 
				'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
			) 
		);
		
		$albums_index_vars = array(
			'gallery' => $this->gallery_bean , 
			'albums' => $albums 
		);
		
		$this->do_header('DfGallery 2 : Admin : Create a new Gallery', $header_breadcrumbs);
		$this->load->view('/admin/albums/index', $albums_index_vars);
		$this->do_footer();
	}

	function add ($gid = null)
	{
		$this->_load_gallery_bean($gid);
		
		$this->validation->set_rules(array(
			'title' => 'required' 
		));
		$this->validation->set_fields(array(
			'title' => 'Album title' 
		));
		
		$album_types = $this->config->item('album_types', 'dfg/properties');
		
		if ($this->validation->run() == FALSE) {
			$this->do_header('DfGallery 2 : Admin : Create a new Album', array(
				array(
					'title' => $this->gallery_bean->title , 
					'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
				) , 
				array(
					'title' => 'add' , 
					'url' => base_url() . 'admin/albums/add/' . $this->gallery_bean->id 
				) 
			));
			$this->load->view('admin/albums/add', array(
				'gallery' => $this->gallery_bean , 
				'album_types' => $album_types 
			));
			$this->do_footer();
		} else {
			$this->load->model('Album_CT_model', 'album_bean');
			$this->album_bean->pid = $gid;
			$this->album_bean->title = $this->input->post('title');
			$this->album_bean->save();
			$this->album_bean->properties->set('album_type', $this->input->post('album_type'));
			$this->redirect_message('green', 'A new album has been created under :' . $this->gallery_bean->title, '/admin/albums/index/' . $this->gallery_bean->id);
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

	function _load_album_bean ($id)
	{
		$this->load->model('Album_CT_model', 'album_bean');
		$this->album_bean->id = $id;
		$this->album_bean->uid = $this->uid;
		$loaded = $this->album_bean->load();
		$this->if_redirect_message(! $loaded, 'red', 'No album found with the specified ID');
	}

	function edit ($id = -1)
	{
		$this->_load_album_bean($id);
		$this->_load_gallery_bean($this->album_bean->pid);
		
		$this->validation->set_rules(array(
			'title' => 'required' 
		));
		$this->validation->set_fields(array(
			'title' => 'Album title' 
		));
		$album_types = $this->config->item('album_types', 'dfg/properties');
		if ($this->validation->run() == FALSE) {
			$this->do_header('DfGallery 2 : Admin : Edit Gallery', array(
				array(
					'title' => $this->gallery_bean->title , 
					'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
				) , 
				array(
					'title' => $this->album_bean->title , 
					'url' => ($this->album_bean->properties->get_value('album_type') == 'custom') ? (base_url() . 'admin/manage/index/' . $this->album_bean->id) : (base_url() . 'admin/albums/edit/' . $this->album_bean->id) 
				) , 
				array(
					'title' => 'edit' , 
					'url' => base_url() . 'admin/albums/edit/' . $this->album_bean->id 
				) 
			));
			$this->load->view('admin/albums/edit', array(
				'gallery' => $this->gallery_bean , 
				'album' => $this->album_bean , 
				'album_types' => $album_types 
			));
			$this->do_footer();
		} else {
			$this->album_bean->title = $this->input->post('title');
			$this->album_bean->update();
			$this->redirect_message('green', 'The album changes has been saved.', 'admin/albums/index/' . $this->album_bean->pid);
		}
	}

	function delete ($id)
	{
		$this->_load_album_bean($id);
		$state = $this->album_bean->delete();
		$this->if_redirect_message($state, 'green', 'Album has been deleted.', 'admin/albums/index/' . $this->album_bean->pid, 'red', 'Unabled to delete the album<br/>Please try again.');
	}

	function properties ($id = -1)
	{
		$this->_load_album_bean($id);
		$this->_load_gallery_bean($this->album_bean->pid);
		$album_type = $this->album_bean->properties->get_value('album_type');
		$properties_sections = $this->config->item('album_properties_' . $album_type, 'dfg/properties');
		$properties = array( 
		);
		if (is_array($properties_sections)) {
			foreach ($properties_sections as $section) {
				foreach ($section['properties'] as $property) {
					array_push($properties, $property);
				}
			}
		}
		
		$this->do_header('DfGallery 2 : Admin : Edit album properties', array(
			array(
				'title' => $this->gallery_bean->title , 
				'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id 
			) , 
			array(
				'title' => $this->album_bean->title , 
				'url' => ($this->album_bean->properties->get_value('album_type') == 'custom') ? (base_url() . 'admin/manage/index/' . $this->album_bean->id) : (base_url() . 'admin/albums/edit/' . $this->album_bean->id) 
			) , 
			array(
				'title' => 'config' , 
				'url' => base_url() . 'admin/albums/properties/' . $this->album_bean->id 
			) 
		));
		
		if (sizeof($properties) == 0) {
			$this->load->view('templates/message', array(
				'level' => 'red' , 
				'message' => 'No configurable properties for the type of album you selected.' 
			));
			$this->do_footer();
			return;
		}
		
		$db_properties = $this->album_bean->properties->get_all_config();
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
				'title' => 'Album settings : ' . $this->album_bean->title 
			));
			$this->load->view('templates/admin/properties', array(
				'form_action' => '/admin/albums/properties/'  . $id , 
				'submit_label' => 'Save album config' , 
				'properties_sections' => $properties_sections 
			));
			$this->do_footer();
		} else {
			foreach ($properties_sections as $section) {
				foreach ($section['properties'] as $property) {
					$this->album_bean->properties->set($property['name'], $this->input->post($property['name']));
				}
			}
			$this->redirect_message('green', 'The album configurations been saved.', 'admin/albums/index/' . $this->album_bean->pid);
		}
	}

}
?>
