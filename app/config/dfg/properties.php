<?php
$config = array( 
);

/**
 * SYSTEM PROPERTIES
 */
$config['system_properties'] = array( 
);

// SYSTEM PROPERTIES SECTION
$config['system_properties']['system_properties'] = array(
	'name' => 'Current System Properties' , 
	'description' => 'Please do not edit system properties if you are not sure what they do.' 
);

$config['system_properties']['system_properties']['properties'][] = array(
	'display_name' => 'Flickr API key' , 
	'rule' => 'min_length[32]|required|alpha_numberic' , 
	'name' => 'flickr_api_key' , 
	'default_value' => '' 
);

$config['system_properties']['system_properties']['properties'][] = array(
	'display_name' => 'Number of images in Manage album' , 
	'rule' => 'min_length[1]|required|integer' , 
	'name' => 'album_num_images' , 
	'default_value' => '10' 
);



/**
 * ALBUM PROPERTIES
 */
// FLICKR PROPERTIES SECTION
$config['album_properties_flickr']['album_properties_flickr'] = array(
	'name' => 'Flickr album config' , 
	'description' => 'Please enter the flickr URL below.<br/>You can also enter a specific album URL if you wish to.' 
);

$config['album_properties_flickr']['album_properties_flickr']['properties'][] = array(
	'display_name' => 'Flickr album URL' , 
	'rule' => 'min_length[17]|required|' , 
	'name' => 'config_flickr_album_url' , 
	'default_value' => '' 
);


// PICASA PROPERTIES SECTION
$config['album_properties_picasa']['album_properties_picasa'] = array(
	'name' => 'Picasa album config' , 
	'description' => 'If you want to display all the albums, please enter the URL of the user (http://picasaweb.google.com/3drockz/), we will fetch all the public albums for the user.<br/><br/> else if you want to display a specific album, please enter the album URL (http://picasaweb.google.com/3drockz/NikonSmallLife)' 
);

$config['album_properties_picasa']['album_properties_picasa']['properties'][] = array(
	'display_name' => 'Picasa album URL' , 
	'rule' => 'min_length[28]|required|' , 
	'name' => 'config_picasa_album_url' , 
	'default_value' => '' 
);


// CUSTOM PROPERTIES SECTION
$config ['album_properties_custom'] ['album_properties_custom'] = array (
		'name' => 'Custom album configurations', 
		'description' => '' 
);

$config ['album_properties_custom'] ['album_properties_custom'] ['properties'] [] = array (
		'display_name' => 'image width', 
		'rule' => 'min_length[2]|required|integer', 
		'name' => 'config_image_width', 
		'default_value' => '1024' 
);

$config ['album_properties_custom'] ['album_properties_custom'] ['properties'] [] = array (
		'display_name' => 'image height', 
		'rule' => 'min_length[2]|required|integer', 
		'name' => 'config_image_height', 
		'default_value' => '768' 
);

$config ['album_properties_custom'] ['album_properties_custom'] ['properties'] [] = array (
		'display_name' => 'thumbnail width', 
		'rule' => 'min_length[2]|required|integer', 
		'name' => 'config_thumbnail_width', 
		'default_value' => '100' 
);

$config ['album_properties_custom'] ['album_properties_custom'] ['properties'] [] = array (
		'display_name' => 'thumbnail height', 
		'rule' => 'min_length[2]|required|integer', 
		'name' => 'config_thumbnail_height', 
		'default_value' => '75' 
);

/**
 * ALBUM TYPES
 */
$config['album_types'] = array(
	'flickr' , 
	'picasa' , 
	'custom' 
);
?>