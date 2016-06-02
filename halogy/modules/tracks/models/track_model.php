<?php
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

class Track_model extends CI_Model {

	var $siteID;
	var $tracks_tbl = 'tracks';
	var $trackcat_tbl = 'trackcat';
	var $machinetype_tbl = 'machinecats';
	var $user_tbl = 'users';
	var $subscription_tbl = 'member_subscription';
	var $images_tbl = 'track_imgs';
	var $videos_tbl = 'track_vids';
	var $events_tbl = 'events';
        var $eventcats_tbl = 'eventcats';
	var $subscriber_tbl = 'track_subscribers';
	var $payment_tbl = 'member_transactions';
	
	var $trackXtrackcat_tbl = 'track_trackcat';
	var $trackXmachinecat_tbl = 'track_machinecats';
	
	var $upl_pth = '/static/uploads';
	var $uploadsPath;
	
	function __construct()
	{
		parent::__construct();
		

		if (!$this->siteID)
		{
			$this->siteID = SITEID;
		}
	}
	
	function getTrackDetails($id){
		if(!$id) return FALSE;
		
		$data = $this->db->get_where($this->tracks_tbl,array('userID'=>$id,));
		
		if($data->num_rows()){
			return $data->row_array();
		}else{
			return FALSE;
		}
	}
	
	
	
	function getUserDetails($id){
		if(!$id) return FALSE;
		
		if( $data = $this->db->get_where($this->user_tbl,array('userID'=>$id,)) ){
			return $data->row_array();
		}else{
			return FALSE;
		}
		
	}
	
	function getSubscriptionDetails($id){
		if(!$id) return FALSE;
		
		if( $data = $this->db->get_where($this->subscription_tbl,array('subscriptionID'=>$id)) ){
			return $data->row_array();
		}else{
			return FALSE;
		}
	
	}
	
	function get_no_of_images($id){
		if(!$id) return FALSE;
		
		if( $data = $this->db->get_where($this->images_tbl,array('tracksID'=>$id)) ){
			return $data->num_rows();
		}else{
			return 0;
		}
	}
	
	function get_no_of_videos($id){
		if(!$id) return FALSE;
		
		if( $data = $this->db->get_where($this->videos_tbl,array('tracksID'=>$id)) ){
			return $data->num_rows();
		}else{
			return 0;
		}
	
	}
	
	function get_images($id,$imgID = ''){
		if(!$id) return FALSE;
		
		//outputs single image data when picID is specified
		//outputs array of images when no picID is specified
		$this->db->order_by('imgID','desc');
		if($imgID != ''){
			$data = $this->db->get_where($this->images_tbl,array('tracksID'=>$id, 'imgID' => $imgID));
			return $data->row_array();
		}else{
			$data = $this->db->get_where($this->images_tbl,array('tracksID'=>$id));
			return $data->result_array();
		}
	
	}
	
	function get_videos($id = '', $vidID = ''){
		if(!$id) return FALSE;
		
		//outputs single video data when vidID is specified
		//outputs array of videos when no vidID is specified
		
		$this->db->order_by('vidID','desc');
		if($vidID != ''){
			$data = $this->db->get_where($this->videos_tbl,array('tracksID'=>$id, 'vidID' => $vidID));
			return $data->row_array();
		}else{
			$data = $this->db->get_where($this->videos_tbl,array('tracksID'=>$id));
			return $data->result_array();
		}
	}
	
