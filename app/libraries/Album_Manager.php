<?php
class CI_Album_Manager   
{

	private $flickr_api_key;
	
	function load_images($album)
	{
		$album->images = array ( 
		);
		$albums = array();
		$type = $album->properties->get_value('album_type');
		if ($type == 'flickr'){
			$albums = $this->process_album_flickr($album);
		}else if($type == 'picasa'){
			$albums = $this->process_album_picasa($album);
		}else if($type == 'custom'){
			$albums = $this->process_album_custom($album);
		}
		
		$final_albums = array();
		foreach ($albums as $album) {
			$albumObj = new stdClass();
			
			if(isset($album->error)) $albumObj->error = $album->error;
			
			$albumObj->properties = array();
			
			$properties = $album->properties->get_all(FALSE);
			
			foreach ($properties as $prop) {
				$albumObj->properties[$prop->name] = $prop->value;
			}
			
			$albumObj->images = $album->images;
			$final_albums[] = $albumObj;
		}
		
		return $final_albums;
	}
	
	function curl_get($url)
	{
		$curl_handle = curl_init($url);
		if($curl_handle){
			curl_setopt($curl_handle, CURLOPT_TIMEOUT, 15);
			curl_setopt($curl_handle, CURLOPT_HEADER, 0);
			curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
			$response = curl_exec ($curl_handle);
			curl_close ($curl_handle);
			if($response == ''){
				throw new Exception("Unable to fetch albums, album server might be down.",3);
			}
		}
		return $response;
	}
	
	function _simplexml_attribute_value($node,$attributename,$default = '')
	{
		$attributes = $node->attributes();
		$attribute = $attributes->$attributename;
		if(is_a($attribute,'SimpleXMLElement')){
				return $attribute[0];
		}
		return $default;
	}
	
	function _flickr_api_invoke($method,$params = array())
	{
		$params = array_merge(array('api_key'=> $this->flickr_api_key,'method'=>$method,'format'=>'php_serial',),$params);

		$encoded_params = array();
		foreach ($params as $k => $v){
			$encoded_params[] = urlencode($k).'='.urlencode($v);
		}
		$url = "http://api.flickr.com/services/rest/?".implode('&', $encoded_params);
		$resp = unserialize($this->curl_get($url));
		if ($resp['stat'] == 'fail'){
			throw new Exception('Album_Manager:flickr_api >> '.$resp['message'],$resp['code']);
		}
		return $resp;
	}
	
	function process_album_flickr_set($album,$photoset_id,$max_size = '_b')
	{
		$photoset = $this->_flickr_api_invoke('flickr.photosets.getInfo',array('photoset_id'=>$photoset_id));
		$photoset = $photoset['photoset'];
		if (intval($photoset['photos'])>0){
			$album->properties->set('title',$photoset['title']['_content'],FALSE);
			$album->properties->set('description',$photoset['description']['_content'],FALSE);
			$album->properties->set('icon',"http://farm$photoset[farm].static.flickr.com/$photoset[server]/$photoset[primary]_$photoset[secret]_s.jpg",FALSE);
			$album->properties->set('exif-type','none',FALSE);
			
			$current_page = 0;
			$per_page = 5;
			$total_pages = ceil($photoset['photos']/$per_page);
			$getPhotos_params = array('photoset_id'=>$photoset_id,'extras'=>'date_upload','per_page'=>'5','page'=>$current_page);
			$pages = array();
			while ($current_page<$total_pages){
				$getPhotos_params['page'] = ++$current_page;
				$pages[] = $this->_flickr_api_invoke('flickr.photosets.getPhotos',$getPhotos_params);
			}
			
			$album->images = array();
			foreach($pages as $page){
				if (is_array($page['photoset']['photo'])){
					foreach ($page['photoset']['photo'] as $photo) {
						$image = array();
						$image['id'] = $photo['id'];
						$image['title'] = $photo['title'];
						$image['timestamp'] = $photo['dateupload'];
						$image['thumbnail'] = "http://farm$photo[farm].static.flickr.com/$photo[server]/$photo[id]_$photo[secret]_s.jpg";
						$image['image'] = "http://farm$photo[farm].static.flickr.com/$photo[server]/$photo[id]_$photo[secret]$max_size.jpg";
						$album->images[] = $image;
					}
				}
			}
			return $album;
		}
		return FALSE;
	}
	
