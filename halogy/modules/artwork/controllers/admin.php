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

	var $includes_path = '/includes/admin';				// path to includes for header and footer

	var $redirect = '/admin/artworks/viewall';				// default redirect

	var $permissions = array();

    var $type = "artwork";

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

		

		//  load models and libs

		$this->load->library('tags');		

		$this->load->model('artworks_model', 'artworks');

	}

	

	function index()

	{

		redirect($this->redirect);

	}



        function preview()

	{

		// get parsed body

		$html = $this->template->parse_body($this->input->post('body'));



		// filter for scripts

		$html = preg_replace('/<script(.*)<\/script>/is', '<em>This block contained scripts, please refresh page.</em>', $html);



		// output

		$this->output->set_output($html);

	}

	

	function viewall()

	{

		// default where

		

		$where = array('siteID' => $this->siteID, 'deleted' => 0, 'draftstatus' => 0);

		

		if ($this->input->post('artworksearch'))

		{

			$this->session->set_userdata('search_art_title', $this->input->post('title'));

			$this->session->set_userdata('search_artist', $this->input->post('artist'));

			$this->session->set_userdata('search_owner', $this->input->post('owner'));

			$this->session->set_userdata('search_artwork_id', $this->input->post('artwork_id'));

			$this->session->set_userdata('search_categ_id', $this->input->post('category_link'));

			$this->session->set_userdata('search_activate', 'yes');

		}

		

		if ($this->input->get('list'))

		{

			$this->session->unset_userdata('search_art_title');

			$this->session->unset_userdata('search_categ_id');

			$this->session->unset_userdata('search_artist');

			$this->session->unset_userdata('search_owner');

			$this->session->unset_userdata('search_artwork_id');

		}

		

		// grab data and display

		$output = $this->core->viewall_artworks('artworks', $where, array('artworkTitle', 'asc'));

		

		$this->load->view($this->includes_path.'/header');

		$this->load->view('admin/viewall',$output);

		$this->load->view($this->includes_path.'/footer');

	}
	
	
	
	function viewall_drafts()

	{

		// default where
		

		$where = array('siteID' => $this->siteID, 'deleted' => 0, 'draftstatus' => 1);

		

		if ($this->input->post('artworksearch'))

		{

			$this->session->set_userdata('search_art_title', $this->input->post('title'));

			$this->session->set_userdata('search_artist', $this->input->post('artist'));

			$this->session->set_userdata('search_owner', $this->input->post('owner'));

			$this->session->set_userdata('search_artwork_id', $this->input->post('artwork_id'));

			$this->session->set_userdata('search_categ_id', $this->input->post('category_link'));

			$this->session->set_userdata('search_activate', 'yes');

		}

		

		if ($this->input->get('list'))

		{

			$this->session->unset_userdata('search_art_title');

			$this->session->unset_userdata('search_categ_id');

			$this->session->unset_userdata('search_artist');

			$this->session->unset_userdata('search_owner');

			$this->session->unset_userdata('search_artwork_id');

		}

		

		// grab data and display

		$output = $this->core->viewall_artworks('artworks', $where, array('artworkTitle', 'asc'));

		

		$this->load->view($this->includes_path.'/header');

		$this->load->view('admin/viewall_drafts',$output);

		$this->load->view($this->includes_path.'/footer');

	}

	

	function add_artwork()

	{

		// check permissions for this page

		if (!in_array('artworks_edit', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}

		

		// required

		$this->core->required = array(

			'artworkTitle' => array('label' => 'Artwork title', 'rules' => 'required|trim'),

		);

		// get values

		$output['data'] = $this->core->get_values('artworks');	

		if (count($_POST))

		{

			// set date

			//$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			
			$this->core->set['dateCreated'] =  date("Y-m-d H:i:s", strtotime($this->input->post('dateCreated').' 12AM'));

			$this->core->set['tags'] = trim(strtolower($this->input->post('tags')));

			$this->core->set['userID'] = $this->session->userdata('userID');

			$this->core->set['artworkDate'] = date("Y-m-d H:i:s", strtotime($this->input->post('artworkDate').' 12AM'));

			$this->core->set['artworkEnd'] = ($this->input->post('artworkEnd')) ? date("Y-m-d H:i:s", strtotime($this->input->post('artworkEnd').' 11.59PM')) : '';

			

			if ($this->input->post('category_link'))

			{

				//insert category name tag

				$this->db->select('categTitle');

				$this->db->where('categID', $this->input->post('category_link'));

				$categinfoquery = $this->db->get('ha_categs');

				if ($categinfoquery->num_rows())

				{

					$categinfoqueryresult = $categinfoquery->result_array();			

					foreach($categinfoqueryresult as $categinfoqueryvar)

					{	

					$category_link_name = $categinfoqueryvar['categTitle'];

					$this->core->set['categoryname_tag'] = $category_link_name;

					}

					

				}

			}

			

			//insert artwork search tags

			$posted_artistID = $this->input->post('artist_link');

			$this->db->select('first_name, last_name');

			$this->db->where('postID', $posted_artistID);

			$artistquery = $this->db->get('ha_artists_posts');

			if ($artistquery->num_rows())

			{

				$artistqueryresult = $artistquery->result_array();			

				foreach($artistqueryresult as $artistqueryvar)

				{	

				$first_name = $artistqueryvar['first_name'];

				$last_name = $artistqueryvar['last_name'];

				$this->core->set['fn_tag'] = $first_name;

				$this->core->set['ln_tag'] = $last_name;

				$this->core->set['search_tags'] = "".$this->input->post('artworkTitle')." ".$first_name." ".$last_name." ".$last_name." ".$first_name."";

				}

		

			}

		

			$config['allowed_types'] = 'gif|jpg|jpeg|png';

			$config['overwrite']  = true;

			$unframed_img = '';

			

			//unframed image

			if ($_FILES['unframed_image']['name'] != '') 

			{

				$randfilename = random_string('alnum');

				$config['upload_path'] = getcwd().'/static/uploads/artists/artworks/unframed_images';

				$extension = end(explode(".", $_FILES['unframed_image']['name']));

				$config['file_name']= $randfilename.".".$extension;

				$this->load->library('upload');

				$this->upload->initialize($config);

				if ( !$this->upload->do_upload('unframed_image')) {

					if ($this->upload->display_errors()){

					//error code here

					}

				}

				else {

					$unframed_img = $randfilename.".".$extension;

					$this->core->set['unframed_image'] = $unframed_img;

				}

			}

			

			$framed_img = '';

			

			//framed image

			if ($_FILES['framed_image']['name'] != '') 

			{

				$randfilename = random_string('alnum');

				$config['upload_path'] = getcwd().'/static/uploads/artists/artworks/framed_images';

				$extension = end(explode(".", $_FILES['framed_image']['name']));

				$config['file_name']= $randfilename.".".$extension;

				$this->load->library('upload');

				$this->upload->initialize($config);

				if ( !$this->upload->do_upload('framed_image')) {

					if ($this->upload->display_errors()){

					//error code here

					}

				}

				else {

					$framed_img = $randfilename.".".$extension;

					$this->core->set['framed_image'] = $framed_img;

				}

			}

			if (!$this->input->post('owner_link'))
			{
				$this->core->set['owner_status'] = 1;
			}
			else
			{
				
				if ($ownerinfo = $this->get_ownerinfo($this->input->post('owner_link')))
				{
					$owner_active_status = $ownerinfo['active'];
					if ($owner_active_status ==1)
					{
						$this->core->set['owner_status'] = 1;
					}
					else
					{
						$this->core->set['owner_status'] = 0;
					}
					
				}
				
			}
			
			

			// update

			if ($this->core->update('artworks'))

			{

				$artworkID = $this->db->insert_id();

				

				/*

				 * Create Image folder on upload modules

				 */

				

				if($this->input->post('gallery') == '1'){

					

					$this->core->set['dateCreated'] = date("Y-m-d H:i:s");

					$this->core->set['folderSafe'] = $this->type.'-'.date('d-m-Y').'-'.url_title(strtolower($this->input->post('artworkTitle')));

					$this->core->set['folderName'] = $this->type.'-'.date('d-m-Y').'-'.url_title(strtolower($this->input->post('artworkTitle')));

					$this->core->set['postID'] = $artworkID;

					$this->core->set['folder_type'] = $this->type;

					$this->core->update('image_folders');

				}

				// update tags

				$this->artworks->update_tags($artworkID, $this->input->post('tags'));

				

				// select all artists

				$this->db->select('postID');

				$this->db->where('deleted', 0);

				$this->db->where('siteID', $this->siteID);

				$allartistquery = $this->db->get('ha_artists_posts');

				if ($allartistquery->num_rows())

				{

					$allartistqueryresult = $allartistquery->result_array();			

					foreach($allartistqueryresult as $allartistqueryvar)

					{	

						$postID = $allartistqueryvar['postID'];

						

						// check artists with consignment type artworks (update artists db)

						$artworkrecquery = "SELECT * FROM ha_artworks WHERE (consignment = 1 OR reduced_consignment = 1)AND artist_link = '".$postID."'"; 

						$artworkrecresult = mysql_query($artworkrecquery) or die(mysql_error());

						$totalartwork_of_artist = mysql_num_rows($artworkrecresult);

						

						if ($totalartwork_of_artist != 0) 

						{

							$artistconsignment = 1;	

						}

						

						if ($totalartwork_of_artist == 0) 

						{

							$artistconsignment = 0;	

						}

						

						$artist_data = array(

									   'consignment' => $artistconsignment,

									);

						

						$this->db->where('postID', $postID);

						$this->db->update('ha_artists_posts', $artist_data); 

						

						// check artists with new release artworks (update artists db)

						$artworknewreleasequery = "SELECT * FROM ha_artworks WHERE new_release = 1 AND artist_link = '".$postID."'"; 

						$artworknewreleaseresult = mysql_query($artworknewreleasequery) or die(mysql_error());

						$total_nr_artwork_of_artist = mysql_num_rows($artworknewreleaseresult);

						

						if ($total_nr_artwork_of_artist != 0) 

						{

							$artistnewrelease = 1;	

						}

						

						if ($total_nr_artwork_of_artist == 0) 

						{

							$artistnewrelease = 0;	

						}

						

						$artist_nr_data = array(

									   'newrelease' => $artistnewrelease,

									);

						

						$this->db->where('postID', $postID);

						$this->db->update('ha_artists_posts', $artist_nr_data); 

					}

				}

				

				if ($this->input->post('addimage') == 1) 

				{

					redirect('admin/artworkimages/viewall?id='.$artworkID.'');

				}

							

				// where to redirect to

				redirect($this->redirect);

			}

		}

		

		// set default date

		$output['data']['artworkDate'] = ($this->input->post('artworkDate')) ? $this->input->post('artworkDate') : dateFmt(date("Y-m-d H:i:s"), 'd M Y');
		
		$output['data']['dateCreated'] = ($this->input->post('dateCreated')) ? $this->input->post('dateCreated') : dateFmt(date("Y-m-d H:i:s"), 'd M Y');

		// templates

		$this->load->view($this->includes_path.'/header3');

		$this->load->view('admin/add_artwork', $output);

		$this->load->view($this->includes_path.'/footer');

	}


			
	function get_ownerinfo($owner_link)
	{
		// default wheres
		$this->db->where('userID', $owner_link);		

		// grab
		$query = $this->db->get('ha_users', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}		
	}			


	function edit_artwork($artworkID)

	{

		// check permissions for this page

		if (!in_array('artworks_edit', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}

		

		// set object ID

		$objectID = array('artworkID' => $artworkID);

				

		// required

		$this->core->required = array(

			'artworkTitle' => array('label' => 'Artwork title', 'rules' => 'required|trim'),

		);

		// get values

		$output['data'] = $this->core->get_values('artworks', $objectID);	

		if (count($_POST))

		{

			// set date

			/*$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			
			
			if($this->input->post('manualdateCreated'))
			{
				$this->core->set['dateCreated'] = date("Y-m-d H:i:s", strtotime($this->input->post('manualdateCreated')));
			}
			*/
			
			$this->core->set['dateCreated'] =  date("Y-m-d H:i:s", strtotime($this->input->post('dateCreated').' 12AM'));

			$this->core->set['tags'] = trim(strtolower($this->input->post('tags')));

			$this->core->set['artworkDate'] = date("Y-m-d H:i:s", strtotime($this->input->post('artworkDate').' 12AM'));

			$this->core->set['artworkEnd'] = ($this->input->post('artworkEnd')) ? date("Y-m-d H:i:s", strtotime($this->input->post('artworkEnd').' 11.59PM')) : '';

			

			$this->core->set['categoryname_tag'] = NULL;

			

			if ($this->input->post('category_link'))

			{

				//insert category name tag

				$this->db->select('categTitle');

				$this->db->where('categID', $this->input->post('category_link'));

				$categinfoquery = $this->db->get('ha_categs');

				if ($categinfoquery->num_rows())

				{

					$categinfoqueryresult = $categinfoquery->result_array();			

					foreach($categinfoqueryresult as $categinfoqueryvar)

					{	

					$category_link_name = $categinfoqueryvar['categTitle'];

					$this->core->set['categoryname_tag'] = $category_link_name;

					}

					

				}

			}

			

			

			//insert artwork search tags

			$posted_artistID = $this->input->post('artist_link');

			$this->db->select('first_name, last_name');

			$this->db->where('postID', $posted_artistID);

			$artistquery = $this->db->get('ha_artists_posts');

			if ($artistquery->num_rows())

			{

				$artistqueryresult = $artistquery->result_array();			

				foreach($artistqueryresult as $artistqueryvar)

				{	

					$first_name = $artistqueryvar['first_name'];

					$last_name = $artistqueryvar['last_name'];

					$this->core->set['fn_tag'] = $first_name;

					$this->core->set['ln_tag'] = $last_name;

					$this->core->set['search_tags'] = "".$this->input->post('artworkTitle')." ".$first_name." ".$last_name." ".$last_name." ".$first_name."";

				}

		

			}

		

			

			if ($this->input->post('remove_framed_image') == 1) {

				$this->core->set['framed_image'] = '';

			}

			

			if ($this->input->post('remove_unframed_image') == 1) {

				$this->core->set['unframed_image'] = '';

			}

			

			

			$config['allowed_types'] = 'gif|jpg|jpeg|png';

			$config['overwrite']  = true;

			$unframed_img = '';

			

			//unframed image

			if ($_FILES['unframed_image']['name'] != '') {

				$randfilename = random_string('alnum');

				$config['upload_path'] = getcwd().'/static/uploads/artists/artworks/unframed_images';

				$extension = end(explode(".", $_FILES['unframed_image']['name']));

				$config['file_name']= $randfilename.".".$extension;

				$this->load->library('upload');

				$this->upload->initialize($config);

				if ( !$this->upload->do_upload('unframed_image')) {

					if ($this->upload->display_errors()){

					//error code here

					}

				}

				else {

					$unframed_img = $randfilename.".".$extension;

					$this->core->set['unframed_image'] = $unframed_img;

				}

			}

			

			$framed_img = '';

			//framed image

			if ($_FILES['framed_image']['name'] != '') 

			{

				$randfilename = random_string('alnum');

				$config['upload_path'] = getcwd().'/static/uploads/artists/artworks/framed_images';

				$extension = end(explode(".", $_FILES['framed_image']['name']));

				$config['file_name']= $randfilename.".".$extension;

				$this->load->library('upload');

				$this->upload->initialize($config);

				if ( !$this->upload->do_upload('framed_image')) {

					if ($this->upload->display_errors()){

					//error code here

					}

				}

				else {

					$framed_img = $randfilename.".".$extension;

					$this->core->set['framed_image'] = $framed_img;

				}

			}

			
			if (!$this->input->post('owner_link'))
			{
				$this->core->set['owner_status'] = 1;
			}
			else
			{
				
				if ($ownerinfo = $this->get_ownerinfo($this->input->post('owner_link')))
				{
					$owner_active_status = $ownerinfo['active'];
					if ($owner_active_status ==1)
					{
						$this->core->set['owner_status'] = 1;
					}
					else
					{
						$this->core->set['owner_status'] = 0;
					}
					
				}
				
			}
			
			

			// update

			if ($this->core->update('artworks', $objectID))

			{

                                

				// update tags

				$this->artworks->update_tags($artworkID, $this->input->post('tags'));

				// set success message

				$this->session->set_flashdata('success', TRUE);

				/*

				 * Create Image folder on upload modules

				 */

				if($this->input->post('gallery') == '1'){

					$this->db->where('postID', $artworkID);

					$this->db->where('deleted', 0);

					$this->db->where('folder_type', $this->type);

					$query = $this->db->get('image_folders');

					

					$this->core->set['folderSafe'] = $this->type.'-'.date('d-m-Y').'-'.url_title(strtolower($this->input->post('artworkTitle')));

					$this->core->set['folderName'] = $this->type.'-'.date('d-m-Y').'-'.url_title(strtolower($this->input->post('artworkTitle')));

					$this->core->set['folder_type'] = $this->type;

					if ($query->num_rows()) {

						print_r($this->core->set);

						$this->core->update('image_folders', array('postID' => $artworkID, 'folder_type' => $this->type));

					}

					else {

						$this->core->set['postID'] = $artworkID;

						$this->core->update('image_folders');

					}

				}

								

				// select all artists

				$this->db->select('postID');

				$this->db->where('deleted', 0);

				$this->db->where('siteID', $this->siteID);

				$allartistquery = $this->db->get('ha_artists_posts');

				if ($allartistquery->num_rows())

				{

					$allartistqueryresult = $allartistquery->result_array();			

					foreach($allartistqueryresult as $allartistqueryvar)

					{	

						$postID = $allartistqueryvar['postID'];

						

						// check artists with consignment type artworks (update artists db)

						$artworkrecquery = "SELECT * FROM ha_artworks WHERE (consignment = 1 OR reduced_consignment = 1)AND artist_link = '".$postID."'"; 

						$artworkrecresult = mysql_query($artworkrecquery) or die(mysql_error());

						$totalartwork_of_artist = mysql_num_rows($artworkrecresult);

						

						if ($totalartwork_of_artist != 0) 

						{

							$artistconsignment = 1;	

						}

						

						if ($totalartwork_of_artist == 0) 

						{

							$artistconsignment = 0;	

						}

						

						$artist_data = array(

									   'consignment' => $artistconsignment,

									);

						

						$this->db->where('postID', $postID);

						$this->db->update('ha_artists_posts', $artist_data); 

						

						// check artists with new release artworks (update artists db)

						$artworknewreleasequery = "SELECT * FROM ha_artworks WHERE new_release = 1 AND artist_link = '".$postID."'"; 

						$artworknewreleaseresult = mysql_query($artworknewreleasequery) or die(mysql_error());

						$total_nr_artwork_of_artist = mysql_num_rows($artworknewreleaseresult);

						

						if ($total_nr_artwork_of_artist != 0) 

						{

							$artistnewrelease = 1;	

						}

						

						if ($total_nr_artwork_of_artist == 0) 

						{

							$artistnewrelease = 0;	

						}

						

						$artist_nr_data = array(

									   'newrelease' => $artistnewrelease,

									);

						

						$this->db->where('postID', $postID);

						$this->db->update('ha_artists_posts', $artist_nr_data); 

					}

				}
				
				
				// where to redirect to

				redirect($this->uri->uri_string());

			}

		}

		// set message

		if ($this->session->flashdata('success'))

		{

			$output['message'] = '<p>Your changes were saved.</p>';

		}

		// templates

		$this->load->view($this->includes_path.'/header3');

		$this->load->view('admin/edit_artwork', $output);

		$this->load->view($this->includes_path.'/footer');

	}


	function edit_artwork_draft($artworkID)

	{

		// check permissions for this page

		if (!in_array('artworks_edit', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}

		

		// set object ID

		$objectID = array('artworkID' => $artworkID);

				

		// required

		$this->core->required = array(

			'artworkTitle_draft' => array('label' => 'Artwork title', 'rules' => 'required|trim'),

		);

		// get values

		$output['data'] = $this->core->get_values('artworks', $objectID);	

		if (count($_POST))

		{

			// set date

			/*$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			
			
			if($this->input->post('manualdateCreated'))
			{
				$this->core->set['dateCreated'] = date("Y-m-d H:i:s", strtotime($this->input->post('manualdateCreated')));
			}
			*/
			
			$this->core->set['dateCreated_draft'] =  date("Y-m-d H:i:s", strtotime($this->input->post('dateCreated_draft').' 12AM'));

			$this->core->set['tags_draft'] = trim(strtolower($this->input->post('tags_draft')));

			$this->core->set['artworkDate_draft'] = date("Y-m-d H:i:s", strtotime($this->input->post('artworkDate_draft').' 12AM'));

			$this->core->set['artworkEnd_draft'] = ($this->input->post('artworkEnd_draft')) ? date("Y-m-d H:i:s", strtotime($this->input->post('artworkEnd_draft').' 11.59PM')) : '';

			

			$this->core->set['categoryname_tag_draft'] = NULL;

			

			if ($this->input->post('category_link_draft'))

			{

				//insert category name tag

				$this->db->select('categTitle');

				$this->db->where('categID', $this->input->post('category_link_draft'));

				$categinfoquery = $this->db->get('ha_categs');

				if ($categinfoquery->num_rows())

				{

					$categinfoqueryresult = $categinfoquery->result_array();			

					foreach($categinfoqueryresult as $categinfoqueryvar)

					{	

					$category_link_name = $categinfoqueryvar['categTitle'];

					$this->core->set['categoryname_tag_draft'] = $category_link_name;

					}

					

				}

			}

			

			

			//insert artwork search tags

			$posted_artistID = $this->input->post('artist_link_draft');

			$this->db->select('first_name, last_name');

			$this->db->where('postID', $posted_artistID);

			$artistquery = $this->db->get('ha_artists_posts');

			if ($artistquery->num_rows())

			{

				$artistqueryresult = $artistquery->result_array();			

				foreach($artistqueryresult as $artistqueryvar)

				{	

					$first_name = $artistqueryvar['first_name'];

					$last_name = $artistqueryvar['last_name'];

					$this->core->set['fn_tag_draft'] = $first_name;

					$this->core->set['ln_tag_draft'] = $last_name;

					$this->core->set['search_tags_draft'] = "".$this->input->post('artworkTitle_draft')." ".$first_name." ".$last_name." ".$last_name." ".$first_name."";

				}

		

			}

		

			

			if ($this->input->post('remove_framed_image_draft') == 1) {

				$this->core->set['framed_image_draft'] = '';

			}

			

			if ($this->input->post('remove_unframed_image_draft') == 1) {

				$this->core->set['unframed_image_draft'] = '';

			}

			

			

			$config['allowed_types'] = 'gif|jpg|jpeg|png';

			$config['overwrite']  = true;

			$unframed_img = '';

			

			//unframed image

			if ($_FILES['unframed_image_draft']['name'] != '') {

				$randfilename = random_string('alnum');

				$config['upload_path'] = getcwd().'/static/uploads/artists/artworks/unframed_images';

				$extension = end(explode(".", $_FILES['unframed_image_draft']['name']));

				$config['file_name']= $randfilename.".".$extension;

				$this->load->library('upload');

				$this->upload->initialize($config);

				if ( !$this->upload->do_upload('unframed_image_draft')) {

					if ($this->upload->display_errors()){

					//error code here

					}

				}

				else {

					$unframed_img = $randfilename.".".$extension;

					$this->core->set['unframed_image_draft'] = $unframed_img;

				}

			}

			

			$framed_img = '';

			//framed image

			if ($_FILES['framed_image_draft']['name'] != '') 

			{

				$randfilename = random_string('alnum');

				$config['upload_path'] = getcwd().'/static/uploads/artists/artworks/framed_images';

				$extension = end(explode(".", $_FILES['framed_image_draft']['name']));

				$config['file_name']= $randfilename.".".$extension;

				$this->load->library('upload');

				$this->upload->initialize($config);

				if ( !$this->upload->do_upload('framed_image_draft')) {

					if ($this->upload->display_errors()){

					//error code here

					}

				}

				else {

					$framed_img = $randfilename.".".$extension;

					$this->core->set['framed_image_draft'] = $framed_img;

				}

			}

			
			if (!$this->input->post('owner_link_draft'))
			{
				$this->core->set['owner_status_draft'] = 1;
			}
			else
			{
				
				if ($ownerinfo = $this->get_ownerinfo($this->input->post('owner_link_draft')))
				{
					$owner_active_status = $ownerinfo['active_draft'];
					if ($owner_active_status ==1)
					{
						$this->core->set['owner_status_draft'] = 1;
					}
					else
					{
						$this->core->set['owner_status_draft'] = 0;
					}
					
				}
				
			}
			
			

			// update

			if ($this->core->update('artworks', $objectID))

			{

                                

				// update tags

				$this->artworks->update_tags($artworkID, $this->input->post('tags_draft'));

				// set success message

				$this->session->set_flashdata('success', TRUE);

				/*

				 * Create Image folder on upload modules

				 */

				if($this->input->post('gallery') == '1'){

					$this->db->where('postID', $artworkID);

					$this->db->where('deleted', 0);

					$this->db->where('folder_type', $this->type);

					$query = $this->db->get('image_folders');

					

					$this->core->set['folderSafe'] = $this->type.'-'.date('d-m-Y').'-'.url_title(strtolower($this->input->post('artworkTitle')));

					$this->core->set['folderName'] = $this->type.'-'.date('d-m-Y').'-'.url_title(strtolower($this->input->post('artworkTitle')));

					$this->core->set['folder_type'] = $this->type;

					if ($query->num_rows()) {

						print_r($this->core->set);

						$this->core->update('image_folders', array('postID' => $artworkID, 'folder_type' => $this->type));

					}

					else {

						$this->core->set['postID'] = $artworkID;

						$this->core->update('image_folders');

					}

				}

								

				// select all artists

				$this->db->select('postID');

				$this->db->where('deleted', 0);

				$this->db->where('siteID', $this->siteID);

				$allartistquery = $this->db->get('ha_artists_posts');

				if ($allartistquery->num_rows())

				{

					$allartistqueryresult = $allartistquery->result_array();			

					foreach($allartistqueryresult as $allartistqueryvar)

					{	

						$postID = $allartistqueryvar['postID'];

						

						// check artists with consignment type artworks (update artists db)

						$artworkrecquery = "SELECT * FROM ha_artworks WHERE (consignment = 1 OR reduced_consignment = 1)AND artist_link = '".$postID."'"; 

						$artworkrecresult = mysql_query($artworkrecquery) or die(mysql_error());

						$totalartwork_of_artist = mysql_num_rows($artworkrecresult);

						

						if ($totalartwork_of_artist != 0) 

						{

							$artistconsignment = 1;	

						}

						

						if ($totalartwork_of_artist == 0) 

						{

							$artistconsignment = 0;	

						}

						

						$artist_data = array(

									   'consignment' => $artistconsignment,

									);

						

						$this->db->where('postID', $postID);

						$this->db->update('ha_artists_posts', $artist_data); 

						

						// check artists with new release artworks (update artists db)

						$artworknewreleasequery = "SELECT * FROM ha_artworks WHERE new_release = 1 AND artist_link = '".$postID."'"; 

						$artworknewreleaseresult = mysql_query($artworknewreleasequery) or die(mysql_error());

						$total_nr_artwork_of_artist = mysql_num_rows($artworknewreleaseresult);

						

						if ($total_nr_artwork_of_artist != 0) 

						{

							$artistnewrelease = 1;	

						}

						

						if ($total_nr_artwork_of_artist == 0) 

						{

							$artistnewrelease = 0;	

						}

						

						$artist_nr_data = array(

									   'newrelease' => $artistnewrelease,

									);

						

						$this->db->where('postID', $postID);

						$this->db->update('ha_artists_posts', $artist_nr_data); 

					}

				}

				if ($this->input->post('draftstatus') != 1)
	
				{

						//$this->db->select('identifier, catID');
						$this->db->where('siteID', 1);
						$this->db->where('artworkID', $artworkID);
						
						//$this->db->where('catID >=', 400);
						//$this->db->order_by('catID', 'asc');
						//$this->db->limit(10);
						$listidquery = $this->db->get('ha_artworks');
						if ($listidquery->num_rows())
						{
							$listidqueryresult = $listidquery->result_array();			
							foreach($listidqueryresult as $listidqueryvar)
							{	
								$identifier_draft = $listidqueryvar['identifier_draft'];
								$catID_draft = $listidqueryvar['catID_draft'];
				
								$artworkID_draft  = $listidqueryvar['artworkID_draft']; 
								$artworkTitle_draft  = $listidqueryvar['artworkTitle_draft']; 
								$dateCreated_draft  = $listidqueryvar['dateCreated_draft']; 
								$dateModified_draft  = $listidqueryvar['dateModified_draft']; 
								$artworkDate_draft  = $listidqueryvar['artworkDate_draft']; 
								$artworkEnd_draft  = $listidqueryvar['artworkEnd_draft']; 
								$time_draft  = $listidqueryvar['time_draft']; 
								$location_draft  = $listidqueryvar['location_draft']; 
								$description_draft  = $listidqueryvar['description_draft']; 
								$excerpt_draft  = $listidqueryvar['excerpt_draft']; 
								$userID_draft  = $listidqueryvar['userID_draft']; 
								$groupID_draft  = $listidqueryvar['groupID_draft']; 
								$tags_draft  = $listidqueryvar['tags_draft']; 
								$published_draft  = $listidqueryvar['published_draft']; 
								$gallery_draft  = $listidqueryvar['gallery_draft']; 
								$featured_draft  = $listidqueryvar['featured_draft']; 
								$deleted_draft  = $listidqueryvar['deleted_draft']; 
								$siteID_draft  = $listidqueryvar['siteID_draft']; 
								$artist_link_draft  = $listidqueryvar['artist_link_draft']; 
								$owner_link_draft  = $listidqueryvar['owner_link_draft']; 
								$medium_link_draft  = $listidqueryvar['medium_link_draft']; 
								$medium_draft  = $listidqueryvar['medium_draft']; 
								
								$consignment_draft  = $listidqueryvar['consignment_draft']; 
								$new_release_draft  = $listidqueryvar['new_release_draft']; 
								$framed_size_draft  = $listidqueryvar['framed_size_draft']; 
								$unframed_size_draft  = $listidqueryvar['unframed_size_draft']; 
								$main_image_draft  = $listidqueryvar['main_image_draft']; 
								$unframed_image_draft  = $listidqueryvar['unframed_image_draft']; 
								$unframed_alt_txt_draft  = $listidqueryvar['unframed_alt_txt_draft']; 
								$framed_image_draft  = $listidqueryvar['framed_image_draft']; 
								$framed_alt_txt_draft  = $listidqueryvar['framed_alt_txt_draft']; 
								$retail_price_draft  = $listidqueryvar['retail_price_draft']; 
								$asking_price_draft  = $listidqueryvar['asking_price_draft']; 
								$reduced_consignment_draft  = $listidqueryvar['reduced_consignment_draft']; 
								$include_in_auction_draft  = $listidqueryvar['include_in_auction_draft']; 
								$wanted_draft  = $listidqueryvar['wanted_draft']; 
								$visible_on_website_draft  = $listidqueryvar['visible_on_website_draft']; 
								$url_text_draft  = $listidqueryvar['url_text_draft']; 
								$meta_tags_draft  = $listidqueryvar['meta_tags_draft']; 
								$include_on_MAO_page_draft  = $listidqueryvar['include_on_MAO_page_draft']; 
								$MAO_content_draft  = $listidqueryvar['MAO_content_draft']; 
								$comment_draft  = $listidqueryvar['comment_draft']; 
								$condition_draft  = $listidqueryvar['condition_draft']; 
								$purchase_year_draft  = $listidqueryvar['purchase_year_draft']; 
								$from_draft  = $listidqueryvar['from_draft']; 
								$certificate_draft  = $listidqueryvar['certificate_draft']; 
								$private_note_draft  = $listidqueryvar['private_note_draft']; 
								$owner_note_draft  = $listidqueryvar['owner_note_draft']; 
								$series_draft  = $listidqueryvar['series'];
								$edition_size_draft  = $listidqueryvar['edition_size_draft']; 
								$collection_draft  = $listidqueryvar['collection_draft']; 
								$retail_price_comment_draft  = $listidqueryvar['retail_price_comment_draft']; 
								$asking_price_comment_draft  = $listidqueryvar['asking_price_comment_draft']; 
								$include_price_draft  = $listidqueryvar['include_price_draft']; 
								$fn_tag_draft  = $listidqueryvar['fn_tag_draft']; 
								$ln_tag_draft  = $listidqueryvar['ln_tag_draft']; 
								$sorting_price_draft  = $listidqueryvar['sorting_price_draft']; 
								$var_com_draft  = $listidqueryvar['var_com_draft']; 
								$search_tags_draft  = $listidqueryvar['search_tags_draft']; 
								$var_com_int_draft  = $listidqueryvar['var_com_int_draft']; 
								$category_link_draft  = $listidqueryvar['category_link_draft']; 
								$categoryname_tag_draft  = $listidqueryvar['categoryname_tag_draft']; 
								$url_string_draft  = $listidqueryvar['url_string_draft']; 
								$url_name_draft  = $listidqueryvar['url_name_draft']; 
								$meta_title_draft  = $listidqueryvar['meta_title_draft']; 
								$meta_desc_draft  = $listidqueryvar['meta_desc_draft']; 
								$meta_keywords_draft  = $listidqueryvar['meta_keywords_draft']; 
								$summary_text_draft  = $listidqueryvar['summary_text_draft']; 
								$feature_draft  = $listidqueryvar['feature_draft']; 
								$owner_status_draft  = $listidqueryvar['owner_status_draft']; 
								$frametype_draft  = $listidqueryvar['frametype_draft']; 
								$framingdescription_draft  = $listidqueryvar['framingdescription_draft']; 
								$signedtype_draft  = $listidqueryvar['signedtype_draft']; 
								$signaturelocation_draft  = $listidqueryvar['signaturelocation_draft']; 
								$artist_nolink_name_draft  = $listidqueryvar['artist_nolink_name_draft']; 
								$net_price_draft  = $listidqueryvar['net_price_draft']; 
								$certificatestatus_draft  = $listidqueryvar['certificatestatus_draft']; 
								$certificate_issued_by_draft  = $listidqueryvar['certificate_issued_by_draft']; 
								$natureofinterest_draft  = $listidqueryvar['natureofinterest_draft']; 
								$edition_number_draft  = $listidqueryvar['edition_number_draft']; 
								
								//m2016
								$visible_on_website_draft  = $listidqueryvar['visible_on_website_draft']; 
								//m2016
								
								
								$data = array(
											   //'title' => $title,
											   //'name' => $name,
											   //'date' => $date
															   
												//'artworkID' => $artworkID,
												'artworkTitle' => $artworkTitle_draft,
												'dateCreated' => $dateCreated_draft,
												'dateModified' => $dateModified_draft,
												'artworkDate' => $artworkDate_draft,
												'artworkEnd' => $artworkEnd_draft,
												'time' => $time_draft,
												'location' => $location_draft,
												'description' => $description_draft,
												'excerpt' => $excerpt_draft,
												'userID' => $userID_draft,
												'groupID' => $groupID_draft,
												'tags' => $tags_draft,
												'published' => $published_draft,
												'gallery' => $gallery_draft,
												'featured' => $featured_draft,
												'deleted' => $deleted_draft,
												'siteID' => $siteID_draft,
												'artist_link' => $artist_link_draft,
												'owner_link' => $owner_link_draft,
												'medium_link' => $medium_link_draft,
												'medium' => $medium_draft,
												'consignment' => $consignment_draft,
												'new_release' => $new_release_draft,
												'framed_size' => $framed_size_draft,
												'unframed_size' => $unframed_size_draft,
												'main_image' => $main_image_draft,
												'unframed_image' => $unframed_image_draft,
												'unframed_alt_txt' => $unframed_alt_txt_draft,
												'framed_image' => $framed_image_draft,
												'framed_alt_txt' => $framed_alt_txt_draft,
												'retail_price' => $retail_price_draft,
												'asking_price' => $asking_price_draft,
												'reduced_consignment' => $reduced_consignment_draft,
												'include_in_auction' => $include_in_auction_draft,
												'wanted' => $wanted_draft,
												
												//m2016
												'visible_on_website' => $visible_on_website_draft,
												//'visible_on_website' => 1,
												//m2016
												
												'url_text' => $url_text_draft,
												'meta_tags' => $meta_tags_draft,
												'include_on_MAO_page' => $include_on_MAO_page_draft,
												'MAO_content' => $MAO_content_draft,
												'comment' => $comment_draft,
												'condition' => $condition_draft,
												'purchase_year' => $purchase_year_draft,
												'from' => $from_draft,
												'certificate' => $certificate_draft,
												//'private_note' => $private_note_draft,
												'owner_note' => $owner_note_draft,
												'series' => $series_draft,
												'edition_size' => $edition_size_draft,
												'collection' => $collection_draft,
												'retail_price_comment' => $retail_price_comment_draft,
												'asking_price_comment' => $asking_price_comment_draft,
												'include_price' => $include_price_draft,
												'fn_tag' => $fn_tag_draft,
												'ln_tag' => $ln_tag_draft,
												'sorting_price' => $sorting_price_draft,
												'var_com' => $var_com_draft,
												'search_tags' => $search_tags_draft,
												'var_com_int' => $var_com_int_draft,
												'category_link' => $category_link_draft,
												'categoryname_tag' => $categoryname_tag_draft,
												'url_string' => $url_string_draft,
												'url_name' => $url_name_draft,
												'meta_title' => $meta_title_draft,
												'meta_desc' => $meta_desc_draft,
												'meta_keywords' => $meta_keywords_draft,
												'summary_text' => $summary_text_draft,
												'feature' => $feature_draft,
												//'owner_status' => $owner_status_draft,
												'owner_status' => 1,
												'frametype' => $frametype_draft,
												'framingdescription' => $framingdescription_draft,
												'signedtype' => $signedtype_draft,
												'signaturelocation' => $signaturelocation_draft,
												'artist_nolink_name' => $artist_nolink_name_draft,
												'net_price' => $net_price_draft,
												'certificatestatus' => $certificatestatus_draft,
												'certificate_issued_by' => $certificate_issued_by_draft,
												'natureofinterest' => $natureofinterest_draft,
												'edition_number' => $edition_number_draft,
											   
											   
											   
											);
								
								$this->db->where('artworkID', $artworkID);
								$this->db->update('ha_artworks', $data); 
								
								
							}
						}		
				
				
				
				
						$this->db->where('artwork_id', $artworkID);
						$this->db->where('draftstatus', 0);
						$this->db->delete('ha_artworkimages'); 				
				
						$this->db->where('siteID', 1);
						$this->db->where('artwork_id', $artworkID);
						$this->db->where('draftstatus', 1);
						$this->db->order_by('sequence_num', 'asc');
						
						$listidquery = $this->db->get('ha_artworkimages');
						if ($listidquery->num_rows())
						{
							$listidqueryresult = $listidquery->result_array();			
							foreach($listidqueryresult as $listidqueryvar)
							{	
								$image = $listidqueryvar['image'];
								$image_alt_txt = $listidqueryvar['image_alt_txt'];
								$sequence_num = $listidqueryvar['sequence_num'];
								
								$data = array(
								   'image' => $image,
								   'image_alt_txt' => $image_alt_txt ,
								   'sequence_num' => $sequence_num,
								   'draftstatus' => 0,
								   'artwork_id' => $artworkID,
								   'siteID' => $this->siteID,
								);
								
								$this->db->insert('ha_artworkimages', $data); 								
								
							}
						}	
						
						/*
						//email admin
						$this->load->library('email');
						
						$this->email->from("consign@divart.com", $this->site->config['siteName']);
						//$this->email->to('joshua@logicreplace.com'); 
						$this->email->to($this->site->config['siteEmail']); 
						$this->email->subject("Artwork (ID: ".$artworkID.") edited by owner (".$firstNameval."  ".$lastNameval."), please review");
						$this->email->message("".site_url()."admin/artworks/edit_artwork_draft/".$artworkID." \n\nBest Regards\n".$this->site->config['siteName']."\nhttp://www.divart.com ");	
						$this->email->send();	
						*/
						
				
				}
						
						

				// where to redirect to

				redirect($this->uri->uri_string());

			}

		}

		// set message

		if ($this->session->flashdata('success'))

		{

			$output['message'] = '<p>Your changes were saved.</p>';

		}

		// templates

		$this->load->view($this->includes_path.'/header');

		$this->load->view('admin/edit_artwork_draft', $output);

		$this->load->view($this->includes_path.'/footer');

	}
	
	//m2016
	function artistinfo()
	{
		$output = "";
		
		// grab data and display
		$this->load->view('admin/artistinfo',$output);
	}
	//m2016
	
	function email_owner($artworkID)
	{
		
		
			if ($artworkinfo = $this->get_artworkinfo($artworkID))
			{
				$artworkIDval = $artworkinfo['artworkID'];
				$owner_linkval = $artworkinfo['owner_link'];
				$artworkTitle_val = $artworkinfo['artworkTitle'];
				
				
				if ($ownerinfo = $this->get_ownerinfo($owner_linkval))
				{
					$email = $ownerinfo['email'];
				}
				
			}		
						
			$this->load->library('email');
			
			//$this->email->from($this->site->config['siteEmail'], $this->site->config['siteName']);
			//$this->email->to('joshua@logicreplace.com'); 
			
			$this->email->from("consign@divart.com", $this->site->config['siteName']);
			$this->email->reply_to("consign@divart.com", $this->site->config['siteName']);
			$this->email->to($email); 

			//$this->email->cc('another@another-example.com'); 
			//$this->email->bcc('them@their-example.com'); 
			
			$this->email->subject('Your art listing is now visible on Diva Art Group: artwork ID '.$artworkID.'');
			$this->email->message("Hello,\n\nThank you for listing your artwork with Diva Art Group. We have approved this listing and it has been added to our site. Please review it online at www.divart.com.\n\n Best Regards\n".$this->site->config['siteName']."\nconsign@divart.com\nhttp://www.divart.com ");	
			
			$this->email->send();	
			
			$this->session->set_userdata('owneremailmsg', 1);
			
			redirect('/admin/artworks/edit_artwork/'.$artworkID.'');
			exit();
	}

	function get_artworkinfo($artworkID)
	{
		// default wheres
		$this->db->where('artworkID', $artworkID);	
		$this->db->where('visible_on_website', 1);		

		// grab
		$query = $this->db->get('ha_artworks', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}		
	}


	function delete_artwork($objectID)

	{

		// check permissions for this page

		if (!in_array('artworks_delete', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}		

		

		if ($this->core->delete('artworks', array('artworkID' => $objectID)))

		{

                        //delete gallery folder

                        //$this->delete_folder($objectID);

                        

			// where to redirect to

			redirect($this->redirect);

		}

	}
	
	
	
	function delete_artwork_draft($objectID)

	{

		// check permissions for this page

		if (!in_array('artworks_delete', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}		

		

		if ($this->core->delete('artworks', array('artworkID' => $objectID)))

		{

                        //delete gallery folder

                        //$this->delete_folder($objectID);

                        

			// where to redirect to

			//redirect($this->redirect);
			
			redirect('/admin/artworks/viewall_drafts');
			

		}

	}
	
	

        function delete_folder($postID)

	{

		// check permissions for this page

		if (!in_array('images', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}

		// where

		$objectID = array('postID' => $postID);

		if ($this->core->delete('image_folders', $objectID))

		{

			// set children to no parent

                        //$this->load->model('images_model', 'images');

			//$this->images->update_children($folderID);

			// where to redirect to

			//redirect('/admin/images/folders');

		}

	}
	
	
	function medium_results()

	{

		

		// set default date

		//$output['data']['artworkDate'] = ($this->input->post('artworkDate')) ? $this->input->post('artworkDate') : dateFmt(date("Y-m-d H:i:s"), 'd M Y');
		

		// templates

		//$this->load->view($this->includes_path.'/header');

		$this->load->view('admin/mediumresults', $output);

		//$this->load->view($this->includes_path.'/footer');

	}
	
	
	function crop($type, $artworkID)

	{

		

		// set default date

		//$output['data']['artworkDate'] = ($this->input->post('artworkDate')) ? $this->input->post('artworkDate') : dateFmt(date("Y-m-d H:i:s"), 'd M Y');
		

		// templates

		//$this->load->view($this->includes_path.'/header');
		
		$output['data']['type'] = $type;
		$output['data']['artworkID'] = $artworkID;

		$this->load->view('admin/crop', $output);

		//$this->load->view($this->includes_path.'/footer');

	}
	
	
	//m2016
	function additional_images_sequence($artworkID)

	{

		// check permissions for this page

		if (!in_array('artworks_edit', $this->permission->permissions))

		{

			redirect('/admin/dashboard/permissions');

		}
		
		
		$additional_images = '<div id="list">
								<div id="response"> </div>
								<ul>
							';

		$this->db->select('image, artworkimageID');
		$this->db->where('artwork_id', $artworkID);
		
		if ($this->input->get('type'))
		{
			$this->db->where('draftstatus', 1);
		}
		else
		{
			$this->db->where('draftstatus', 0);
		}
		
		
		$this->db->order_by('sequence_num', 'asc');
		$this->db->limit(10);
		$listidquery = $this->db->get('ha_artworkimages');
		if ($listidquery->num_rows())
		{
			$listidqueryresult = $listidquery->result_array();			
			foreach($listidqueryresult as $listidqueryvar)
			{	
				$image = $listidqueryvar['image'];
				$artworkimageID = $listidqueryvar['artworkimageID'];

				$additional_images .= '<li id="arrayorder_'.$artworkimageID.'" style="cursor:move">
				
				<img src="'.site_url().'thumb.php?src=/static/uploads/artists/artworkimages/'.$image.'&w=70&h=70"/>
				
				<div class="clear"></div>
				</li>';
				
				
			}
		}
		
		$additional_images .= '    </ul>
								  </div>
								</div>
							';
		

		// templates
		$output['message'] = '';
		
		$output['additional_images'] = $additional_images;
		$output['artworkID'] = $artworkID;
		
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/additional_images_sequence', $output);
		$this->load->view($this->includes_path.'/footer');

	}
	
	function updatelist()
	{
	
		$array	= $this->input->post('arrayorder');
		
		if ($_POST['update'] == "update"){
			
			$count = 1;
			foreach ($array as $idval) {
				//$query = "UPDATE dragdrop SET listorder = " . $count . " WHERE id = " . $idval;
				//mysql_query($query) or die('Error, insert query failed');
				
				$data = array(
							   'sequence_num' => $count,
							);
				
				$this->db->where('artworkimageID', $idval);
				$this->db->update('ha_artworkimages', $data); 				
				
				
				$count ++;	
			}
			echo 'Updated';
		}	
		
	}
	
	
	//m2016
	
	

}