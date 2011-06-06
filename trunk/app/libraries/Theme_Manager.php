<?php
/**
 * global includes.
 */
include_once BASEPATH . 'helpers/file_helper' . EXT;

/**
 * the theme manager provides a helper functions to get the list of 
 * themes and related properties for the theme.
 *
 */
class CI_Theme_Manager
{

	/**
	 * returns all the available themes in the resource folders.
	 *
	 * @return array - contains the themes and skins.
	 */
	function get_themes()
	{
		$themes = array ( 
		);
		
		if (is_dir ( THEMES_FOLDER )) {
			
			$dir_handle = opendir ( THEMES_FOLDER );
			
			// scan through the theme folder for files.
			while ( ($file = readdir ( $dir_handle )) !== false ) {
				// scan check if the file is not the current folder or the parent folder.	
				if ($file != '.' && $file != '..') {
					$theme_folder = THEMES_FOLDER . $file . '/';
					if (is_dir ( $theme_folder )) {
						// if its a valid folder then check if a theme SWF exists.
						if (file_exists ( $theme_folder . THEMES_SWF_FILENAME )) {
							// if so also check if the skin folder exists.
							if (is_dir ( $theme_folder . THEMES_SKINS_FOLDER )) {
								$skins = array ( 
								);
								$skin_folder = $theme_folder . THEMES_SKINS_FOLDER;
								$skins_dir_handle = opendir ( $skin_folder );
								
								// scan and get all the skins for the theme
								while ( ($skin_file = readdir ( $skins_dir_handle )) !== false ) {
									if (is_file ( $skin_folder . $skin_file )) {
										$arr = explode ( '.', $skin_file );
										if (strtolower ( array_pop ( $arr ) ) == THEMES_SKINS_EXT) {
											array_push ( $skins, array (
													'name' => array_pop ( $arr ), 
													'file' => $skin_file 
											) );
										}
									}
								}
								
								// if there are any real skins then push it into the return array.
								if (sizeof ( $skins ) > 0) {
									$themes [$file] = array (
											'theme' => $file, 
											'skins' => $skins 
									);
								}
							}
						}
					}
				}
			}
		}
		return $themes;
	}

	/**
	 * get all the properties of a theme.
	 *
	 * @param string $theme
	 * @return array
	 */
	function get_theme_properties($theme, $merged = FALSE)
	{
		$properties = array ( 
		);
		
		$theme_properties_file = THEMES_FOLDER . '/' . $theme . '/' . 'properties' . EXT;
		if (is_file ( $theme_properties_file )) {
			include_once $theme_properties_file;
		}
		$maxi = sizeof ( $properties );
		for($i = 0; $i < $maxi; $i ++) {
			$property = &$properties [$i];
			$maxj = sizeof ( $property ['properties'] );
			for($j = 0; $j < $maxj; $j ++) {
				$config_property = & $property ['properties'] [$j];
				$config_property ['name'] = 'config_theme_' . $config_property ['name'];
			}
		}
		
		if ($merged === FALSE) {
			return $properties;
		} else {
			$merged_properties = array ( 
			);
			foreach ( $properties as $merged_property ) {
				foreach ( $merged_property ['properties'] as $item ) {
					$merged_properties [$item ['name']] = $item;
				}
			}
			return $merged_properties;
		}
	}
}
?>