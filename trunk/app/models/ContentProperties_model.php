<?php

/**
 * global includes.
 */
include_once APPPATH . 'models/ContentProperty_model' . EXT;

/**
 * the content properties class provides collection features for the content property model
 */
class ContentProperties_model extends ContentProperty_model
{

	/**
	 * content ID
	 *
	 * @var int
	 */
	var $cid;

	/**
	 * an array of all the available properties for the specific content.
	 *
	 * @var array
	 */
	var $properties;

	/**
	 * construct the ContentProperties_model 
	 *
	 * @return ContentProperties_model
	 */
	function ContentProperties_model()
	{
		parent::ContentProperty_model ();
		$this->properties = array ( 
		);
	}

	/**
	 * fetch all the properties into the collection based on the content Id.
	 *
	 * @return array
	 */
	function get_all($from_db = TRUE)
	{
		if($from_db){
			$cp_model = new ContentProperty_model ( );
			$cp_model->cid = $this->cid;
			$properties = $cp_model->load_all ();
			foreach ( $properties as $property ) {
				$this->properties [$property->name] = $property;
			}
		}
		return $this->properties;
	}

	/**
	 * return the list of properties that are similar in the name
	 *
	 * @param string $name
	 * @return array
	 */
	function get_all_like($name)
	{
		$this->db->where ( 'cid', $this->cid );
		$this->db->like ( 'name', $name, 'after' );
		$query = $this->db->get ( $this->table_name );
		$properties = $this->_load_all_where_result ( $query );
		foreach ( $properties as $property ) {
			$this->properties [$property->name] = $property;
		}
		return $properties;
	}

	/**
	 * return all the properties that start with 'config_'
	 *
	 * @return array
	 */
	function get_all_config()
	{
		return $this->get_all_like ( 'config_' );
	}

	/**
	 * delete all properties that are like '$name'
	 *
	 * @param string $name
	 * @return array
	 */
	function delete_all_like($name)
	{
		return $this->db->delete ( $this->table_name, "cid='$this->cid' AND name LIKE '$name%'" );
	}

	/**
	 * delete all the properties for a content.
	 *
	 * @return boolean
	 */
	function delete_all()
	{
		return $this->db->delete ( $this->table_name, "cid=$this->cid" );
	}

	/**
	 * return the property object based on the name
	 *
	 * @param string $name
	 * @param boolean $reload - if you want to reload a specific property you could set this to true
	 * @return ContentProperty_model
	 */
	function get($name, $reload = FALSE)
	{
		if (! isset ( $this->properties [$name] ) || $reload) {
			$cp_model = new ContentProperty_model ( );
			$cp_model->cid = $this->cid;
			$cp_model->name = $name;
			$state = $cp_model->load ();
			if ($state) {
				$this->properties [$name] = $cp_model;
			}
		}
		if (isset ( $this->properties [$name] )) {
			return $this->properties [$name];
		}
		return false;
	}

	/**
	 * forces the load of a property and returns the the property object.
	 *
	 * @param string $name
	 * @return ContentProperty_model
	 */
	function load($name)
	{
		return $this->get ( $name, TRUE );
	}

	/**
	 * return the value of a property or return the default value
	 *
	 * @param string $name
	 * @param string $default - you can return a default value if the property doesn't exist.
	 * @return string
	 */
	function get_value($name, $default = FALSE)
	{
		$property = $this->get ( $name );
		if ($property) {
			return $property->value;
		}
		return $default;
	}

	/**
	 * set or update the value for a specific property in the database.
	 *
	 * @param string $name
	 * @param string $value
	 * @return boolean
	 */
	function set($name, $value,$db = TRUE)
	{
		if($db){
			$this->load ( $name );
		}
		if (isset ( $this->properties [$name] )) {
			$property = $this->properties [$name];
			$property->value = $value;
			if($db){
				return $property->update ();
			}
		} else {
			$property = new ContentProperty_model ( );
			$property->cid = $this->cid;
			$property->name = $name;
			$property->value = $value;
			if($db){
				return $property->save ();
			}else{
				$this->properties[$name] = $property;
			}
		}
	}

	/**
	 * delete a specific property from the database
	 *
	 * @param string $name
	 * @return boolean
	 */
	function delete($name = null)
	{
		$property = new ContentProperty_model ( );
		$property->cid = $this->cid;
		$property->name = $name;
		return $property->delete ();
	}

}

?>