<?php

/**
 * global includes.
 */
include_once APPPATH . 'models/Content_model' . EXT;

/**
 * the album content type model
 */
class Album_CT_model extends Content_model
{

	/**
	 * construct the content model with album type.
	 *
	 * @return Album_CT_model
	 */
	function Album_CT_model ()
	{
		parent::Content_model('album');
	}

}

?>