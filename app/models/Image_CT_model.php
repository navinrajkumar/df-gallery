<?php

/**
 * global includes.
 */
include_once APPPATH . 'models/Content_model' . EXT;

/**
 * the album content type model
 */
class Image_CT_model extends Content_model
{

	/**
	 * construct the content model with image type.
	 *
	 * @return Image_CT_model
	 */
	function Image_CT_model ()
	{
		parent::Content_model('image');
	}

	/**
	 * returns the number of images for a specific album.
	 *
	 * @return int
	 */
	function count ()
	{
		return parent::count(array(
			'pid' => $this->pid 
		));
	}

}

?>