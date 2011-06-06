<?php

/**
 * Construct a User Model.
 */
class User_model extends DF_Model
{

	/**
	 * the unique id for the user
	 *
	 * @var int
	 */
	var $id;

	/**
	 * username of the user
	 *
	 * @var string
	 */
	var $username;

	/**
	 * password hash of the user.
	 *
	 * @var string
	 */
	var $password;

	/**
	 * email id of the user
	 *
	 * @var string
	 */
	var $mail;

	/**
	 * the timestamp of the user creation.
	 *
	 * @var long
	 */
	var $created;

	/**
	 * construct a user model with the required fields in the table.
	 *
	 * @return User_model
	 */
	function User_model ()
	{
		parent::DF_Model('User_model', 'users', array(
			'id' , 
			'username' , 
			'password' , 
			'mail' , 
			'created' 
		), 'id');
	}

	/**
	 * validate the username and password record and return the userID if the 
	 * record exists or return -1
	 *
	 * @param string $username
	 * @param string $password
	 * @return int
	 */
	function validate_credentials ($username, $password)
	{
		$result = $this->db->get_where('users', array(
			'username' => $username , 
			'password' => $password 
		));
		if ($result!=NULL && $result->num_rows() > 0)
		{
			$row = $result->row();
			return $row->id;
		} else
		{
			return - 1;
		}
	}

	/**
	 * update the user password with a new password hash only if the old password has  matches.
	 *
	 * @param string $old_password
	 * @param string $new_password
	 * @return boolean
	 */
	function update_password ($old_password, $new_password)
	{
		if ($this->password == $old_password)
		{
			$this->password = $new_password;
			return $this->update();
		}
		return false;
	}

}
?>