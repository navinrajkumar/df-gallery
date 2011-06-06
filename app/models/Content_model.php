<?php

/**
 * global includes.
 */
include_once APPPATH . 'models/ContentProperties_model' . EXT;

/**
 * the base class for all content models.
 * This class provides the default fields and content properties.
 */
class Content_model extends DF_Model
{

	/**
	 * unique id for the content.
	 *
	 * @var int
	 */
	var $id;

	/**
	 * every content can have a parent, or can also be NULL
	 *
	 * @var int
	 */
	var $pid;

	/**
	 * the user to which the content belongs to.
	 *
	 * @var int
	 */
	var $uid;

	/**
	 * a string representation of the content.
	 *
	 * @var string
	 */
	var $type;

	/**
	 * every content can have a title.
	 *
	 * @var string
	 */
	var $title;

	/**
	 * all content within the system can have instance specific properties.
	 *
	 * @var ContentProperties_model
	 */
	var $properties;

	/**
	 * construct a content object with a specific type.
	 *
	 * @param unknown_type $type
	 * @return Content_model
	 */
	function Content_model($type = '')
	{
		$this->type = $type;
		parent::DF_Model ( 'Content_model', 'content', array (
				'id', 
				'pid', 
				'uid', 
				'type', 
				'title' 
		), 'id' );
		
		$this->properties = new ContentProperties_model ( );
	}

	/**
	 * return an array of this specific content type with a where clause, limit and offset.
	 *
	 * @param array $where
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	function load_all($where = array(), $limit = null, $offset = null)
	{
		if (isset ( $this->uid )
		 && is_numeric ( $this->uid )) {
			$where ['uid'] = $this->uid;
		}
		if (isset ( $this->pid ) && is_numeric ( $this->pid )) {
			$where ['pid'] = $this->pid;
		}
		$where ['type'] = $this->type;
		return $this->load_all_where ( $where, $limit, $offset );
	}

	/**
	 * based on the uid, type we reload the content model.
	 * you can also specify a custom where clause
	 *
	 * @param array $where
	 * @return boolean
	 */
	function load($where = array())
	{
		if (isset ( $this->uid ) && is_numeric ( $this->uid )) {
			$where ['uid'] = $this->uid;
		}
		$where ['type'] = $this->type;
		return $this->load_where ( $where );
	}

	/**
	 * based on the uid, type we delete the content model.
	 * you can also specify a custom where clause
	 *
	 * @param array $where
	 * @return boolean
	 */
	function delete($where = array())
	{
		$where = array_merge ( $where, array (
				'uid' => $this->uid 
		) );
		
		$status = $this->delete_where ( $where );
		if ($status) {
			$this->properties->delete_all ();
		}
		return $status;
	}

	/**
	 * the 'load' callback when the record is loaded or modified.
	 */
	function on_load()
	{
		$this->properties->cid = $this->id;
	}

	/**
	 * on_insert this function is invoked.
	 */
	function on_insert()
	{
		$this->properties = new ContentProperties_model ( );
		$this->properties->cid = $this->id;
	}

	/**
	 * return the number of records based on the where clause.
	 *
	 * @param array $where
	 * @return int
	 */
	function count($where = array())
	{
		$where = array_merge ( $where, array (
				'uid' => $this->uid, 
				'type' => $this->type 
		) );
		return parent::count ( $where );
	}

}

?>