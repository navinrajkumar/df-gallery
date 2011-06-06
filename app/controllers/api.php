<?php
/**
 * set a debug mode to turn on all the errors in the XML
 */
define ( 'DEBUG_MODE', TRUE );

/**
 * a wrapper function to call the Api::_show_error method.
 * This is hooked into the php error reporting so that all errors are caught.
 *
 * @param int $errno
 * @param string $errstr
 * @param string $errfile
 * @param int $errline
 */
function rest_error_handler($errno, $errstr, $errfile, $errline)
{
	$message = 'Unhandled php error';
	$debug = '';
	global $CI;
	if (DEBUG_MODE) {
		$debug = $errstr . " @ $errfile @ $errline";
		$CI->rest_show_error ( 'php-' . $errno, $message . "\n<br/>" . $debug, TRUE );
	} else {
		$CI->rest_show_error ( 'php-' . $errno );
	}
	return FALSE;
}

/**
 * Api controller provides the controller for all /api calls. we don't have any api key for now.
 */
class Api extends DF_Controller
{

	var $response_format = null;
	/**
	 * define all known errors within this array with a beautifed text.
	 *
	 * @var array
	 */
	var $errors = array (
			'php-2' => 'missing parameters for the method.',
			'php-3' => 'cURL is disabled.',  
			'100' => 'Action is not defined.', 
			'400' => 'bad request.', 
			'404' => 'method has not been found.', 
			'500' => 'Internal server error', 
			'1001' => 'Gallery not found with the specified ID.' 
	);

	/**
	 * default controller action.
	 * this will indirectly deligate to the rest action.
	 */
	function index()
	{
		$args = func_get_args ();
		$this->rest ( $args );
	}

	/**
	 * REST action provides the REST based protocol, 
	 * while it takes in parameters and methods in the constructor itself.
	 * example "http://localhost/dfgallery/api/rest/method_name/param1/param2/param3....
	 */
	function rest()
	{
		header ( 'Content-Type: text/plain;' );
		header ( "Cache-Control: no-cache, must-revalidate" ); // HTTP/1.1
		header ( "Expires: Sat, 26 Jul 1997 05:00:00 GMT" ); // Date in the past
		

		error_reporting ( E_ALL ^ E_NOTICE );
		require_once (LIBPATH . 'xml/objectxml.php');
		if (version_compare ( PHP_VERSION, '5', '>=' )) {
			require_once (LIBPATH . 'xml/domxml-php4-to-php5.php');
			set_error_handler ( rest_error_handler, E_ALL ^ E_NOTICE );
		} else {
			set_error_handler ( rest_error_handler );
		}
		
		$args = func_get_args ();
		
		$action = array_shift ( $args );
		if(strtolower($args[sizeof($args)-1]) == "json"){
			array_pop($args);
			if ( function_exists('json_encode')  ){
				$this->response_format = "json";
			}
		}
		
		if ($action == NULL) {
			
			$this->rest_show_error ( '100' );
		
		} else {
			$method = 'api_' . $action;
			if (method_exists ( $this, $method )) {
				$result = call_user_func_array ( array (
						&$this, 
						$method 
				), $args );
				if (is_array ( $result ) && array_key_exists ( 'error', $result )) {
					$this->rest_show_error ( $result ['error'] ['code'] );
				} else {// if ($result != NULL) {
					
					//@todo uncomment during production.
					//$this->output->cache(10);
					
					if($this->response_format == "json"){
						echo json_encode($result);
					}else{
						if (file_exists(APPPATH . 'views/api/rest/' . $method)){
							$this->load->view ( "api/rest/$method", $result );
						}
					}
				//} else {
				//	$this->rest_show_error ( '404' );
				}
			} else {
				$this->rest_show_error ( '400' );
			}
		}
	}

	/**
	 * display an error response with a error code and a message and exit.
	 *
	 * @param mixed $code
	 * @param string $message
	 * @param bool $override
	 */
	function rest_show_error($code = NULL, $message = NULL, $override = FALSE)
	{
		if ($override == FALSE) {
			if (array_key_exists ( $code, $this->errors )) {
				$message = $this->errors [$code];
			} else {
				$message = ($code != NULL) ? ($message != NULL) ? $message : 'Unknown error.' : $this->errors ['400'];
			}
		}
		if($this->response_format == "json"){
			exit(json_encode(
					array (
					'code' => $code, 
					'message' => $message 
					)
			));	
		}
		$result = $this->load->view ( 'api/rest/error', array (
				'code' => $code, 
				'message' => $message 
		), TRUE );
		exit($result);
	}

