<?php
include_once APPPATH.'config/config.php';

function get_type_message($type,$msg){
	if ($type == 1){
		return "<span style='display:block;padding:5px;'><span class='icon ico_add'></span> &nbsp;" . $msg . '</span>';
	}else{
		return "<span style='display:block;padding:5px;'><span class='icon ico_delete'></span> &nbsp;" . $msg . '</span>';
	}
}

function verify_installation(){
	
	$url_excludes = array('/api/rest/test_curl');
	
	$uri = $_SERVER['REQUEST_URI'];
	foreach ($url_excludes as $url) {
		$uripos = strpos($uri,$url);
		$uripos = ($uripos === FALSE) ? 0 : $uripos;
		if (substr($uri,$uripos) == $url){
			return;
		}
	}
	
	$root_dir = dirname(FCPATH);
	
	// CUSTOM DEZINERFOLIO CODE FOR THE REQUIREMENTS CHECK
	$checks = array();

	
	// EXTENSIONS TO LOAD
	$required_extensions = array('curl','json');
	$required_extensions_check =  array('Required Extensions',true,'');
	$extension_prefix = (PHP_SHLIB_SUFFIX === 'dll') ? 'php_' : '';
	
	foreach ($required_extensions as $extension) {
		if(extension_loaded($extension)){
			$required_extensions_check[2] .= get_type_message(1,$extension . ' is loaded.');
		}else{
	    	if(dl($extension_prefix . $extension .'.'. PHP_SHLIB_SUFFIX) != 1){
				$required_extensions_check[1] &= false;
	    		$required_extensions_check[2] .= get_type_message(0,$extension . ' is disabled.');
	    	}else{
				$required_extensions_check[2] .= get_type_message(1,$extension . ' is loaded.');
	    	}
		}
	}
	if ($required_extensions_check[1] == false){
		$required_extensions_check[2] .= "<i>Please load the required PHP extensions in your php.ini before you continue.</i>";
	}
	$checks[] = $required_extensions_check;
	
	
	
	
	// FILES TO EXIST
	$files_to_exist = array();
	$files_to_exist[] = '/.htaccess';
	$files_to_exist[] = '/app/config/dfg/upgrade_state.php';
	
	$files_to_exist_check =  array('Files that need to exist',true,'');
	foreach ($files_to_exist as $file) {
		if(file_exists($root_dir . $file)){
			$files_to_exist_check[2] .= get_type_message(1,$file . ' exists.');
		}else{
			$files_to_exist_check[1] &= false;
			$files_to_exist_check[2] .= get_type_message(0,$file . ' doesn\'t exist!.');
		}
	}
	if ($files_to_exist_check[1] == false){
		$files_to_exist_check[2] .= "<br/><i>Please verify the required files exist in the installation.<br/>Please copy over the files from the installation archive into the gallery folder.</i>";
	}
	$checks[] = $files_to_exist_check;

	
	
	// DIRECTORIES AND FILES THAT SHOULD BE WRITABLE.
	$writable_dirs = array();
	$writable_dirs[] = '/resources/cache/';
	$writable_dirs[] = '/resources/images/original/';
	$writable_dirs[] = '/resources/images/large/';
	$writable_dirs[] = '/resources/images/thumbnail/';
	$writable_dirs[] = '/app/config/dfg/upgrade_state.php';
	
	$writable_dirs_check =  array('Check for writable files and directories',true,'');
	foreach ($writable_dirs as $dir) {
		if(is_writable($root_dir . $dir)){
			$writable_dirs_check[2] .= get_type_message(1,$dir . ' is writable.');
		}else{
			$writable_dirs_check[1] &= false;
			$writable_dirs_check[2] .= get_type_message(0,$dir . ' is not writable!');
		}
	}
	if ($writable_dirs_check[1] == false){
		$writable_dirs_check[2] .= "<i><strong>Please provide write permissions (chmod 777)  for required files/directories.</strong></i>";
	}
	$checks[] = $writable_dirs_check;

	
	
	// CLEAN URLS.
	if(extension_loaded('curl')){
		$curl_url = 'http://'.$_SERVER['SERVER_NAME'].substr($_SERVER['SCRIPT_NAME'],0,strpos($_SERVER['SCRIPT_NAME'],'/index.php')). '/api/rest/test_curl';
		$clean_url_check = curl_init($curl_url);
		curl_setopt($clean_url_check,CURLOPT_RETURNTRANSFER,true);
		$clean_url_check_resp = curl_exec($clean_url_check);
		if (curl_errno($clean_url_check)>0){
			$checks[] = array('A curl error occured while checking for clean urls.',false,"URL : $curl_url<br/>cURL message : ". curl_error($clean_url_check));
		}else{
			if($clean_url_check_resp == 'true'){
				$checks[] = array('Clean urls\' have been enabled.',true,'');
			}else{
				$checks[] = array('Invalid cURL response.',false,"We expected 'true' from the url : $curl_url");
			}
		}
	}else{
		$checks[] = array('Unable to check clean urls, as curl is disabled.',false,'Please enable cURL extension first.');
	}
	
	
	// check for database.php 777
	if(file_exists(APPPATH.'config/database.php')){
		$db_file_is_writable = is_writable(APPPATH.'config/database.php');
		include_once APPPATH.'config/database.php';
		$is_db_configured = is_array($config) && array_key_exists('active_group',$config);
		if ($is_db_configured){
			if ($db_file_is_writable){
				$db_check =  array(APPPATH.'config/database.php is writable!',true,'Please change it to read permissions (chmod 0664)  for this file to continue.');;
			}else{
				$db_check = array('Database Config is in read mode.',true,'');
			}
		}else{
			if ($db_file_is_writable){
				$db_check = array('Database Config is writable.',true,'');
			}else{
				$db_check = array('Database Config is not writable.',false,'Please provide write permissions (chmod 0777)  for '.APPPATH.'config/database.php file to continue.');
			}
		}
	}else{
		$db_check =  array(APPPATH.'config/database.php file doesnt\' exist .',false,'Please create an empty database.php file under app/config directory, with write permissions.');;
	}
	$checks[] = $db_check;
	
	
	// END ALL CHECKS...
	
	// render the status if any...
	$_continue = true;
	foreach ($checks as $key => $check) {
		$_continue = $_continue && $check[1];
	}
	
	if(!$_continue){
		function base_url(){
			global $config;
			return $config['base_url'];
		}
		function load_view($file,$vars = array()){
			extract($vars);
			include APPPATH.'views/'. $file . EXT;
		}
		
		load_view('templates/header');
		echo '<h3>Not all requirements of DfGallery have been met.</h3><br/>';
		foreach ($checks as $key => $check) {
			$msg = "<strong>$check[0]</strong>";
			if(sizeof($check)>2){
				$msg .= '<br/>'.$check[2];
			}
			$level = ($check[1]) ? 'green' : 'red';
			load_view('templates/message',array('level'=>$level,'message'=>$msg));
		}
		load_view('templates/footer');
		exit;
	}
// END CUSTOM DEZINERFOLIO CODE FOR THE REQUIREMENTS CHECK
}
?>