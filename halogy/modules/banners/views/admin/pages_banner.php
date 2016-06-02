<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

class Pages extends MX_Controller {

        var $aboutYou = '';
        var $featuredResult = '';
        var $latestNews = '';
        var $upcomingEvents = '';
        var $static_image = "/static/uploads/about_you/";
        var $uploads_path = "/static/uploads/";
        var $default_mailing_list = 1;

	function __construct()
	{
		parent::__construct();
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
	}
		
	function index()
	{		
	$sendthru = array();
	ini_set('display_errors', 1);	
		if ($this->uri->segment(1))
		{
				
			// deprecated uri code (now its always just the uri string)
			$num = 1;
			$uri = '';
			while ($segment = $this->uri->segment($num))
			{
				$uri .= $segment.'/';
				$num ++;
			}
			$new_length = strlen($uri) - 1;
			$uri = substr($uri, 0, $new_length);

			if($this->uri->segment(1) == 'add-a-track'){
				$this->load->module('account');
				$sendthru = $this->account->track_owner_signup('',TRUE);
			}
								
		}
		else
		{
		

			$uri = 'home';
		}

                $this->load->model('pages_model', 'pages');
                
                
                // $this->aboutYou();
		$this->view($uri,$sendthru);
                
                
	}
	
	function _rotating_banners()
			{
			
			  $rotating_banners = array();
  
			  // get featured products 
			  $this->db->order_by('sequence_order');
			  $query = $this->db->get('banners');
			  if ($query->num_rows() > 0)
			  {
				   $rotating_banners = $query->result_array();
				   foreach($rotating_banners as $key => $val)
			   		{
					$rotating_banners[$key]['banner_img'] = $this->uploads_path . '/' . $val['banner_file'];
					$rotating_banners[$key]['text'] = $val['headline'];
	
		}
			  }
			 $rotating_banners[0]['active'] = 'active';
			 
			  return $rotating_banners;
			 }
		
	
        function aboutYou(){         
             $this->aboutYou        = $this->pages->getAboutYou();
             $this->featuredResult  = $this->pages->getFeaturedResult();
             $this->latestNews      = $this->pages->getLatestNews(5);
             $this->featuredNews      = $this->pages->getFeaturedNews(5);
             $this->upcomingEvents  = $this->pages->get_events(5);
             $this->blogs           = $this->pages->get_posts(5);
             $this->load->library('parser');
	     $lj = array('post:postNames' => $this->aboutYou['postName'],
			 'post:postLogo' => $this->aboutYou['postLogo'],
			 'post:postSlogan' => $this->aboutYou['postSlogan'], );
			
        }
		
		
		
	
 
 
        function formatHomePage() {
            /*Rupert - Get Custom Homepage Content =====================================*/
                $arrAboutYou        = $this->aboutYou;
                $arrFeaturedResult  = $this->featuredResult;
                $arrLatestNews      = $this->latestNews;
                $arrUpcomingEvents  = $this->upcomingEvents;

                //print_r($this->upcomingEvents);

                $str = "";
                $user_id = $arrAboutYou['userID'];
                $static_image = "/static/uploads/about_you/";
                // top
				
                //left content
                $str .= "<div id='left-container'>
      
					<div class='sub-holder'>
						<div class='subnav-left'>						   
							<ul id='home-left'>
							  <li class='left-list-top'><a class='color-white' href='/home'>Home</a></li>
							</ul>
						
					</div>
                                         <div class='left-shadow'>&nbsp;</div>
                                        </div>";
							
							
					$str .= "<div class='activities-holder'>
					<div id='bottom-left-container'>
						<div id='latest-box'>
							<div class='bg-latest'>
								<div class='latest-title color-white'>Featured Activities</div>
							</div>
						</div>";
						$str .= "<div id='blog-roll'>";
                                                if($this->upcomingEvents){
							foreach($this->upcomingEvents as $item){
								
								$str .= "   <div class='blog-roll-container'>
												<div>
													<a href='".site_url('events/viewevent/'.$item['eventID'])."'><strong>".$item['eventTitle']."</strong></a>
												</div>
												 ".dateFmt($item['eventDate'], 'Y/m/d')."
											</div>";
							}
                                                }else{
                                                    $str .= " No Featured Activities";
                                                }
							$str .= "</div>";
                            
						$str .= "</div>";
             
               // $str .="<div class='left-box-shadow'>&nbsp;</div>";
				$str .= "</div>";
                $str .= "</div>";


                //Mid content
		$str .= "<div id='top-right-container'>";
                
                //Get  Homepage Images
               $hero_name_format = "_hero_";
                $str .= "<div id='hero-image-container'>";
                for($x=1; $x<4; $x++) {
                    if($arrAboutYou['img'.$x] != '') {
                        $str .= "<div id='heroimage_$x' class='hero_image'>
									<img src='$static_image".$user_id.$hero_name_format.$x."_.".$arrAboutYou['img'.$x]."' />
									<div class='img-caption'>
										<div class='img-title'><strong>".$arrAboutYou['img'.$x.'_title']."</strong></div>
										<div class='img-text'>".$arrAboutYou['img'.$x.'_text']."</div>
									</div>
                                </div>";
                    }
                }
                $str .= "</div>";
				$str .="<div class='banner-shadow'>&nbsp;</div>";

		$str .= "</div>"; //top left container end
                
              
              
				// School Introduction
				$str .= "<div class='bottom-container'>";
					$str .= "<div class='home-content'>";
							/*
							$str .= "<div class='home-title'>".$arrAboutYou['postIntro']."</div>";
							$str .= "<div style='float:left; padding-right:7px;'><img src='".$static_image.$user_id."_pic.".$arrAboutYou['postPic']."' /></div>
							<div id='intro'>".$arrAboutYou['body']."</div>";
							*/
					$str .= " {block2}	";
						
						
					$str .= "</div> ";
				  //right Blog====================================================
							$str .= "<div id='news-holder'>
							
							<div class='news-box'>
								<div id='right-news-box'>
									<div class='bg-news'>
										<div class='news-title color-white'>Featured News</div>
									</div>
								</div>";
								$str .= "<div id='blog-roll'>";
                                                                if($this->blogs){
                                                                    foreach($this->blogs as $item){
																		
                                                                        $str .= "   <div class='blog-roll-container'>
																			<div>
																				<a href='".site_url('news/'.dateFmt($item['dateCreated'], 'Y/m').'/'.$item['uri'])."'>".$item['postTitle']."</a>
																			</div>
																				".dateFmt($item['dateCreated'],'Y/m/d')."
                                                                        </div>";
                                                                    }
                                                                }else{
                                                                    $str .= " No Featured News	";
                                                                }
								$str .= "</div>";
                $str .= "</div> 
				<div class='right-box-shadow'></div>";
                $str .= "</div> ";
				
                
                $str .= "</div> ";
				
                //END of Right Blog ============================================

                $str .= "<div class='clr'></div>";



               
		//$str .= $this->pages->getBottomContent();
                $str .= "<div class='clr'></div>";
                                
                return $str;
            
            /*===========================================================================*/
        }