	/**
	 * DEFINE api_ methods below to be used in reflection.
	 */
	function api_get_gallery($id)
	{
		
		// check if we have curl or fopen enabled.
		
		if (!function_exists('curl_exec')){
			$this->rest_show_error ( 'php-3');
		}

		$result = array ( 
		);
		$result['albums'] = array();
		
		$this->load->model ( 'Gallery_CT_model', 'gallery_bean' );
		
		$this->gallery_bean->id = $id;
		
		$loaded = $this->gallery_bean->load ();
		
		if (! $loaded) {
			
			$result ['error'] ['code'] = '1001';
			
			return $result;
		} else {
			
			// set the meta data
			$result ['meta'] = array (
					'generator' => 'dfGallery', 
					'version' => $this->version (), 
					'description' => 'dfGallery configuration file.', 
					'author' => 'Navin Raj Kumar G.S.', 
					'timestamp' => time () 
			);
			;
			
			// set all 'config' properties
			$result ['config'] ['global'] = array ( 
			);
			
			$result ['config'] ['theme'] = array ( 
			);
			
			$result ['config'] ['skin'] = array ( 
			);
			
			// get all the properties and populate in cofig
			$this->load->library ( 'Theme_Manager', 'theme_manager' );
			
			$gallery_properties = $this->gallery_bean->properties->get_all ();
			
			$theme = '';
			
			foreach ( $gallery_properties as $property ) {
				
				$propObj = new stdClass();
				if ($property->name == 'theme') {
					
					$theme = $property->value;
					
					$result ['config'] ['global'] [$property->name] = THEMES_FOLDER_URI . $property->value;
					
				} else if (strpos ( $property->name, 'config_theme_' ) === FALSE) {
					
					$result ['config'] ['global'] [$property->name] = $property->value;
				}
			}
			
			// set theme properties
			$theme_properties = $this->theme_manager->get_theme_properties ( $theme, TRUE );
			
			foreach ( $theme_properties as $property ) {
				
				$property = ( object ) $property;
				
				if (array_key_exists ( $property->name, $gallery_properties )) {
					
					$property->value = $gallery_properties [$property->name]->value;
				
				} else {
					
					$property->value = $property->default_value;
				
				}
				$result ['config'] ['theme'] [$property->name] = $property->value;
			}
			
			// set skin properties
			$use_skin_config = 'false';
			if( array_key_exists ( 'config_theme_use_skin_config', $gallery_properties ) ){
				$use_skin_config = $gallery_properties ['config_theme_use_skin_config']->value;
			}else if( array_key_exists ( 'config_theme_use_skin_config', $theme_properties ) ){
				$use_skin_config = $theme_properties ['config_theme_use_skin_config'] ['default_value'];
			}
			if ((strtolower ( $use_skin_config ) == 'true')) {
				
				$skin_xml_uri = THEMES_FOLDER . $theme . '/' . THEMES_SKINS_FOLDER . $gallery_properties ['skin']->value;
				
				$skin_xml_uri = substr ( $skin_xml_uri, 0, strrpos ( $skin_xml_uri, '.png' ) ) . '.xml';
				if (file_exists ( $skin_xml_uri )) {
					
					$skin_xml_contents = file_get_contents ( $skin_xml_uri );
					
					/* $skin_xml_contents = substr ( $skin_xml_contents, strpos ( $skin_xml_contents, "?>" ) + 2 ); */
					
					//$property = new stdClass ( );
					
//					$property->value = $skin_xml_contents;
					//$skin_xml_contents = preg_replace("/\n/","\n\t\t\t\t\t\t",$skin_xml_contents) . "\n\t\t\t\t\t";
					//$result ['config'] ['skin'] ['config_xml'] = htmlentities($skin_xml_contents);
					$result ['config'] ['skin'] ['config_xml'] = $skin_xml_contents;
				}
			}
			
			$this->load->model ( 'Album_CT_model', 'album_bean' );
			
			$this->album_bean->pid = $id;
			
			$this->load->library ( 'Album_Manager', 'album_manager' );
			$albums = $this->album_bean->load_all ();
			
			foreach ( $albums as $album ) {
				$album->properties->get_all();
				$album = $this->album_manager->load_images ( $album );
				if (is_array($album)){
					$result ['albums'] = array_merge($result ['albums'],$album);
				}else if(($album != NULL)){
					$result ['albums'] [] = $album;
				}
			}
			
			// finally return the result
			return $result;
		}
	}
	
	/**
	 * DEFINE api_ methods below to be used in reflection.
	 */
	function api_test_curl()
	{
		echo "true";
	}
	
}

?>