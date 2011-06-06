<?php

/**
 * Base controller for all the admin controllers.
 */
class DF_AdminController extends DF_Controller
{

	/**
	 * create the dfgallery header
	 *
	 * @param string $title
	 * @param array $breadcrumbs
	 * @param boolean $include_left_column
	 */
	function do_header ($title = "DfGallery 2 : Admin ", $breadcrumbs = array(), $include_left_column = TRUE)
	{
		parent::do_header(array('title'=>$title,'version'=>$this->version()));
		
		if ($include_left_column)
		{
			$this->load->view('templates/admin/left_column');
		}
		
		$this->do_pre_status();

		$this->do_status();
		
		$this->do_breadcrumb($breadcrumbs);
	}

	/**
	 * do footer
	 */
	function do_footer ()
	{
		$this->load->view('templates/admin/footer');
		
		parent::do_footer();
	}

	/**
	 * create breadcrumbs
	 *
	 * @param array $breadcrumbs
	 */
	function do_breadcrumb ($breadcrumbs)
	{
		$breadcrumbs = array_merge(array(
			array(
				'url' => base_url() . 'admin/' , 
				'title' => 'dfGallery' 
			) 
		), $breadcrumbs);
		
		parent::do_breadcrumb($breadcrumbs);
	}
	
	function do_pre_status(){
		if($this->config->item('configured_password', 'dfg/resetpassword') !== FALSE){
			$this->session->set_userdata('level', 'red');
			$msg = $this->session->userdata('message');
			if($msg !== FALSE){
				$this->session->set_userdata('message', $msg . '<br/> "configured_password" option has been enabled, please comment the lines in config/dfg/resetpassword.php immediately');
			}else{
				$this->session->set_userdata('message', '"configured_password" option has been enabled!<br/>This option should have been turned on only during admin password reset.<br/>Please disable this by commenting the lines in the  config/dfg/resetpassword.php file ASAP.');			}
		}		
	}
	
}

?>