<?php

include_once APPPATH . 'libraries/DF_AdminController' . EXT;

class Images extends DF_AdminController
{

	function delete ($aid, $pid, $id)
	{
		$this->_load_image_bean($id);
		$state = $this->image_bean->delete();
		$this->if_redirect_message($state, 'green', 'Image has been deleted.', "admin/manage/index/$aid/$pid/" . $this->album_bean->pid, 'red', 'Unabled to delete the album<br/>Please try again.');
	}

	function edit ($id)
	{
		$this->_load_image_bean($id);
		if ($this->input->post('title')) {
			$this->image_bean->title = $this->input->post('title');
			$this->image_bean->update();
			echo "1";
		}else{
			echo "0";
		}
	}

	function _load_image_bean ($id)
	{
		$this->load->model('Image_CT_model', 'image_bean');
		$this->image_bean->id = $id;
		$this->image_bean->uid = $this->uid;
		$loaded = $this->image_bean->load();
		$this->if_redirect_message(! $loaded, 'red', 'No image found with the specified ID');
	}

}
?>