        function getNews() {

            $this->blogs2 = $this->pages->get_posts(5,true);

            $str = "<div id='blog-roll'>";
            if($this->blogs2) {
                foreach($this->blogs2 as $item) {
                    
                    $str .= "   <div class='blog-roll-container'>
                                 <div>
                                        <a href='".site_url('news/'.dateFmt($item['dateCreated'], 'Y/m').'/'.$item['uri'])."'>".$item['postTitle']."</a>
                                 </div>
                                 ".dateFmt($item['dateCreated'],'Y/m/d')."</div>";
                }
				 $str .= " <div id='news-view-more'><a href='/news' /> View more </a> </div>";
            }else {
                $str .= " No Featured News	";
            }
            $str .= "</div>";

            return $str;
        }


	function view($page, $sendthru = '', $module = FALSE, $return = FALSE)
	{
         

        $this->load->model('pages_model', 'pages');
        /*
        $this->aboutYou();
        $sendthru['page:name'] = $this->aboutYou['postName'];
        $sendthru['page:logo'] = "<img src='".site_url().$this->static_image.$this->aboutYou['userID'].'_logo.'.$this->aboutYou['postLogo']."?rand=".rand()."'/>";
        $sendthru['page:slogan'] = $this->aboutYou['postSlogan'];
        $sendthru['page:globalNews'] = $this->getNews();
        */
        $sendthru['form:err'] = '';
	$sendthru['js:scroll'] = '';
	$sendthru['name:err'] = '';
	$sendthru['email:err'] = '';
	$sendthru['captcha:err'] = '';
	$sendthru['posted:name'] =  $this->input->post('subscriber_name');
	$sendthru['posted:email'] =  $this->input->post('subscriber_email');
	
	if( $this->input->post('subscribe-to-newsletter') )
	{
		$sendthru = $this->_validate_newsletter_form($sendthru);
	}
	
	/*TRACKS*/
	$this->load->model('tracks/track_model', 'tracks');
	$trackdata = $this->tracks->getTrackDetails($this->session->userdata('userID'));
	$sendthru['track:image'] = $trackdata['profile_img'];
	
	/*SPONSORS
	$this->load->model('sponsor/sponsor_model', 'sponsor');
	if($this->uri->segment(1) == 'sponsors' or $this->uri->segment(1) == ''){
		$sendthru['sponsors'] = $this->sponsor->viewallSponsors();
	}*/
	
	/*SCHEDULE
	if($this->uri->segment(1) == 'schedule'){
		$this->load->model('race/race_model', 'race');
		$sendthru['races'] = $this->race->viewallRaces();
	}*/
	
	//FEATURED NEWS
	$sendthru['featured_news'] = $this->pages->get_posts(1,TRUE);
        foreach($sendthru['featured_news'] as $key=>$val){
              $sendthru['featured_news'][$key]['link'] = site_url('news/'.dateFmt($sendthru['featured_news'][$key]['dateCreated'], 'Y/m').'/'.$sendthru['featured_news'][$key]['uri']);
              $sendthru['featured_news'][$key]['dateCreated'] = dateFmt($sendthru['featured_news'][$key]['dateCreated'], ($this->site->config['dateOrder'] == 'MD') ? 'M jS Y' : 'jS M Y');
        }


                // FOR TRACK-SEARCHBAR
		$this->load->model('tracks/track_model','tracks');
                $sendthru['form:country'] = display_countries('t_country',($this->input->get('t_country'))?$this->input->get('t_country'):"US",'class="form-control" id="s_country"');
		$sendthru['form:state'] = display_states('t_state',$this->input->get('t_state'),'class="form-control" id="s_state"'.(@$_GET['t_country'] != 'US' or @$_GET['t_country'] == ''?"disabled":""));
		$sendthru['form:keyword'] = form_input('keyword',$this->input->get('keyword'),'class="form-control" id="s_keyword" placeholder="Search by Name"');
		$sendthru['quick_search'] = form_input('key_word',$this->input->get('key_word'),'class="form-control" id="s_keyword" style="color:#fff !important" placeholder="Search"');
		
		// $output['form:eventtype'] =  form_dropdown('eventtype', array(), 'large');
		$this->db->order_by('machinecatsOrder','asc');
		$get_machine = $this->db->get($this->tracks->machinetype_tbl);
		$machine_type[''] = 'All Machines';
		foreach($get_machine->result_array() as $val){
			$machine_type[$val['machinecatsID']] = $val['machine_type'];
		}
		$sendthru['form:machinetype'] = form_dropdown('machinecatsID', $machine_type,$this->input->get('machinecatsID'),'class="form-control" id="s_machine"');
		
		$this->db->order_by('trackcatOrder','asc');
		$get_trackcat = $this->db->get($this->tracks->trackcat_tbl);
		$trackcat[''] = 'All Types';
		foreach($get_trackcat->result_array() as $val){
			$trackcat[$val['trackcatID']] = $val['track'];
		}
		$sendthru['form:trackcat'] = form_dropdown('trackcatID', $trackcat,$this->input->get('trackcatID'),'class="form-control" id="s_track"');
		
		$this->db->order_by('eventcatsOrder','asc');
		$get_eventcats = $this->db->get($this->tracks->eventcats_tbl);
		$eventcats[''] = 'All Events';
		foreach($get_eventcats->result_array() as $val){
			$eventcats[$val['event_type']] = $val['event_type'];
		}
		$sendthru['form:eventtype'] = form_dropdown('eventtype',$eventcats,$this->input->get('type'),'class="form-control" id="s_events"');
		// $sendthru['form:eventtype'] = form_dropdown('eventtype',array(''=>'All Events','Practice'=>'PRACTICE','Race'=>'RACE'),$this->input->get('eventtype'),'class="form-control" id="s_event"');
		
		// END TRACK-SEARCHBAR
                
		//ADVERTS
		$sendthru['rotating:banners'] = $this->_rotating_banners();
		$this->load->model('adverts/adverts_model','adverts');
		//$sendthru['slides'] = $this->adverts->viewallActiveBanner();
		
		$sendthru['advert:top'] = $this->adverts->viewAdvert('top');
		$sendthru['advert:bottom'] = $this->adverts->viewAdvert('bottom');
		$sendthru['advert:side'] = $this->adverts->viewAdvert('side');		
		
		// FOR HOME PAGE
		$this->load->model('tracks/tracks_model','tracks');

			//get latest shots
			//$this->db->order_by('dateUpload','desc');
			//$get_vids = $this->db->get($this->tracks->videos_tbl,1,0);
			$get_vids = $this->db->query('select * from ha_track_vids join ha_tracks on ha_track_vids.tracksID =ha_tracks.tracksID where ha_tracks.subscriptionID=2 order by dateUpload desc ');
			$sendthru['latest_vid'] = $get_vids->result_array();
                        
			//$this->db->order_by('dateUpload','desc');
			//$get_pics = $this->db->get($this->tracks->images_tbl,12,0);
                        $get_pics = $this->db->query('select * from ha_track_imgs join ha_tracks on ha_track_imgs.tracksID =ha_tracks.tracksID where ha_tracks.subscriptionID=2 order by dateUpload desc');
			$sendthru['latest_imgs'] = $get_pics->result_array();

                       
			//get recent updated tracks
                        $this->db->where('subscriptionID',2);
			$this->db->order_by('lastUpdate','desc');                        
			$get_tracks = $this->db->get($this->tracks->tracks_tbl,5,0);
			$sendthru['recent_update_tracks']=$result = $get_tracks->result_array();
                        
                        $sendthru['lastUpdate']=date("d F, Y",  strtotime($result[0]['lastUpdate']));

			//get events
			$this->db->join($this->tracks->tracks_tbl,'ha_events.userID = ha_tracks.userID','LEFT');
			
			$this->db->where('eventDate >=',date('Y-m-d H:i:s'));
			$this->db->order_by('eventDate', "asc"); 
			$get_events = $this->db->get($this->tracks->events_tbl,10,0);
			$events = $get_events->result_array();
			$this->load->helper('text');
				foreach($events as $key => $event){
					// if($event['type'] == 'RACE'){
							// $sendthru['events'][$key]['indicator'] = 'success';
						// }elseif($event['type'] == 'PRACTICE'){
							// $sendthru['events'][$key]['indicator'] = 'danger';
						// }
						// $events[$val['eventcatsID']] = $val['event_type'];
						
						$sendthru['events'][$key]['eventID'] = $event['eventID'];
						$sendthru['events'][$key]['eventTitle'] = $event['eventTitle'];
						$sendthru['events'][$key]['type'] = $event['type'];
						$sendthru['events'][$key]['description'] = word_limiter($event['description'],5);
						$sendthru['events'][$key]['eventDate'] = date("d M Y", strtotime($event['eventDate']) );
						$sendthru['events'][$key]['eventEnd'] = date("d M Y", strtotime($event['eventEnd']) );
						$sendthru['events'][$key]['time'] = $event['time'];
						$sendthru['events'][$key]['location'] = $event['location'];
						$sendthru['events'][$key]['state'] = $event['state'];
						$sendthru['events'][$key]['trackname'] = $event['trackname'];
				}
		//	print_r($sendthru);
		// END FOR HOMEPAGE

        // Displays cart total summary in any page
        $cart_total_items = 0;
        if (isset($this->session->userdata['cart']) && !empty($this->session->userdata['cart'])) {

              foreach ($this->session->userdata['cart'] as $value) {
                   $cart_total_items = $cart_total_items + $value;
              }

              if (isset($this->session->userdata['cart_total'])) {
                   $cart_Amount = $this->session->userdata['cart_total'];
              } else {
                   $cart_Amount = 0;
              }
        }

        if ($cart_total_items >= 1) {
             // $myCart_summary = '$' . number_format($cart_Amount, 2, '.', ',');
             $myCart_summary = sprintf("%d item(s), Total: \$%.2f", $cart_total_items, $cart_Amount);
             $cart_menu_label = 'View Cart';
        } else {
             $myCart_summary = ''; 
             $cart_menu_label = 'Shopping Cart';
        }

        $sendthru['page:cartTotal'] = $myCart_summary; 
        $sendthru['page:cartMenuLabel'] = $cart_menu_label; 

        if (!isset($sendthru['signupRedirect'])) {
                $sendthru['signupRedirect'] = '';
        } 
 
                
		// set default parse file
		$parseFile = 'default';
		
		// check the page is not ajax or a return
		if (!$this->core->is_ajax() && !$return)
		{
                    
			// check to see if the user is logged in as admin and has rights to edit the page inline
			if ($this->session->userdata('session_admin'))
			{
				$parseFile = 'view_template_inline';			
			}
		}
		
		// handle web form
		if (count($_POST) && !$module)
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
				
		// see if the cms is to generate a page from a module or a function of the site
		if ($module)
		{
                
			// set template tag
			$this->template->template['page:template'] = $page;
			
			// look up the page to see if there is any overriding meta data
			if ($metadata = $this->core->get_page(FALSE, substr($this->uri->uri_string(), 1)))
			{
				// redirect if set
				if ($metadata['redirect'])
				{
					$metadata['redirect'] = preg_replace('/^\//', '', $metadata['redirect']);
					redirect($metadata['redirect']);
				}				
				
				if ($metadata['active'] ||
					(!$metadata['active'] && $this->session->userdata('session_admin') &&
						((@in_array('pages_edit', $this->permission->permissions) && in_array('pages_all', $this->permission->permissions)) ||
						(!@in_array('pages_all', $this->permission->permissions) && $this->session->userdata('groupID') && $metadata['groupID'] == $this->session->userdata('groupID')))
					)
				)
				{
					// set a title as long as its not a default
					if ($metadata['title'] != $metadata['pageName'])
					{
						$sendthru['page:title'] = $metadata['title'];
					}

					// set meta data
					$sendthru['page:keywords'] = $metadata['keywords'];
					$sendthru['page:description'] = $metadata['description'];
				}
				else
				{
					show_404();
				}
			}
			
			// get template by name
			if ($pagedata = $this->core->get_module_template($page))
			{
				// get template and blocks from cms
				$module = $this->template->generate_template($pagedata);
	
				// merge the sendthru data with page data		
				$template = (is_array($sendthru)) ? array_merge($module, $sendthru) : $module;

				// set a null title
				$template['page:title'] = (!isset($sendthru['page:title'])) ? $this->site->config['siteName'] : $sendthru['page:title'];
	
				// output data
				if ($return === FALSE)
				{
					$this->parser->parse($parseFile, $template);
				}
				else
				{
					return $this->parser->parse($parseFile, $template, TRUE);
				}
			}	

			// else just show it from a file template
			else
			{	
				// get module name
				$module = (is_string($module)) ? $module : $this->uri->segment(1);

				// get module template
				if ($file = @file_get_contents(APPPATH.'modules/'.$module.'/views/templates/'.$page.'.php'))
				{	
					// make a template out of the file
					$module = $this->template->generate_template(FALSE, $file);
	
					// merge the sendthru data with page data		
					$template = (is_array($sendthru)) ? array_merge($module, $sendthru) : $module;

					// set a null title
					$template['page:title'] = (!isset($sendthru['page:title'])) ? $this->site->config['siteName'] : $sendthru['page:title'];

					// output data
					if ($return === FALSE)
					{
						$this->parser->parse($parseFile, $template);
					}
					else
					{
						return $this->parser->parse($parseFile, $template, TRUE);
					}
				}
				else
				{
					show_error('Templating error!');
				}
			}
		}

		// else just grab the page from cms
		elseif ($this->session->userdata('session_admin') && $pagedata = $this->core->get_page(FALSE, $page))
		{
                
                 
			// redirect if set
			if ($pagedata['redirect'])
			{
                            
				$pagedata['redirect'] = preg_replace('/^\//', '', $pagedata['redirect']);
				redirect($pagedata['redirect']);
			}
			
			// show cms with admin functions
			if ((@in_array('pages_edit', $this->permission->permissions) && in_array('pages_all', $this->permission->permissions)) ||
			(!@in_array('pages_all', $this->permission->permissions) && $this->session->userdata('groupID') && $pagedata['groupID'] == $this->session->userdata('groupID')))
			{
                        
				$versionIDs = array();
				
				// check that this is not the live version and then add page version
				if ($versions = $this->core->get_versions($pagedata['pageID']))
				{
					foreach ($versions as $version)
					{
						$versionIDs[] = $version['versionID'];
					}
				}
				if ((!$pagedata['versionID'] && !$pagedata['draftID']) || @in_array($pagedata['draftID'], $versionIDs))
				{
					$this->core->add_draft($pagedata['pageID']);
					redirect($this->uri->uri_string());
				}		
				
				// set no cache headers
				$this->output->set_header('Cache-Control: no-Store, no-Cache, must-revalidate');
				$this->output->set_header('Expires: -1');
				
				// show admin inline editor
				$output = $this->core->generate_page($pagedata['pageID'], TRUE);
				
				// merge output with any other data
				$output = (is_array($sendthru)) ? array_merge($output, $sendthru) : $output;

                                /*
                                if($page == 'home'){
                                   $output['block1'] = $this->formatHomePage();
                                }
                                */
                                
                                
                                
				// output images
				$where = '';
				if (!@in_array('images_all', $this->permission->permissions))
				{
					$where['userID'] = $this->session->userdata('userID');
				}
				$images = $this->core->viewall('images', $where, array('dateCreated', 'desc'), 99);
				$output['images'] = $images['images'];

                                

				// parse with main cms template
				if ($return === FALSE)
				{
                                        
					$this->parser->parse($parseFile, $output);
                                         
				}
				else
				{
                                       
					return $this->parser->parse($parseFile, $output, TRUE);
                                        
				}
			}

			// otherwise they are admin but they don't have permission to this page
			else
			{
                        
				// just get normal page
				$output = $this->core->generate_page($pagedata['pageID']);

				// merge output with any other data
				$output = (is_array($sendthru)) ? array_merge($output, $sendthru) : $output;

				// parse with main cms template				
				if ($return === FALSE)
				{
					$this->parser->parse($parseFile, $output);
				}
				else
				{
					return $this->parser->parse($parseFile, $output, TRUE);
				}				
			}
                       
		}
                
		// display normal page
		elseif ($pagedata = $this->core->get_active_page($page))
		{
                
			// redirect if set
			if ($pagedata['redirect'])
			{
				$pagedata['redirect'] = preg_replace('/^\//', '', $pagedata['redirect']);
				redirect($pagedata['redirect']);
			}
		
			// add view
			$this->core->add_view($pagedata['pageID']);

			// merge output with any other data
			$pagedata = (is_array($sendthru)) ? array_merge($pagedata, $sendthru) : $pagedata;

			// just get normal page
			$output = $this->core->generate_page($pagedata['pageID']);

			// merge output with any other data
			$output = (is_array($sendthru)) ? array_merge($output, $sendthru) : $output;

                        /*For not login
                        if($page == 'home'){
                                
                                   $output['block1'] = $this->formatHomePage();

                                   
                        } 
                        */

			// set no cache headers
			$this->output->set_header('Content-Type: text/html');
                        
			// parse with main cms template
			if ($return === FALSE)
			{
                                
				$this->parser->parse($parseFile, $output);
                               
			}
			else
			{

                                
				return $this->parser->parse($parseFile, $output, TRUE);
			}

		}

		// if nothing then 404 it!
		else
		{
			show_404();
		}
                
                
               
	}

