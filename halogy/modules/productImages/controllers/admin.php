<?php
/**
 * Halogy
 *
 * A user friendly, modular content management system for PHP 5.0
 * Built on CodeIgniter - http://codeigniter.com
 *
 * @package		Halogy
 * @author		Haloweb Ltd.
 * @copyright	Copyright (c) 2008-2011, Haloweb Ltd.
 * @license		http://halogy.com/license
 * @link		http://halogy.com/
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Admin extends MX_Controller {

	// set defaults
	var $table = 'pages';								// table to update
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/shop/products';				// default redirect
	var $permissions = array();
	var $uploadsPath;
	function __construct()
	{
		parent::__construct();

		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}
		
		// get permissions and redirect if they don't have access to this module
		if (!$this->permission->permissions)
		{
			redirect('/admin/dashboard/permissions');
		}
		if (!in_array($this->uri->segment(2), $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
                $this->uploadsPath = $this->config->item('uploadsPath');
                $this->siteVars = $this->site->config;
		// load libs
		$this->load->model('shop_model', 'shop');
		$this->load->library('tags');
	}
	
	function index()
	{
		redirect($this->redirect);
	}
	
        
     
}