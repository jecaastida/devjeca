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

class Pages extends MX_Controller {
	
	function __construct()
	{
		parent::__construct();
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
        
        $this->load->model('pages_model', 'pages');
        
       
	}
	
	function index()
	{			
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
		}
		else
		{
			$uri = 'home';
		}
		
		$this->view($uri);
	}
	
	function view($page, $sendthru = '', $module = FALSE, $return = FALSE)
	{	
        
        
         if($page == 'home')
         {
            $this->db->where('deleted', 0);
            $this->db->where('home', 1);
            
            $this->db->order_by('productName','asc');
                
            
            $get_products = $this->db->get('shop_products',10,0);
            $products = $get_products->result_array();
            
            foreach ($products as $key => $product)
            {
                $sendthru['products'][$key]['product_title'] = $product['productName'];
                $sendthru['products'][$key]['product_id'] = $product['productID'];
                 $sendthru['products'][$key]['productLink'] = '/shop/'.$product['productID'].'/'.strtolower(url_title($product['productName']));
                $sendthru['products'][$key]['image'] = $product['imageName'];
                // $sendthru['products'][$key]['price'] = $product['price'];
               
                $tmpStr = "";
                                if($this->session->userdata('groupID') != "8"){
                                    if($product['sale_price'] != 0){
                                        $tmpStr = "<p class='retail-price crossed-out' style='text-decoration: line-through;' style='text-decoration: line-through;>Original Price: <span>".currency_symbol().number_format($product['price'],2)."</span></p>
                                                <p class='sale-price'>".currency_symbol().number_format($product['sale_price'],2)."</p>";
                                    }else{
                                        $tmpStr = "<p class='retail-price crossed-out'>".currency_symbol().number_format($product['price'],2)."</p>";
                                    }
                                }else{
                                    if($product['wholesale_price'] == 0){
                                        $tmpStr = "<p class='price'>".currency_symbol().number_format($product['price'],2)."</p>";
                                    }else{
                                        $tmpStr = "<p class='wholesale-price'>".currency_symbol().number_format($product['wholesale_price'],2)."</p>";
                                    }
                                }
                                $sendthru['products'][$key]['price'] = $tmpStr;
            }
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
    
    
    function _populate_products($products)
	{
		if ($products && is_array($products))
		{
			$itemsPerRow = $this->shop->siteVars['shopItemsPerRow'];
			$i = 0;
			$x = 0;
			$t = 0;
						
			foreach ($products as $product)
			{
				// get body and excerpt
				$productBody = (strlen($this->_strip_markdown($product['description'])) > 100) ? substr($this->_strip_markdown($product['description']), 0, 100).'...' : nl2br($this->_strip_markdown($product['description']));
				$productExcerpt = nl2br($this->_strip_markdown($product['excerpt']));
				
				// get images
				if (!$image = $this->uploads->load_image($product['productID'], false, true))
				{
					$image['src'] = base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
				}
				if (!$thumb = $this->uploads->load_image($product['productID'], true, true))
				{
					$thumb['src'] = base_url().$this->config->item('staticPath').'/images/nopicture.jpg';
				}
				
				// populate template array
				$data[$x] = array(
					'product:id' => $product['productID'],
					'product:link' => base_url().'shop/'.$product['productID'].'/'.strtolower(url_title($product['productName'])),
					'product:title' => $product['productName'],
					'product:subtitle' => $product['subtitle'],
					'product:body' => $productBody,
					'product:excerpt' => $productExcerpt,
					'product:image-path' =>	base_url().$image['src'],				
					'product:thumb-path' => base_url().$thumb['src'],
					'product:cell-width' => floor(( 1 / $itemsPerRow) * 100),
					'product:price' => currency_symbol().number_format($product['price'],2),
					'product:stock' => $product['stock']
				);
				
				// get tags
				if ($product['tags'])
				{
					$tags = explode(',', $product['tags']);

					$t = 0;
					foreach ($tags as $tag)
					{
						$data[$x]['product:tags'][$t]['tag:link'] = site_url('shop/tag/'.$this->tags->make_safe_tag($tag));
						$data[$x]['product:tags'][$t]['tag'] = $tag;
						
						$t++;
					}
				}				
				
				if (($i % $itemsPerRow) == 0 && $i > 1)
				{
					$data[$x]['product:rowpad'] = '</tr><tr>'."\n";
					$i = 0;
				}
				else
				{
					$data[$x]['product:rowpad'] = '';
				}

				$i++;
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