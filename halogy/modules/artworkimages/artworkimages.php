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

class Artworkimages extends MX_Controller {

	var $partials = array();
        var $type = 'artworkimage';
		
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
		$this->load->library('calendar', array('show_next_prev' => TRUE, 'next_prev_url' => '/artworkimages'));		
		$this->load->model('artworkimages_model', 'artworkimages');
                //$this->CI->load->model('news_model', 'news');
                $this->load->module('news');
		$this->load->module('pages');		

		// load partials - archive
		if ($archive = $this->artworkimages->get_archive())
		{
			foreach($archive as $date)
			{
				$this->partials['artworkimages:archive'][] = array(
					'archive:link' => site_url('/artworkimages/'.$date['year'].'/'.$date['month'].'/'),
					'archive:title' => $date['dateStr'],
					'archive:count' => $date['numArtworkimages']
				);
			}
		}

		// load partials - latest
		if ($latest = $this->artworkimages->get_headlines())
		{
			foreach($latest as $artworkimage)
			{
				$this->partials['artworkimages:latest'][] = array(
					'latest:link' => site_url('artworkimages/viewartworkimage/'.$artworkimage['artworkimageID']),
					'latest:title' => $artworkimage['artworkimageTitle'],
					'latest:date' => date((($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y'), strtotime($artworkimage['artworkimageDate'])),
				);
			}
		}	

		// load partials - calendar
		$month = ($this->uri->segment(3) && intval($this->uri->segment(2))) ? $this->uri->segment(3) : date('m', time());
		$year = ($this->uri->segment(2) && intval($this->uri->segment(2))) ? $this->uri->segment(2) : date('Y', time());
		$monthArtworkimages = array();
		if ($data['month'] = $this->artworkimages->get_month($month, $year))
		{
			foreach($data['month'] as $artworkimage)
			{
				$monthArtworkimages[date('j', strtotime($artworkimage['artworkimageDate']))] = '/artworkimages/'.date('Y/m/d', strtotime($artworkimage['artworkimageDate']));
			}
		}
		@$this->partials['artworkimages:calendar'] = $this->calendar->generate($year, $month, $monthArtworkimages);
	}

	function index()
	{
		// get partials
                
		$output = $this->partials;
						
		// get latest artworkimages
		$artworkimages = $this->artworkimages->get_artworkimages(10);
		$output['artworkimages:artworkimages'] = $this->_populate_artworkimages($artworkimages);

		// send artworkimages to page
		$data['artworkimages'] = $artworkimages;

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages';
		$output['page:heading'] = 'Upcoming Artworkimages';
		
                 // set meta description
			if ($post['excerpt'])
			{
				$output['page:description'] = $post['excerpt'];
			}
		// display with cms layer
		$this->pages->view('artworkimages', $output, TRUE);	
	}
	
	function featured()
	{
		// get partials
		$output = $this->partials;
						
		// get latest artworkimages
		$artworkimages = $this->artworkimages->get_featured_artworkimages();
		$output['artworkimages:featured'] = $this->_populate_artworkimages($artworkimages);

		// send artworkimages to page
		$data['artworkimages'] = $artworkimages;

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages';
		$output['page:heading'] = 'Featured Artworkimages';
		
		// display with cms layer
		$this->pages->view('artworkimages_featured', $output, TRUE);	
	}

	function viewartworkimage($artworkimageID = '')
	{
		// get partials
		$output = $this->partials;
				
		// get artworkimage
		if ($artworkimage = $this->artworkimages->get_artworkimage($artworkimageID))
		{				

			// populate template
			$output['artworkimage:title'] = $artworkimage['artworkimageTitle'];
			$output['artworkimage:link'] = site_url('artworkimages/viewartworkimage/'.$artworkimage['artworkimageID']);
			$output['artworkimage:location'] = $artworkimage['location'];
			$output['artworkimage:date'] = date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artworkimage['artworkimageDate'])).
						(($artworkimage['time']) ? ' ('.$artworkimage['time'].')' : '').
						(($artworkimage['artworkimageEnd'] > 0) ? ' - '.date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artworkimage['artworkimageEnd'])) : '');
			$output['artworkimage:body'] = $this->template->parse_body($artworkimage['description']);
                        //get gallery
                        $output['artworkimage:gallery'] = "";
                        //print_r($artworkimage);
                        if($artworkimage['gallery'] == 1){
                            //echo 'yes';
                            //$this->load->model('news_model', 'news');
                            $gal = $this->artworkimages->getGallery($artworkimage['artworkimageID'], $this->type);
                            //print_r($gal);
                            $str = "";
                            if($gal){
                                $output['artworkimage:gallery'] = $this->artworkimages->formatGallery($gal);
                            }
                        }

