<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Halogy
 *
 * A user friendly, modular content management system for PHP 5.0
 * Built on CodeIgniter - http://codeigniter.com
 *
 * @package		Halogy
 * @author		Haloweb Ltd
 * @copyright	Copyright (c) 2012, Haloweb Ltd
 * @license		http://halogy.com/license
 * @link		http://halogy.com/
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Admin extends MX_Controller {
	var $includes_path = '/includes/admin';		// path to includes for header and footer
	var $redirect = '/admin/tracks/trackOwners';

	function __construct()
	{
		parent::__construct();
		// check user is logged in, if not send them away from this controller
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}
		
		//get permissions and redirect if they don't have access to this module
		// if (!$this->permission->permissions)
		// {
			// redirect('/admin/dashboard/permissions');
		// }
		
		// check permissions for this page
		// if (!in_array('tracks', $this->permission->permissions))
		// {
			// redirect('/admin/dashboard/permissions');
		// }

		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
		
		$this->load->model('track_model', 'track');
	}
	
	function index()
	{			
		redirect($this->redirect);
	}
	
	function order($field = ''){
		$this->core->order(key($_POST), $field);
	}
//----TRACK----//	
	
	function trackOwners(){
		
		$this->db->join($this->track->subscription_tbl, 'member_subscription.subscriptionID = tracks.subscriptionID');
		$this->db->join($this->track->user_tbl, 'users.userID = tracks.userID');
		$query = $this->db->get($this->track->tracks_tbl);
		$output['tracks'] = $query->result_array();
		
		

//print_r($output);
		$this->load->view($this->includes_path.'/header');
		$this->load->view('/admin/trackOwnerMgr',$output);
		$this->load->view($this->includes_path.'/footer');
	}
	
	function editTrack($id=''){
           
		if(!$id or !is_numeric($id)) show_404();
		
		$this->form_validation->set_rules('email', 'Email', 'required');
		
		
		if($message = $this->session->flashdata('message')) $output['message'] = $message;

		$this->db->join($this->track->subscription_tbl, 'member_subscription.subscriptionID = tracks.subscriptionID');
		$this->db->join($this->track->user_tbl, 'users.userID = tracks.userID');
		$this->db->join($this->track->payment_tbl, 'member_transactions.user_id = tracks.userID ');				
		$query = $this->db->get_where($this->track->tracks_tbl,array('tracksID'=>$id));
		$output['data'] = $query->row_array();
                
		$query2 = $this->db->get_where($this->track->tracks_tbl,array('tracksID'=>$id));
		$output['tracks'] = $query2->row_array();
		
		
		
		$get_subscriptions=$this->db->get_where($this->track->subscription_tbl);
		$subscriptions = $get_subscriptions->result_array();
		foreach($subscriptions as $val){
			$output['subscriptions'][$val['subscriptionID']] = $val['subscriptionName'];
		}
		
		$output['id'] = $id;
		@$output['userid'] = $output['data']['userID'];
		
		$get_trackcats = $this->db->get($this->track->trackcat_tbl);
		$trackcats = $get_trackcats->result_array();

		foreach($trackcats as $key => $val){
			$get_trackXtrackcats = $this->db->get_where($this->track->trackXtrackcat_tbl,array('tracksID'=>$id,'trackcatID'=>$val['trackcatID']));
			$trackXtrackcats = $get_trackXtrackcats->row_array();

			$output['trackcats'][$key]['data1']=
			'<div class="checkbox"><label>'
			.form_checkbox('trackcat['.$val['trackcatID'].']',1,($get_trackXtrackcats->num_rows()?TRUE:FALSE))
			.$val['track']
			.'</label></div>';

		}
		
		$get_machinetype = $this->db->get($this->track->machinetype_tbl);
		$machinetype = $get_machinetype->result_array();

		foreach($machinetype as $key => $val){
			$get_trackXmachinecat = $this->db->get_where($this->track->trackXmachinecat_tbl,array('tracksID'=>$id,'machinecatsID'=>$val['machinecatsID']));
			$trackXmachinecat = $get_trackXmachinecat->row_array();

			$output['machinecats'][$key]['data2']=
			'<div class="checkbox"><label>'
			.form_checkbox('machinecat['.$val['machinecatsID'].']',1,($get_trackXmachinecat->num_rows()?TRUE:FALSE))
			.$val['machine_type']
			.'</label></div>';

		}
		
		
	//print_r($output);
		if(count($_POST) and $this->form_validation->run() == TRUE){
			@$userdata = array(
				'firstName' => $this->input->post('firstName'),
				'lastname' => $this->input->post('lastName'),
				'password' => ($this->input->post('password')?md5($this->input->post('password')):$output['data']['password']),
				'email' => $this->input->post('email'),
				'billingAddress1' => $this->input->post('billingAddress1'),
				'billingAddress2' => $this->input->post('billingAddress2'),
				'billingAddress3' => $this->input->post('billingAddress3'),
				'billingState' => $this->input->post('billingState'),
				'billingCountry' => $this->input->post('billingCountry'),
				'dateModified' => date('Y-m-d H:i:s'),
				'siteID' => 1,
			);
			
			$trackdata = array(
				'trackname' => $this->input->post('_trackname'),
				'trackdesc' => $this->input->post('_trackdesc'),
				'address' => $this->input->post('_address'),
				'city' => $this->input->post('_city'),
				't_state' => $this->input->post('_state'),
				't_country' => $this->input->post('_country'),
				'phone' => $this->input->post('_phone'),
				'email' => $this->input->post('_email'),
				'website' => $this->input->post('_website'),
				'facebook' => $this->input->post('_facebook'),
				'twitter' => $this->input->post('_twitter'),
				'instagram' => $this->input->post('_instagram'),
				'youtube' => $this->input->post('_youtube'),
				'latitude' => $this->input->post('_latitude'),
				'longitude' => $this->input->post('_longitude'),
				'subscriptionID' => $this->input->post('subscriptionID'),
				'status' => $this->input->post('status'),
				'siteID' => 1,
				'lastUpdate' => date('Y-m-d H:i:s')
			);
			
			
			
			
			
			//VERIFY IMAGE UPLOAD TOO!
			if($oldFileName = @$_FILES['image']['name']){
				$this->uploads->allowedTypes = 'jpg|gif|png';
				$this->uploads->maxWidth = '1000';
				$this->uploads->maxHeight = '1000';
				$this->uploads->maxSize = '5000';
				
				if($imageData = $this->uploads->upload_image(TRUE)){
					$trackdata['profile_img'] = $imageData['file_name'];
					$this->uploads->delete_file($output['tracks']['profile_img']);
				}
				
				// get image errors if there are any
				if ($this->uploads->errors){
					$this->form_validation->set_error($this->uploads->errors);
				}
			}
			
			
		if( $this->db->update($this->track->tracks_tbl,$trackdata,array('tracksID'=>$id)) ) {
			if( $this->db->update($this->track->user_tbl,$userdata,array('userID'=>$output['tracks']['userID'])) ) {
				
				
				
				
				//track types machine types
				//delete then add
				$this->db->delete($this->track->trackXtrackcat_tbl, array('tracksID' => $id)); 
				if(@$_POST['trackcat']){
					foreach($_POST['trackcat'] as $key => $val){
						$this->db->insert($this->track->trackXtrackcat_tbl,array('tracksID'=>$id,'trackcatID'=>$key));
					}
				}

				//delete then add
				$this->db->delete($this->track->trackXmachinecat_tbl, array('tracksID' => $id)); 
				if(@$_POST['machinecat']){
					foreach($_POST['machinecat'] as $key => $val){
						$this->db->insert($this->track->trackXmachinecat_tbl,array('tracksID'=>$id,'machinecatsID'=>$key));
					}
				}
				
				$useridval = $this->lookup_userID($id);
				
				if ($this->input->post('status') == 1)
				{
					$data = array(
								   'published' => 1,
								);
					
					$this->db->where('userID', $useridval);
					$this->db->update('ha_events', $data); 

				}
				elseif($this->input->post('status') == 0)
				{
					$data = array(
								   'published' => 0,
								);
					
					$this->db->where('userID', $useridval);
					$this->db->update('ha_events', $data); 
				}
				
					$this->db->where('userID',$useridval);
					$this->db->update('users',array('active'=>$this->input->post('status')));
			
				
					$output['message'] = "<p>Changes successfully saved!</p>";
					redirect(current_url());
				}
			}
			
		
		}
		
		
		$this->load->view($this->includes_path.'/header');
		$this->load->view('/admin/editTrack',$output);
		$this->load->view($this->includes_path.'/footer');
	}
	
	function delete_tracks($id='')
		{
			
			$query_trax = $this->db->get_where('tracks',array('tracksID'=>$id));
			$trackdata = $query_trax->row_array();
			// print_r($trackdata);
			// check permissions for this page
			if (!in_array('tracks', $this->permission->permissions))
			{
				redirect('/admin/dashboard/permissions');
			}
					
			if ($this->db->delete('tracks', array('tracksID' => $id)))
			{
			$this->db->delete('users', array('userID' => $trackdata['userID']));
			
				
				$this->session->set_flashdata('message','Track deleted successfully!');
				// where to redirect to
				redirect($this->redirect);
			}
			
		}
	
	
    function lookup_userID($trackID, $display = FALSE)
	{
		// default wheres
		$this->db->where('tracksID', $trackID);

		// grab
		$query = $this->db->get('tracks', 1);

		if ($query->num_rows())
		{
			$row = $query->row_array();
				return $row['userID'];
		}
		else
		{
			return FALSE;
		}		
	}

	function addTrack(){
		if($message = $this->session->flashdata('message')) $output['message'] = $message;
		
		$output['tracks'] = array();
		
		
		$get_subscriptions=$this->db->get_where($this->track->subscription_tbl);
		$subscriptions = $get_subscriptions->result_array();
		foreach($subscriptions as $val){
			$output['subscriptions'][$val['subscriptionID']] = $val['subscriptionName'];
		}
				
		
		$this->form_validation->set_rules('email', 'Email', 'required');
		
		
	//print_r($output);
		if(count($_POST) and $this->form_validation->run() == TRUE){
		// if(count($_POST)){
			
			
			
			$userdata = array(
				'firstName' => $this->input->post('firstName'),
				'lastname' => $this->input->post('lastName'),
				'email' => $this->input->post('email'),
				'password' => md5($this->input->post('password')),
				'billingAddress1' => $this->input->post('billingAddress1'),
				'billingAddress2' => $this->input->post('billingAddress2'),
				'billingAddress3' => $this->input->post('billingAddress3'),
				'billingState' => $this->input->post('billingState'),
				'billingCountry' => $this->input->post('billingCountry'),
				'dateCreated' => date('Y-m-d H:i:s'),
				'siteID' => 1,
			);
			
			
			if( $this->db->insert($this->track->user_tbl,$userdata) ) {
			$userID = $this->db->insert_id();
				$trackdata = array(
					'trackname' => $this->input->post('_trackname'),
					'trackdesc' => $this->input->post('_trackdesc'),
					'address' => $this->input->post('_address'),
					'city' => $this->input->post('_city'),
					't_state' => $this->input->post('_state'),
					't_country' => $this->input->post('_country'),
					'phone' => $this->input->post('_phone'),
					'email' => $this->input->post('_email'),
					'website' => $this->input->post('_website'),
					'facebook' => $this->input->post('_facebook'),
					'twitter' => $this->input->post('_twitter'),
					'instagram' => $this->input->post('_instagram'),
					'youtube' => $this->input->post('_youtube'),
					'latitude' => $this->input->post('_latitude'),
					'longitude' => $this->input->post('_longitude'),
					'subscriptionID' => $this->input->post('subscriptionID'),
					'status' => $this->input->post('status'),
					'userID' => $userID,
					'siteID' => 1,
					'profile_img' => 'noavatar.gif'
				);
				
			
				if( $this->db->insert($this->track->tracks_tbl,$trackdata) ) {
						$transactData=array(
							'txn_code'=> 'ADDED VIA ADMIN',
							'amount' => 0,
							'user_id' => $userID,
							'date_created' => date('Y-m-d H:i:s'),
						);
				
					$this->db->insert('member_transactions',$transactData);
				
					$trackid = $this->db->insert_id();
					$this->session->set_flashdata('message','Successfully added track!');
					//redirect('admin/tracks');
				}
			}
			
		
		}
		
		
	
		$this->load->view($this->includes_path.'/header');
		$this->load->view('/admin/addTrack',$output);
		$this->load->view($this->includes_path.'/footer');
	}
	
	
	function editImages($id='',$trackID=''){
		if(!$id or !is_numeric($id)) show_404();

		$output=array();
		$output['id'] = $id;	
                $output['trackID'] = $trackID;	
		
		$user = $this->track->getTrackDetails($id);
		$subscription = $this->track->getSubscriptionDetails($user['subscriptionID']);
		if($this->track->get_no_of_images($user['tracksID'])){
			$output['images'] = $this->track->get_images($user['tracksID']);
		}else{
			$output['images'] = FALSE;
		}
		
		if($this->track->get_no_of_images($user['tracksID']) <  $subscription['photos'] or $subscription['photos'] == -1){
		//can add photo
			$output['upload'] = form_hidden('upload',1).form_upload('image',set_value('image'));
			$output['title'] = form_input('title',set_value('title'),"class='formelement'");	
			
		}else{
		//cant add
			$output['upload'] = FALSE;
			$output['title'] = FALSE;
		}
		
		
		// $this->core->required = array(
			// 'title' => array('label' => 'Title', 'rules' => 'required|ucfirst'),
		// );
		
		if(count($_POST)){
			//delete photos if something is checked
			
			if(@$_POST['delete_photos'] and @$_POST['imagedata']){
				foreach($_POST['imagedata'] as $key => $val){
					$img =  $this->track->get_images($user['tracksID'], $key);
					$this->db->delete($this->track->images_tbl, array('imgID' => $key)); 
					$this->uploads->delete_file($img['image']);
				}
				$this->session->set_flashdata('message','Image(s) deleted successfully!');
				redirect("/admin/tracks/editTrack/".$trackID);
			}
			
			if(@$_POST['upload_photo']){
				//upload photos
				//VERIFY IMAGE UPLOAD TOO!
				
				if($oldFileName = @$_FILES['image']['name']){
					$this->uploads->allowedTypes = 'jpg|gif|png';
					$this->uploads->maxWidth = '5000';
					$this->uploads->maxHeight = '5000';
					$this->uploads->maxSize = '5000';
					
					if($imageData = $this->uploads->upload_image(TRUE)){
						$this->db->set('tracksID', $user['tracksID']); 
						$this->db->set('image', $imageData['file_name']); 
						$this->db->set('title', $this->input->post('title'	)); 
						//$this->uploads->delete_file($data['image']);
					}else{
						$this->session->set_flashdata('errors','No image selected!');
						redirect(current_url());
					}
					// get image errors if there are any
					if ($this->uploads->errors){
						$this->form_validation->set_error($this->uploads->errors);
					}else{
						//save to db
						if($this->db->insert($this->track->images_tbl)){
							$this->session->set_flashdata('message','Image added successfully!');
							$this->track->sendUpdateEmail($user['tracksID'],'photos');
							redirect("/admin/tracks/editTrack/".$trackID);
						}else $output['errors'] = "Image not saved !";
					}					
				}else{
					$this->session->set_flashdata('errors','No image selected!');
					redirect("/admin/tracks/editTrack/".$trackID);
				}
				
			}
		}
		
		$output['message'] = ($this->session->flashdata('message')?$this->session->flashdata('message'):'');
		$output['errors'] = ($this->session->flashdata('errors')?$this->session->flashdata('errors'):'');
		$this->load->view('/admin/editImages',$output);
		
		
	}
	
	function editVideos($id='',$trackID=''){
		if(!$id or !is_numeric($id)) show_404();

	$output=array();
	$output['id'] = $id;	
	$output['trackID'] = $trackID;	
		$user = $this->track->getTrackDetails($id);
		$subscription = $this->track->getSubscriptionDetails($user['subscriptionID']);
		
		if($this->track->get_no_of_videos($user['tracksID'])){
			$output['videos'] = $this->track->get_videos($user['tracksID']);
		}else{
			$output['videos'] = FALSE;
		}
		
		if($this->track->get_no_of_videos($user['tracksID']) <  $subscription['videos'] or $subscription['videos'] == -1){
		//can add photos
			$output['upload'] = form_input('video',set_value('video'),"class='formelement'");	
			$output['title'] = form_input('title',set_value('title'),"class='formelement'");	
		}else{
		//cant add
			$output['upload'] = FALSE;
			$output['title'] = FALSE;
		}
		
				
		if(count($_POST)){
			//delete photos if something is checked
			if(@$_POST['delete_videos'] and @$_POST['videodata']){
				foreach($_POST['videodata'] as $key => $val){
					// $vid =  $this->tracks->get_videos($user['tracksID'], $key);
					$this->db->delete($this->track->videos_tbl, array('vidID' => $key)); 
					// $this->uploads->delete_file($vid['image']);
				}
				$this->session->set_flashdata('message','Video(s) deleted successfully!');
				redirect("/admin/tracks/editTrack/".$trackID);
			}
			
			if(@$_POST['upload_vids']){
				$required_fields[] = array('field' => 'video', 'label'=> 'Youtube Link', 'rules'=> 'required');
				$required_fields[] = array('field' => 'title', 'label'=> 'Title', 'rules'=> 'required|ucfirst');
				
				$this->form_validation->set_rules($required_fields);
				$everything_validated = $this->form_validation->run();
				
				if($everything_validated){
					//save to db
					$videolink = $this->track->get_youtubeID( $this->input->post('video') );
					$this->db->set('video',$videolink);
					$this->db->set('title',$this->input->post('title'));
					$this->db->set('tracksID',$user['tracksID']);
					if($this->db->insert($this->track->videos_tbl)){
						//success
						$this->session->set_flashdata('message','Video added successfully!');
						$this->track->sendUpdateEmail($user['tracksID'],'videos');
						redirect("/admin/tracks/editTrack/".$trackID);
					}else $output['errors'] = "Video not saved!";
				}
			}
		}
		$output['message'] = ($this->session->flashdata('message')?$this->session->flashdata('message'):'');
		$output['errors'] = validation_errors();
		// $output['errors'] = ($this->session->flashdata('errors')?$this->session->flashdata('errors'):'');
		$this->load->view("/admin/editVideos",$output);
		//redirect("/admin/tracks/editTrack/".$id);
	}
	
	function editEvents($id='',$trackID=''){
            
		if(!$id or !is_numeric($id)) show_404();
		if(!$trackID or !is_numeric($trackID)) show_404();
		
		$output=array();
		$output['id'] = $id;
		$output['trackID'] = $trackID;
		
		$this->db->order_by('eventDate','asc');
		$data = $this->core->viewall($this->track->events_tbl,array('userID'=>$id),'',50);
                
		$output['events'] = array();
		foreach($data['events'] as $key => $event){
			$output['events'][$key]['eventID'] = $event['eventID'];
			$output['events'][$key]['eventTitle'] = $event['eventTitle'];
			$output['events'][$key]['type'] = $event['type'];
			$output['events'][$key]['description'] = substr($event['description'],0,15);
			$output['events'][$key]['eventDate'] = date("d M Y", strtotime($event['eventDate']) );
			$output['events'][$key]['eventEnd'] = date("d M Y", strtotime($event['eventEnd']) );
			$output['events'][$key]['time'] = $event['time'];
		}
		
                    $this->db->where('tracksID',$trackID);
                   $res= $this->db->get('tracks');
                   $res=$res->result_array();
                 
		$output['city1'] = form_hidden('location',$res[0]['city'],"class='formelement' required autofocus");
		$output['state'] = form_hidden('state',$res[0]['t_state'],"class='formelement' required autofocus");
		$output['title'] = form_input('eventTitle',set_value('eventTitle'),"class='formelement' required autofocus");
						 // $this->load->model('moto/moto_model','moto');
						 $this->db->order_by('eventcatsOrder','DESC');
		$eventtype = $this->core->viewall($this->track->eventcats_tbl);
		
		$event_opt[''] = "==SELECT EVENT==";
		foreach($eventtype[$this->track->eventcats_tbl] as $e){
			$event_opt[$e['event_type']] = $e['event_type'];
		}
		
		$output['type'] = form_dropdown('type',$event_opt,'',"class='formelement' required autofocus");
		$output['start'] = form_input('eventDate',set_value('eventDate'),"class='datepicker formelement' required autofocus");
		$output['end'] = form_input('eventEnd',set_value('eventEnd'),"class='datepicker formelement' required autofocus");
		$output['recur'] = form_dropdown('recur',array('single'=>'Single','recur'=>'Recurring'),'single',"id='recur' class='formelement' required autofocus");
		$option = array(
			'' => "==SELECT DAY==",
			'monday' => "Mon",
			'tuesday' => "Tue",
			'wednesday' => "Wed",
			'thursday' => "Thu",
			'friday' => "Fri",
			'saturday' => "Sat",
			'sunday' => "Sun",
		);
		$output['day'] = form_dropdown('day',$option,'',"class='formelement' autofocus");
		$output['time'] = form_input('time',set_value('time'),"class='formelement' required autofocus");
		$output['description'] = form_textarea('description',set_value('description'),"class='formelement' style='height:30px;' required autofocus");
		$output['pagination'] =$this->pagination->create_links();		
		//VERIFY DATA FIRST!
		if(count($_POST)){
			if(@$_POST['delete_events'] and @$_POST['eventdata']){
				foreach($_POST['eventdata'] as $key => $val){
					$this->db->delete($this->track->events_tbl, array('eventID' => $key)); 
				}
				$this->session->set_flashdata('message','Event(s) deleted successfully!');
				redirect("/admin/tracks/editTrack/".$trackID);
			}
		
			if(@$_POST['add_event']){
			$required_fields =
			array(
				array(
					'field'   => 'eventTitle', 
					'label'   => 'Title', 
					'rules'   => 'required'
				),array(
					'field'   => 'type', 
					'label'   => 'Type', 
					'rules'   => 'required'
				),array(
					'field'   => 'description', 
					'label'   => 'Description', 
					'rules'   => 'required'
				),array(
					'field'   => 'location', 
					'label'   => 'City', 
					'rules'   => 'required'
				),array(
					'field'   => 'state', 
					'label'   => 'State', 
					'rules'   => 'required'
				),array(
					'field'   => 'time', 
					'label'   => 'Time', 
					'rules'   => 'required'
				),array(
					'field'   => 'eventDate', 
					'label'   => 'Start Date', 
					'rules'   => 'required'
				),array(
					'field'   => 'eventEnd', 
					'label'   => 'End Date', 
					'rules'   => 'required'
				)
			);
			
			$success = FALSE;
				if($_POST['recur'] == 'recur'){
				//
				// $required_fields[] = 	array('field'   => 'day', 'label'   => 'Day', 'rules'   => 'required');
					
				// $this->form_validation->set_rules($required_fields);
				// $validation = $this->form_validation->run();	
				
				// if($validation == TRUE){
					$dow   = $this->input->post('day');
					$step  = 1;
					$unit  = 'W';

					$start = new DateTime($this->input->post('eventDate'));
					$end = new DateTime($this->input->post('eventEnd'));

					$start->modify($dow); // Move to first occurence
					//$end->add(new DateInterval('P1Y')); // Move to 1 year from start

					$interval = new DateInterval("P{$step}{$unit}");
					$period   = new DatePeriod($start, $interval, $end);
					foreach ($period as $date) {
						$this->core->set['eventTitle'] = $this->input->post('eventTitle') ;
						$this->core->set['description'] = $this->input->post('description') ;
						$this->core->set['location'] = $this->input->post('location') ;
						$this->core->set['state'] = $this->input->post('state') ;
						$this->core->set['type'] = $this->input->post('type');
						$this->core->set['time'] = $this->input->post('time');
						$this->core->set['eventDate'] = $date->format('Y-m-d H:i:s');
						$this->core->set['userID'] = $id;
						
						$success = $this->core->update($this->track->events_tbl);
					}
				// }
				}elseif($_POST['recur'] == 'single'){
				
				$this->form_validation->set_rules($required_fields);
				$validation = $this->form_validation->run();	
				if($validation == TRUE){
					$this->core->set['eventTitle'] = $this->input->post('eventTitle') ;
					$this->core->set['description'] = $this->input->post('description') ;
					$this->core->set['location'] = $this->input->post('location') ;
					$this->core->set['state'] = $this->input->post('state') ;
					$this->core->set['type'] = $this->input->post('type');
					$this->core->set['time'] = $this->input->post('time');
					$this->core->set['eventDate'] = $this->input->post('eventDate');
					$this->core->set['eventEnd'] = $this->input->post('eventEnd');
					$this->core->set['userID'] = $id;
				
					$success = $this->core->update($this->track->events_tbl);
				}
				}	
		
			if ($success == TRUE){
				$this->session->set_flashdata('message','Event saved succesfully!');
				$trackdata = $this->track->getTrackDetails($id);
				$this->track->sendUpdateEmail($trackdata['tracksID'],'events');
				redirect("/admin/tracks/editTrack/".$trackID);
			}		
			}
						
		}
		$output['message'] = ($this->session->flashdata('message')?$this->session->flashdata('message'):'');
		$output['errors'] = validation_errors();
		
		//print_r($output);
		$this->load->view('/admin/editEvents',$output);

	}
	
	function editSingleEvent($id='',$trackID=''){
		if(!$id) show_404();
		if(!$trackID) show_404();

	$data = $this->core->get_values($this->track->events_tbl,array('eventID'=>$id));
	$output['id'] = $id;
	$output['trackID'] = $trackID;
	
	$output['title'] = form_input('eventTitle',set_value('eventTitle',$data['eventTitle']),"class='formelement' required autofocus");
	$output['type'] = form_dropdown('event_type', set_value('event_type', $data['type']), 'class="formelement"');
	$output['city'] = form_input('location',set_value('location',$data['location']),"class='formelement' required autofocus");
	$output['state'] = display_states('state',set_value('state',$data['state']),"class='formelement' required autofocus");
	$output['start'] = form_input('eventDate',set_value('eventDate',date("Y-m-d", strtotime($data['eventDate']))),"class='datepicker formelement' required autofocus");
	$output['end'] = form_input('eventEnd',set_value('eventEnd',date("Y-m-d", strtotime($data['eventEnd']))),"class='datepicker formelement' required autofocus");
	$output['time'] = form_input('time',set_value('time',$data['time']),"class='formelement' required autofocus");
	$output['description'] = form_textarea('description',set_value('description',$data['description']),"class='formelement' style='height:50px;' required autofocus");
	
	$this->core->required = array(
		'type' => array('label' => 'Event Type', 'rules' => 'required'),
		'eventTitle' => array('label' => 'Event Title', 'rules' => 'required|ucfirst'),
		'description' => array('label' => 'Description', 'rules' => 'required|ucfirst'),
		'location' => array('label' => 'City', 'rules' => 'required|ucfirst'),
		'state' => array('label' => 'State', 'rules' => 'required'),
		'time' => array('label' => 'Time', 'rules' => 'required'),
		'eventDate' => array('label' => 'Start Date', 'rules' => 'required'),
		'eventEnd' => array('label' => 'End Date', 'rules' => 'required'),
	);
		if(count($_POST)){
			$required_fields =
					array(
						array(
							'field'   => 'eventTitle', 
							'label'   => 'Title', 
							'rules'   => 'required'
						),array(
							'field'   => 'type', 
							'label'   => 'Type', 
							'rules'   => 'required'
						),array(
							'field'   => 'description', 
							'label'   => 'Description', 
							'rules'   => 'required'
						),array(
							'field'   => 'location', 
							'label'   => 'City', 
							'rules'   => 'required'
						),array(
							'field'   => 'state', 
							'label'   => 'State', 
							'rules'   => 'required'
						),array(
							'field'   => 'time', 
							'label'   => 'Time', 
							'rules'   => 'required'
						),array(
							'field'   => 'eventDate', 
							'label'   => 'Start', 
							'rules'   => 'required'
						),array(
							'field'   => 'eventEnd', 
							'label'   => 'Start', 
							'rules'   => 'required'
						)
					);
			$this->form_validation->set_rules($required_fields);
			$validation = $this->form_validation->run();	

			if($this->core->update($this->track->events_tbl,array('eventID'=>$id))){
				$this->session->set_flashdata('message','Changes saved successfully!');
				redirect("/admin/tracks/editTrack/".$trackID);
			}
		
		}
		$output['message'] = ($this->session->flashdata('message')?$this->session->flashdata('message'):'');
		$output['errors'] = validation_errors();
		
		$this->load->view($this->includes_path.'/header');
		$this->load->view('/admin/editSingleEvent',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	
	
}