	function process_album_flickr($album){
		$album_org = $album->deep_clone();
		$albums_list = array();
		try{
			global $CI;
			$url = $album->properties->get_value('config_flickr_album_url');
			$CI->load->model ( 'SystemSettings_model','system_settings');
			$this->flickr_api_key = $CI->system_settings->get_value('flickr_api_key');
			
			$CI->load->model ( 'SystemSettings_model','system_settings');
			$max_size = $CI->system_settings->get_value('flickr_max_image_size');
			if($max_size == '' ){
				$max_size = '_b';
			}
			
			if (strpos($url,'/sets/')!=0){
				$set_id = preg_replace('/\//','',substr($url,strpos($url,'/sets/')+6));
				$set = $this->process_album_flickr_set($album_org->deep_clone(),$set_id,$max_size);
				if (is_object($set)){
					$albums_list[] = $set;
				}
			}else{
				$data = $this->_flickr_api_invoke('flickr.urls.lookupUser',array('url'=>$url));
				$user_id = $data['user']['id']; //86329672@N00
				
				// process photosets.
				$photosets_list = $this->_flickr_api_invoke('flickr.photosets.getList',array('user_id'=>$user_id));
				if (sizeof($photosets_list)>0 && sizeof($photosets_list['photosets'])>0 && sizeof($photosets_list['photosets']['photoset'])>0){
					$photosets_list = $photosets_list['photosets']['photoset'];
					foreach ($photosets_list as $photoset) {
						$set = $this->process_album_flickr_set($album_org->deep_clone(),$photoset['id'],$max_size);
						if (is_object($set)){
							$albums_list[] = $set;
						}
					}
				}
				// now process the search and generate the global flickr album.
				$total_pages = 1;
				$pages = array();
				for ($page = 1; $page<=$total_pages; $page++){
					$pages[] = $this->_flickr_api_invoke('flickr.photos.search',array('user_id'=>$user_id,'privacy_filter'=>1,'content_type'=>1,'per_page'=>500,'page'=>$page));
					$total_pages = $pages[0]['photos']['pages'];
				}
				
				$search_images = array();
				foreach($pages as $page){
					if (is_array($page['photos']['photo'])){
						foreach ($page['photos']['photo'] as $photo) {
							$image = array();
							$image['id'] = $photo['id'];
							$image['title'] = $photo['title'];
							$image['timestamp'] = 0;//during a search timestamp is not returned.
							$image['thumbnail'] = "http://farm$photo[farm].static.flickr.com/$photo[server]/$photo[id]_$photo[secret]_s.jpg";
							$image['image'] = "http://farm$photo[farm].static.flickr.com/$photo[server]/$photo[id]_$photo[secret]$max_size.jpg";
							$search_images[] = $image;
						}
					}
				}
				
				// group all set images into one.
				$set_images = array();
				foreach ($albums_list as $album) {
					foreach ($album->images as $image) {
						$set_images[] = $image['id'];
					}
				}
				$global_flickr_album = $album_org->deep_clone();
				$global_flickr_album->images = array();
				
				// check if images of search is in the sets.
				foreach ($search_images as $search_image) {
					if(array_search($search_image['id'],$set_images) === FALSE){
						$global_flickr_album->images[] = $search_image;
					}
				}

				// get album info
				$data = $this->_flickr_api_invoke('flickr.people.getInfo',array('user_id'=>$user_id));
				$data = $data['person'];
				
				$global_flickr_album->properties->set('title',$data['username']['_content'].'\'s photos',FALSE);
				$global_flickr_album->properties->set('description','Unorganized photo set.',FALSE);
				$global_flickr_album->properties->set('icon',"http://farm$data[iconfarm].static.flickr.com/$data[iconserver]/buddyicons/$user_id.jpg",FALSE);
				$global_flickr_album->properties->set('exif-type','none',FALSE);
				
				$albums_list[] = $global_flickr_album;
			}
		} catch (Exception $e){
			$album_org->error = array('code'=>$e->getCode(),'message'=>$e->getMessage());
			$albums_list[] = $album_org;
		}
		return $albums_list;
	}
	