			$output['artworkimage:excerpt'] = $this->template->parse_body($artworkimage['excerpt']);
			$output['artworkimage:author'] = $this->artworkimages->lookup_user($artworkimage['userID'], TRUE);
			$output['artworkimage:author-id'] = $artworkimage['userID'];
			$output['artworkimage:edit'] = ($artworkimage['userID'] == $this->session->userdata('userID')) ? ' | '.anchor('/ce/artworkimages/edit_artworkimage/'.$artworkimageID, 'Edit Artworkimage') : '';

			// set title
			$output['page:title'] = $this->site->config['siteName'].' Artworkimages - '.$artworkimage['artworkimageTitle'];
			$output['keywords'] = $artworkimage['tags'];
			
			// set meta description
			if ($artworkimage['excerpt'])
			{
				$output['page:description'] = $artworkimage['excerpt'];
			}
			
			// output other stuff
			$data['artworkimage'] = $artworkimage;
			$data['tags'] = explode(' ', $artworkimage['tags']);	
						
			// display with cms layer
			$this->pages->view('artworkimages_single', $output, TRUE);
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
		$artworkimages = $this->artworkimages->get_artworkimages_by_tag($tag);
		$output['artworkimages:artworkimages'] = $this->_populate_artworkimages($artworkimages);

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages';
		$output['page:heading'] = 'Artworkimages on "'.$tag.'"';		

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';
						
