<?php

/**
 * global includes.
 */
include_once APPPATH . 'models/Image_CT_model' . EXT;

/**
 * all the image uploads and imports are handled in this class.
 * It converts the images into thumbnails, and the large files and also publishes it into the DB.
 */
class CI_Image_Manager
{

	/**
	 * holds an instance of the codeIgniter image library
	 *
	 * @var CI_Image_lib
	 */
	var $image_lib;

	/**
	 * instance of the code image upload.
	 *
	 * @var CI_Upload
	 */
	var $uploader;

	/**
	 * the available file types to upload
	 *
	 * @var string
	 */
	var $allowed_types;

	/**
	 * The album ID into which the image needs to be saved.
	 *
	 * @var int
	 */
	var $album_id;

	/**
	 * the owner of the album.
	 *
	 * @var int
	 */
	var $uid;

	/**
	 * an array of the large image sizes, and thumbnail sizes.
	 *
	 * @var array
	 */
	var $sizes;

	/**
	 * a counter for the number of image beans that we create.
	 *
	 * @var int
	 */
	var $bean_id = 0;

	/**
	 * constructor that loads the image library and upload library, and set the 
	 * allowed file types for an upload.
	 *
	 * @return CI_Image_Manager
	 */
	function CI_Image_Manager ()
	{
		$this->image_lib = & load_class('Image_lib');
		
		$this->uploader = & load_class('Upload');
		
		$this->allowed_types = array(
			'gif' , 
			'jpg' , 
			'jpeg' , 
			'png' , 
			'bmp' 
		);
	}

	/**
	 * initialize the Image_Manager for  specific album.
	 *
	 * @param int $uid
	 * @param int $album_id
	 * @param array $sizes
	 */
	function initialize ($uid, $album_id, $sizes = array('thumbnail_width'=>100,'thumbnail_height'=>75,'image_width'=>1024,'image_height'=>768))
	{
		$this->uid = $uid;
		
		$this->album_id = $album_id;
		
		$this->sizes = $sizes;
		
		$this->_check_images_folder();
	}

	/**
	 * get all the importable files
	 *
	 * @param string $folder
	 * @return array
	 */
	function get_importable_files ($folder)
	{
		$this->_check_images_folder();
		
		$uploadable_files = array( 
		);
		

		$files = $this->_get_files($folder, FALSE);
		
		foreach ($files as $file)
		{
			
			$ext = strtolower(substr(strrchr($file, '.'), 1));
			
			if (array_search($ext, $this->allowed_types))
			{
				
				$filename = substr($file, 0, strrpos($file, '.'));
				
				$new_name = $this->_get_new_filename(UPLOAD_IMAGES_ORIGNAL_FOLDER, $filename, $ext);
				
				$img_name = $new_name . '.' . $ext;
				
				if ($new_name !== FALSE)
				{
					$uploadable_files[] = array(
						'file' => $file , 
						'img_name' => $img_name , 
						'ext' => $ext , 
						'title' => $new_name 
					);
				}
			}
		}
		return $uploadable_files;
	}

	/**
	 * save an image to the database
	 *
	 * @param array $file
	 * @return array  - status message
	 */
	function publish_image ($file)
	{
		$thumb_config = array(
			'maintain_ratio' => TRUE , 
			'width' => $this->sizes['thumbnail_width'] , 
			'height' => $this->sizes['thumbnail_height'] , 
			'source_image' => UPLOAD_IMAGES_ORIGNAL_FOLDER . $file['img_name'] , 
			'new_image' => UPLOAD_IMAGES_THUMBNAIL_FOLDER . $file['img_name'] 
		);
		
		$image_config = array(
			'maintain_ratio' => TRUE , 
			'width' => $this->sizes['image_width'] , 
			'height' => $this->sizes['image_height'] , 
			'source_image' => UPLOAD_IMAGES_ORIGNAL_FOLDER . $file['img_name'] , 
			'new_image' => UPLOAD_IMAGES_LARGE_FOLDER . $file['img_name'] 
		);
		
		$this->image_lib->clear();
		
		$this->image_lib->error_msg = array( 
		);
		
		$this->image_lib->initialize($thumb_config);
		
		if ($this->image_lib->resize())
		{
			
			$this->image_lib->clear();
			
			$this->image_lib->error_msg = array( 
			);
			
			$this->image_lib->initialize($image_config);
			
			if ($this->image_lib->resize())
			{
				
				$this->bean_id = $this->bean_id + 1;
				
				$model_name = 'image_bean_' . $this->bean_id;
				
				$this->$model_name = new Image_CT_model();
				
				$this->$model_name->uid = $this->uid;
				
				$this->$model_name->pid = $this->album_id;
				
				$this->$model_name->title = $file['title'];
				
				$this->$model_name->save();
				
				$this->$model_name->properties->set('thumbnail_file', $file['img_name']);
				
				$this->$model_name->properties->set('image_file', $file['img_name']);
				
				$this->$model_name->properties->set('timestamp', time() );
				
				if ($file['uploaded'])
				{
					$this->$model_name->properties->set('uploaded', $file['uploaded']);
				}
				return array(
					'level' => 'green' , 
					'message' => 'Uploaded.' 
				);
			
			}
		}
		
		if (is_file(UPLOAD_IMAGES_LARGE_FOLDER . $file['img_name']))
		{
			unlink(UPLOAD_IMAGES_LARGE_FOLDER . $file['img_name']);
		}
		
		if (is_file(UPLOAD_IMAGES_THUMBNAIL_FOLDER . $file['img_name']))
		{
			unlink(UPLOAD_IMAGES_THUMBNAIL_FOLDER . $file['img_name']);
		}
		
		return array(
			'level' => 'red' , 
			'message' => $this->image_lib->display_errors() 
		);
	}

