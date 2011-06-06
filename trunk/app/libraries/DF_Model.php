<?php

/**
 * Base class for all the models
 * this class provides an extended level of active record for a table.
 */
class DF_Model extends Model
{

	/**
	 * table name for the model
	 *
	 * @var string
	 */
	var $table_name;

	/**
	 * an array of field names in the table
	 *
	 * @var array
	 */
	var $fields;

	/**
	 * a string value of the primary field in the table
	 *
	 * @var string
	 */
	var $primary_key;

	/**
	 * the class name of the object that should be type casted.
	 *
	 * @var string
	 */
	var $parent_classname;

	/**
	 * if the class is loaded, then this value is true.
	 *
	 * @var boolean
	 */
	var $_loaded;

	/**
	 * Active-record database hook
	 *
	 * @var CI_DB_active_record
	 */
	var $db;
	
	/**
	 * base constructor for all our models, and acts like a setter for the parameters.
	 *
	 * @param string $parent_classname
	 * @param string $table_name
	 * @param array $fields
	 * @param string $primary_key
	 * @return DF_Model
	 */
	function DF_Model ($parent_classname = null, $table_name = null, $fields = null, $primary_key = null)
	{
		parent::Model();
		
		$this->parent_classname = $parent_classname;
		
		$this->table_name = $table_name;
		
		$this->fields = $fields;
		
		$this->primary_key = $primary_key;
		
		$this->_loaded = false;
		
		$this->load->database();
	}

	/**
	 * returns an array of the models based on the criteries you pass.
	 *
	 * @param array $where
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	function load_all_where ($where = array(), $limit = null, $offset = null)
	{
		$query = $this->db->get_where($this->table_name, $where, $limit, $offset);
		
		return $this->_load_all_where_result($query);
	}

	/**
	 * converts a result object into and array of models.
	 * It will construct different models and initialize them as loaded.
	 *
	 * @param CI_DB_result $query
	 * @return array
	 */
	function _load_all_where_result ($query)
	{
		$result = array( 
		);
		
		if ($query)
		{
			
			$result_objects = $query->result_object();
			
			$clazz_name = $this->parent_classname;
			
			foreach ($result_objects as $obj)
			{
				
				$final_res_obj = new $clazz_name();
				
				foreach ($this->fields as $key)
				{
					$final_res_obj->$key = $obj->$key;
				}
				
				$final_res_obj->_loaded = true;
				
				if (method_exists($final_res_obj, 'on_load'))
				{
					$final_res_obj->on_load();
				}
				
				array_push($result, $final_res_obj);
			}
			$query->free_result();
		}
		
		return $result;
	}

	/**
	 * load the current model with the given where conditions, and provides 
	 * you an option to wether save the values for the into the model, or just 
	 * to check if there is a record based on your query really exists or not
	 *
	 * @param array $where
	 * @param boolean $write_values
	 * @return boolean
	 */
	function load_where ($where = array(), $write_values = TRUE)
	{
		$primary_key = $this->primary_key;
		
		if (isset($this->$primary_key))
		{
			$where[$this->primary_key] = $this->$primary_key;
		}
		
		$query = $this->db->get_where($this->table_name, $where, 1);
		
		if ($query)
		{
			if ($query->num_rows() > 0)
			{
				if ($write_values)
				{
					
					$obj = $query->row();
					
					foreach ($obj as $key => $value)
					{
						$this->$key = $value;
					}
					
					$this->_loaded = true;
					
					if (method_exists($this, 'on_load'))
					{
						$this->on_load();
					}
				}
				return true;
			}
		}
		
		return false;
	}

	/**
	 * force load where based on the primary key.
	 *
	 */
	function load ()
	{
		$this->load_where();
	}

	/**
	 * insert the set field values into the database and fires 
	 * an 'on_insert' callback
	 *
	 * @return boolean
	 */
	function insert ()
	{
		$values = array( 
		);
		
		foreach ($this->fields as $key)
		{
			
			if ($key == $this->primary_key)
			{
				if (isset($this->$key))
				{
					$values[$key] = $this->$key;
				}
			} else
			{
				$values[$key] = $this->$key;
			}
		}
		
		$res = $this->db->insert($this->table_name, $values);
		
		if ($res)
		{
			
			$primary_key = $this->primary_key;
			
			$this->$primary_key = $this->db->insert_id();
			
			$this->_loaded = true;
			
			if (method_exists($this, 'on_insert'))
			{
				$this->on_insert();
			}
			
			return true;
		}
		return false;
	}

	/**
	 * saves the model into the DB, or just updates it if the 
	 * model was loaded earlier
	 *
	 * @return boolean
	 */
	function save ()
	{
		if ($this->_loaded)
		{
			return $this->update_where();
		} else
		{
			return $this->insert();
		}
	}

	/**
	 * update the record in the DB from the values that was set in the model
	 *
	 * @return boolean
	 */
	function update ()
	{
		return $this->update_where();
	}

	/**
	 * update the records based on the where clause
	 *
	 * @param array $where
	 * @param array $values
	 * @return boolean
	 */
	function update_where ($where = array(), $values = array())
	{
		foreach ($this->fields as $key)
		{
			
			if (! array_key_exists($key, $where))
			{
				
				if ($key == $this->primary_key)
				{
					
					if (isset($this->$key))
					{
						$values[$key] = $this->$key;
					}
				
				} else
				{
					$values[$key] = $this->$key;
				}
			}
		}
		$primary_key = $this->primary_key;
		
		if (isset($this->$primary_key))
		{
			$where[$this->primary_key] = $this->$primary_key;
		}
		
		return $this->db->update($this->table_name, $values, $where);
	}

	/**
	 * delete the record based on the where query.
	 *
	 * @param array $where
	 * @return boolean
	 */
	function delete_where ($where = array ())
	{
		
		$primary_key = $this->primary_key;
		
		if (isset($this->$primary_key))
		{
			$where[$this->primary_key] = $this->$primary_key;
			
			return $this->db->delete($this->table_name, $where);
		}
		
		return false;
	}

	/**
	 * just delete the current model from the DB if it 
	 * was loaded, or the primary key is set.
	 *
	 * @return boolean
	 */
	function delete ()
	{
		return $this->delete_where();
	}

	/**
	 * returns the count of records based on your where parameters
	 *
	 * @param array $where
	 * @return int
	 */
	function count ($where = array())
	{
		$this->db->from($this->table_name);
		
		foreach ($where as $key => $value)
		{
			$this->db->where($key, $value);
		}
		
		return $this->db->count_all_results();
	}

	/**
	 * deep clone a model
	 *
	 * @return DF_Model
	 */
	function deep_clone()
	{
		return unserialize(serialize($this));
	}
	
}

?>