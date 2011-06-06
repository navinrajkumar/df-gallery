<?php

/**
 * The base controller for all other controllers.
 */
class DF_Controller extends Controller
{
	
	/**
	 * stores the user ID
	 *
	 * @var int
	 */
	var $uid;

	/**
	 * default constructor,sets the UID based on the session data
	 *
	 * @return DF_Controller
	 */
	function DF_Controller()
	{
		parent::Controller ();
		
		$this->uid = $this->session->userdata ( 'uid' );
	}

	/**
	 * includes the templates/header view and sets the title for the header.
	 *
	 * @param array $params
	 */
	function do_header($params = array('title'=> "DfGallery 2"))
	{
		$this->load->view ( 'templates/header', $params );
		$this->do_status();
	}

	function do_footer()
	{
		$this->load->view ( 'templates/footer', array (
				'version' => $this->version () ) );
	}

	/**
	 * loads the breadcrumb view
	 *
	 * @param array $breadcrumbs
	 */
	function do_breadcrumb($breadcrumbs)
	{
		$this->load->view ( 'templates/breadcrumbs', array (
				'breadcrumbs' => $breadcrumbs ) );
	}

	/**
	 * display the status as a flash from the session.
	 */
	function do_status ()
	{
		$message = $this->session->userdata('message');
		
		if ($message !== FALSE)
		{
			$level = $this->session->userdata('level');
			
			$this->load->view('templates/message', array(
				'level' => $level , 
				'message' => $message 
			));
			
			$this->session->unset_userdata('message');
			
			$this->session->unset_userdata('level');
		}
	}

	function version()
	{
		$version = $this->config->item ( 'upgrade_version', 'dfg/upgrade_state' );
		return ($version === FALSE) ? $version : '2.0';
	}
	
	/**
	 * set a status message and redirect the user.
	 *
	 * @param string $level
	 * @param string $message
	 * @param string $url
	 */
	function redirect_message ($level, $message, $url = 'admin/gallery/status')
	{
		$this->session->set_userdata('level', $level);
		
		$this->session->set_userdata('message', $message);
		
		redirect($url);
	}
	
	/**
	 * if the state is true then redirect with a status message, or else redirect with a bad status message.
	 *
	 * @param boolean $state
	 * @param string $level
	 * @param string $message
	 * @param string $url
	 * @param string $level2
	 * @param string $message2
	 * @param string $url2
	 */
	function if_redirect_message ($state, $level, $message, $url = null, $level2 = null, $message2 = null, $url2 = null)
	{
		if ($url2 == null)
		{
			$url2 = $url;
		}
		
		if ($state)
		{
			$this->redirect_message($level, $message, $url);
		} else
		{
			if (! is_null($level2))
			{
				$this->redirect_message($level2, $message2, $url2);
			}
		}
	}

}

?>