	// file viewer
	function files($type = '', $ref = '')
	{
		// format filename
		$filenames = @explode('.', $ref);
		$extension = end($filenames);
		$filename = str_replace('.'.$extension, '', $ref);
		
		// css
		if ($type == 'css')
		{
			if ($include = $this->core->get_include($ref))
			{
				$this->output->set_header('Content-Type: text/css');
				$this->output->set_header('Expires: ' . gmdate('D, d M Y H:i:s', time()+14*24*60*60) . ' GMT');
				
				$this->output->set_output($include['body']);
			}
			else
			{
				show_404();
			}
		}

		// js
		elseif ($type == 'js')
		{
			if ($include = $this->core->get_include($ref))
			{
				$this->output->set_header('Content-Type: text/javascript');
				$this->output->set_header('Expires: ' . gmdate('D, d M Y H:i:s', time()+14*24*60*60) . ' GMT');				

				$this->output->set_output($include['body']);
			}
			else
			{
				show_404();
			}
		}

		// images
		elseif ($type == 'images' || $type == 'gfx' | $type == 'thumbs')
		{
			if ($extension == 'gif')
			{
				$this->output->set_header('Content-Type: image/gif');
			}
			elseif ($extension == 'jpg' || $extension == 'jpeg')
			{
				$this->output->set_header('Content-Type: image/pjpeg');
				$this->output->set_header('Content-Type: image/jpeg');
			}
			elseif ($extension == 'png')
			{
				$this->output->set_header('Content-Type: image/png');
			}
			else
			{
				show_404();
			}

			// output image
			if ($image = $this->uploads->load_image($filename))
			{
				// set thumbnail
				$image = ($type == 'thumbs' && $thumb = $this->uploads->load_image($filename, TRUE)) ? $thumb : $image;

				$imageOutput = file_get_contents('.'.$image['src']);

				$fs = stat('.'.$image['src']);
				
				$this->output->set_header("Etag: ".sprintf('"%x-%x-%s"', $fs['ino'], $fs['size'],base_convert(str_pad($fs['mtime'],16,"0"),10,16)));
				$this->output->set_header('Expires: '.gmdate('D, d M Y H:i:s', time()+14*24*60*60) . ' GMT');
				$this->output->set_output($imageOutput);
			}
			else
			{
				show_404();
			}
		}

		// uploaded files
		elseif ($type == 'files')
		{	
			// get the file, by reference or by filename
			if (@!$filenames[1])
			{
				$file = $this->uploads->load_file($ref, TRUE);
			}
			else
			{
				$file = $this->uploads->load_file($filename, TRUE);
			}
			
			if ($file)
			{
				if (@$file['error'] == 'expired')
				{
					show_error('Sorry, this download has now expired. Please contact support.');
				}
				elseif (@$file['error'] == 'premium')
				{
					show_error('This is a premium item and must be purchased in the shop.');
				}
				else
				{
					// set headers
					if ($extension == 'ico')
					{
						$this->output->set_header('Content-Type: image/x-icon');
					}
					elseif ($extension == 'swf')
					{
						$this->output->set_header('Content-Type: application/x-shockwave-flash');
					}
					else
					{	
						$this->output->set_header("Pragma: public");
						$this->output->set_header("Expires: -1");
						$this->output->set_header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
						$this->output->set_header("Content-Type: application/force-download");
						$this->output->set_header("Content-Type: application/octet-stream");
						$this->output->set_header("Content-Length: " .(string)(filesize('.'.$file['src'])) );
						$this->output->set_header("Content-Disposition: attachment; filename=".$file['fileRef'].$file['extension']);
						$this->output->set_header("Content-Description: File Transfer");
					}
					
					// output file contents
					$output = file_get_contents('.'.$file['src']);
					$this->output->set_output($output);
				}
			}
			else
			{
				show_404();
			}
		}

		// else 404 it
		else
		{
			show_404();
		}
	}

