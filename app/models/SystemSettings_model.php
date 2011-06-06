<?php

/**
 * the system property class holds all variables required.
 * You can set custom system properties and read them from the DB.
 * All properties are cached for the specific request.
 *
 * This class acts like a value object and also as a collection class.
 */
class SystemSettings_model extends DF_Model
{

	/**
	 * the key of the property.
	 *
	 * @var string
	 */
	var $name;

	/**
	 * string value of the key.
	 *
	 * @var string
	 */
	var $value;

	/**
	 * the collection of the system settings hashmap.
	 *
	 * @var array of SystemSettings_model
	 */
	var $properties;

	/**
	 * constructs a SystemSettings_model object with the settings for Active record.
	 * you will be able to set / get and delete properties.
	 *
	 * @return SystemSettings_model
	 */
	function SystemSettings_model ()
	{
		parent::DF_Model('SystemSettings_model', 'systemprop', array(
			'name' , 
			'value' 
		), 'name');
		$this->properties = array( 
		);
	}

	/**
	 * get all the system properties from the database.
	 *
	 * @return array
	 */
	function get_all ()
	{
		$properties = $this->load_all_where(array( 
		));
		foreach ($properties as $property)
		{
			$this->properties[$property->name] = $property;
		}
		return $this->properties;
	}

	/**
	 * return a subset of system properties that match the like criteria 
	 * Example 'theme_' will return all the properties that start with 'theme_'
	 *
	 * @param string $name
	 * @return array
	 */
	function get_all_like ($name)
	{
		$this->db->like('name', $name);
		$query = $this->db->get($this->table_name);
		$properties = $this->_load_all_where_result($query);
		foreach ($properties as $property)
		{
			$this->properties[$property->name] = $property;
		}
		return $properties;
	}

	/**
	 * delete all the properties that match the like criteria
	 *
	 * @param string $name
	 * @return boolean
	 */
	function delete_all_like ($name)
	{
		return $this->db->delete($this->table_name, "name LIKE '$name'");
	}

	/**
	 * return a system property based on the name.
	 *
	 * @param string $name
	 * @param boolean $reload force to fetch from database.
	 * @return SystemSettings_model
	 */
	function get ($name, $reload = FALSE)
	{
		if (! isset($this->properties[$name]) || $reload)
		{
			$property = new SystemSettings_model();
			$property->name = $name;
			$property->load_where(array(
				'name' => $this->name 
			));
			$this->properties[$name] = $property;
		}
		if (isset($this->properties[$name]))
		{
			return $this->properties[$name];
		}
		return false;
	}

	/**
	 * force load of the property from the database and return a SystemSettings_model
	 *
	 * @param string $name
	 * @return SystemSettings_model
	 */
	function load ($name, $return_obj = FALSE)
	{
		if ($return_obj)
		{
			return $this->get($name, TRUE);
		} else
		{
			$this->get($name, TRUE);
		}
	}

	/**
	 * get the string value of a key if the key exists and you can force load from database if required
	 *
	 * @param string $name
	 * @return string
	 */
	function get_value ($name, $reload = FALSE)
	{
		$property = $this->get($name, $reload);
		if ($property)
		{
			return $property->value;
		}
		return false;
	}

	/**
	 * set a new system property or update an existing property value in the database.
	 * This will also reflect the SystemSettings_model collection cache
	 *
	 * @param string $name
	 * @param string $value
	 * @return boolean
	 */
	function set ($name, $value)
	{
		if (isset($this->properties[$name]))
		{
			$property = $this->properties[$name];
			$property->value = $value;
			return $property->update();
		} else
		{
			$property = new SystemSettings_model();
			$property->name = $name;
			$property->value = $value;
			return $property->save();
		}
	}

	/**
	 * delete a system property based on the name.
	 *
	 * @param string $name
	 * @return boolean
	 */
	function delete ($name = null)
	{
		$property = new SystemSettings_model();
		$property->name = $name;
		return $property->delete();
	}
}

?>