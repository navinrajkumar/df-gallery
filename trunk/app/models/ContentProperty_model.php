<?php

/**
 * The base class for a content proeprty in the Df architecture.
 * 
 * This allows you to add any type of property for a content.
 */
class ContentProperty_model extends DF_Model
{

	/**
	 * the unique ID for the content property
	 *
	 * @var int
	 */
	var $id;

	/**
	 * the content id which this property is linked to.
	 * This should be a foreign key relation and the cid object must exist.
	 *
	 * @var int
	 */
	var $cid;

	/**
	 * the name of the property
	 *
	 * @var string
	 */
	var $name;

	/**
	 * the string value of the property.
	 *
	 * @var string
	 */
	var $value;

	/**
	 * construct the ContentPorperty_model with the required fields and table settings.
	 *
	 * @return ContentProperty_model
	 */
	function ContentProperty_model ()
	{
		parent::DF_Model('ContentProperty_model', 'contentprop', array(
			'id' , 
			'cid' , 
			'name' , 
			'value' 
		), 'id');
	}

	/**
	 * fetch all the possible records based on the cid that has been set for this object.
	 *
	 * @return array
	 */
	function load_all ()
	{
		return $this->load_all_where(array(
			'cid' => $this->cid 
		));
	}

	/**
	 * force the load of the content property object based on the cid, and name that you have set for this value object.
	 * @return boolean if the record was loaded.
	 */
	function load ()
	{
		return $this->load_where(array(
			'cid' => $this->cid , 
			'name' => $this->name 
		));
	}

	/**
	 * returns true if the record exists in the database
	 *
	 * @return boolean
	 */
	function property_exists ()
	{
		return $this->load_where(array(
			'cid' => $this->cid , 
			'name' => $this->name 
		), FALSE);
	}

	/**
	 * save the current settings of this object.
	 * if the record was loaded, the we just update it.
	 *
	 * @return boolean
	 */
	function save ()
	{
		$cp_model = new ContentProperties_model();
		
		$cp_model->cid = $this->cid;
		
		$cp_model->name = $this->name;
		
		$loaded = $cp_model->load($this->name);
		
		if ($loaded)
		{
			if ($this->value != $cp_model->value)
			{
				return $this->update_where(array(
					'cid' => $this->cid , 
					'name' => $this->name 
				));
			}
		} else
		{
			return $this->insert();
		}
		return false;
	}

	/**
	 * delete the current ContentProperty_model from the database.
	 * please remember to set the cid, name etc...
	 *
	 * @return boolean
	 */
	function delete ()
	{
		if (! is_null($this->name))
		{
			return $this->delete_where(array(
				'cid' => $this->cid , 
				'name' => $this->name 
			));
		}
		return $this->db->delete($this->table_name, array(
			'cid' => $this->cid 
		));
		;
	}
}
?>