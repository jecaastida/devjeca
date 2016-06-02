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
class Artworks_Model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}	
	}
	function get_all_artworks()
	{
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);
		
		$query = $this->db->get('artworks');
		if ($query->num_rows() > 0)
		{
			$result = $query->result_array();
			return $result;
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_artwork($artworkID)
	{				
		$this->db->where('artworkID', $artworkID);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);		
		$this->db->where('siteID', $this->siteID);
		
		$query = $this->db->get('artworks', 1);
		
		if ( $query->num_rows() == 1 )
		{
			$artwork = $query->row_array();
						
			return $artwork;
		}
		else
		{
			return FALSE;
		}
	}		
        function formatGallery($gal) {
            // load libs etc
            $str = "";
            $this->load->model('images/images_model', 'images');
            //$gal = $this->images->get_images_by_folder_ref($gallery['folderSafe']);
            if ($gallery = $this->images->get_images_by_folder_ref($gal['folderSafe'])) {
                // fill up template array
                $i = 0;
                foreach ($gallery as $galleryimage) {
                    if ($imageData = $this->template->get_image($galleryimage['imageRef'])) {
                        $imageHTML = display_image($imageData['src'], $imageData['imageName'], 100, 'class=""');
                        $imageHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/images/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);
                        $thumbHTML = display_image($imageData['src'], $imageData['imageName']);
                        $thumbHTML = preg_replace('/src=("[^"]*")/i', 'src="'.site_url('/thumbs/'.$imageData['imageRef'].strtolower($imageData['ext'])).'"', $imageHTML);
                        /*
						$img[$i] = array(
								'galleryimage:link' => site_url('images/'.$imageData['imageRef'].$imageData['ext']),
								'galleryimage:title' => $imageData['imageName'],
								'galleryimage:image' => $imageHTML,
								'galleryimage:thumb' => $thumbHTML,
								'galleryimage:filename' => $imageData['imageRef'].$imageData['ext'],
								'galleryimage:date' => dateFmt($imageData['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'),
								'galleryimage:author' => $this->images->lookup_user($imageData['userID'], TRUE),
								'galleryimage:author-id' => $imageData['userID'],
								'galleryimage:class' => $imageData['class']
						);
                        */
                        $str .= "<a style='padding:5px 0 0 5px;' class='lightbox' href='".site_url('images/'.$imageData['imageRef'].$imageData['ext'])."' title='".$imageData['imageName']."'/>".$thumbHTML." </a>";
                        $i++;
                    }
                }
            }
            else {
                $str = "";
            }
            return $str;
        }
        function getGallery($id,$type){
            $this->db->where('siteID', $this->siteID);
            $this->db->where('postID', $id);
            $this->db->where('deleted', 0);
            $this->db->where('folder_type', $type);
            $query = $this->db->get('image_folders');
            /*
            $sql = $this->db->query("SELECT * FROM ".$this->db->dbprefix."image_folders if
                WHERE
                        siteID=".$this->siteID."
                    AND postID=".$id."
                    AND deleted="."0"."
                        ");
            //$sql->result_array();
            */
            if ($query->num_rows()) {
                $arrConfig = $query->result_array();
                return $arrConfig[0];
            }
            else {
                return false;
            }
        }
        
	function get_post_by_id($artworkID)
	{
		$this->db->where('artworkID', $artworkID);
		
		$query = $this->db->get('artwork_post', 1);
		
		if ($query->num_rows())
		{
			$post = $query->row_array();
			
			return $post;
		}
		else
		{
			return FALSE;
		}
	}
	function get_tags()
	{
		$this->db->join('tags_ref', 'tags_ref.tag_id = tags.id');	
		$this->db->where('tags_ref.siteID', $this->siteID);
		
		$query = $this->db->get('tags');
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}	
	function update_tags($artworkID = '', $tags = '')
	{
		// add tags
		if ($tags)
		{
			$this->tags->delete_tag_ref(
			array(			 		
				'table' => 'artworks',
				'row_id' => $artworkID,
				'siteID' => $this->siteID)
			);
			
			$tags = str_replace(',', ' ', trim($tags));
			$tagsArray = explode(' ', $tags);
			foreach($tagsArray as $key => $tag)
			{
				$tag = trim($tag);
				if (isset($tag) && $tags != '' && strlen($tag) > 0)
				{
					$tidyTagsArray[] = $tag;
				}
			}
			$tags = array(
		 		'table' => 'artworks',
		 		'tags' => $tidyTagsArray,
				'row_id' => $artworkID,
				'siteID' => $this->siteID
			);
			$this->tags->add_tags($tags);
			return true;
		}
		else
		{
			return FALSE;
		}
	}
	function get_artworks($num = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID
		);
		// wheres
		$this->db->where($where);
		
		// check artwork isn't passed
		$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		// order by artwork date
		$this->db->order_by('artworkDate', 'asc');
		// get rows with paging
		$query = $this->db->get('artworks', $num);
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_artworks_special($num = '')
	{
		// default where
		/*
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID
		);
		*/
		
		$this->db->select('retail_price, asking_price, artworkID');
		//$this->db->where('emailID', $deploy['emailID']);
		$artquery = $this->db->get('ha_artworks');
		if ($artquery->num_rows())
		{
			$artqueryresult = $artquery->result_array();			
			foreach($artqueryresult as $artqueryvar)
			{	
			$retail_price = $artqueryvar['retail_price'];
			$asking_price = $artqueryvar['asking_price'];
			$artworkID = $artqueryvar['artworkID'];
			
			
			//if ($retail_price != 0) {
			//$sorting_price = $retail_price;	
			//}
			
			if ($asking_price != 0) {
			$sorting_price = $asking_price;	
			}
			
			if ($asking_price == 0) {
			$sorting_price = 0;	
			}
			
			$pricedata = array(
						   'sorting_price' => $sorting_price,
						);
			
			$this->db->where('artworkID', $artworkID);
			$this->db->update('ha_artworks', $pricedata); 
			
			}
			
		}
		
		
		$type1 = site_url('artworks/specials/under_100');
		$type2 = site_url('artworks/specials/under_500');
		$type3 = site_url('artworks/specials/under_1000');
		$type4 = site_url('artworks/specials/under_2000');
		$current_url = site_url($this->uri->uri_string());
		
		
		if ($current_url == $type1) {
		$where = array(
			'deleted' => 0,
			'consignment' => 1,
			'siteID' => $this->siteID,
			'sorting_price <=' => 100,
		);
		}
		if ($current_url == $type2) {
		$where = array(
			'deleted' => 0,
			'consignment' => 1,
			'siteID' => $this->siteID,
			'sorting_price <=' => 500,
			'sorting_price >=' => 101,
		);
		}
		if ($current_url == $type3) {
		$where = array(
			'deleted' => 0,
			'consignment' => 1,
			'siteID' => $this->siteID,
			'sorting_price <=' => 1000,
			'sorting_price >=' => 501,
		);
		}
		
		if ($current_url == $type4) {
		$where = array(
			'deleted' => 0,
			'consignment' => 1,
			'siteID' => $this->siteID,
			'sorting_price <=' => 2000,
			'sorting_price >=' => 1001,
		);
		}
		// wheres
		$this->db->where($where);
		$this->db->where('visible_on_website', 1);
		$this->db->where('owner_status', 1);
		$this->db->where('sorting_price !=', 0);
		
		// check artwork isn't passed
		//$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		$this->db->order_by('ln_tag', 'asc');
		$this->db->order_by('artist_link', 'asc');
		
		// order by artwork title
		//$this->db->order_by('artworkTitle', 'asc');
		
		$the_string = '"The ';
		$the_substitute_string = '"';
		$artworkTitle_replace = "replace(artworkTitle,'".$the_string."','".$the_substitute_string."')";
		$this->db->order_by($artworkTitle_replace, 'asc');
		
		// get rows with paging
		$query = $this->db->get('artworks', $num);
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_artworks_auction($num = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID,
			'include_in_auction' => 1,
			'visible_on_website' => 1,
			'owner_status' => 1,
                        'feature' => 1,
		);
		// wheres
		$this->db->where($where);
		
		// check artwork isn't passed
		//$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		$this->db->order_by('ln_tag', 'asc');
		$this->db->order_by('artist_link', 'asc');
		
		// order by artwork title
		//$this->db->order_by('artworkTitle', 'asc');
		
		$the_string = '"The ';
		$the_substitute_string = '"';
		$artworkTitle_replace = "replace(artworkTitle,'".$the_string."','".$the_substitute_string."')";
		$this->db->order_by($artworkTitle_replace, 'asc');
		// get rows with paging
		$query = $this->db->get('artworks', $num);
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_artworks_search($num = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'siteID' => $this->siteID,
			//'include_in_auction' => 1,
                        'feature' => 1,
		);
		// wheres
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);
		
		if (!$this->input->post('aa_search')) {
		$this->db->where('siteID', 3);
		}
		
		
		
		if ($this->input->post('aa_search') == "1") {
			
			if ($this->input->post('art_search')) {
				$art_search = $this->input->post('art_search');
				$this->db->like('search_tags', $art_search); 
			}
			
			if (!$this->input->post('art_search')) {
				$this->db->where('published', 3);
			}
			
		}
		
		
		// check artwork isn't passed
		//$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		$this->db->order_by('ln_tag', 'asc');
		$this->db->order_by('artist_link', 'asc');
		// get rows with paging
		
		$the_string = '"The ';
		$the_substitute_string = '"';
		$artworkTitle_replace = "replace(artworkTitle,'".$the_string."','".$the_substitute_string."')";
		$this->db->order_by($artworkTitle_replace, 'asc');
		
		
		$query = $this->db->get('artworks', $num);
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function get_featured_artworks($num = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,
			'featured' => 1,
			'siteID' => $this->siteID
		);
		// wheres
		$this->db->where($where);
		// order by artwork date
		$this->db->order_by('artworkDate', 'desc');
		// get rows with paging
		$query = $this->db->get('artworks');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	function get_month($month = '', $year = '')
	{
		// default where
		$where = array(
			'deleted' => 0,
			'published' => 1,			
			'siteID' => $this->siteID
		);
		// where artwork is not old and is in this month
		$month = ($month) ? $month : date("m", time());
		$next_month = $month + 1;
		$year = ($year) ? $year : date("Y", time());
		$from =  date("Y-m-d H:i:s", mktime(0, 0, 0, $month, 1, $year));
		$to =  date("Y-m-d H:i:s", mktime(23, 59, 59, $next_month, 0, $year));
		
		$where['artworkDate >='] = $from;
		$where['artworkDate <='] = $to;
		// wheres
		$this->db->where($where);
		// order by artwork date
		$this->db->order_by('artworkDate', 'asc');
		// get rows with paging
		$query = $this->db->get('artworks');
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}	
	function get_artworks_by_tag($tag, $limit = 10)
	{
		// get rows based on this tag
		$result = $this->tags->fetch_rows(array(
			'table' => 'artworks',
			'tags' => array(1, $tag),
			'siteID' => $this->siteID
		));
		$tags = $result->result_array();
		foreach ($tags as $tag)
		{
			$tagsArray[] = $tag['row_id'];
		}
		// default where
		$this->db->where(array(
			'deleted' => 0,
			'published' => 1,			
			'siteID' => $this->siteID
		));
		// check artwork isn't passed
		$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		// where tags
		$this->db->where_in('artworkID', $tagsArray);
		$this->db->order_by('artworkDate', 'asc');
		
		$query = $this->db->get('artworks', $limit);
		
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	function get_artworks_by_date($year, $month = '', $day = 0)
	{
		if ($month)
		{
			$from =  date("Y-m-d H:i:s", mktime(0, 0, 0, $month, ((!$day) ? 1 : $day), $year));
			$to = ($day < 1) ? date("Y-m-d H:i:s", mktime(23, 59, 59, ($month+1), $day, $year)) : date("Y-m-d H:i:s", mktime(23, 59, 59, $month, $day, $year));
		}
		else
		{
			$from = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, ((!$day) ? 1 : $day), $year));
			$to = date("Y-m-d H:i:s", mktime(0, 0, 0, 1, 1, ($year+1)));
		}	
		$this->db->where('artworkDate >=', $from);
		$this->db->where('artworkDate <=', $to);
		$this->db->where('deleted', 0);	
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);
		$this->db->order_by('artworkDate');
				
		$query = $this->db->get('artworks');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	function get_post_by_title($title = '')
	{
		$this->db->where('artworkTitle', $title);
		$this->db->where('deleted', 0);	
		$this->db->where('published', 1);		
		$this->db->where('siteID', $this->siteID);
				
		$query = $this->db->get('artworks');
		if ($query->num_rows() > 0)
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}
	}
	function get_archive()
	{
		// selects
		$this->db->select('COUNT(artworkID) as numArtworks, DATE_FORMAT(artworkDate, "%M %Y") as dateStr, DATE_FORMAT(artworkDate, "%m") as month, DATE_FORMAT(artworkDate, "%Y") as year', FALSE);
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);		
		$this->db->where('siteID', $this->siteID);	
		// check artwork isn't passed
		$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		// group by month
		$this->db->group_by('dateStr');
		
		$query = $this->db->get('artworks');
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	function get_headlines($num = 10)
	{
		$this->db->select('artworkID, artworkTitle, artworkDate, description');
		return $this->get_artworks($num);
	}
	function search_artworks($query = '', $ids = '')
	{
		if (!$query && !$ids)
		{
			return FALSE;
		}
		// default wheres
		$this->db->where('deleted', 0);
		$this->db->where('published', 1);
		$this->db->where('siteID', $this->siteID);
		// search
		if ($query)
		{
			// tidy query
			$q = $this->db->escape_like_str($query);
			$sql = '(artworkTitle LIKE "%'.$q.'%" OR description LIKE "%'.$q.'%")';
		}
		if ($ids)
		{
			$sql .= ' OR artworkID IN ('.implode(',', $ids).')';
		}
		$this->db->where($sql);
	
		// check artwork isn't passed
		$this->db->where('IF(artworkEnd > 0, artworkEnd, artworkDate) >=', date("Y-m-d H:i:s", time()));
		
		$this->db->order_by('artworkDate', 'asc');			
	
		$query = $this->db->get('artworks');
		
		if ($query->num_rows() > 0)
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}
	}
	
	function artistsearch()
	{
		$artistlisting = "";
        $this->db->select('first_name, last_name, postID');
        $this->db->where('deleted', 0);
		$this->db->order_by('last_name', 'asc');
        $artistquery = $this->db->get('ha_artists_posts');
        if ($artistquery->num_rows())
        {
            $artistqueryresult = $artistquery->result_array();			
            foreach($artistqueryresult as $artistqueryvar)
            {	
				$first_name = $artistqueryvar['first_name'];
				$last_name = $artistqueryvar['last_name'];
				$artist_id = $artistqueryvar['postID'];
				
				if ($first_name) 
				{
					$first_name = ", ".$first_name."";
				}
				else {
					$first_name = "";
				}
				$artistlisting .= "<option value='".$artist_id."'>".$last_name."".$first_name."</option>";
            }
    
        }
		return $artistlisting;
	}

	function ownersearch()
	{
		$artistlisting = "";
        $this->db->select('first_name, last_name, ID');
		$this->db->order_by('last_name', 'asc');
        $artistquery = $this->db->get('ha_owner');
        if ($artistquery->num_rows())
        {
            $artistqueryresult = $artistquery->result_array();			
            foreach($artistqueryresult as $artistqueryvar)
            {	
				$first_name = $artistqueryvar['first_name'];
				$last_name = $artistqueryvar['last_name'];
				$artist_id = $artistqueryvar['ID'];
				if ($first_name) 
				{
					$first_name = ", ".$first_name."";
				}
				else {
					$first_name = "";
				}
				$artistlisting .= "<option value='".$artist_id."'>".$last_name."".$first_name."</option>";
            }
    
        }
		return $artistlisting;
	}
	
	function owner_search()
	{
		$artistlisting = "";
        $this->db->select('firstName, lastName, userID');
		$this->db->where('groupID', 9);		
		$this->db->order_by('lastName', 'asc');
        $artistquery = $this->db->get('ha_users');
        if ($artistquery->num_rows())
        {
            $artistqueryresult = $artistquery->result_array();			
            foreach($artistqueryresult as $artistqueryvar)
            {	
				$first_name = $artistqueryvar['firstName'];
				$last_name = $artistqueryvar['lastName'];
				$artist_id = $artistqueryvar['userID'];
				if ($first_name) 
				{
					$first_name = ", ".$first_name."";
				}
				else {
					$first_name = "";
				}
				$artistlisting .= "<option value='".$artist_id."'>".$last_name."".$first_name."</option>";
            }
    
        }
		return $artistlisting;
	}
	
	
	
	function categorysearch()
	{
		$categlisting = "";
        //select categories
        $this->db->select('categTitle, categID');
        $this->db->order_by('categTitle', 'asc');
        $categquery = $this->db->get('ha_categs');
        if ($categquery->num_rows())
        {
            $categqueryresult = $categquery->result_array();			
            foreach($categqueryresult as $categqueryvar)
            {	
				$categTitle = $categqueryvar['categTitle'];
				$categID = $categqueryvar['categID'];
				$categlisting .="<option value='".$categID."'>".$categTitle."</option>";
            }
        }
		return $categlisting;
	}
	
	
	function selectartisturl($artist_link)
	{
		// default wheres
		$this->db->where('postID', $artist_link);		

		// grab
		$query = $this->db->get('ha_artists_posts', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}		
	}
	
	function selectartist($artist_link)
	{
		// default wheres
		$this->db->where('postID', $artist_link);		
        $this->db->where('deleted', 0);
		// grab
		$query = $this->db->get('ha_artists_posts', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}		
	}
	
	function selectowner($owner_link)
	{
		// default wheres
		$this->db->where('userID', $owner_link);		
       // $this->db->where('deleted', 0);
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



	function artistlist($artist_link)
	{
		$artistdropdown = "";
		$this->db->select('first_name, last_name, postID');
		$this->db->where('deleted', 0);
		$this->db->order_by('not_in_list', 'desc');
		$this->db->order_by('last_name', 'asc');
		$artistquery = $this->db->get('ha_artists_posts');
		if ($artistquery->num_rows())
		{
			$artistqueryresult = $artistquery->result_array();			
			foreach($artistqueryresult as $artistqueryvar)
			{	
				$first_name = $artistqueryvar['first_name'];
				$last_name = $artistqueryvar['last_name'];
				$artist_id = $artistqueryvar['postID'];
				$selected = '';
				if ($artist_id == $artist_link) 
				{
					$selected = 'selected="selected"';	
				}
				
				$artistdropdown .= "<option value='".$artist_id."' ".$selected.">".$last_name." ".$first_name."</option>";
			}
		}
		return $artistdropdown;
	}
	
	function ownerlist($artist_link)
	{
		$artistdropdown = "";
		$this->db->select('first_name, last_name, ID');
		$this->db->order_by('last_name', 'asc');
		$artistquery = $this->db->get('ha_owner');
		if ($artistquery->num_rows())
		{
			$artistqueryresult = $artistquery->result_array();			
			foreach($artistqueryresult as $artistqueryvar)
			{	
				$first_name = $artistqueryvar['first_name'];
				$last_name = $artistqueryvar['last_name'];
				$artist_id = $artistqueryvar['ID'];
				$selected = '';
				if ($artist_id == $artist_link) 
				{
					$selected = 'selected="selected"';	
				}
				
				$artistdropdown .= "<option value='".$artist_id."' ".$selected.">".$last_name." ".$first_name."</option>";
			}
		}
		return $artistdropdown;
	}
	
	
	function owner_list($artist_link)
	{
		$artistdropdown = "";
		$this->db->select('firstName, lastName, userID');
		$this->db->order_by('not_in_list', 'desc');
		$this->db->order_by('lastName', 'asc');
		$artistquery = $this->db->get('ha_users');
		if ($artistquery->num_rows())
		{
			$artistqueryresult = $artistquery->result_array();			
			foreach($artistqueryresult as $artistqueryvar)
			{	
				$first_name = $artistqueryvar['firstName'];
				$last_name = $artistqueryvar['lastName'];
				$artist_id = $artistqueryvar['userID'];
				$selected = '';
				if ($artist_id == $artist_link) 
				{
					$selected = 'selected="selected"';	
				}
				
				$artistdropdown .= "<option value='".$artist_id."' ".$selected.">".$last_name." ".$first_name."</option>";
			}
		}
		return $artistdropdown;
	}
	
	
	function mediumlist($medium_link)
	{
		$mediumdropdown = "";
		$this->db->select('mediumID, mediumTitle');
		$this->db->where('deleted', 0);
		$this->db->order_by('mediumTitle', 'asc');
		$mediumquery = $this->db->get('ha_mediums');
		if ($mediumquery->num_rows())
		{
			$mediumqueryresult = $mediumquery->result_array();			
			foreach($mediumqueryresult as $mediumqueryvar)
			{	
				$mediumID = $mediumqueryvar['mediumID'];
				$mediumTitle = $mediumqueryvar['mediumTitle'];
				$selected = '';
				if ($mediumID == $medium_link) 
				{
					$selected = 'selected="selected"';	
				}
				$mediumdropdown .= "<option value='".$mediumID."' ".$selected.">".$mediumTitle."</option>";
			}
		}
		return $mediumdropdown;
	}
	
	function categorylist($category_link)
	{
		$categorydropdown = "";
		$this->db->select('categTitle, categID');
		$this->db->order_by('categTitle', 'asc');
		$categquery = $this->db->get('ha_categs');
		if ($categquery->num_rows())
		{
			$categqueryresult = $categquery->result_array();			
			foreach($categqueryresult as $categqueryvar)
			{	
				$categTitle = $categqueryvar['categTitle'];
				$categID = $categqueryvar['categID'];
				$selected = '';
				if ($categID == $category_link) {
					$selected = 'selected="selected"';	
				}
				$categorydropdown .= "<option value='".$categID."' ".$selected.">".$categTitle."</option>";
			}
		}
		return $categorydropdown;
	}
	
	
	
	function lookup_user($userID, $display = FALSE)
	{
		// default wheres
		$this->db->where('userID', $userID);
		// grab
		$query = $this->db->get('users', 1);
		if ($query->num_rows())
		{
			$row = $query->row_array();
			
			if ($display !== FALSE)
			{
				return ($row['displayName']) ? $row['displayName'] : $row['firstName'].' '.$row['lastName'];
			}
			else
			{
				return $row;
			}
		}
		else
		{
			return FALSE;
		}		
	}
	/// OLD!!! ///
	
		
	function tag_cloud($num)
	{
		$this->db->select('t.tag, COUNT(pt.tagID) as qty', FALSE);
		$this->db->join('tags pt', 'pt.tag_id = t.id', 'inner');
		$this->db->groupby('t.id');
		
		$query = $this->db->get('tags t');
		
		$built = array();
		
		if ($query->num_rows > 0)
		{
			$result = $query->result_array();
			
			foreach ($result as $row)
			{
				$built[$row['tag']] = $row['qty'];
			}
			
			return $built;
		}
		else
		{
			return array();
		}
	}
	
	function get_imginfo($artworkID)
	{
		// default wheres
		$this->db->where('artworkID', $artworkID);		
	
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
	
	//m2016
	function seller_details($userID)
	{
		$this->db->where('userID', $userID);
		//$this->db->where('catID >=', 400);
		//$this->db->order_by('catID', 'asc');
		//$this->db->limit(10);
		$listidquery = $this->db->get('ha_users');
		if ($listidquery->num_rows())
		{
			$listidqueryresult = $listidquery->result_array();			
			foreach($listidqueryresult as $listidqueryvar)
			{	
				$first_name = $listidqueryvar['firstName'];
				$last_name = $listidqueryvar['lastName'];
				$companyName = $listidqueryvar['companyName'];
				$address1 = $listidqueryvar['address1'];
				$address2 = $listidqueryvar['address2'];
				$city = $listidqueryvar['city'];
				$state = lookup_state($listidqueryvar['state']);
				$postcode = $listidqueryvar['postcode'];
				$country = lookup_country($listidqueryvar['country']);
				$phone = $listidqueryvar['phone'];
				$mobile = $listidqueryvar['mobile'];
				$email = $listidqueryvar['email'];
				$additionalemail = $listidqueryvar['additionalemail'];
				$natureofinterest = $listidqueryvar['natureofinterest'];
				$noi = "";
				if ($natureofinterest == 1)
				{
					$noi = "Private Collector";
				}
				elseif ($natureofinterest == 2)
				{
					$noi = "Dealer/Gallery";
				}
				elseif ($natureofinterest == 3)
				{
					$noi = "Appraiser";
				}
				$active = $listidqueryvar['active'];
				
				if ($active == 1)
				{
					$sitestatus = "Active";
				}
				else
				{
					$sitestatus = "Inactive";
				}
				$details = '<a class="fancybox" href="#inline1" title="Details">View Details ('.$last_name.' '.$first_name.')</a>
				
					<div id="inline1" style="width:400px;display: none;">
							<strong>Name:</strong> '.$first_name.' '.$last_name.' <br/>
							<strong>Company Name:</strong> '.$companyName.' <br/>
							<strong>Address 1:</strong> '.$address1.' <br/>
							<strong>Address 2:</strong> '.$address2.' <br/>
							<strong>City:</strong> '.$city.' <br/>
							<strong>State:</strong> '.$state.' <br/>
							<strong>ZIP:</strong> '.$postcode.' <br/>
							<strong>Country:</strong> '.$country.' <br/>
							<strong>Phone:</strong> '.$phone.' <br/>
							<strong>Mobile:</strong> '.$mobile.' <br/>
							<strong>Email:</strong> '.$email.' <br/>
							<strong>Additional Email:</strong> '.$additionalemail.' <br/>
							<strong>Most Appropriate:</strong> '.$noi.' <br/>
							<strong>Site Status:</strong> '.$sitestatus.' <br/>
					</div>				
					';	
				
			}
		}
		
		return  $details;
		
	}
	//m2016		
			
}