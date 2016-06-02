<?php

class Admin extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/banners/viewall';				// default redirect
	var $permissions = array();
	var $uploadsPath;
        var $table;
	
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

                $this->table = 'banners';

		// load libs
		// $this->load->model('banners_model', 'banners');

	}
	
	function index()
	{
		redirect($this->redirect);
	}
	
	function viewall()
	{		
		// check permissions for this page
		if (!in_array('banners', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

	        $banners = $this->core->viewall($this->table);
		// send data to view
		$output['banners'] = @$banners['banners'];      	

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/banners',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_banner($banner_id = '')
	{
		// check permissions for this page
		if (!in_array('banners_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'time_delay' => array('label' => 'Time delay', 'rules' => 'required|trim|is_natural_no_zero'),
			'sequence_order' => array('label' => 'Order', 'rules' => 'required|is_natural_no_zero'),
			'banner_url' => array('label' => 'Banner URL', 'rules' => 'required|trim')		
		);

		// where
		$objectID = array('banner_id' => $banner_id, 'deleted' => 0, 'siteID' => $this->siteID);	

		// get values
		$output['data'] = $this->core->get_values('banners', $objectID);
                
                if (!$output['data']) 
                {
                	show_404();
                }	

		$this->uploads->maxWidth = '1280';
		$this->uploads->maxHeight = '480';	
		// upload image
		if (@$_FILES['image']['name'] != '')
		{
			if ($imageData = $this->uploads->upload_image(FALSE, '', 'image'))
			{	
				$this->core->set['banner_file'] = $imageData['file_name'];
				$this->core->set['file_type'] = $imageData['file_ext'];
			}
		}

		// get image errors if there are any
		if ($this->uploads->errors)
		{
			$this->form_validation->set_error($this->uploads->errors);
		}
		else
		{	
			if (count($_POST) && $this->core->check_errors())
			{	
			
				$this->core->set['banner_url'] = prep_url(trim($this->input->post('banner_url')));
				// update
				if ($this->core->update('banners', $objectID))
				{
					// set success message
                                        $this->session->set_flashdata('success', 'Your changes were saved.');
 				
				} 
				else 
				{
					// set error message
					$this->session->set_flashdata('success', 'An error occured while Banner details were being updated.');

				}
				
				// where to redirect to
				redirect('/admin/banners/edit_banner/'.$banner_id);

			}
				
		}		

		// set message
		if ($message = $this->session->flashdata('success'))
		{
			$output['message'] = '<p>'.$message.'</p>';
		}

		// set image path!
		if ($image = $output['data']['banner_file']) 
		{
			$output['imagePath'] = $this->uploadsPath .'/'. $image; 
		} 
		else 
		{
			$output['imagePath'] = $this->config->item('staticPath').'/images/nopicture.jpg';
		}

		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/manage_banner',$output);
		$this->load->view($this->includes_path.'/footer');			
		
	}

}