	/**
	 * get a file from the uploader and process it.
	 *
	 * @param string $title
	 * @param string $field
	 * @return array
	 */
	function upload_file ($title, $field)
	{
		
		$upload_config = array(
			'upload_path' => UPLOAD_IMAGES_ORIGNAL_FOLDER , 
			'allowed_types' => join('|', $this->allowed_types) 
		);
		
		$this->uploader->initialize($upload_config);
		
		if ($this->uploader->do_upload($field))
		{
			return array(
				'level' => 'green' , 
				'message' => 'uploaded.' , 
				'img_name' => $this->uploader->file_name , 
				'title' => (is_null($title)) ? substr($this->uploader->file_name, 0, strrpos($this->uploader->file_name, '.')) : $title 
			);
		}
		
		return array(
			'level' => 'red' , 
			'message' => $title . ' : ' . $this->uploader->display_errors('', '') 
		);
	}

	/**
	 * check if all the upload folders exist, or create the required folders
	 */
	function _check_images_folder ()
	{
		global $CI;
		if (! is_dir(UPLOAD_IMAGES_THUMBNAIL_FOLDER))
		{
			mkdir(UPLOAD_IMAGES_THUMBNAIL_FOLDER, 777);
		}
		$CI->if_redirect_message(! is_dir(UPLOAD_IMAGES_THUMBNAIL_FOLDER), 'red', 'Unable to create thumbnail folder:<br/>' . UPLOAD_IMAGES_THUMBNAIL_FOLDER);
		
		if (! is_dir(UPLOAD_IMAGES_LARGE_FOLDER))
		{
			mkdir(UPLOAD_IMAGES_LARGE_FOLDER, 777);
		}
		
		$CI->if_redirect_message(! is_dir(UPLOAD_IMAGES_LARGE_FOLDER), 'red', 'Unable to create large images folder:<br/>' . UPLOAD_IMAGES_LARGE_FOLDER);
		
		if (! is_dir(UPLOAD_IMAGES_ORIGNAL_FOLDER))
		{
			mkdir(UPLOAD_IMAGES_ORIGNAL_FOLDER, 777);
		}
		
		$CI->if_redirect_message(! is_dir(UPLOAD_IMAGES_ORIGNAL_FOLDER), 'red', 'Unable to create original images folder:<br/>' . UPLOAD_IMAGES_ORIGNAL_FOLDER);
	}

	/**
	 * create a new filename for a a given file upload
	 *
	 * @param string $path
	 * @param string $file
	 * @param string $ext
	 * @return string
	 */
	function _get_new_filename ($path, $file, $ext)
	{
		$file = preg_replace('/\s+/', '_', $file);
		
		if (! file_exists($path . $file . '.' . $ext))
		{
			return $file;
		}
		
		$not_found = TRUE;
		
		$i = 0;
		
		while ($not_found)
		{
			$i ++;
			
			if (! file_exists($path . $file . $i . '.' . $ext))
			{
				return $file . $i;
			}
		}
		
		return false;
	}

	/**
	 * returns a list of files within a directory.
	 *
	 * @param string $source_dir
	 * @param boolean $include_path
	 * @return array
	 */
	function _get_files ($source_dir, $include_path = FALSE)
	{
		$_filedata = array( 
		);
		
		if (FALSE !== ($fp = opendir($source_dir)))
		{
			
			while (FALSE !== ($file = readdir($fp)))
			{
				if (is_file($source_dir . $file))
				{
					$file = ($include_path == TRUE) ? $source_dir . $file : $file;
					$_filedata[] = str_replace("\\", '/', $file);
				}
			}
			return $_filedata;
		
		} else
		{
			return FALSE;
		}
	}

}

?>