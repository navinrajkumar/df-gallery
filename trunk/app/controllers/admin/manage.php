<?php

include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class Manage extends DF_AdminController
{

	function index ($aid = -1, $page = 1)
	{
		$this->_is_gd_loaded();
		$this->_load_album_bean($aid);
		$this->_load_gallery_bean($this->album_bean->pid);
		$this->load->model('Image_CT_model', 'image_bean');
		$this->load->model('SystemSettings_model', 'system_settings');
		
		$images_per_page = intval($this->system_settings->get_value('album_num_images'));
		$images_per_page = ($images_per_page == 0) ? 10 : $images_per_page;
		$this->image_bean->pid = $aid;
		$total_images = $this->image_bean->count();
		$images = array();
		
		$this->image_bean->uid = $this->uid;
		$images = $this->image_bean->load_all(array(
			'pid' => $aid), $images_per_page, ($page - 1) * $images_per_page);
		if (sizeof($images) == 0 && page > 1)
		{
			redirect("admin/manage/index/$aid/1/");
		}
		$this->do_header('DfGallery 2 : Admin : Manage album', array(
			array(
				'title' => $this->gallery_bean->title , 
				'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id) , 
			array(
				'title' => $this->album_bean->title , 
				'url' => ($this->album_bean->properties->get_value('album_type') == 'custom') ? (base_url() . 'admin/manage/index/' . $this->album_bean->id) : (base_url() . 'admin/albums/edit/' . $this->album_bean->id)) , 
			array(
				'title' => 'manage' , 
				'url' => base_url() . 'admin/albums/manage/index' . $this->album_bean->id)));
		

		$this->load->view('admin/manage/index', array(
			'album' => $this->album_bean , 
			'total_images' => $total_images , 
			'current_page' => $page , 
			'images_per_page' => $images_per_page , 
			'images' => $images));
		
		$this->do_footer();
	}

	
	function import ($aid = -1)
	{
		$this->_is_gd_loaded();
		$this->_load_album_bean($aid);
		$this->_load_gallery_bean($this->album_bean->pid);
		
		$this->validation->set_rules(array(
			'folder' => 'required|min_length[2]|callback__folder_exists'));
		
		$this->validation->set_fields(array(
			'folder' => 'folder'));
		
		if ($this->validation->run() == FALSE)
		{
			
			$this->do_header('DfGallery 2 : Admin : Import images from folder', array(
				array(
					'title' => $this->gallery_bean->title , 
					'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id) , 
				array(
					'title' => $this->album_bean->title , 
					'url' => ($this->album_bean->properties->get_value('album_type') == 'custom') ? (base_url() . 'admin/manage/index/' . $this->album_bean->id) : (base_url() . 'admin/albums/edit/' . $this->album_bean->id)) , 
				array(
					'title' => 'import' , 
					'url' => base_url() . 'admin/manage/import/' . $this->album_bean->id)));
			

			$this->load->view('admin/manage/import', array(
				'album' => $this->album_bean));
			
			$this->do_footer();
		
		} else
		{
			
			$this->load->helper('file');
			$this->load->library('Image_Manager');
			
			$folder = $this->input->post('folder');
			$folder = rtrim(realpath($folder), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
			
			$uploadable_files = $this->image_manager->get_importable_files($folder);
			foreach ($uploadable_files as $key => $file)
			{
				$file['file_uri'] = str_replace('\\', '\\\\', $folder . $file['file']);
				$uploadable_files[$key] = $file;
			}
			
			$this->if_redirect_message((sizeof($uploadable_files) == 0), 'red', 'Unable to find any files within ' . $folder . ' that can be uploadable as images<br/>The supported formats are ' . join(',', $this->image_manager->allowed_types));
			
			$this->do_header('DfGallery 2 : Admin : Import images from folder', array(
				array(
					'title' => $this->gallery_bean->title , 
					'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id) , 
				array(
					'title' => $this->album_bean->title , 
					'url' => ($this->album_bean->properties->get_value('album_type') == 'custom') ? (base_url() . 'admin/manage/index/' . $this->album_bean->id) : (base_url() . 'admin/albums/edit/' . $this->album_bean->id)) , 
				array(
					'title' => 'import' , 
					'url' => base_url() . 'admin/manage/import/' . $this->album_bean->id)));
			
			$this->load->view('admin/manage/import_progress', array(
				'album' => $this->album_bean , 
				'uploadable_files' => $uploadable_files , 
				'folder' => $folder));
			$this->do_footer();
		}
	}

	function import_file ($aid)
	{
		$file = array(
			'title' => $this->input->post('title') , 
			'file' => $this->input->post('file') , 
			'img_name' => $this->input->post('img_name'));
		
		$result = array(
			'level' => 'red' , 
			'message' => 'Unable to import file.');
		
		if (file_exists($file['file']) && $file['img_name'])
		{
			
			$this->_load_album_bean($aid);
			
			$this->load->library('Image_Manager');
			
			$this->album_bean->properties->get_all_config();
			
			$sizes = array(
				'thumbnail_width' => $this->album_bean->properties->get_value('config_thumbnail_width', 100) , 
				'thumbnail_height' => $this->album_bean->properties->get_value('config_thumbnail_height', 75) , 
				'image_width' => $this->album_bean->properties->get_value('config_image_width', 1024) , 
				'image_height' => $this->album_bean->properties->get_value('config_image_height', 768));
			
			$this->image_manager->initialize($this->uid, $aid, $sizes);
			
			copy($file['file'], UPLOAD_IMAGES_ORIGNAL_FOLDER . $file['img_name']);
			
			$result = $this->image_manager->publish_image($file);
		
		}
		$this->load->view('templates/message', $result);
	}

	
	function upload ($aid = -1)
	{
		$this->_is_gd_loaded();
		$this->_load_album_bean($aid);
		$this->do_header('DfGallery 2 : Admin : Upload images', array(
			array(
				'title' => $this->gallery_bean->title , 
				'url' => base_url() . 'admin/albums/index/' . $this->gallery_bean->id) , 
			array(
				'title' => $this->album_bean->title , 
				'url' => ($this->album_bean->properties->get_value('album_type') == 'custom') ? (base_url() . 'admin/manage/index/' . $this->album_bean->id) : (base_url() . 'admin/albums/edit/' . $this->album_bean->id)) , 
			array(
				'title' => 'upload' , 
				'url' => base_url() . 'admin/manage/upload/' . $this->album_bean->id)));
		if (ini_get('safe_mode'))
		{
			$this->load->view('templates/message', array(
				'level' => 'grey' , 
				'message' => 'PHP safe_mode is enabled. If you are importing/uploading a largeimages you might want to disable safe_mode so that the script can execute for more than 30 seconds.'));
		}
		$this->load->view('admin/manage/upload', array(
			'album' => $this->album_bean));
		$this->load->view('templates/message', array(
			'level' => 'none' , 
			'message' => "\nIf you want to upload files larger than  " . ini_get('upload_max_filesize') . ", please increase the upload size limit in your php.ini or contact your site administartor."));
		$this->do_footer();
	}

	function upload_file ($aid = -1, $counter = 1)
	{
		$msg = $this->load->view('templates/message', array(
			'level' => 'red' , 
			'message' => "No file field specified."), TRUE);
		
		if (isset($_FILES['input_image_' . $counter]))
		{
			$this->_is_gd_loaded();
			$this->_load_album_bean($aid);
			
			$this->load->library('Image_Manager');
			$this->album_bean->properties->get_all_config();
			$sizes = array(
				'thumbnail_width' => $this->album_bean->properties->get_value('config_thumbnail_width', 100) , 
				'thumbnail_height' => $this->album_bean->properties->get_value('config_thumbnail_height', 75) , 
				'image_width' => $this->album_bean->properties->get_value('config_image_width', 1024) , 
				'image_height' => $this->album_bean->properties->get_value('config_image_height', 768));
			
			$this->image_manager->initialize($this->uid, $aid, $sizes);
			$result = $this->image_manager->upload_file($this->input->post('input_title'), 'input_image_' . $counter);
			
			if ($result['img_name'])
			{
				$result = $this->image_manager->publish_image($result);
			}
			$msg = $this->load->view('templates/message', $result, TRUE);
		}
		echo "{";
		echo "error: '',\n";
		echo "msg: '" . nl2br($msg) . "'\n";
		echo "}";
	}

	
	function _folder_exists ($str)
	{
		$this->validation->set_message('folder_exists', 'The %s you selected doesn\'t exist.');
		return (is_dir($str));
	}

	function _is_gd_loaded ()
	{
		if (! extension_loaded('gd'))
		{
			$this->redirect_message('red', 'GD extension was not found.<br/>Please enable GD extension in your php.ini to upload/import images.');
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

	function _load_album_bean ($id, $redirect = TRUE)
	{
		$this->load->model('Album_CT_model', 'album_bean');
		$this->album_bean->id = $id;
		$this->album_bean->uid = $this->uid;
		$loaded = $this->album_bean->load();
		if ($redirect)
		{
			$this->if_redirect_message(! $loaded, 'red', 'No album found with the specified ID');
		}
	}

}

?>