	function get_youtubeID($link=''){
         if(!$link) return FALSE;

            $video_id = explode("?v=", $link); // For videos like http://www.youtube.com/watch?v=...
            if (empty($video_id[1]))
                $video_id = explode("/v/", $link); // For videos like http://www.youtube.com/watch/v/..

            $video_id = explode("&", $video_id[1]); // Deleting any other params
          return $video_id[0];

    }


// SAVE COMMANDS //

	
	function saveTrackDetails($id,$trackname,$address,$city,$state,$country,$phone,$email,$website,$facebook,$twitter,$instagram,$youtube,$image){
		if(!$id) return FALSE;
		
		$this->db->set('trackname',$trackname);
		$this->db->set('address',$address);
		$this->db->set('city',$city);
		$this->db->set('t_state',$state);
		$this->db->set('t_country',$country);
		$this->db->set('phone',$phone);
		$this->db->set('email',$email);
		$this->db->set('website',$website);
		$this->db->set('facebook',$facebook);
		$this->db->set('twitter',$twitter);
		$this->db->set('instagram',$instagram);
		$this->db->set('youtube',$youtube);
		$this->db->set('image',$image);
		
		$this->db->where('userID',$id);
		
		if( $this->db->update($this->tracks_tbl) ){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	function get_recent_tracks($offset=0){
                $this->db->order_by('lastUpdate','desc');
		$get_tracks = $this->db->get($this->tracks_tbl,5,$offset);
		return $get_tracks->result_array();                
	}
	function saveTrackDescription($id,$trackdesc){
		if(!$id) return FALSE;
		
		$this->db->set('trackdesc',$trackdesc);
		
		$this->db->where('tracksID',$id);
		
		if( $this->db->update($this->tracks_tbl) ){
			return TRUE;
		}else{
			return FALSE;
		}
	}
	
	
	function saveMyDetails($id,$firstName,$lastName,$password,$facebook,$twitter){
		if(!$id) return FALSE;
		
		$this->db->set('firstName',$firstName);
		$this->db->set('lastName',$lastName);
		$this->db->set('password',md5($password));
		$this->db->set('facebook',$facebook);
		$this->db->set('twitter',$twitter);
		
		$this->db->where('userID',$id);
		
		if( $this->db->get_where($this->user_tbl) ){
			return TRUE;
		}else{
			return FALSE;
		}
	
	}
	
	
	function sendSubscribeEmail($id){
		$get_subscriber = $this->db->get_where($this->subscriber_tbl,array('subscriberID'=>$id));
		$data = $get_subscriber->row_array();
		
		$get_trackdata = $this->db->get_where($this->tracks_tbl,array('tracksID'=>$data['tracksID']));
		$trackdata = $get_trackdata->row_array();
		
		$message =
		"Hi ".$data['name'].",

		You have been subscribed to Motocrosstracks.com to receive updates from ".$trackdata['trackname']." Track.

		You will be receiving emails when the track updates their Track details, track description, photos, videos, events and location on map.

		Regards,
		Motocrosstracks.com


		----------------------------------------------------------
		If you wish to unsubscribe to this track's updates, kindly click the link below.
		".site_url('/tracks/unsubscribeTrack/'.$data['unsubscribeCode']);
				
		
		$this->load->library('email');
		$this->email->from('no-reply@motocrosstracks.com', 'Motocrosstracks.com');
		$this->email->to($data['email']);
		$this->email->subject($trackdata['trackname'].' - Track Subscription');
		$this->email->message($message);
		$this->email->send();
	
	
	}
	
	function sendUpdateEmail($id,$updateType=''){
		$get_subscribers = $this->db->get_where($this->subscriber_tbl,array('tracksID'=>$id));
		$subscribers = $get_subscribers->result_array();
		
		$get_trackdata = $this->db->get_where($this->tracks_tbl,array('tracksID'=>$id));
		$trackdata = $get_trackdata->row_array();
		
		foreach($subscribers as $val){
				
				$message =
		"Hi ".$val['name'].",

		This is to notify you that ".$trackdata['trackname']." has updated their ".$updateType.".

		Click the link below to go to their track page
		".site_url('/tracks/track_page/'.$id)."

		Regards,
		Motocrosstracks.com


		----------------------------------------------------------
		If you wish to unsubscribe to this track's updates, kindly click the link below.
		".site_url('/tracks/unsubscribeTrack/'.$val['unsubscribeCode']);
				
				
		
		$this->load->library('email');
		$this->email->from('no-reply@motocrosstracks.com', 'Motocrosstracks.com');
		$this->email->to($val['email']);
		$this->email->subject($trackdata['trackname'].' - Track Updated');
		$this->email->message($message);
		$this->email->send();
            }
        }
	
	

	// function viewallRaces(){
                // $this->db->order_by('start','asc');
		// $query = $this->db->get($this->race_tbl);
		// $out = $query->result_array();
		
		// foreach($out as $count=>$val){
			// $out[$count]['start'] = dateFmt($val['start'], 'M j, Y', FALSE);
			// $out[$count]['end'] = dateFmt($val['end'], 'M j, Y', FALSE);
		// }
		// if ($query->num_rows())
			// return $out;
		// else return FALSE;
		
	// }
	
	// function viewSingleRace($id = ''){
		// if(!$id) return FALSE;
		// $query = $this->db->get_where($this->race_tbl,array('raceID'=>$id));
		
		// if ($query->num_rows())
			// return $query->row_array();
		// else return FALSE;
	// }
	
	// function addRace($makeName = ''){
		// if(!$makeName) return FALSE;
		
		// if ($this->db->insert($this->makes_tbl,array('makeName'=>$makeName)) == TRUE)
			// return TRUE;
		// else return FALSE;
	// }
	
	// function updateRace($id = '', $makeName = ''){
		// if(!$id) return FALSE;
		
		// if ($this->db->update($this->makes_tbl,array('makeID'=>$id,'makeName'=>$makeName)) == TRUE)
			// return TRUE;
		// else return FALSE;
	// }
	
	// function deleteRace($id = ''){
		// if(!$id) return FALSE;
		
		// if ($this->db->delete($this->race_tbl,array('raceID'=>$id)) == TRUE)
			// return TRUE;
		// else return FALSE;
	// }

	// function update_page_images($boatID = '', $uploaded_images = array(), $del_img_arr = array()){
		// if($boatID){
			// $img_arr = array();			
			// $query = $this->db->get_where($this->images_tbl, array('boatID' => $boatID));
			// if ($all_uploaded = $query->result_array()){
				// foreach($all_uploaded as $val){
					// $correct_key = $val['img_num'];

					// if (!isset($del_img_arr[$correct_key])){
						// $img_arr[$correct_key] = $correct_key;
						

						// $this->db->where(array('boatID'=> $boatID, 'img_num' => $correct_key));
						// $data = array(
						   // 'img_caption' => $this->input->post('capt_'.$correct_key)
						// );
						// if (isset($uploaded_images[$correct_key])){
							// $data['img'] = $uploaded_images[$correct_key];
						// }
							// $this->db->update($this->images_tbl, $data);
					// }
					
				// }
			// }

			// if ($uploaded_images){
				// if ($new_imgs = array_diff_key($uploaded_images, $img_arr)){
					// $this->insert_page_images($boatID, $new_imgs);
				// }				
			// }	
			
			// $this->delete_page_images($boatID, $del_img_arr);			
		// }

	// }

	// function delete_page_images($boatID = '', $del_img_arr = array()){
		// if($boatID){
			// if ($del_img_arr && is_array($del_img_arr)){
				// $this->db->where('boatID', $boatID);
				// $this->db->where_in('img_num', $del_img_arr);
				// $this->db->delete($this->images_tbl); 
			// }			
		// }

	// }

	// function get_page_images($boatID = '', $limit = 999){
		// if (!$boatID){
			// return FALSE;
		// }

		// $this->db->order_by('img_num');
		// $query = $this->db->get_where($this->images_tbl, array('boatID' => $boatID), $limit);
		// if ($query->num_rows()){
			// return $query->result_array();
		// }else{
			// return FALSE;
		// }		
	// }

	// function load_image($image, $new_format = FALSE){
		// $imagePath = $this->uploadsPath . '/' . $image;	

		// if ($new_format){
			// $imagePath = $this->upl_pth . '/' . $image;
		// }

		// $ext = substr($image,strrpos($image,'.'));
		// $thumbPath = str_replace($ext, '', $imagePath).'_thumb'.$ext;
		// $thumbpath = (file_exists('.'.$thumbPath)) ? $thumbPath : $imagePath;

		// return $thumbpath;
	// }
	
	// function insert_page_images($boatID = '', $uploaded_images = array())
	// {
		// if($boatID)
		// {
			// if ($uploaded_images && is_array($uploaded_images))
			// {
				// $set = array();
				// foreach($uploaded_images as $key => $val)
				// {
					// $set[] = array(
						// 'boatID' => $boatID,
						// 'img_num' => $key,
						// 'img' => $val,
					// );
				// }
				
				// $this->db->insert_batch($this->images_tbl, $set); 
			// }			
		// }

	// }

	
}