		// display with cms layer
		$this->pages->view('artworkimages', $output, TRUE);
	}

	function month()
	{
		// get partials
		$output = $this->partials;

		// get artworkimage based on uri
		$year = $this->uri->segment(2);
		$month = $this->uri->segment(3);		

		// get tags
		$artworkimages = $this->artworkimages->get_artworkimages_by_date($year, $month);
		$output['artworkimages:artworkimages'] = $this->_populate_artworkimages($artworkimages);

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages - '.date('F Y', mktime(0,0,0,$month,1,$year));
		$output['page:heading'] = date('F Y', mktime(0,0,0,$month,1,$year));			

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';		

		// display with cms layer
		$this->pages->view('artworkimages', $output, TRUE);	
	}
	
	function year()
	{
		// get partials
		$output = $this->partials;

		// get artworkimage based on uri
		$year = $this->uri->segment(2);	

		// get tags
		$artworkimages = $this->artworkimages->get_artworkimages_by_date($year);
		$output['artworkimages:artworkimages'] = $this->_populate_artworkimages($artworkimages);

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages - '.date('Y', mktime(0,0,0,1,1,$year));
		$output['page:heading'] = date('Y', mktime(0,0,0,1,1,$year));			

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';		

		// display with cms layer
		$this->pages->view('artworkimages', $output, TRUE);	
	}

	function day()
	{
		// get partials
		$output = $this->partials;

		// get artworkimage based on uri
		$year = $this->uri->segment(2);
		$month = $this->uri->segment(3);
		$day = $this->uri->segment(4);	

		// get tags
		$artworkimages = $this->artworkimages->get_artworkimages_by_date($year, $month, $day);
		$output['artworkimages:artworkimages'] = $this->_populate_artworkimages($artworkimages);

		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages - '.date('D jS F Y', mktime(0,0,0,$month,$day,$year));
		$output['page:heading'] = date('D jS F Y', mktime(0,0,0,$month,$day,$year));

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';	
				
		// display with cms layer
		$this->pages->view('artworkimages', $output, TRUE);	
	}

	function search($query = '')
	{
		// get partials
		$output = $this->partials;

		// set tags
		$query = ($query) ? $query : $this->input->post('query');

		// get result from tags
		$objectIDs = $this->tags->search('artworkimages', $query);

		$artworkimages = $this->artworkimages->search_artworkimages($query, $objectIDs);
		$output['artworkimages:artworkimages'] = $this->_populate_artworkimages($artworkimages);
		$output['query'] = $query;		
		
		// set title
		$output['page:title'] = $this->site->config['siteName'].' | Artworkimages - Searching Artworkimages for "'.$output['query'].'"';
		$output['page:heading'] = 'Search artworkimages for: "'.$output['query'].'"';

		// set pagination
		$output['pagination'] = ($pagination = $this->pagination->create_links()) ? $pagination : '';	
		
		// display with cms layer
		$this->pages->view('artworkimages_search', $output, TRUE);		
	}

	function ac_search()
	{
		$tags = strtolower($_POST["q"]);
        if (!$tags)
        {
        	return FALSE;
        }

		if ($objectIDs = $this->tags->search('artworkimages', $tags))
		{		
			// form dropdown and myql get countries
			if ($searches = $this->artworkimages->search_artworkimages($objectIDs))
			{
				// go foreach
				foreach($searches as $search)
				{
					$items[$search['tags']] = array('id' => $search['artworkimageID'], 'name' => $search['artworkimageTitle']);
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
		$data['feed_name'] = $this->site->config['siteName'] . ' | Artworkimages RSS Feed';
		$data['feed_url'] = site_url('/artworkimages');
		$data['page_description'] = 'Artworkimages RSS Feed for '.$this->site->config['siteName'].'.';
		$data['page_language'] = 'en';
		$data['creator_email'] = $this->site->config['siteEmail'];
		$data['artworkimages'] = $this->artworkimages->get_artworkimages(10);
		
        $this->output->set_header('Content-Type: application/rss+xml');
		$this->load->view('rss', $data);
	}

    function _populate_artworkimages($artworkimages = '')
    {
    	if ($artworkimages && is_array($artworkimages))
    	{
			$x = 0;
			foreach($artworkimages as $artworkimage)
			{
				// populate template array
				$data[$x] = array(
					'artworkimage:link' => site_url('artworkimages/viewartworkimage/'.$artworkimage['artworkimageID']),
					'artworkimage:title' => $artworkimage['artworkimageTitle'],
					'artworkimage:location' => $artworkimage['location'],
					'artworkimage:date' => date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artworkimage['artworkimageDate'])). 
						(($artworkimage['time']) ? ' ('.$artworkimage['time'].')' : '').
						(($artworkimage['artworkimageEnd'] > 0) ? ' - '.date(($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y', strtotime($artworkimage['artworkimageEnd'])) : ''),
					'artworkimage:day' => date('d', strtotime($artworkimage['artworkimageDate'])),
					'artworkimage:month' => date('M', strtotime($artworkimage['artworkimageDate'])),
					'artworkimage:year' => date('y', strtotime($artworkimage['artworkimageDate'])),																
					'artworkimage:body' => $this->template->parse_body($artworkimage['description'], TRUE, site_url('artworkimages/viewartworkimage/'.$artworkimage['artworkimageID'])),
					'artworkimage:excerpt' => $this->template->parse_body($artworkimage['excerpt'], TRUE, site_url('artworkimages/viewartworkimage/'.$artworkimage['artworkimageID'])),
					'artworkimage:author' => $this->artworkimages->lookup_user($artworkimage['userID'], TRUE),
					'artworkimage:author-id' => $artworkimage['userID']
				);
	
				// get tags
				if ($artworkimage['tags'])
				{
					$tags = explode(' ', $artworkimage['tags']);

					$i = 0;
					foreach ($tags as $tag)
					{
						$data[$x]['artworkimage:tags'][$i]['tag:link'] = site_url('blog/tag/'.$tag);
						$data[$x]['artworkimage:tags'][$i]['tag'] = $tag;
						
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

}