	function _captcha_check()
	{
		if (!$this->core->captcha_check())
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

function _validate_newsletter_form( $sendthru = array() )
	{
		$default_list = $this->default_mailing_list;
		$err = FALSE;

		$this->form_validation->set_error_delimiters('<span class="newsletter-error">', '</span><br />');
		//$this->form_validation->set_message('required', 'All fields required');
		$this->form_validation->set_message('valid_email', 'Invalid e-Mail');

		$required = array(
			array(
				'field'   => 'subscriber_name', 
				'label'   => 'Name', 
				'rules'   => 'required|trim'
			),
			array(
				'field'   => 'subscriber_email', 
				'label'   => 'e-Mail', 
				'rules'   => 'required|valid_email|trim'
			)
		); 

		$this->form_validation->set_rules($required);
		$validate_prod_form = $this->form_validation->run();	

		$sendthru['name:err'] =  form_error('subscriber_name');
		$sendthru['email:err'] =  form_error('subscriber_email');
		// $sendthru['captcha:err'] =  form_error('captcha_code');		

		if ( !$sendthru['email:err'] )
		{
			$query_email = $this->db->get_where('email_list_subscribers', array('email' => trim($this->input->post('subscriber_email')), 'listID' => $default_list));
			if ($query_email->num_rows() > 0)
			{
				$sendthru['email:err'] = '<span class="newsletter-error">e-Mail already subscribed!</span><br />';
				$validate_prod_form = FALSE;
			}
		}			
				
		if ( $validate_prod_form )
		{
			// update mailing list
			$this->pages->_update_mailing_list($default_list);
										
			// redirect to success page
			redirect('/newsletter-success');
		}
		else
		{		
			$sendthru['form:err'] = '<p style="margin:0;" class="newsletter-error">Error(s) were found!</p>';
			//$sendthru['js:scroll'] = "$('html, body').animate({scrollTop:$(document).height()}, 'slow');";
			// $sendthru['js:scroll'] = "$('html, body').animate({ scrollTop: $(document).height()-$(window).height() }, 'slow');";
			$sendthru['js:scroll'] = "var aTag = $(\"form[name='newsletter-subscription']\");\n";
			$sendthru['js:scroll'] .= "$('html,body').animate({scrollTop: aTag.offset().top},'slow');";
			return $sendthru;			
		}
		
	}

}