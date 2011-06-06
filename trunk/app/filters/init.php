<?php

/**
 * Init filter, loads all the required configurations, and sets the required constants within the system.
 */
class Init_filter extends Filter
{

	/**
	 * declare all the required configurations in this method.
	 *
	 */
	function before()
	{
		// load all required config files.
		global $CFG;
		
		$CFG->load ( 'database', TRUE, TRUE );
		
		$CFG->load ( 'dfg/upgrade', TRUE, TRUE );
		
		$CFG->load ( 'dfg/upgrade_state', TRUE, TRUE );
		
		$CFG->load ( 'dfg/properties', TRUE, TRUE );
		
		$CFG->load ( 'dfg/resetpassword', TRUE, TRUE );
		
		// define the file locations 
		define ( 'DATABASE_CONFIG_FILE', APPPATH . 'config/database.php' );
		
		define ( 'UPGRADE_CONFIG_FILE', APPPATH . 'config/dfg/upgrade.php' );
		
		define ( 'UPGRADE_CONFIG_STATE_FILE', APPPATH . 'config/dfg/upgrade_state.php' );
		
		define ( 'UPGRADE_FOLDER', APPPATH . 'upgrade/' );
		
		define ( 'RESOURCES_FOLDER', dirname ( FCPATH ) . '/resources/' );
		
		// folders to upload images
		define ( 'UPLOAD_IMAGES_ORIGNAL_FOLDER', RESOURCES_FOLDER . 'images/original/' );
		
		define ( 'UPLOAD_IMAGES_LARGE_FOLDER', RESOURCES_FOLDER . 'images/large/' );
		
		define ( 'UPLOAD_IMAGES_THUMBNAIL_FOLDER', RESOURCES_FOLDER . 'images/thumbnail/' );
		
		// URI for images that reside within these upload folders.
		define ( 'RESOURCES_FOLDER_URI', $CFG->slash_item ( 'base_url' ) . 'resources/' );
		
		define ( 'UPLOAD_IMAGES_ORIGNAL_FOLDER_URI', RESOURCES_FOLDER_URI . 'images/original/' );
		
		define ( 'UPLOAD_IMAGES_LARGE_FOLDER_URI', RESOURCES_FOLDER_URI . 'images/large/' );
		
		define ( 'UPLOAD_IMAGES_THUMBNAIL_FOLDER_URI', RESOURCES_FOLDER_URI . 'images/thumbnail/' );
		
		// themes folder extensions, and locations.
		define ( 'THEMES_FOLDER', RESOURCES_FOLDER . 'themes/' );
		
		define ( 'THEMES_FOLDER_URI', RESOURCES_FOLDER_URI . 'themes/' );
		
		define ( 'THEMES_SWF_FILENAME', 'theme.swf' );
		
		define ( 'THEMES_SKINS_FOLDER', 'skins/' );
		
		define ( 'THEMES_SKINS_EXT', 'png' );
	}

	function after()
	{
	}

}

?>