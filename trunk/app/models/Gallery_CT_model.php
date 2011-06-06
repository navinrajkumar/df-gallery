<?php

/**
 * global includes.
 */
include_once APPPATH . 'models/Content_model' . EXT;

/**
 * the gallery content type model
 */
class Gallery_CT_model extends Content_model
{

	/**
	 * construct the content model with gallery type.
	 *
	 * @return Gallery_CT_model
	 */
	function Gallery_CT_model ()
	{
		parent::Content_model('gallery');
	}

}

?>