	function process_album_picasa($album){
		
		$ns = array(
			'gphoto'=>	'http://schemas.google.com/photos/2007',
			'exif' 	=> 	'http://schemas.google.com/photos/exif/2007',
			'media'	=>	'http://search.yahoo.com/mrss/'
		);
		$has_sub_albums = false;
		$album_org = $album->deep_clone();
		$url = $album_org->properties->get_value('config_picasa_album_url');
		$url = substr($url, strpos($url,'picasaweb.google.com/')+21);
		if (strrpos($url,"/")==strlen($url)-1){
			$url = substr($url,0,strlen($url)-1);
		}
		$breadcrumb = split("/",$url);
		
		$user = $breadcrumb[0];
		$albums_list = array();
		$album_names = array();
		$album_fetch_num_results = $album_org->properties->get_value('album_fetch_num_results',9999);
		
		try {
			if (sizeof($breadcrumb) == 1) {
				$has_sub_albums = true;
				$response = $this->curl_get("http://picasaweb.google.com/data/feed/api/user/$user/?kind=album&max-results=100");
				if(substr($response,0,1) != "<"){
					throw new Exception("AlbumManager:".$album_org->id.":".$response,1);
				}else{
					$doc = new SimpleXmlElement($response);
				}
				foreach ($doc->entry as $entry) {
					$album_names[] = strval($entry->children($ns['gphoto'])->name);
				}
			}
			if( sizeof($breadcrumb) == 2 ){
				$album_names[] = $breadcrumb[1];
			}
			
			foreach ($album_names as $album_name) {
				$album = $album_org->deep_clone();
				try{
					$response = $this->curl_get("http://picasaweb.google.com/data/feed/api/user/$user/album/$album_name/?kind=photo&max-results=$album_fetch_num_results");	
					if(substr($response,0,1) != "<"){
						throw new Exception("AlbumManager:".$album_name.":".$response,2);
					}
					$doc = new SimpleXmlElement($response);
					$album->properties->set('title',strval($doc->title),FALSE);
					$album->properties->set('icon',strval($doc->icon),FALSE);
					$album->properties->set('exif-type','none',FALSE);
					
					if($has_sub_albums){
						$album->properties->set('config_picasa_album_url',"http://picasaweb.google.com/$url/$album_name",FALSE);
					}
					
					$images = array();
					$entries = $doc->entry;
					foreach ($entries as $entry) {
						$image=array();
						
						$media = $entry->children($ns['media']);
						//$gphoto = $entry->children($ns['gphoto']);
						
						$image['title'] = strval($entry->title);
						$image['timestamp'] = strtotime(strval($entry->updated));
						
						$image['thumbnail'] = strval($this->_simplexml_attribute_value($media->group->thumbnail[0],'url'));
						$image['image'] = strval($this->_simplexml_attribute_value($media->group->content[0],'url'));
						
						// dont handle exif for the 1st phase
						//$exif = $entry->children($ns['exif']);
						
						$images[] = $image;
					}
					$album->images = $images;
					
				} catch (Exception $e){
					$album->error = array('code'=>$e->getCode(),'message'=>$e->getMessage());
				}
				$albums_list[] = $album;
			}
		
		} catch (Exception $e){
			$album_org->error = array('code'=>$e->getCode(),'message'=>$e->getMessage());
			$albums_list[] = $album_org;
		}
		return $albums_list;
	}

	function process_album_custom($album){
		global $CI;
		$CI->load->model('Image_CT_model','image_model');
		$images_list = $CI->image_model->load_all_where(array('pid'=>$album->id));
		$album->images = array();
		foreach ($images_list as $image_bean) {
			$image = array();
			$image['title'] = $image_bean->title;
			$image['timestamp'] = $image_bean->properties->get_value('timestamp');
			$image['thumbnail'] = UPLOAD_IMAGES_THUMBNAIL_FOLDER_URI.$image_bean->properties->get_value('thumbnail_file');
			$image['image'] = UPLOAD_IMAGES_LARGE_FOLDER_URI.$image_bean->properties->get_value('image_file');
			$album->images[]= $image;
		}
		
		$album->properties->set('title',$album->title,FALSE);
		$album->properties->set('description','album of uploaded files.',FALSE);
		$album->properties->set('icon',$album->images[sizeof($album->images)-1]['thumbnail'],FALSE);
		$album->properties->set('exif-type','none',FALSE);
		return array($album);
	}
	
}

?>