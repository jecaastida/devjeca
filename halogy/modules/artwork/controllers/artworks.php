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
class Artworks extends MX_Controller {
	var $partials = array();
        var $type = 'artwork';
		
	function __construct()
	{
		parent::__construct();
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
		// get site permissions and redirect if it don't have access to this module
		if (!$this->permission->sitePermissions)
		{
			show_error('You do not have permission to view this page');
		}
		if (!in_array($this->uri->segment(1), $this->permission->sitePermissions))
		{
			show_error('You do not have permission to view this page');
		}		
		// load models and modules
		$this->load->library('tags');
		$this->load->library('calendar', array('show_next_prev' => TRUE, 'next_prev_url' => '/artworks'));		
		$this->load->model('artworks_model', 'artworks');
                //$this->CI->load->model('news_model', 'news');
                $this->load->module('news');
		$this->load->module('pages');		
		// load partials - archive
		if ($archive = $this->artworks->get_archive())
		{
			foreach($archive as $date)
			{
				$this->partials['artworks:archive'][] = array(
					'archive:link' => site_url('/artworks/'.$date['year'].'/'.$date['month'].'/'),
					'archive:title' => $date['dateStr'],
					'archive:count' => $date['numArtworks']
				);
			}
		}
		// load partials - latest
		if ($latest = $this->artworks->get_headlines())
		{
			foreach($latest as $artwork)
			{
				$this->partials['artworks:latest'][] = array(
					'latest:link' => site_url('artworks/viewartwork/'.$artwork['artworkID']),
					'latest:title' => $artwork['artworkTitle'],
					'latest:date' => date((($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'), strtotime($artwork['artworkDate'])),
				);
			}
		}	
		// load partials - calendar
		$month = ($this->uri->segment(3) && intval($this->uri->segment(2))) ? $this->uri->segment(3) : date('m', time());
		$year = ($this->uri->segment(2) && intval($this->uri->segment(2))) ? $this->uri->segment(2) : date('Y', time());
		$monthArtworks = array();
		if ($data['month'] = $this->artworks->get_month($month, $year))
		{
			foreach($data['month'] as $artwork)
			{
				$monthArtworks[date('j', strtotime($artwork['artworkDate']))] = '/artworks/'.date('Y/m/d', strtotime($artwork['artworkDate']));
			}
		}
		@$this->partials['artworks:calendar'] = $this->calendar->generate($year, $month, $monthArtworks);
	}
	function index()
	{
		// get partials
                
		$output = $this->partials;
						
		// get latest artworks
		$artworks = $this->artworks->get_artworks(10);
		$output['artworks:artworks'] = $this->_populate_artworks($artworks);
		// send artworks to page
		$data['artworks'] = $artworks;
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks';
		$output['page:heading'] = 'Upcoming Artworks';
		
		// display with cms layer
		$this->pages->view('artworks', $output, TRUE);	
	}
	
	function specials()
	{
		// get partials
                
		$output = $this->partials;
						
		// get latest artworks
		$artworks = $this->artworks->get_artworks_special(1000);
		$output['artworks:artworks'] = $this->_populate_artworks_special($artworks);
		// send artworks to page
		$data['artworks'] = $artworks;
		
		$type1 = site_url('artworks/specials/under_100');
		$type2 = site_url('artworks/specials/under_500');
		$type3 = site_url('artworks/specials/under_1000');
		$type4 = site_url('artworks/specials/under_2000');
		$current_url = site_url($this->uri->uri_string());
		
		if ($current_url == $type1) {
			$pagehead = "Fine Art Gift Ideas - Under $100.00";	
			$subspecialpage_id = 115;
			$framepage = 'specials-under-100.00-heading-and-text';
		}
		
		if ($current_url == $type2) {
			$pagehead = "Fine Art Gift Ideas - Under $500.00";	
			$subspecialpage_id = 116;
			$framepage = 'specials-under-500.00-heading-body-text';
		}
		
		if ($current_url == $type3) {
			$pagehead = "Fine Art Gift Ideas - Under $1,000.00";	
			$subspecialpage_id = 117;
			$framepage = 'specials-under-1000.00-heading-body-text';
		}
		
		if ($current_url == $type4) {
			$pagehead = "Fine Art Gift Ideas - Under $2,000.00";	
			$subspecialpage_id = 118;
			$framepage = 'specials-under-2000.00-heading-body-text';
		}
		
		//sub special page (heading and text)
		$this->db->select('versionID, keywords, description, title');
		$this->db->where('pageID', $subspecialpage_id);
		$pagequery = $this->db->get('ha_pages');
		if ($pagequery->num_rows())
		{
			$pagequeryresult = $pagequery->result_array();			
			foreach($pagequeryresult as $pagequeryvar)
			{	
				
				$versionID = $pagequeryvar['versionID'];
				$keywordsval = $pagequeryvar['keywords'];
				$descriptionval = $pagequeryvar['description'];
				$titleval = $pagequeryvar['title'];
				
				$this->db->select('body');
				$this->db->where('versionID', $versionID);
				$bodyquery = $this->db->get('ha_page_blocks');
				if ($bodyquery->num_rows())
				{
					$bodyqueryresult = $bodyquery->result_array();			
					foreach($bodyqueryresult as $bodyqueryvar)
					{	
					$body_val = $bodyqueryvar['body'];
					$bodytext = $this->template->parse_body($body_val);
					}
				}
			}
		}
		
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
		// set title
		$output['page:title'] = $titleval;
		$output['page:heading'] = "Artworks";
		$output['page:textcontent'] = $bodytext;
		$output['page:framepage'] = $framepage;
		
		$output['page:keywords'] = $keywordsval;
		$output['page:description'] = $descriptionval;
		// display with cms layer
		$this->pages->view('artworks_special', $output, TRUE);	
	}
	
	function auction()
	{
		// get partials
                
		$output = $this->partials;
						
		// get latest artworks
		$artworks = $this->artworks->get_artworks_auction(1000);
		$output['artworks:artworks'] = $this->_populate_artworks_auction($artworks);
		// send artworks to page
		$data['artworks'] = $artworks;
		
		//auction page (heading and text)
		$this->db->select('versionID');
		$this->db->where('pageID', 119);
		$pagequery = $this->db->get('ha_pages');
		if ($pagequery->num_rows())
		{
			$pagequeryresult = $pagequery->result_array();			
			foreach($pagequeryresult as $pagequeryvar)
			{	
				
				$versionID = $pagequeryvar['versionID'];
				$this->db->select('body');
				$this->db->where('versionID', $versionID);
				$bodyquery = $this->db->get('ha_page_blocks');
				if ($bodyquery->num_rows())
				{
					$bodyqueryresult = $bodyquery->result_array();			
					foreach($bodyqueryresult as $bodyqueryvar)
					{	
						$body_val = $bodyqueryvar['body'];
						$bodytext = $this->template->parse_body($body_val);
					}
				}
			}
		}
		
		
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Auction';
		$output['page:heading'] = $pagehead;
		$output['page:textcontent'] = $bodytext;
		
		// display with cms layer
		$this->pages->view('artworks_auction', $output, TRUE);	
	}
	
	function search()
	{
		
		// get partials
                
		$output = $this->partials;
						
		// get latest artworks
		$artworks = $this->artworks->get_artworks_search(1000);
		$output['artworks:artworks'] = $this->_populate_artworks_search($artworks);
		// send artworks to page
		$data['artworks'] = $artworks;
		
		
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks Search';
		$output['page:heading'] = "Search";
		$output['page:textcontent'] = "Search";
		
		// display with cms layer
		$this->pages->view('artworks_search', $output, TRUE);	
	}
	
	function featured()
	{
		// get partials
		$output = $this->partials;
						
		// get latest artworks
		$artworks = $this->artworks->get_featured_artworks();
		$output['artworks:featured'] = $this->_populate_artworks($artworks);
		// send artworks to page
		$data['artworks'] = $artworks;
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks';
		$output['page:heading'] = 'Featured Artworks';
		
		// display with cms layer
		$this->pages->view('artworks_featured', $output, TRUE);	
	}
       
	function viewartwork($artworkID = '')
	{
		// get partials
		$output = $this->partials;
				
		// get artwork
		if ($artwork = $this->artworks->get_artwork($artworkID))
		{				
			// populate template
			$output['artwork:title'] = $artwork['artworkTitle'];
                        //$output['artwork:name'] = $artwork['artistname'];
			$output['artwork:link'] = site_url('artworks/viewartwork/'.$artwork['artworkID']);
			$output['artwork:location'] = $artwork['location'];
			$output['artwork:date'] = date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artwork['artworkDate'])).
						(($artwork['time']) ? ' ('.$artwork['time'].')' : '').
						(($artwork['artworkEnd'] > 0) ? ' - '.date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artwork['artworkEnd'])) : '');
			$output['artwork:body'] = $this->template->parse_body($artwork['description']);
			//get gallery
			$output['artwork:gallery'] = "";
			//print_r($artwork);
			if($artwork['gallery'] == 1){
				//echo 'yes';
				//$this->load->model('news_model', 'news');
				$gal = $this->artworks->getGallery($artwork['artworkID'], $this->type);
				//print_r($gal);
				$str = "";
				if($gal){
					$output['artwork:gallery'] = $this->artworks->formatGallery($gal);
				}
			}
            $output['artworks:artworks'] = $this->_populate_artworks($artworks);
			$output['artwork:excerpt'] = $this->template->parse_body($artwork['excerpt']);
			$output['artwork:author'] = $this->artworks->lookup_user($artwork['userID'], TRUE);
			$output['artwork:author-id'] = $artwork['userID'];
			$output['artwork:edit'] = ($artwork['userID'] == $this->session->userdata('userID')) ? ' | '.anchor('/ce/artworks/edit_artwork/'.$artworkID, 'Edit Artwork') : '';
			// set title
			$output['page:title'] = $this->site->config['siteName'].$artwork['meta_title'];
			$output['keywords'] = $artwork['tags'];
			
			if ($this->input->get('url_string')) {
                        $output['page:url_text'] = $artwork['url_string'];
                       }
			
			
			// output other stuff
			$data['artwork'] = $artwork;
			$data['tags'] = explode(' ', $artwork['tags']);	
						
			// display with cms layer
			$this->pages->view('artworks_single', $output, TRUE);
		}
		else
		{
			show_404();
		}
	}
	function tag($tag = '')
	{		
		// get partials
		$output = $this->partials;
		// get tags
		$artworks = $this->artworks->get_artworks_by_tag($tag);
		$output['artworks:artworks'] = $this->_populate_artworks($artworks);
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks';
		$output['page:heading'] = 'Artworks on "'.$tag.'"';		
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
						
		// display with cms layer
		$this->pages->view('artworks', $output, TRUE);
	}
	function month()
	{
		// get partials
		$output = $this->partials;
		// get artwork based on uri
		$year = $this->uri->segment(2);
		$month = $this->uri->segment(3);		
		// get tags
		$artworks = $this->artworks->get_artworks_by_date($year, $month);
		$output['artworks:artworks'] = $this->_populate_artworks($artworks);
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks - '.date('F Y', mktime(0,0,0,$month,1,$year));
		$output['page:heading'] = date('F Y', mktime(0,0,0,$month,1,$year));			
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';		
		// display with cms layer
		$this->pages->view('artworks', $output, TRUE);	
	}
	
	function year()
	{
		// get partials
		$output = $this->partials;
		// get artwork based on uri
		$year = $this->uri->segment(2);	
		// get tags
		$artworks = $this->artworks->get_artworks_by_date($year);
		$output['artworks:artworks'] = $this->_populate_artworks($artworks);
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks - '.date('Y', mktime(0,0,0,1,1,$year));
		$output['page:heading'] = date('Y', mktime(0,0,0,1,1,$year));			
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';		
		// display with cms layer
		$this->pages->view('artworks', $output, TRUE);	
	}
	function day()
	{
		// get partials
		$output = $this->partials;
		// get artwork based on uri
		$year = $this->uri->segment(2);
		$month = $this->uri->segment(3);
		$day = $this->uri->segment(4);	
		// get tags
		$artworks = $this->artworks->get_artworks_by_date($year, $month, $day);
		$output['artworks:artworks'] = $this->_populate_artworks($artworks);
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworks - '.date('D jS F Y', mktime(0,0,0,$month,$day,$year));
		$output['page:heading'] = date('D jS F Y', mktime(0,0,0,$month,$day,$year));
		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';	
				
		// display with cms layer
		$this->pages->view('artworks', $output, TRUE);	
	}
	function ac_search()
	{
		$tags = strtolower($_POST["q"]);
        if (!$tags)
        {
        	return FALSE;
        }
		if ($objectIDs = $this->tags->search('artworks', $tags))
		{		
			// form dropdown and myql get countries
			if ($searches = $this->artworks->search_artworks($objectIDs))
			{
				// go foreach
				foreach($searches as $search)
				{
					$items[$search['tags']] = array('id' => $search['artworkID'], 'name' => $search['artworkTitle']);
				}
				foreach ($items as $key=>$value)
				{
					$id = $value['id'];
					$name = $value['name'];
					/* If you want to force the results to the query
					if (strpos(strtolower($key), $tags) !== false)
					{
						echo "$key|$id|$name\n";
					}*/
					$this->output->set_output("$key|$id|$name\n");
				}
			}
		}
	}
	
	function feed()
	{
		$tagdata = array();
		$this->load->helper('xml');
		
		$data['encoding'] = 'utf-8';
		$data['feed_name'] = $this->site->config['siteName'] . ' | Artworks RSS Feed';
		$data['feed_url'] = site_url('/artworks');
		$data['page_description'] = 'Artworks RSS Feed for '.$this->site->config['siteName'].'.';
		$data['page_language'] = 'en';
		$data['creator_email'] = $this->site->config['siteEmail'];
		$data['artworks'] = $this->artworks->get_artworks(10);
		
        $this->output->set_header('Content-Type: application/rss+xml');
		$this->load->view('rss', $data);
	}
    function _populate_artworks($artworks = '')
    {
    	if ($artworks && is_array($artworks))
    	{
			$x = 0;
			foreach($artworks as $artwork)
			{
				// populate template array
				$data[$x] = array(
					'artwork:link' => site_url('artworks/viewartwork/'.$artwork['artworkID'].$artwork['url_string']),
					'artwork:title' => $artwork['artworkTitle'],
					'artwork:location' => $artwork['location'],
					'artwork:date' => date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artwork['artworkDate'])). 
						(($artwork['time']) ? ' ('.$artwork['time'].')' : '').
						(($artwork['artworkEnd'] > 0) ? ' - '.date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artwork['artworkEnd'])) : ''),
					'artwork:day' => date('d', strtotime($artwork['artworkDate'])),
					'artwork:month' => date('M', strtotime($artwork['artworkDate'])),
					'artwork:year' => date('y', strtotime($artwork['artworkDate'])),																
					'artwork:body' => $this->template->parse_body($artwork['description'], TRUE, site_url('artworks/viewartwork/'.$artwork['artworkID'])),
					'artwork:excerpt' => $this->template->parse_body($artwork['excerpt'], TRUE, site_url('artworks/viewartwork/'.$artwork['artworkID'])),
					'artwork:author' => $this->artworks->lookup_user($artwork['userID'], TRUE),
					'artwork:author-id' => $artwork['userID']
				);
	
				// get tags
				if ($artwork['tags'])
				{
					$tags = explode(' ', $artwork['tags']);
					$i = 0;
					foreach ($tags as $tag)
					{
						$data[$x]['artwork:tags'][$i]['tag:link'] = site_url('blog/tag/'.$tag);
						$data[$x]['artwork:tags'][$i]['tag'] = $tag;
						
						$i++;
					}
				}
	
				$x++;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
    }
	
    function _populate_artworks_special($artworks = '')
    {
    	if ($artworks && is_array($artworks))
    	{
			$x = 0;
			$y = 1;
			foreach($artworks as $artwork)
			{
				//select artist
				$this->db->select('first_name, last_name, published');
				$this->db->where('postID',  $artwork['artist_link']);
				$artistquery = $this->db->get('ha_artists_posts');
				if ($artistquery->num_rows())
				{
					$artistqueryresult = $artistquery->result_array();			
					foreach($artistqueryresult as $artistqueryvar)
					{	
						$published = $artistqueryvar['published'];
						$firstname = $artistqueryvar['first_name'];
						$lastname = $artistqueryvar['last_name'];
						
						if ($firstname) 
						{
							$firstname = ", ".$firstname	."";
						}
						else 
						{
							$firstname = "";
						}
						
						$artistname = "".$lastname."".$firstname."";
						
						$artistname = "<div class='center' style='float:left;width:99%; color:#F5D068;padding:5px; border-top: 1px solid #F5D068; margin-top: 15px; padding-top: 15px;'><div style='font-size:14px;font-weight:bold'><strong>".$artistname."</strong></div></div><br/>";
						
						
					}
					
				}
				
				//code to prevent artist name form repeating
				if ($this->session->userdata('the_artist_id_specials') ==  $artwork['artist_link'])
				{
					$artistname = '';	
				}
 
				$artist_id_specials = array(
					'the_artist_id_specials' => $artwork['artist_link']
				);
				
				$this->session->set_userdata($artist_id_specials);
				
				$additional_img = '';
				
				//select additional artwork images
				$this->db->select('image, image_alt_txt');
				$this->db->where('artwork_id',  $artwork['artworkID']);
				$additionalimgquery = $this->db->get('ha_artworkimages');
				if ($additionalimgquery->num_rows())
				{
					$additional_img .= '<div style="width:550px;float:left;padding-bottom:20px">';
					$additional_img .= '<ul id="mycarousel'.$y.'" class="jcarousel-skin-tango">';
						
					$additionalimgqueryresult = $additionalimgquery->result_array();			
					foreach($additionalimgqueryresult as $additionalimgqueryvar)
					{	
						$image = $additionalimgqueryvar['image'];
						$image_alt_txt = $additionalimgqueryvar['image_alt_txt'];
						$image_alt_txt = htmlentities($image_alt_txt);
						$additional_img .= '<li><a class="fancybox" title="'.$image_alt_txt.'" data-fancybox-group="gallery'.$y.'" href"'.site_url().'artworkimages/image_single/'.$image.'" <!--href="'.site_url().'/static/uploads/artists/artworkimages/'.$image.'"--> ><img src="'.site_url().'/thumb.php?src=/static/uploads/artists/artworkimages/'.$image.'&w=75&h=75" title="'.$image_alt_txt.'" alt="'.$image_alt_txt.'" /></a></li>';
					}
						
					$additional_img .= '</ul>';
					$additional_img .= '</div>';
				}
				
				else 
				{
					$additional_img  = '</ul>';	
				}
				
				//select medium
				$this->db->select('mediumTitle');
				$this->db->where('mediumID',  $artwork['medium_link']);
				$mediumquery = $this->db->get('ha_mediums');
				if ($mediumquery->num_rows())
				{
					$mediumqueryresult = $mediumquery->result_array();			
					foreach($mediumqueryresult as $mediumqueryvar)
					{	
					$mediumTitle = $mediumqueryvar['mediumTitle'];
					}
					
				}
				
				else 
				{
					$mediumTitle  = '';	
				}
				
				if ($artwork['medium']) 
				{
					$mediuminfo = "<span class='artwork_label'><strong>Medium:</strong></span> <span class='artwork_info'>". $artwork['medium']."</span><br/><br/>";	
					
				}
				else 
				{
					$mediuminfo = '';
				}
				
				//series
				if ($artwork['series']) 
				{
					$seriesinfo = "<span class='artwork_label'><strong>Suite:</strong></span> <span class='artwork_info'>".$artwork['series']."</span><br/><br/>";	
				}
				
				else 
				{
				    $seriesinfo = '';	
				}
				
				//edition size
				if ($artwork['edition_size']) 
				{
					$editionsizeinfo = "<span class='artwork_label'><strong>Edition Size:</strong></span> <span class='artwork_info'>".$artwork['edition_size']."</span><br/><br/>";	
				}
				
				else 
				{
				    $editionsizeinfo = '';	
				}
				
				//collection
				if ($artwork['collection']) 
				{
					$collectioninfo = "<span class='artwork_label'><strong>Collection:</strong></span> <span class='artwork_info'>".$artwork['collection']."</span><br/><br/>";	
				}
				
				else 
				{
				    $collectioninfo = '';	
				}
				
				//unframed size
				if ($artwork['unframed_size']) 
				{
					$unframedsizeinfo = "<span class='artwork_label'><strong>Unframed Size:</strong></span> <span class='artwork_info'>".$artwork['unframed_size']."</span><br/><br/>";	
				}
				
				else 
				{
				    $unframedsizeinfo = '';	
				}
				
				//framed size
				if ($artwork['framed_size']) 
				{
					$framedsizeinfo = "<span class='artwork_label'><strong>Framed Size:</strong></span> <span class='artwork_info'>".$artwork['framed_size']."</span><br/><br/>";	
				}
				
				else 
				{
				    $framedsizeinfo = '';	
				}
				
				//retail price
				if ($artwork['retail_price'] != 0) 
				{
					if ($artwork['retail_price_comment']) 
					{
						$retail_comment = "- ".$artwork['retail_price_comment']."";	
					}
					else 
					{
						$retail_comment = "";	
					}
					$retailpriceinfo = "<span class='artwork_label'><strong>Retail Price:</strong></span> <span class='artwork_info'>".currency_symbol()."".number_format($artwork['retail_price'],2)." ".$retail_comment."</span><br/><br/>";	
				}
				
				else 
				{
				    $retailpriceinfo = '';	
				}
				
				//asking price
				if ($artwork['include_on_MAO_page'] == 0) 
				{
					if ($artwork['asking_price'] != 0) 
					{
						
						if ($artwork['asking_price_comment']) 
						{
							$asking_comment = "- ".$artwork['asking_price_comment']."";	
						}
						else 
						{
							$asking_comment = "";	
						}
						
						$askingpriceinfo = "<span class='artwork_label'><strong>Asking Price:</strong></span> <span class='artwork_info'>".currency_symbol()."".number_format($artwork['asking_price'],2)." ".$asking_comment."</span><br/><br/>";	
					}
					
					else 
					{
						$askingpriceinfo = '';	
					}
				}
				
				if ($artwork['include_on_MAO_page'] == 1) 
				{
					if ($artwork['asking_price_comment']) 
					{
						$asking_comment = "- ".$artwork['asking_price_comment']."";	
					}
					else 
					{
						$asking_comment = "";	
					}
					
					$cur_symbol = '';
					$ask_price = '';
					
					if ($artwork['include_price']) 
					{
						if ($artwork['asking_price'] != 0) 
						{
							$cur_symbol = currency_symbol();
							$ask_price = "".number_format($artwork['asking_price'],2)." - ";
						}
						
					}
					
					if ($artwork['MAO_content']) 
					{
						$askingpriceinfo = "<span class='artwork_label'><strong>Asking Price:</strong></span> <span class='artwork_info'>".$cur_symbol."".$ask_price."".$artwork['MAO_content']." ".$asking_comment."</span><br/><br/>";	
					}
					
					else 
					{
						$askingpriceinfo = '<span class="artwork_label"><strong>Asking Price:</strong></span>';	
					}
				}
				
				//comment
				if ($artwork['description']) 
				{
					$commentinfo = "<!--strong>Comment:</strong--><span class='artwork_info'>".$this->template->parse_body($artwork['description'])."</span>";	
				}
				
				else 
				{
				    $commentinfo = '';	
				}
				
				
				//m2016 add url_title
				//unframed image
				if ($artwork['unframed_image']) 
				{
					$unframedimageinfo = "<a href='".site_url()."artworks/view_artwork/".$artwork['artworkID']."/".strtolower(url_title($artwork['url_string']))."'><img src='".site_url()."/thumb.php?src=/static/uploads/artists/artworks/unframed_images/".$artwork['unframed_image']."&w=180'  title='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."'  alt='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."'></a><br/><br/>";
				}
				//m2016 add url_title
				
				else 
				{
				    $unframedimageinfo = '';	
				}
				
				//m2016 add url_title
				//framed image
				if (!$artwork['unframed_image'] && $artwork['framed_image']) {
					$framedimageinfo = "<a href='".site_url()."artworks/view_artwork/".$artwork['artworkID']."/".strtolower(url_title($artwork['url_string']))."'><img src='".site_url()."/thumb.php?src=/static/uploads/artists/artworks/framed_images/".$artwork['framed_image']."&w=180'></a><br/><br/>";	
				}
				//m2016 add url_title
				
				else 
				{
				    $framedimageinfo = '';	
				}
				
				if ($published == 1) 
				{
					$artworks_element = "<div style=''>";
					$artworks_element .= "<div style=''>";

					$artworks_element .= $artistname;
					$artworks_element .= "<div class='artist_box' style='height:500px'>
											 <div class='image_item' align='center'>";
						$artworks_element .= $unframedimageinfo;
						$artworks_element .= $framedimageinfo;
						$artworks_element .= "</div>
											  ";
						
						$artworks_element .= "<div class='mobile-arwork-desc'  id='mobile-arwork-desc' style=''>";
							$artworks_element .= "<span class='artwork_label'>Title:</span> <span class='artwork_info'>". $artwork['artworkTitle']."</span><br/><br/>";
							$artworks_element .= $mediuminfo;
							$artworks_element .= $unframedsizeinfo;
							$artworks_element .= $framedsizeinfo;
							$artworks_element .= $retailpriceinfo;
							$artworks_element .= $askingpriceinfo;
							
							//m2016 add url_title
							 $artworks_element .= "<div ><a href='".site_url()."artworks/view_artwork/".$artwork['artworkID']."/".strtolower(url_title($artwork['url_string']))."'><img src='".site_url()."static/images/buttons/btn-more-info.gif' class='more'/></a></div><br/>";
							//m2016 add url_title
							
							
							$artworks_element .= "<br/>";
						$artworks_element .= "</div>
										  </div>
										 ";
					$artworks_element .= "</div>";
					$artworks_element .= "</div>";
				}
				
				// populate template array
				$data[$x] = array(
					'artwork:elements' => $artworks_element
				);
	
	
				$x++;
				$y++;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
    }
	
    function _populate_artworks_auction($artworks = '')
    {
    	if ($artworks && is_array($artworks))
    	{
			$x = 0;
			$y = 1;
			foreach($artworks as $artwork)
			{
				//select artist
				$this->db->select('first_name, last_name, published');
				$this->db->where('postID',  $artwork['artist_link']);
				$artistquery = $this->db->get('ha_artists_posts');
				if ($artistquery->num_rows())
				{
					$artistqueryresult = $artistquery->result_array();			
					foreach($artistqueryresult as $artistqueryvar)
					{	
						$published = $artistqueryvar['published'];
						$firstname = $artistqueryvar['first_name'];
						$lastname = $artistqueryvar['last_name'];
						
						if ($firstname) 
						{
							$firstname = ", ".$firstname	."";
						}
						else 
						{
							$firstname = "";
						}
						
						$artistname = "".$lastname."".$firstname."";
						$artistname = "<div style='float:left;width:910px;padding:5px' class='center title_search'><div style='font-size:14px;font-weight:bold'><strong style='color:#F5D068'>".$artistname."</strong></div></div><br/>";
						
					}
				}
				
				//code to prevent artist name form repeating
				if ($this->session->userdata('the_artist_id_auction') ==  $artwork['artist_link'])
				{
					$artistname = '';	
				}
 
				$artist_id_auction = array(
					'the_artist_id_auction' => $artwork['artist_link']
				);
				
				$this->session->set_userdata($artist_id_auction);
				
				//select additional artwork images
				$additional_img = '';
				$this->db->select('image, image_alt_txt');
				$this->db->where('artwork_id',  $artwork['artworkID']);
				$additionalimgquery = $this->db->get('ha_artworkimages');
				if ($additionalimgquery->num_rows())
				{
					$additional_img .= '<div style="width:550px;float:left;padding-bottom:20px">';
					$additional_img .= '<ul id="mycarousel'.$y.'" class="jcarousel-skin-tango">';
						
					$additionalimgqueryresult = $additionalimgquery->result_array();			
					foreach($additionalimgqueryresult as $additionalimgqueryvar)
					{	
						$image = $additionalimgqueryvar['image'];
						$image_alt_txt = $additionalimgqueryvar['image_alt_txt'];
						$image_alt_txt = htmlentities($image_alt_txt);
						$additional_img .= '<li><a class="fancybox" title="'.$image_alt_txt.'" data-fancybox-group="gallery'.$y.'" href"'.site_url().'artworkimages/image_single/'.$image.'" <!--href="'.site_url().'/static/uploads/artists/artworkimages/'.$image.'"--> ><img src="'.site_url().'/thumb.php?src=/static/uploads/artists/artworkimages/'.$image.'&w=75&h=75" title="'.$image_alt_txt.'" alt="'.$image_alt_txt.'" /></a></li>';
					}
						
					$additional_img .= '</ul>';
					$additional_img .= '</div>';
				}
				
				else 
				{
					$additional_img  = '</ul>';	
				}
				
				//select medium
				$this->db->select('mediumTitle');
				$this->db->where('mediumID',  $artwork['medium_link']);
				$mediumquery = $this->db->get('ha_mediums');
				if ($mediumquery->num_rows())
				{
					$mediumqueryresult = $mediumquery->result_array();			
					foreach($mediumqueryresult as $mediumqueryvar)
					{	
						$mediumTitle = $mediumqueryvar['mediumTitle'];
					}
				}
				
				else 
				{
					$mediumTitle  = '';	
				}
				
				if ($artwork['medium']) 
				{
					$mediuminfo = "<div class='artworklabel'>Medium:</div><div class='artwork-right'>". $artwork['medium']."</div><br/>";	
					
				}
				else 
				{
					$mediuminfo = '';
				}
				
				//series
				if ($artwork['series']) 
				{
					$seriesinfo = "<div class='artworklabel'>Suite:</div><div class='artwork-right'>".$artwork['series']."</div><br/>";	
				}
				
				else 
				{
				    $seriesinfo = '';	
				}
				
				//edition size
				if ($artwork['edition_size']) 
				{
					$editionsizeinfo = "<div class='artworklabel'>Edition Size:</div><div class='artwork-right'>".$artwork['edition_size']."</div><br/>";	
				}
				
				else 
				{
				    $editionsizeinfo = '';	
				}
				
				//collection
				if ($artwork['collection']) 
				{
					$collectioninfo = "<div class='artworklabel'>Collection:</div><div class='artwork-right'>".$artwork['collection']."</div><br/>";
				}
				
				else 
				{
				    $collectioninfo = '';	
				}
				
				//unframed size
				if ($artwork['unframed_size']) 
				{
					$unframedsizeinfo = "<div class='artworklabel'>Unframed Size:</div><div class='artwork-right'>".$artwork['unframed_size']."</div><br/>";
				}
				
				else 
				{
				    $unframedsizeinfo = '';	
				}
				
				//framed size
				if ($artwork['framed_size']) 
				{
					$framedsizeinfo = "<div class='artworklabel'>Framed Size:</div><div class='artwork-right'>".$artwork['framed_size']."</div><br/>";	
				}
				
				else 
				{
				    $framedsizeinfo = '';	
				}
				
				//retail price
				if ($artwork['retail_price'] != 0) 
				{
					
					if ($artwork['retail_price_comment']) 
					{
						$retail_comment = "- ".$artwork['retail_price_comment']."";	
					}
					else 
					{
						$retail_comment = "";	
					}
					
					$retailpriceinfo = "<div class='artworklabel'>Retail Price:</div> <div class='artwork-right'>".currency_symbol()."".number_format($artwork['retail_price'],2)." ".$retail_comment."</div><br/>";	
				}
				
				else 
				{
				    $retailpriceinfo = '';	
				}
				
				//asking price
				if ($artwork['include_on_MAO_page'] == 0) 
				{
					if ($artwork['asking_price'] != 0) 
					{
						
						if ($artwork['asking_price_comment']) 
						{
							$asking_comment = "- ".$artwork['asking_price_comment']."";	
						}
						else 
						{
							$asking_comment = "";	
						}
						
						$askingpriceinfo = "<div class='artworklabel'>Asking Price:</div> <div class='artwork-right'>".currency_symbol()."".number_format($artwork['asking_price'],2)." ".$asking_comment."</div><br/>";
					}
					
					else 
					{
						$askingpriceinfo = '';	
					}
				}
				
				if ($artwork['include_on_MAO_page'] == 1) 
				{
					
					if ($artwork['asking_price_comment']) 
					{
						$asking_comment = "- ".$artwork['asking_price_comment']."";	
					}
					else 
					{
						$asking_comment = "";	
					}
					
					$cur_symbol = '';
					$ask_price = '';
					
					if ($artwork['include_price']) 
					{
						if ($artwork['asking_price'] != 0) 
						{
							$cur_symbol = currency_symbol();
							$ask_price = "".number_format($artwork['asking_price'],2)." - ";
						}
						
					}
					
					if ($artwork['MAO_content']) 
					{
						$askingpriceinfo = "<div class='artworklabel'>Asking Price ".$cur_symbol." :</div><div class='artwork-right'>".$ask_price."".$artwork['MAO_content']." ".$asking_comment."</div><br/>";	
					}
					
					else 
					{
						$askingpriceinfo = '<div class="artworklabel">Asking Price:</div>';	
					}
				}
				
				//comment
				if ($artwork['description']) 
				{
					$commentinfo = "<!--<strong>Comment:</strong>--><div class='artwork-right' style='clear:both'>".$this->template->parse_body($artwork['description'])."</div>";	
				}
				
				else 
				{
				    $commentinfo = '';	
				}
				
				//unframed image
				if ($artwork['unframed_image']) 
				{
					$unframedimageinfo = "<a class='fancybox' title='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."' data-fancybox-group='gallerya".$y."' href='".site_url()."/static/uploads/artists/artworks/unframed_images/".$artwork['unframed_image']."'><img src='".site_url()."/thumb.php?src=/static/uploads/artists/artworks/unframed_images/".$artwork['unframed_image']."&w=120' alt='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."' title='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."'></a><br/><br/>";
				
;	
				}
				else 
				{
				    $unframedimageinfo = '';	
				}
				
				//framed image
				if ($artwork['framed_image']) 
				{
					$framedimageinfo = "<a class='fancybox' title='".htmlspecialchars($artwork['framed_alt_txt'], ENT_QUOTES)."' data-fancybox-group='galleryb".$y."' href='".site_url()."/static/uploads/artists/artworks/framed_images/".$artwork['framed_image']."'><img src='".site_url()."/thumb.php?src=/static/uploads/artists/artworks/framed_images/".$artwork['framed_image']."&w=120' alt='".htmlspecialchars($artwork['framed_alt_txt'], ENT_QUOTES)."'></a><br/><br/>";
				
;	
				}
				else 
				{
				    $framedimageinfo = '';	
				}
								
				if ($published == 1) 
				{
					$artworks_element = "<div style='' class='artwork_outer_inner'>";
					$artworks_element .= "<div style='width:700px;float:left' class='artwork_boxes'>";
					$artworks_element .= $artistname;
					$artworks_element .= "<div style='width:140px;float:left;padding-top:10px' class='artwork-img'>";
					$artworks_element .= $unframedimageinfo;
					$artworks_element .= $framedimageinfo;
					$artworks_element .= "</div>";
					$artworks_element .= "<div style='float:right;padding-top:10px' class='mobile-arwork-desc'>";
					$artworks_element .= "<div class='artworklabel'>Title:</div><div class='artwork-right'>". $artwork['artworkTitle']."</div><br/>";
					$artworks_element .= $mediuminfo;
					$artworks_element .= $seriesinfo;
					$artworks_element .= $collectioninfo;
					$artworks_element .= $unframedsizeinfo;
					$artworks_element .= $framedsizeinfo;
					$artworks_element .= $editionsizeinfo;
					$artworks_element .= $retailpriceinfo;
					$artworks_element .= $askingpriceinfo;
					$artworks_element .= $commentinfo;
					$artworks_element .= "<br/>";
					$artworks_element .= "</div>";
					$artworks_element .= "</div>";
					$artworks_element .= $additional_img ;
					$artworks_element .= "</div>";
				}
				
				// populate template array
				$data[$x] = array(
					'artwork:elements' => $artworks_element
				);
	
	
				$x++;
				$y++;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
    }
	
	
    function _populate_artworks_search($artworks = '',$uri = '')
    {
		
		$this->session->unset_userdata('the_artist_id_search');
    	if ($artworks && is_array($artworks))
    	{
			$x = 0;
			$y = 1;
			foreach($artworks as $artwork)
			{
				//select artist
				$this->db->select('first_name, last_name, published');
				$this->db->where('postID',  $artwork['artist_link']);
				$artistquery = $this->db->get('ha_artists_posts');
				if ($artistquery->num_rows())
				{
					$artistqueryresult = $artistquery->result_array();			
					foreach($artistqueryresult as $artistqueryvar)
					{	
						$published = $artistqueryvar['published'];
						$firstname = $artistqueryvar['first_name'];
						$lastname = $artistqueryvar['last_name'];
						if ($firstname) 
						{
							$firstname = ", ".$firstname	."";
						}
						else 
						{
							$firstname = "";
						}
						
						$artistname = "".$lastname."".$firstname."";
						$artistname = "<div style='float:left;width:910px;padding:5px' class='center title_search'><div style='font-size:14px;font-weight:bold'><strong style='color:#F5D068'>".$artistname."</strong></div></div><br/>";
					}
				}
				
				//code to prevent artist name form repeating
				if ($this->session->userdata('the_artist_id_search') ==  $artwork['artist_link'])
				{
					$artistname = '';	
				}
 
				$artist_id_search = array(
					'the_artist_id_search' => $artwork['artist_link']
				);
				
				$this->session->set_userdata($artist_id_search);
				
				//select additional artwork images
				$additional_img = '';
				$this->db->select('image, image_alt_txt');
				$this->db->where('artwork_id',  $artwork['artworkID']);
				$additionalimgquery = $this->db->get('ha_artworkimages');
				if ($additionalimgquery->num_rows())
				{
					$additional_img .= '<div style="width:550px;float:left;padding-bottom:20px">';
					$additional_img .= '<ul id="mycarousel'.$y.'" class="jcarousel-skin-tango">';
						
					$additionalimgqueryresult = $additionalimgquery->result_array();			
					foreach($additionalimgqueryresult as $additionalimgqueryvar)
					{	
						$image = $additionalimgqueryvar['image'];
						$image_alt_txt = $additionalimgqueryvar['image_alt_txt'];
						$image_alt_txt = htmlentities($image_alt_txt);
						$additional_img .= '<li><a class="fancybox" title="'.$image_alt_txt.'" data-fancybox-group="gallery'.$y.'"  href="'.site_url().'/static/uploads/artists/artworkimages/'.$image.'"><img src="'.site_url().'/thumb.php?src=/static/uploads/artists/artworkimages/'.$image.'&w=75&h=75" title="'.$image_alt_txt.'" alt="'.$image_alt_txt.'" /></a></li>';
					}
						
					$additional_img .= '</ul>';
					$additional_img .= '</div>';
				}
				
				else 
				{
					$additional_img  = '</ul>';	
				}
				
				//select medium
				$this->db->select('mediumTitle');
				$this->db->where('mediumID',  $artwork['medium_link']);
				$mediumquery = $this->db->get('ha_mediums');
				if ($mediumquery->num_rows())
				{
					$mediumqueryresult = $mediumquery->result_array();			
					foreach($mediumqueryresult as $mediumqueryvar)
					{	
						$mediumTitle = $mediumqueryvar['mediumTitle'];
					}
				}
				
				else 
				{
					$mediumTitle  = '';	
				}
				
				if ($artwork['medium']) 
				{
					//$mediuminfo = "<div class='artworklabel'>Medium:</div><div class='artwork-right'>". $mediumTitle."</div><br/>";
					$mediuminfo = "<div class='artworklabel'>Medium:</div><div class='artwork-right'>". $artwork['medium']."</div><br/>";	
				}
				else 
				{
					$mediuminfo = '';
				}
				
				
				//series
				if ($artwork['series']) 
				{
					$seriesinfo = "<div class='artworklabel'>Suite:</div><div class='artwork-right'>".$artwork['series']."</div><br/>";
				}
				
				else 
				{
				    $seriesinfo = '';	
				}
				
				//edition size
				if ($artwork['edition_size']) 
				{
					$editionsizeinfo = "<div class='artworklabel'>Edition Size:</div><div class='artwork-right'>".$artwork['edition_size']."</div><br/>";	
				}
				
				else 
				{
				    $editionsizeinfo = '';	
				}
				
				//collection
				if ($artwork['collection']) 
				{
					$collectioninfo = "<div class='artworklabel'>Collection:</div><div class='artwork-right'>".$artwork['collection']."</div><br/>";	
				}
				
				else 
				{
				    $collectioninfo = '';	
				}
				
				//unframed size
				if ($artwork['unframed_size']) 
				{
					$unframedsizeinfo = "<div class='artworklabel'>Unframed Size:</div><div class='artwork-right'>".$artwork['unframed_size']."</div><br/>";	
				}
				
				else 
				{
				    $unframedsizeinfo = '';	
				}
				
				//framed size
				if ($artwork['framed_size']) 
				{
					$framedsizeinfo = "<div class='artworklabel'>Framed Size:</div><div class='artwork-right'>".$artwork['framed_size']."</div><br/>";	
				}
				
				else 
				{
				    $framedsizeinfo = '';	
				}
				
				//retail price
				if ($artwork['retail_price'] != 0) 
				{
					
					if ($artwork['retail_price_comment']) 
					{
						$retail_comment = "- ".$artwork['retail_price_comment']."";	
					}
					else 
					{
						$retail_comment = "";	
					}
					$retailpriceinfo = "<div class='artworklabel'>Retail Price:</div> <div class='artwork-right'>".currency_symbol()."".number_format($artwork['retail_price'],2)." ".$retail_comment."</div><br/>";	
				}
				
				else 
				{
				    $retailpriceinfo = '';	
				}
				
				//asking price
				if ($artwork['include_on_MAO_page'] == 0) 
				{
					if ($artwork['asking_price'] != 0) 
					{
						
						if ($artwork['asking_price_comment']) 
						{
							$asking_comment = "- ".$artwork['asking_price_comment']."";	
						}
						else 
						{
							$asking_comment = "";	
						}
						
						$askingpriceinfo = "<div class='artworklabel'>Asking Price ".currency_symbol()." :</div><div class='artwork-right'>".number_format($artwork['asking_price'],2)." ".$asking_comment."</div><br/>";	
					}
					
					else 
					{
						$askingpriceinfo = '';	
					}
				}
				
				if ($artwork['include_on_MAO_page'] == 1) 
				{
					
					if ($artwork['asking_price_comment']) 
					{
						$asking_comment = "- ".$artwork['asking_price_comment']."";	
					}
					else 
					{
						$asking_comment = "";	
					}
					
					$cur_symbol = '';
					$ask_price = '';
					
					if ($artwork['include_price']) 
					{
						
						if ($artwork['asking_price'] != 0) 
						{
							$cur_symbol = currency_symbol();
							$ask_price = "".number_format($artwork['asking_price'],2)." - ";
						}
						
					}
					
					if ($artwork['MAO_content']) 
					{
						$askingpriceinfo = "<div class='artworklabel'>Asking Price ".$cur_symbol." :</div><div class='artwork-right'>".$ask_price."".$artwork['MAO_content']." ".$asking_comment."</div><br/>";	
					}
					
					else 
					{
						$askingpriceinfo = '<div class="artworklabel">Asking Price:</div>';	
					}
				}
				
				//comment
				if ($artwork['description']) 
				{
					$commentinfo = "<!--<strong>Comment:--><div class='artwork-right' style='clear:both'>".$this->template->parse_body($artwork['description'])."</div>";	
				}
				
				else 
				{
				    $commentinfo = '';	
				}
				
				//unframed image
				if ($artwork['unframed_image']) 
				{
					$unframedimageinfo = "<a class='fancybox' title='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."' data-fancybox-group='gallerya".$y."' href='".site_url()."/static/uploads/artists/artworks/unframed_images/".$artwork['unframed_image']."'><img src='".site_url()."/thumb.php?src=/static/uploads/artists/artworks/unframed_images/".$artwork['unframed_image']."&w=120' alt='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."' title='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."'></a><br/><br/>";	
				}
				else {
				    $unframedimageinfo = '';	
				}
				
				//framed image
				if ($artwork['framed_image']) 
				{
					$framedimageinfo = "<a class='fancybox' title='".htmlspecialchars($artwork['framed_alt_txt'], ENT_QUOTES)."' data-fancybox-group='galleryb".$y."' href='".site_url()."/static/uploads/artists/artworks/framed_images/".$artwork['framed_image']."'><img src='".site_url()."/thumb.php?src=/static/uploads/artists/artworks/framed_images/".$artwork['framed_image']."&w=120' alt='".htmlspecialchars($artwork['framed_alt_txt'], ENT_QUOTES)."'></a><br/><br/>";
				
;	
				}
				else {
				    $framedimageinfo = '';	
				}
								
				if ($published == 1) 
				{
					$artworks_element = "<div style='' class='artwork_outer_inner'>";
					$artworks_element .= "<div style='width:700px;float:left' class='artwork_boxes'>";
					$artworks_element .= $artistname;
					$artworks_element .= "<div style='width:140px;float:left;padding-top:10px' class='artwork-img c'>";
					$artworks_element .= $unframedimageinfo;
					$artworks_element .= $framedimageinfo;
					$artworks_element .= "</div>";
					$artworks_element .= "<div style='float:right;padding-top:10px' class='mobile-arwork-desc'>";
					$artworks_element .= "<div class='artworklabel'>Title:</div><div class='artwork-right'>". $artwork['artworkTitle']."</div><br/>";
					$artworks_element .= $mediuminfo;
					$artworks_element .= $seriesinfo;
					$artworks_element .= $collectioninfo;
					$artworks_element .= $unframedsizeinfo;
					$artworks_element .= $framedsizeinfo;
					$artworks_element .= $editionsizeinfo;
					$artworks_element .= $retailpriceinfo;
					$artworks_element .= $askingpriceinfo;
					$artworks_element .= $commentinfo;
					$artworks_element .= "<br/>";
					$artworks_element .= "</div>";
					$artworks_element .= "</div>";
					$artworks_element .= $additional_img ;
					$artworks_element .= "</div>";
				}
				
				
				// populate template array
				$data[$x] = array(
					'artwork:elements' => $artworks_element
				);
	
	
				$x++;
				$y++;
			}
			return $data;
		}
		else
		{
			return FALSE;
		}
    }
	
	function get_artworkactiveinfo($artworkID)
	{
		// default wheres
		$this->db->where('artworkID', $artworkID);
		$this->db->where('owner_status', 1);		
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
	
	
    function view_artwork($artworkID = '',$uri = '')
    {
			//check if artwork is active or not
			if ($artworkactiveinfo = $this->get_artworkactiveinfo($artworkID))
			{
				$artworkIDpresence = $artworkactiveinfo['artworkID'];
			}
		
			if (!$artworkIDpresence)
			{
				exit();	
			}
			
			$addedinterestmsg = "";
			$addedinterestform_vis = "";
			
			if ($this->input->post('addtointerest'))
			{
				if (!$this->session->userdata('interest_session_id'))
				{
					$randval_userid = random_string('alnum');			
					$this->session->set_userdata('interest_session_id', $randval_userid);
				}
				
				if ($this->session->userdata('interest_session_id'))
				{
					
					if ($interestinfo = $this->get_interestinfo($artworkID))
					{
						$interestID = $interestinfo['interestID'];
					}		
					else
					{
						$this->add_artwork_interest($artworkID);
						/////////////////
					}
				}
				
				
			}
			
			if ($artworkID)
			{
				if (!$this->session->userdata('lastview_session_id'))
				{
					$lastview_userid = random_string('alnum');			
					$this->session->set_userdata('lastview_session_id', $lastview_userid);
				}
				
				if ($this->session->userdata('lastview_session_id'))
				{
					
					if ($lastviewinfo = $this->get_lastviewinfo($artworkID))
					{
						$lastviewID = $lastviewinfo['lastviewID'];
					}		
					else
					{
						$this->add_artwork_lastview($artworkID);
						/////////////////
					}
				}
				
				
			}
			
			
			if ($interestinfo = $this->get_interestinfo($artworkID) && $this->session->userdata('interest_session_id'))
			{
				$interestID = $interestinfo['interestID'];
				$addedinterestmsg = "<div style='border: 1px solid #696969;padding:7px;margin-bottom:10px'> Added to Interest List!</div>";
				$addedinterestform_vis = "style='display:none'";
			}		
			
			
			$query = $this->db->get_where('artworks',array('artworkID'=>$artworkID));
			//$query = $this->db->get_where('artworks',array('artworkID'=>$artworkID, 'owner_status'=>1 , 'visible_on_website'=>1));
	
			if($query->num_rows())
			{
				$artwork = $query->row_array();
				
				//select artist
				$this->db->select('first_name, last_name');
				$this->db->where('postID',  $artwork['artist_link']);
				$artistquery = $this->db->get('ha_artists_posts');
				if ($artistquery->num_rows())
				{
					$artistqueryresult = $artistquery->result_array();			
					foreach($artistqueryresult as $artistqueryvar)
					{	
						$firstname = $artistqueryvar['first_name'];
						$lastname = $artistqueryvar['last_name'];
						$artistname = "".$firstname." ".$lastname."";
					}
					
				}
				
				/*
				//code to prevent artist name form repeating
				if ($this->session->userdata('the_artist_id') ==  $artwork['artist_link']){
					$artistname = '';	
				}
 
				$artist_id_specials = array(
					'the_artist_id' => $artwork['artist_link']
				);
				
				$this->session->set_userdata($artist_id_specials);
				//code to prevent artist name form repeating - end
				*/
				
				$additional_img = '';
				
				//select additional artwork images
				$this->db->select('image, image_alt_txt');
				$this->db->where('artwork_id',  $artwork['artworkID']);
				$this->db->where('draftstatus',  0);
				
				$additionalimgquery = $this->db->get('ha_artworkimages');
				if ($additionalimgquery->num_rows())
				{
					$additionalimgqueryresult = $additionalimgquery->result_array();			
					foreach($additionalimgqueryresult as $additionalimgqueryvar)
					{	
						$image = $additionalimgqueryvar['image'];
						$image_alt_txt = $additionalimgqueryvar['image_alt_txt'];
						$image_alt_txt = htmlentities($image_alt_txt);
						$additional_img .= '<img src="'.site_url().'/static/uploads/artists/artworkimages/'.$image.'" title="'.$image_alt_txt.'" alt="'.$image_alt_txt.'" align="center"/>';
					}
				}
				
				else {
					$additional_img  = '';	
				}
				
				//select medium
				$this->db->select('mediumTitle');
				$this->db->where('mediumID',  $artwork['medium_link']);
				$mediumquery = $this->db->get('ha_mediums');
				if ($mediumquery->num_rows())
				{
					$mediumqueryresult = $mediumquery->result_array();			
					foreach($mediumqueryresult as $mediumqueryvar)
					{	
						$mediumTitle = $mediumqueryvar['mediumTitle'];
					}
					
				}
				
				else {
					$mediumTitle  = '';	
				}
				
				if ($artwork['medium']) {
					$mediuminfo = "<div class='artworklabel'>Medium:</div><div class='artwork-right'>". $artwork['medium']."</div><br/>";	
				}
				else {
					$mediuminfo = '';
				}
				
				//series
				if ($artwork['series']) {
					$seriesinfo = "<div class='artworklabel'>Suite:</div><div class='artwork-right'>".$artwork['series']."</div><br/>";	
				}
				
				else {
				    $seriesinfo = '';	
				}
				
				//edition size
				if ($artwork['edition_size']) {
					$editionsizeinfo = "<div class='artworklabel'>Edition Size:</div><div class='artwork-right'>".$artwork['edition_size']."</div><br/>";	
				}
				
				else {
				    $editionsizeinfo = '';	
				}
				
				//edition number
				if ($artwork['edition_number']) {
					$editionnumber = "<div class='artworklabel'>Edition Number:</div><div class='artwork-right'>".$artwork['edition_number']."</div><br/>";	
				}
				
				else {
				    $editionnumber = '';	
				}
				
				//collection
				if ($artwork['collection']) {
					$collectioninfo = "<div class='artworklabel'>Collection:</div><div class='artwork-right'>".$artwork['collection']."</div><br/>";	
				}
				
				else {
				    $collectioninfo = '';	
				}
				
				//unframed size
				if ($artwork['unframed_size']) {
					$unframedsizeinfo = "<div class='artworklabel'>Unframed Size:</div><div class='artwork-right'>".$artwork['unframed_size']."</div><br/>";	
				}
				
				else {
				    $unframedsizeinfo = '';	
				}
				
				//framed size
				if ($artwork['framed_size']) {
					$framedsizeinfo = "<div class='artworklabel'>Framed Size:</div><div class='artwork-right'>".$artwork['framed_size']."</div><br/>";	
				}
				
				else {
				    $framedsizeinfo = '';	
				}
				
				//retail price
				if ($artwork['retail_price'] != 0) {
					
					if ($artwork['retail_price_comment']) {
						$retail_comment = "- ".$artwork['retail_price_comment']."";	
					}
					else {
						$retail_comment = "";	
					}
					$retailpriceinfo = "<div class='artworklabel'>Retail Price:</div> <div class='artwork-right'>".currency_symbol()."".number_format($artwork['retail_price'],2)." ".$retail_comment."</div><br/>";	
				}
				else {
				    $retailpriceinfo = '';	
				}
				
				//condition
				if ($artwork['condition']) {
					$condition = "<div class='artworklabel'>Condition:</div><div class='artwork-right'>".$artwork['condition']."</div><br/>";	
				}
				
				else {
				    $condition = '';	
				}
				
				//purchase_year
				if ($artwork['purchase_year']) {
					$purchase_year = "<div class='artworklabel'>Purchased Year:</div><div class='artwork-right'>".$artwork['purchase_year']."</div><br/>";	
				}
				
				else {
				    $purchase_year = '';	
				}
				
				//From
				if ($artwork['from']) {
					$from = "<div class='artworklabel'>Purchased from:</div><div class='artwork-right'>".$artwork['from']."</div><br/>";	
				}
				
				else {
				    $from = '';	
				}
				
				//Certificate 
				if ($artwork['certificate']) {
					$certificate = "<div class='artworklabel'>Certificate of Authenticity:</div><div class='artwork-right'>".$artwork['certificate']."</div><br/>";	
				}
				
				else {
				    $certificate = '';	
				}
				
				//Certificate status
				if ($artwork['certificatestatus'] == "1") {
					$certificatestatus = "<div class='artworklabel'>Certificate:</div><div class='artwork-right'>Yes</div><br/>";	
				}
				else {
				    $certificatestatus = '';	
				}
				
				//Certificate 
				if ($artwork['certificate_issued_by']) {
					$certificate_issued_by = "<div class='artworklabel'>Certificate <br/>Issued By:</div><div class='artwork-right'>".$artwork['certificate_issued_by']."</div><br/>";	
				}
				
				else {
				    $certificate_issued_by = '';	
				}
				
				//asking price
				if ($artwork['include_on_MAO_page'] == 0) {
					if ($artwork['asking_price'] != 0) {
						
						if ($artwork['asking_price_comment']) {
							$asking_comment = "- ".$artwork['asking_price_comment']."";	
						}
						else {
							$asking_comment = "";	
						}
						
						$askingpriceinfo = "<div class='artworklabel'>Asking Price:</div> <div class='artwork-right'>".currency_symbol()."".number_format($artwork['asking_price'],2)." ".$asking_comment."</div><br/>";	
					}
					
					else {
						$askingpriceinfo = '';	
					}
				}
				
				if ($artwork['include_on_MAO_page'] == 1) {
					
					if ($artwork['asking_price_comment']) {
						$asking_comment = "- ".$artwork['asking_price_comment']."";	
					}
					else {
						$asking_comment = "";	
					}
					
					$cur_symbol = '';
					$ask_price = '';
					
					if ($artwork['include_price']) {
						
						if ($artwork['asking_price'] != 0) {

						$cur_symbol = currency_symbol();
						$ask_price = "".number_format($artwork['asking_price'],2)." - ";
						}
						
					}
					
					if ($artwork['MAO_content']) {
						$askingpriceinfo = "<div class='artworklabel'>Asking Price:</div><div class='artwork-right'> ".$cur_symbol."".$ask_price."".$artwork['MAO_content']." ".$asking_comment."</div><br/>";	
					}
					
					else {
						$askingpriceinfo = '<div class="artworklabel">Asking Price:</div>';	
					}
				}
				
				//comment
				if ($artwork['description']) {
					$commentinfo = "<br /><br /><br /><div class='artworklabel'>Comment:<div class='artist_comment'>".$this->template->parse_body($artwork['description'])."</div></div>";	
				}
				
				else {
				    $commentinfo = '';	
				}
				
				//unframed image
				if ($artwork['unframed_image']) {
                                   $unframedimageinfo = "<img src='".site_url()."static/uploads/artists/artworks/unframed_images/".$artwork['unframed_image']."' alt='".htmlspecialchars($artwork['unframed_alt_txt'], ENT_QUOTES)."' width='auto' height='' align='center'>";
				}
				
				else {
				    $unframedimageinfo = '';	
				}
				
				//framed image
				if ($artwork['framed_image']) {
					$framedimageinfo = "<img src='".site_url()."static/uploads/artists/artworks/framed_images/".$artwork['framed_image']."' alt='".htmlspecialchars($artwork['framed_alt_txt'], ENT_QUOTES)."' width='auto' height='' align='center' >";			
				}
				else {
				    $framedimageinfo = '';	
				}
				
				if ($artwork['frametype'] == 2) {
					$frametype = "<div class='artworklabel'>Framed without Glass or Plexiglas:</div><div class='artwork-right'>".$artwork['framingdescription']."</div><br/>";	
				}
				
				if ($artwork['frametype'] == 3) {
					$frametype = "<div class='artworklabel'>Framed with Glass:</div><div class='artwork-right'>".$artwork['framingdescription']."</div><br/>";	
				}
				if ($artwork['frametype'] == 4) {
					$frametype = "<div class='artworklabel'>Framed with Plexiglas:</div><div class='artwork-right'>".$artwork['framingdescription']."</div><br/>";	
				}
				
				if ($artwork['frametype'] == 5) {
					$frametype = "<div class='artworklabel'>Other Frame:</div><div class='artwork-right'>".$artwork['framingdescription']."</div><br/>";	
				}

				if ($artwork['frametype'] == 6) {
					$frametype = "<div class='artworklabel'>N/A:</div><div class='artwork-right'>".$artwork['framingdescription']."</div><br/>";	
				}
				
				if ($artwork['signedtype'] == 2) {
					$signedtype = "<div class='artworklabel'>Estate signed:</div><div class='artwork-right'>".$artwork['signaturelocation']."</div><br/>";	
				}
				
				if ($artwork['signedtype'] == 3) {
					$signedtype = "<div class='artworklabel'>Hand signed:</div><div class='artwork-right'>".$artwork['signaturelocation']."</div><br/>";	
				}
				
				if ($artwork['signedtype'] == 4) {
					$signedtype = "<div class='artworklabel'>Plate-Signed:</div><div class='artwork-right'>".$artwork['signaturelocation']."</div><br/>";	
				}

				if ($artwork['signedtype'] == 5) {
					$signedtype = "<div class='artworklabel'>Foundry Signature with Stamp:</div><div class='artwork-right'>".$artwork['signaturelocation']."</div><br/>";	
				}

				if ($artwork['signedtype'] == 6) {
					$signedtype = "<div class='artworklabel'>Sculpture Foundry Mark:</div><div class='artwork-right'>".$artwork['signaturelocation']."</div><br/>";	
				}

				if ($artwork['signedtype'] == 7) {
					$signedtype = "<div class='artworklabel'>Other:</div><div class='artwork-right'>".$artwork['signaturelocation']."</div><br/>";	
				}
				
				// $this->session->set_userdata('catlink', $catlink);
				//code to prevent category name from repeating
				$artworks_element = '';
				$artworks_element .= "<div style='width:350px;float:left;border-bottom: 1px solid #696969;margin-bottom:30px;'>";
				$artworks_element .= "<div style='width:350px;float:left' class='elements'>";
				$artworks_element .= "<div style='width:350px;float:left;padding-top:10px' class='mobile-arwork-desc'>";
				$artworks_element .= "<div class='artworklabel'>Title:</div><div class='artwork-right'>". $artwork['artworkTitle']."</div><br/>";
				$artworks_element .= $mediuminfo;
				$artworks_element .= $seriesinfo;
				$artworks_element .= $collectioninfo;
				$artworks_element .= $unframedsizeinfo;
				$artworks_element .= $framedsizeinfo;
				$artworks_element .= $editionsizeinfo;
				$artworks_element .= $condition;
				$artworks_element .= $purchase_year;
				$artworks_element .= $from;
				$artworks_element .= $certificatestatus;
				$artworks_element .= $certificate_issued_by;
				
				if($condition !='' || $purchase_year != '' || $from != '' || $certificate != '') {
					$artworks_element .= '<div margin-bottom:15px;clear:both;">&nbsp;</div>';
				}
				
				$artworks_element .= $retailpriceinfo;
				$artworks_element .= $askingpriceinfo;
				$artworks_element .= $frametype;
				$artworks_element .= $signedtype;
				$artworks_element .= $commentinfo;
				$artworks_element .= "<br/>";$artworks_element .= "<br/>";$artworks_element .= "<br/>";
				$artworks_element .= "</div>";
				$artworks_element .= "</div>";
				$artworks_element .= "</div>";
				
				// set meta description
				if ($artwork['meta_desc'])
				{
					$output['page:description'] = $artwork['meta_desc'];
				}
	
				// set meta keywords
				if ($artwork['meta_keywords'])
				{
					$output['page:keywords'] = $artwork['meta_keywords'];
				}
	
				// set meta url string
				if ($artwork['url_string'])
				{
					$output['page:url_text'] = $artwork['url_string'];
				}
					
				$numberpresence = "";
				
				if ($framedimageinfo)
				{
				  $fi_img = 1;
				
				}
				
				if ($unframedimageinfo)
				{
				  $ui_img = 1;
				}
				
				if ($additional_img)
				{
				  $add_img = 1;
				}
				
				$numberpresence = $fi_img +  $ui_img  +   $add_img ;
				
				if ($numberpresence > 1)
				{
					$imagescroller = 1;
				}
				else
				{
					$imagescroller = 0;	
				}
				
				// populate template array
				$output['artwork:addedinterestmsg'] = $addedinterestmsg;
				$output['artwork:addedinterestform_vis'] = $addedinterestform_vis;
				$output['artwork:imagescroller'] = $imagescroller;
				$output['artwork:elements'] = $artworks_element;
				$output['artwork:framedalt'] = $artwork['framed_alt_txt'];
				$output['artwork:unframedalt'] = $artwork['unframed_alt_txt'];
				$output['artwork:additionalimg'] = $additional_img;
                $output['artwork:uframed_img'] = $unframedimageinfo;
                $output['artwork:framed_img'] = $framedimageinfo;
                $output['artwork:artist_name'] = $artistname;
				$output['artwork:title'] = $artwork['artworkTitle'];
				$output['artwork:idval'] = $artwork['artworkID'];
				$output['artwork:titleval'] = htmlspecialchars($artwork['artworkTitle'], ENT_QUOTES);
				$output['page:title'] = $this->site->config['siteName'].' - '.$artwork['artworkTitle'];
				$output['summary_text'] = $this->template->parse_body($artwork['summary_text']);
			}
	
			// handle web form
			if (count($_POST))
			{
				if ($message = $this->core->web_form())
				{
					$sendthru['message'] = $message;
					$this->template->template['message'] = $sendthru['message'];
				}
				else
				{
					$sendthru['errors'] = validation_errors();
					$this->template->template['errors'] = $sendthru['errors'];
				}
			}
			// print_r($artwork['url_string']);
			$this->pages->view('singleartwork',$output,TRUE);
    }

	function add_artwork_interest($artworkID)
	{
		$data = array(
		   'artworkID' => $artworkID,
		   'sessionID' => $this->session->userdata('interest_session_id'),
		   'siteID' => $this->siteID,
		);
		
		$this->db->insert('ha_interests', $data);  			
	}
	
	function get_interestinfo($artworkID)
	{
		
		// default wheres
		$this->db->where('artworkID', $artworkID);		
		$this->db->where('sessionID', $this->session->userdata('interest_session_id'));	
		
		// grab
		$query = $this->db->get('ha_interests', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}		
	}	
	
	function get_lastviewinfo($artworkID)
	{
		
		// default wheres
		$this->db->where('artworkID', $artworkID);		
		$this->db->where('sessionID', $this->session->userdata('lastview_session_id'));	
		
		// grab
		$query = $this->db->get('ha_lastviews', 1);

		if ($query->num_rows())
		{
			return $query->row_array();
		}
		else
		{
			return FALSE;
		}		
	}	
	
	
	function add_artwork_lastview($artworkID)
	{
		$data = array(
		   'artworkID' => $artworkID,
		   'sessionID' => $this->session->userdata('lastview_session_id'),
		   'siteID' => $this->siteID,
		);
		
		$this->db->insert('ha_lastviews', $data);  			
	}
	
}