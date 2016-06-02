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
	
        
        function gateway(){
            
                $output = "";
                $output['data']['shop_api_key'] = $this->siteVars['shop_api_key'];
                $output['data']['shop_private_key'] = $this->siteVars['shop_private_key'];
                $output['data']['shop_subdomain'] = $this->siteVars['shop_subdomain'];
                
                //print_r($this->siteVars);
                if($_POST){
                    $objectID = array('siteID' => $this->siteID);
                    $this->core->set['shop_api_key'] = $this->input->post('shop_api_key');
                    $this->core->set['shop_private_key'] = $this->input->post('shop_private_key');
                    $this->core->set['shop_subdomain'] = $this->input->post('shop_subdomain');
                  
                    // update
			if ($this->core->update('sites', $objectID))
			{                             
				$output['message'] = '<p>Your details have been updated.</p>';
                                $output['data']['shop_api_key'] = $this->input->post('shop_api_key');
                                $output['data']['shop_private_key'] = $this->input->post('shop_private_key');
                                $output['data']['shop_subdomain'] = $this->input->post('shop_subdomain');
                                
			}
                    
                }
            
                $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/gateway_settings',$output);
		$this->load->view($this->includes_path.'/footer');
        }
        
        
        function variations(){
                $where = "";
            
                $output = $this->core->viewall('shop_variations_types', $where);
                
                
                $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/variations',$output);
		$this->load->view($this->includes_path.'/footer');
        }
        
        function products_search(){
                
        }
        
	function products($catID = '')
	{		
		// get featured
		$featured = ($catID == 'featured') ? TRUE : FALSE;
		
		// set order segment
		if (is_numeric($catID) || $catID == 'featured')
		{
			$this->shop->uri_assoc_segment = 5;
			
			// output selected category
			$output['catID'] = $catID;
		}
		else
		{
			$output['catID'] = '';
		}
		
		// check catID isnt paging or featured
		$catID = ($catID == 'page' || $catID == 'featured' || $catID == 'orderasc' || $catID == 'orderdesc') ? '' : $catID;
		
		// set limit
		$limit = (!$catID) ? $this->site->config['paging'] : 999;
		
		// get products
		$output['products'] = $this->shop->get_products($catID, $this->input->post('searchbox'), $featured, $limit);
		
                //$arrProd = array();
                
                $arrProd = $output['products'];
                
                $ctr = 0;
                foreach($arrProd as $prod){
                    $cat = $this->shop->get_cats_for_product($prod['productID']);
                    //echo implode(',',$cat);
                    $output['products'][$ctr]['categories'] = @implode(',',$cat);
                    $ctr += 1;
                }
                
                //$output['data']['categories'] = $this->shop->get_cats_for_product($productID);
                //print_r($output['products']);
                
		// get categories
		$output['categories'] = $this->shop->get_categories();
		
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/products',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_product()
	{
		// check permissions for this page
		if (!in_array('shop_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'productName' => 'Product name'
			//'catalogueID' => array('label' => 'Catalogue ID', 'rules' => 'required|unique[shop_products.catalogueID]|trim')
		);
       
        //can add photo
			$output['upload'] = form_hidden('upload',1).form_upload('image',set_value('image'));
			$output['caption'] = form_input('img_caption',set_value('img_caption'),"class='formelement'");

		if ($this->input->post('cancel'))
		{			
			redirect($this->redirect);
		}
		else
		{			
			// set date
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['userID'] = $this->session->userdata('userID');

			// upload image
			if (@$_FILES['image']['name'] != '')
			{
                                $this->uploads->maxSize = 1000000;
				if ($imageData = $this->uploads->upload_image())
				{
					$this->core->set['imageName'] = $imageData['file_name'];
				}
			} 
            
            
			
			// get values
			$output['data'] = $this->core->get_values('shop_products');	

			// get image errors if there are any
			if ($this->uploads->errors)
			{
                                
				$this->form_validation->set_error($this->uploads->errors);
			}
			else
			{
				// tidy tags
				$tags = '';
				if ($this->input->post('tags'))
				{
					foreach (explode(',', $this->input->post('tags')) as $tag)
					{
						$tags[] = ucwords(trim(strtolower(str_replace('-', ' ', $tag))));
					}
					$tags = implode(', ', $tags);
				}
				
				// set tags
				$this->core->set['tags'] = $tags;
				
				// update
				if ($this->core->update('shop_products') && count($_POST))
				{
					// get insert id
					$productID = $this->db->insert_id();
                                        
                                        if(isset($_POST['chk-copy']) && isset($_POST['product_copy_id'])){                                           
                                            $variations = $this->shop->get_variations($_POST['product_copy_id']);
                                            if($variations){
                                                foreach($variations as $vars){
                                                    $varID = $this->shop->add_variation($productID, $vars['type'], $vars['variation'], $vars['price'], $vars['name'],$vars['img']);
                                                }
                                            }
                                        }else{                                      
                                            $varid = 1;
                                            foreach($_POST['variation'] as $k => $a ){

                                                $arr = array();

                                                $ctr =0;
                                                $optctr = 0;
                                                foreach($a['option'] as $b){
                                                    $arr[] = $b;                                              
                                                    $ctr ++;


                                                    if($ctr == 3){
                                                        $ctr = 0;

                                                        if($arr[0] != ''){
                                                            $img = $arr[2];
                                                            if(isset($_FILES['variation']['name'][$k]['img'][$optctr])  && $_FILES['variation']['name'][$k]['img'][$optctr]!= ''){


                                                                $_FILES['userfile']['name']     = $_FILES['variation']['name'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['type']     = $_FILES['variation']['type'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['tmp_name'] = $_FILES['variation']['tmp_name'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['error']    = $_FILES['variation']['error'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['size']     = $_FILES['variation']['size'][$k]['img'][$optctr];  

                                                                if ($imageData = $this->uploads->upload_image(false,'','userfile'))
                                                                {
                                                                        $img = $imageData['file_name'];
                                                                }else{
                                                                    //print_r($this->errors);
                                                                    //exit();
                                                                }


                                                            }

                                                            $varID = $this->shop->add_variation($productID, $varid, $arr[0], $arr[1],$a['name'],$img);
                                                            //echo $_FILES['variation']['name'][$k]['img'][$optctr];

                                                            $optctr ++;


                                                        }
                                                        $arr = array();
                                                    }
                                                }
                                                $varid++;
                                            }
                                        }
                                        
                                        
                                
					// update categories
					$this->shop->update_cats($productID, $this->input->post('catsArray'));
					
					// update tags
					$this->tags->update_tags('shop_products', $productID, $tags);
                    
                    
                    //upload more images
                    if(@$_POST['upload_photo'])
                    {
                        if ($oldFileName = @$_FILES['image']['name'])
                        {
                            $this->uploads->allowedTypes = 'jpg|gif|png';
                           
                 
                                // get image errors if there are any
                               					
                                    // set image ref
                                    if($imageData = $this->uploads->upload_image(TRUE)){
                                      $this->db->set('productID', $productID); 
                                      $this->db->set('imgID', $imgID); 
                                        $this->db->set('image', $imageData['file_name']); 
                                        $this->db->set('img_caption', $this->input->post('img_caption'	)); 											
                                    }
                                    
                                    // update
                                    $this->db->insert('shop_productsImages');
                                    		
                                
                           
                            	
                        }
                    }
                    
					
					// where to redirect to
					redirect($this->redirect);
				}
                                
			}
            
			
			// get categories
			$output['categories'] = $this->shop->get_categories();

			// get products
			$output['files'] = $this->shop->get_files();

			// get bands
			$output['bands'] = $this->shop->get_bands();
			
			// set default stock
			$output['data']['stock'] = 1;
            
            
            
            
		}
                
                
                //getListOfProducts                         
                $listOfProducts = $this->shop->get_all_products();

                $output['listOfProducts'] = '';
                if ($listOfProducts) {
                    $str = "<ul />";

                    foreach ($listOfProducts as $item) {
                        $str .= "<li><input type='radio' name='product_copy_id' value='" . $item['productID'] . "' />" . $item['productName'] . "</li>";
                    }

                    $str .= "</ul>";

                    $output['listOfProducts'] = $str;
                }
                
                
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_product',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function edit_product($productID)
	{
                //die('test');
               
		// check permissions for this page
		if (!in_array('shop_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// required
		$this->core->required = array(
			'productName' => 'Product name'
			//'catalogueID' => array('label' => 'Catalogue ID', 'rules' => 'required|unique[shop_products.catalogueID]|trim')			
		);

		// where
		$objectID = array('productID' => $productID);	

		// get values
		$output['data'] = $this->core->get_values('shop_products', $objectID);	
        
        
		if ($this->input->post('cancel'))
		{			
			redirect($this->redirect);
		}
		else
		{	
			// upload image
			if (@$_FILES['image']['name'] != '')
			{
                                $this->uploads->maxSize = 1000000;
				if ($imageData = $this->uploads->upload_image())
				{
					$this->core->set['imageName'] = $imageData['file_name'];
				}
			}
                        
                         
                        
                        //echo 'test';
                        //print_r($this->uploads->errors);
                        
			// get image errors if there are any
			if ($this->shop->errors)
			{
				$output['errors'] = $this->shop->errors;
			}
			else
			{
				// set stock
				if ($this->input->post('status') == 'O' || ($this->site->config['shopStockControl'] && !$this->input->post('stock')))
				{
					$this->core->set['stock'] = 0;
					$this->core->set['status'] = 'O';
				}
					
				// tidy tags
				$tags = '';
				if ($this->input->post('tags'))
				{
					foreach (explode(',', $this->input->post('tags')) as $tag)
					{
						$tags[] = ucwords(trim(strtolower(str_replace('-', ' ', $tag))));
					}
					$tags = implode(', ', $tags);
				}
				
				// set tags
				$this->core->set['tags'] = $tags;
				
				// update
				if ($this->core->update('shop_products', $objectID) && count($_POST))
				{					                                        
                                        //print_r($_POST);
                                        //print_r($_FILES);
                                        //exit();
                                        
					
					if(isset($_POST['chk-copy']) && isset($_POST['product_copy_id'])){
                                            $this->shop->clear_variations($productID);
                                            
                                            $variations = $this->shop->get_variations($_POST['product_copy_id']);
                                            if($variations){
                                                foreach($variations as $vars){
                                                    $varID = $this->shop->add_variation($productID, $vars['type'], $vars['variation'], $vars['price'], $vars['name'],$vars['img']);
                                                }
                                            }
                                        }else{		
                                            $this->shop->clear_variations($productID);
                                            $varid = 1;

                                            foreach($_POST['variation']  as $k  =>$a){

                                                //print_r($a);

                                                //exit();

                                                $arr = array();

                                                $ctr =0;
                                                $optctr = 0;
                                                foreach($a['option'] as $b){
                                                    $arr[] = $b;                                              
                                                    $ctr ++;
                                                    if($ctr == 3){
                                                        $ctr = 0;

                                                        if($arr[0] != ''){
                                                            $img = $arr[2];
                                                            if(isset($_FILES['variation']['name'][$k]['img'][$optctr])  && $_FILES['variation']['name'][$k]['img'][$optctr]!= ''){


                                                                $_FILES['userfile']['name']     = $_FILES['variation']['name'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['type']     = $_FILES['variation']['type'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['tmp_name'] = $_FILES['variation']['tmp_name'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['error']    = $_FILES['variation']['error'][$k]['img'][$optctr];
                                                                $_FILES['userfile']['size']     = $_FILES['variation']['size'][$k]['img'][$optctr];  

                                                                if ($imageData = $this->uploads->upload_image(false,'','userfile'))
                                                                {
                                                                        $img = $imageData['file_name'];
                                                                }else{
                                                                    //print_r($this->errors);
                                                                    //exit();
                                                                }


                                                            }

                                                            $varID = $this->shop->add_variation($productID, $varid, $arr[0], $arr[1],$a['name'],$img);
                                                            //echo $_FILES['variation']['name'][$k]['img'][$optctr];

                                                            $optctr ++;


                                                        }
                                                        $arr = array();
                                                    }
                                                }
                                                $varid++;
                                            }
                                        }
					//exit();
                    
             
                 
                 
					// update categories
					$this->shop->update_cats($productID, $this->input->post('catsArray'));

					// update tags
					$this->tags->update_tags('shop_products', $productID, $tags);

					// set success message
					$this->session->set_flashdata('success', 'Your changes were saved.');
                                        
					// view page
					if ($this->input->post('view'))
					{
						redirect('/shop/'.$productID.'/'.strtolower(url_title($this->input->post('productName'))));
					}
					else
					{																	
						// where to redirect to
						redirect('/admin/shop/edit_product/'.$productID);
					}
				}
			}	

    $output['productID'] = $data['productID'];	            

			// set message
			if ($message = $this->session->flashdata('success'))
			{
				$output['message'] = '<p>'.$message.'</p>';
			}

			// set image path!
			$image = $this->uploads->load_image($productID, true, true);
			$output['imagePath'] = $image['src'];
			$image = $this->uploads->load_image($productID, false, true);
			$output['imageThumbPath'] = $image['src'];
			
			// get categories
			$output['categories'] = $this->shop->get_categories();
			
			// get categories for this product
			$output['data']['categories'] = $this->shop->get_cats_for_product($productID);
                        //print_r($output['data']['categories']);

			// get variations
			$output['variations'] = $this->shop->get_variations($productID);
			
                        
                        if($output['variations']){
                            $varID = "";
                            $ctr = 0;
                            $arr = array();
                            foreach($output['variations'] as $var){
                                if($var['type'] != $varID){ 
                                    $ctr++;
                                    $arr[$ctr][] =  $var['name'];                                
                                    $varID = $var['type'];
                                }
                                $arr[$ctr][] = $var['variation'].'~~'.$var['price'].'~~'.$var['img'];
                                
                            }
                            
                        }
                        
                        
                        
                        $str = "";
                        if(isset($arr) && is_array($arr)){
                            $ctr=0;
                            
                            foreach($arr as $a){
                                $str .= "<div class='variation-container' id='var-".$ctr."'>";
                                $ctr2 =0;
                                foreach($a as $b){
                                    if($ctr2==0){
                                        $str.= "<div><a class='del-variation' href='javascript:void(0);'>Remove Variation</a></div>";
                                        $str.= "<div class='var-name'><label>Name:</label> <input type='text' value='".$b."' name='variation[".$ctr."][name]' class='v-name formelement'></div><br class='clear' />";
                                        $str.= "<div class='var-options'>
                                                <table class='tbl-options'> 
                                                    <tr><th>Options</th><th>Image</th><th>Price</th></tr>";
                                    }else{
                                        $arrB = explode('~~',$b);
                                        
                                        
                                        $imgField = "<input type='hidden' name='variation[".$ctr."][option][]' value=''>";
                                        if($arrB[2] != ''){
                                            $rand = rand();
                                            $imgField = "<input class='hid-img' type='hidden' name='variation[".$ctr."][option][]' value='".$arrB[2]."'>";
                                            $arrB[2] = "<img style='width: 50px;' src='".  site_url().$this->uploadsPath."/".$arrB[2]."?rand=$rand'>&nbsp;<input type='checkbox' class='chk-del' /> Delete this image on save<br />";
                                            
                                        }
                                        
                                        $str .=         "<tr>   <td>
                                                                    <input type='text' value='".$arrB[0]."' name='variation[".$ctr."][option][]' class='opt-name formelement'>
                                                                    
                                                                </td>
                                                                <td style='text-align:center'>
                                                                    
                                                                    <div class='uploadfile '>     ".$arrB[2]."<input type='file' name='variation[".$ctr."][img][]'> </div>
                                                                </td>
                                                                <td>
                                                                    <input type='text'  value='".$arrB[1]."'  name='variation[".$ctr."][option][]' class='opt-price formelement small'>
                                                                    $imgField    
                                                                    &nbsp;&nbsp;<a class='del-option' hrerf='javascript:void(0);'>Remove</a>
                                                                </td>
                                                         </tr>";
                                    }
                                    $ctr2++;
                                }
                                $str .=         "</table>";
                                $str .=         "</div>";
                                $str .=         "<a class='add-option' href='javascript:void(0);'>Add Option</a>";
                                $str .= "</div>";
                                
                                $ctr++;
                            }
                        }
                        
                        $output['str'] = $str;
                        
			// get bands
			$output['bands'] = $this->shop->get_bands();

			// get products
			$output['files'] = $this->shop->get_files();	
            
           
            
                        
                        //getListOfProducts                         
                        $listOfProducts = $this->shop->get_all_products();
                                                
                        $output['listOfProducts'] = '';
                        if($listOfProducts){
                            $str = "<ul />";
                            
                            foreach($listOfProducts as $item){
                                $str .= "<li><input type='radio' name='product_copy_id' value='".$item['productID']."' />".$item['productName']."</li>";
                            }
                            
                            $str .= "</ul>";
                            
                            $output['listOfProducts'] = $str;
                        }
                        
                 
                        
                        
			// templates
			$this->load->view($this->includes_path.'/header');
			$this->load->view('admin/edit_product',$output);
			$this->load->view($this->includes_path.'/footer');			
		}
	}
    
    //add more images
    function editImages($productID='')
    {
		if(!$productID or !is_numeric($productID)) show_404();

		$output=array();
		// $output['id'] = $id;	
                $output['productID'] = $productID;	
		
		
			$output['images'] = $this->shop->get_images($productID);
		
		
		
		//can add photo
			$output['upload'] = form_hidden('upload',1).form_upload('image',set_value('image'));
			$output['title'] = form_input('title',set_value('title'),"class='formelement'");	
			
		
		
		// $this->core->required = array(
			// 'title' => array('label' => 'Title', 'rules' => 'required|ucfirst'),
		// );
		
		if(count($_POST)){
			//delete photos if something is checked
			
			if(@$_POST['delete_photos'] and @$_POST['imagedata']){
				foreach($_POST['imagedata'] as $key => $val){
					$img =  $this->shop->get_images($productsID, $key);
					$this->db->delete('shop_productsImages', array('imgID' => $key)); 
					$this->uploads->delete_file($img['image']);
				}
				$this->session->set_flashdata('message','Image(s) deleted successfully!');
				redirect(current_url());
			}
			
			if(@$_POST['upload_photo']){
				//upload photos
				//VERIFY IMAGE UPLOAD TOO!
				
				if($oldFileName = @$_FILES['image']['name']){
					
					// $this->uploads->uploadsPath = $this->props->upl_pth;
					$this->uploads->allowedTypes = 'jpg|gif|png';
					// $this->uploads->maxWidth = '5000';
					// $this->uploads->maxHeight = '5000';
					// $this->uploads->maxSize = '5000';
					
					if($imageData = $this->uploads->upload_image(TRUE)){
						$this->db->set('productID', $productID); 
						$this->db->set('img_num', $key); 
						$this->db->set('image', $imageData['file_name']); 
						$this->db->set('img_caption', $this->input->post('img_caption'	)); 
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
						if($this->db->insert('shop_productsImages')){
							$this->session->set_flashdata('message','Image added successfully!');
							
						redirect(current_url());
						}else $output['errors'] = "Image not saved !";
					}					
				}else{
					$this->session->set_flashdata('errors','No image selected!');
					redirect(current_url());
				}
				
			}
		}
		
		$output['message'] = ($this->session->flashdata('message')?$this->session->flashdata('message'):'');
		$output['errors'] = ($this->session->flashdata('errors')?$this->session->flashdata('errors'):'');
		$this->load->view('/admin/editImages/'.$productID,$output);
		
		
	}

  
	function delete_product($productID)
	{
		// check permissions for this page
		if (!in_array('shop_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		if ($this->core->delete('shop_products', array('productID' => $productID)));
		{
			// remove category mappings
			$this->shop->update_cats($productID);

			// where to redirect to
			redirect($this->redirect);
		}
	}
        
        function copy_product($productID)
	{
		// check permissions for this page
		if (!in_array('shop_edit', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		$product = $this->shop->get_product($productID);
                $product['productID'] = '';
                $product['productName'] = $product['productName'].' (Copy)';
                $product['dateCreated'] = date("Y-m-d H:i:s");
                
                $this->db->insert('ha_shop_products', $product); 
                $id = $this->db->insert_id();
                
                
                $cats = $this->shop->get_cats_for_product($productID);
                
                if($cats){
                    foreach($cats as $key => $desc){
                        $arrData['siteID']     = $this->siteID;
                        $arrData['catID']      = $key;
                        $arrData['productID']  = $id;
                        $this->db->insert('ha_shop_catmap', $arrData);
                    }
                }
                
                $variations = $this->shop->get_variations($productID);
                
                if($variations){
                    $arrData = array();
                    foreach($variations as $desc){
                        $arrData['siteID']      = $this->siteID;
                        $arrData['variation']   = $desc['variation'];
                        $arrData['price']       = $desc['price'];
                        $arrData['img']         = $desc['img'];
                        $arrData['type']        = $desc['type'];
                        $arrData['name']        = $desc['name'];
                        $arrData['productID']   = $id;
                        $this->db->insert('ha_shop_variations', $arrData);
                    }
                }
                
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

	function categories()
	{
		// check permissions for this page
		if (!in_array('shop_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
			
		// get parents
		if ($parents = $this->shop->get_category_parents())
		{
			// get children
			foreach($parents as $parent)
			{
				$children[$parent['catID']] = $this->shop->get_category_children($parent['catID']);
			}
		}

		// send data to view
		$output['parents'] = @$parents;
		$output['children'] = @$children;

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/categories',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_cat()
	{
		// check permissions for this page
		if (!in_array('shop_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'catName' => 'Title',
		);

		// populate form
		$output['data'] = $this->core->get_values();
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// set stuff
				$this->core->set['dateModified'] = date("Y-m-d H:i:s");
				$this->core->set['catSafe'] = url_title(strtolower(trim($this->input->post('catName'))));
				
				// update
				if ($this->core->update('shop_cats'))
				{
					redirect('/admin/shop/categories');
				}
			}
		}

		// get parents
		$output['parents'] = $this->shop->get_category_parents();		

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/category_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_cat($catID)
	{
		// check permissions for this page
		if (!in_array('shop_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'catName' => 'Title',
		);

		// where
		$objectID = array('catID' => $catID);

		// get values from version
		$row = $this->shop->get_category($catID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// set stuff
				$this->core->set['dateModified'] = date("Y-m-d H:i:s");
				$this->core->set['catSafe'] = url_title(strtolower(trim($this->input->post('catName'))));
				
				// update
				if ($this->core->update('shop_cats', $objectID))
				{
					redirect('/admin/shop/categories');
				}
			}
		}

		// get parents
		$output['parents'] = $this->shop->get_category_parents();		

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/category_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_cat($catID)
	{
		// check permissions for this page
		if (!in_array('shop_cats', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('catID' => $catID);	
		
		if ($this->core->delete('shop_cats', $objectID))
		{
			// delete sub categories
			$objectID = array('parentID' => $catID);
			
			$this->core->delete('shop_cats', $objectID);
			
			// where to redirect to
			redirect('/admin/shop/categories');
		}		
	}

	function order($field = '')
	{
		$this->core->order(key($_POST), $field);
	}

	function orders($status = 'U')
	{
		// check permissions for this page
		if (!in_array('shop_orders', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
						
		// grab data and display
		$output['orders'] = $this->shop->get_orders($status, NULL, $this->input->post('searchbox'));

		$output['trackingStatus'] = $status;
		$output['statusArray'] = $this->shop->statusArray;

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/orders',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function view_order($transactionID)
	{	
		// check permissions for this page
		if (!in_array('shop_orders', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// set object ID
		$objectID = array('transactionID' => $transactionID);
	
		// get values
		$output['data'] = $this->core->get_values('shop_transactions', $objectID);

		// grab data and display
		$output['order'] = $this->shop->get_order($transactionID);
		$output['transactionID'] = $transactionID;
		
		if (count($_POST))
		{
			// force unpaid uncheckout to paid and send order email
			if (!$output['data']['paid'] && $this->input->post('trackingStatus') != 'N')
			{
				modules::run('shop/shop/_create_order', $output['data']['transactionCode']);
				
				$this->core->set['paid'] = 1;
			}
			elseif ($this->input->post('trackingStatus') == 'N')
			{
				$this->core->set['trackingStatus'] = 'U';
				$this->core->set['paid'] = 0;
			}
			
			// update
			if ($this->core->update('shop_transactions', $objectID))
			{
				if ($this->input->post('trackingStatus') == 'D')
				{
					// set header and footer
					$emailHeader = str_replace('{name}', $output['order']['firstName'].' '.$output['order']['lastName'], $this->site->config['emailHeader']);
					$emailHeader = str_replace('{email}', $output['order']['email'], $emailHeader);
					$emailFooter = str_replace('{name}', $output['order']['firstName'].' '.$output['order']['lastName'], $this->site->config['emailFooter']);
					$emailFooter = str_replace('{email}', $output['order']['email'], $emailFooter);
					$emailDispatch = str_replace('{name}', $output['order']['firstName'].' '.$output['order']['lastName'], $this->site->config['emailDispatch']);
					$emailDispatch = str_replace('{email}', $output['order']['email'], $emailDispatch);
					$emailDispatch = str_replace('{order-id}', '#'.$output['order']['transactionCode'], $emailDispatch);
									
					// send shipping email to customer
					$userBody = $emailHeader."\n\n".$emailDispatch."\n\n";
					$footerBody = $emailFooter;
			
					// load email lib and email user and admin
					$this->load->library('email');
		
					$this->email->to($output['order']['email']);
					$this->email->subject('Your order has been shipped (#'.$output['order']['transactionCode'].')');
					$this->email->message($userBody.$footerBody);
					$this->email->from($this->shop->siteVars['siteEmail'], $this->shop->siteVars['siteName']);			
					$this->email->send();
				}
				
				// set success message
				$this->session->set_flashdata('success', 'Your changes were saved.');
	
				redirect('/admin/shop/view_order/'.$transactionID);
			}
		}

		// set view flag
		if (!$output['order']['viewed'])
		{
			$this->shop->view_order($transactionID);
		}
		
                $group_id = $output['order']['groupID'];
                $output['order']['groupName'] = $this->shop->getGroupName($group_id);
                //print_r($output);
		$output['item_orders'] = $this->shop->get_item_orders($transactionID);
                
                
                
                
		$output['statusArray'] = $this->shop->statusArray;
		
		// set message
		if ($message = $this->session->flashdata('success'))
		{
			$output['message'] = '<p>'.$message.'</p>';
		}		

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/view_order',$output);
		$this->load->view($this->includes_path.'/footer');
	}
	
	function delete_order($transactionID)
	{
		// check permissions for this page
		if (!in_array('shop_orders', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// set object ID
		$objectID = array('transactionID' => $transactionID);
	
		// get values
		$output['data'] = $this->core->get_values('shop_transactions', $objectID);
		
		if ($this->core->delete('shop_transactions', $objectID))
		{
			// where to redirect to
			redirect('/admin/shop/orders');
		}		
	}

	function subscriptions($status = '', $subscriptionID = '')
	{
		// check permissions for this page
		if (!in_array('shop_subscriptions', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// default where
		$where = array();
		if ($status == 'active')
		{
			$where = array('active' => 1);
		}
		elseif ($status == 'cancelled')
		{
			$where['active'] = 0;
		}

		// by subscription ID
		if (intval($subscriptionID))
		{
			$where['subscriptionID'] = $subscriptionID;
		}	

		// search
		if ($this->input->post('q'))
		{
			$where = array('referenceID' => $this->input->post('q'));
		}
		
		// grab data and display
		$output = $this->core->viewall('subscriptions', $where);

		// populate dropdown
		$output['status'] = $status;		

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/subscriptions',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_subscription()
	{	
		// get values
		$output['data'] = $this->core->get_values('subscriptions');
	
		if (count($_POST))
		{	
			// required
			$this->core->required = array(
				'subscriptionName' => array('label' => 'Subscription Name', 'rules' => 'required|unique[subscriptions.subscriptionName]'),
			);
	
			// set stuff
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
			$this->core->set['subscriptionRef'] = trim(strtolower(url_title($this->input->post('subscriptionName'))));
	
			// update
			if ($this->core->update('subscriptions'))
			{
				// where to redirect to
				redirect('/admin/shop/subscriptions');
			}
		}
		
		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/add_subscription', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');		
	}

	function edit_subscription($subscriptionID)
	{	
		// set object ID
		$objectID = array('subscriptionID' => $subscriptionID);

		// get values
		$output['data'] = $this->core->get_values('subscriptions', $objectID);

		if (count($_POST))
		{
			// required
			$this->core->required = array(
				'subscriptionName' => array('label' => 'Subscription Name', 'rules' => 'required|unique[subscriptions.subscriptionName]'),
			);
	
			// set stuff
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");
			$this->core->set['subscriptionRef'] = trim(strtolower(url_title($this->input->post('subscriptionName'))));			

			// update
			if ($this->core->update('subscriptions', $objectID))
			{
				// where to redirect to
				redirect('/admin/shop/subscriptions');
			}
		}	

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/edit_subscription', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');		
	}

	function delete_subscription($objectID)
	{
		// delete subscriptions
		if ($this->core->delete('subscriptions', array('subscriptionID' => $objectID)))
		{
			// where to redirect to
			redirect('/admin/shop/subscriptions');
		}
	}

	function subscribers($status = '', $subscriptionID = '')
	{
		// check permissions for this page
		if (!in_array('shop_subscriptions', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// default where
		$where = array();
		if ($status == 'active')
		{
			$where = array('active' => 1);
		}
		elseif ($status == 'cancelled')
		{
			$where['active'] = 0;
		}

		// by subscription ID
		if (intval($subscriptionID))
		{
			$where['subscriptionID'] = $subscriptionID;
		}		

		// search
		if ($this->input->post('q'))
		{
			$where = array('referenceID' => $this->input->post('q'));
		}
		
		// grab data and display
		$output = $this->core->viewall('subscribers', $where);

		// populate dropdown
		$output['status'] = $status;		

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/subscribers',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function sub_payments($referenceID, $subscriberID)
	{
		// check permissions for this page
		if (!in_array('shop_subscriptions', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// default where
		$where = array('referenceID' => $referenceID);
		
		// grab data and display
		$output = $this->core->viewall('sub_payments', $where);

		// populate dropdown
		$output['status'] = 'all';		

		// get subscription data
		$output['subscriber'] = $this->shop->get_subscriber($subscriberID);

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/sub_payments',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function bands()
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// grab data and display
		$output = $this->core->viewall('shop_bands', NULL, 'multiplier', 99);

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/bands',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_band()
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'bandName' => 'Band Name',
			'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_bands.multiplier]')
		);

		// populate form
		$output['data'] = $this->core->get_values();
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_bands'))
				{
					redirect('/admin/shop/bands');
				}
			}
		}

		// set default
		$output['data']['multiplier'] = 1;

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/band_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_band($bandID)
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'bandName' => 'Band Name',
			'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_bands.multiplier]')
		);

		// where
		$objectID = array('bandID' => $bandID);

		// get values from version
		$row = $this->shop->get_band($bandID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_bands', $objectID))
				{
					redirect('/admin/shop/bands');
				}
			}
		}

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/band_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_band($bandID)
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('bandID' => $bandID);	
		
		if ($this->core->delete('shop_bands', $objectID))
		{
			// where to redirect to
			redirect('/admin/shop/bands');
		}		
	}

	function postages()
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// grab data and display
		$output = $this->core->viewall('shop_postages', NULL, 'total', 99);

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/postages',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_postage()
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'total' => 'total',
			'cost' => 'Cost'
		);

		// populate form
		$output['data'] = $this->core->get_values();
		$output['data']['total'] = '0.00';
		$output['data']['cost'] = '5.00';		
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_postages'))
				{
					redirect('/admin/shop/postages');
				}
			}
		}

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/postage_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_postage($postageID)
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'total' => 'total',
			'cost' => 'Cost'
		);

		// where
		$objectID = array('postageID' => $postageID);

		// get values from version
		$row = $this->shop->get_postage($postageID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_postages', $objectID))
				{
					redirect('/admin/shop/postages');
				}
			}
		}

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/postage_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_postage($postageID)
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('postageID' => $postageID);	
		
		if ($this->core->delete('shop_postages', $objectID))
		{
			// where to redirect to
			redirect('/admin/shop/postages');
		}		
	}

	function modifiers()
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// grab data and display
		$output['shop_modifiers'] = $this->shop->get_modifiers();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/modifiers',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_modifier($bandID = '')
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'modifierName' => 'Name',
			'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_modifiers.multiplier]')
		);

		// populate form
		$output['data'] = $this->core->get_values();
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_modifiers'))
				{
					redirect('/admin/shop/modifiers');
				}
			}
		}

		// set default
		$output['data']['multiplier'] = 1;
		$output['bands'] = $this->shop->get_bands();

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/modifier_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_modifier($modifierID, $bandID = '')
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'modifierName' => 'Name',
			'multiplier' => array('label' => 'Multiplier', 'rules' => 'required|unique[shop_modifiers.multiplier]')
		);

		// where
		$objectID = array('modifierID' => $modifierID);

		// get values from version
		$row = $this->shop->get_modifier($modifierID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_modifiers', $objectID))
				{
					redirect('/admin/shop/modifiers');
				}
			}
		}

		// get bands
		$output['bands'] = $this->shop->get_bands();

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/modifier_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_modifier($modifierID)
	{
		// check permissions for this page
		if (!in_array('shop_shipping', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('modifierID' => $modifierID);	
		
		if ($this->core->delete('shop_modifiers', $objectID))
		{
			// where to redirect to
			redirect('/admin/shop/modifiers');
		}		
	}

	function discounts()
	{
		// check permissions for this page
		if (!in_array('shop_discounts', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// grab data and display
		$output = $this->core->viewall('shop_discounts', NULL, 'expiryDate');

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/discounts',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_discount()
	{
		// check permissions for this page
		if (!in_array('shop_discounts', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'code' => array('label' => 'Code', 'rules' => 'required|unique[shop_discounts.code]|trim'),
			'discount' => 'Discount',
			'expiryDate' => 'Expiry Date'
		);

		// populate form
		$output['data'] = $this->core->get_values();
		
		// deal with post
		if (count($_POST))
		{			
			// set dates
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");		
			$this->core->set['expiryDate'] = date("Y-m-d 23:59:59", strtotime($this->input->post('expiryDate').' 11.59PM'));

			// set object ID
			if ($this->input->post('catID')) $this->core->set['objectID'] = $this->input->post('catID');
			if ($this->input->post('productID') > 0) $this->core->set['objectID'] = implode(',', $this->input->post('productID'));
			
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_discounts'))
				{
					redirect('/admin/shop/discounts');
				}
			}
		}

		// get products
		$output['products'] = $this->shop->get_all_products();

		// get categories
		$output['categories'] = $this->shop->get_categories();		

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/discount_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_discount($discountID)
	{
		// check permissions for this page
		if (!in_array('shop_discounts', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// required fields
		$this->core->required = array(
			'code' => array('label' => 'Code', 'rules' => 'required|unique[shop_discounts.code]|trim'),
			'discount' => 'Discount',
			'expiryDate' => 'Expiry Date'
		);

		// where
		$objectID = array('discountID' => $discountID);

		// get values from version
		$row = $this->shop->get_discount($discountID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{	
			// set dates
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");	
			$this->core->set['expiryDate'] = date("Y-m-d 23:59:59", strtotime($this->input->post('expiryDate').' 11.59PM'));

			// set object ID
			if ($this->input->post('catID') > 0) $this->core->set['objectID'] = $this->input->post('catID');
			if ($this->input->post('productID') > 0) $this->core->set['objectID'] = implode(',', $this->input->post('productID'));
			
			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_discounts', $objectID))
				{
					redirect('/admin/shop/discounts');
				}
			}
		}

		// get products
		$output['products'] = $this->shop->get_all_products();

		// get categories
		$output['categories'] = $this->shop->get_categories();

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/discount_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_discount($discountID)
	{
		// check permissions for this page
		if (!in_array('shop_discounts', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('discountID' => $discountID);	
		
		if ($this->core->delete('shop_discounts', $objectID))
		{
			// where to redirect to
			redirect('/admin/shop/discounts');
		}		
	}

	function reviews()
	{
		// check permissions for this page
		if (!in_array('shop_reviews', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// grab data and display
		$output['reviews'] = $this->shop->get_reviews();

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/reviews',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function approve_review($reviewID)
	{
		// check permissions for this page
		if (!in_array('shop_reviews', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		if ($this->shop->approve_review($reviewID))
		{
			redirect('/admin/shop/reviews');
		}
	}

	function delete_review($objectID)
	{
		// check permissions for this page
		if (!in_array('shop_reviews', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// check permissions for this page
		if (!in_array('shop_delete', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		if ($this->core->delete('shop_reviews', array('reviewID' => $objectID)))
		{
			// where to redirect to
			redirect('/admin/shop/reviews/');
		}
	}
	
	function upsells()
	{
		// check permissions for this page
		if (!in_array('shop_upsells', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// grab data and display
		$output = $this->core->viewall('shop_upsells', NULL, 'upsellOrder', 99);

		$this->load->view($this->includes_path.'/header');
		$this->load->view('admin/upsells',$output);
		$this->load->view($this->includes_path.'/footer');
	}

	function add_upsell()
	{
		// check permissions for this page
		if (!in_array('shop_upsells', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// populate form
		$output['data'] = $this->core->get_values();
		
		// deal with post
		if (count($_POST))
		{
			if ($this->core->check_errors())
			{
				// set dates
				$this->core->set['dateCreated'] = date("Y-m-d H:i:s");
	
				// set product IDs
				if ($this->input->post('productIDs') > 0) $this->core->set['productIDs'] = implode(',', $this->input->post('productIDs'));
				
				// update
				if ($this->core->update('shop_upsells'))
				{
					redirect('/admin/shop/upsells');
				}
			}
		}

		// get products
		$output['products'] = $this->shop->get_all_products();

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/upsell_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function edit_upsell($upsellID)
	{
		// check permissions for this page
		if (!in_array('shop_upsells', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}

		// where
		$objectID = array('upsellID' => $upsellID);

		// get values from version
		$row = $this->shop->get_upsell($upsellID);

		// populate form
		$output['data'] = $this->core->get_values($row);
		
		// deal with post
		if (count($_POST))
		{
			// set dates
			$this->core->set['dateCreated'] = date("Y-m-d H:i:s");

			// set product IDs
			if ($this->input->post('productIDs') > 0) $this->core->set['productIDs'] = implode(',', $this->input->post('productIDs'));

			if ($this->core->check_errors())
			{							
				// update
				if ($this->core->update('shop_upsells', $objectID))
				{
					redirect('/admin/shop/upsells');
				}
			}
		}
		
		// get products
		$output['products'] = $this->shop->get_all_products();

		// templates
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/header');
		$this->load->view('admin/upsell_form', $output);
		if (!$this->core->is_ajax()) $this->load->view($this->includes_path.'/footer');
	}

	function delete_upsell($upsellID)
	{
		// check permissions for this page
		if (!in_array('shop_upsells', $this->permission->permissions))
		{
			redirect('/admin/dashboard/permissions');
		}
				
		// where
		$objectID = array('upsellID' => $upsellID);	
		
		if ($this->core->delete('shop_upsells', $objectID))
		{
			// where to redirect to
			redirect('/admin/shop/upsells');
		}		
	}

	function renew_downloads($transactionID)
	{
		if ($this->shop->renew_downloads($transactionID))
		{
			// set success message
			$this->session->set_flashdata('success', 'The expiry date for downloads on this order has been renewed for another 5 days.');

			// where to redirect to
			redirect('/admin/shop/view_order/'.$transactionID);
		}
	}
	
	function export_orders()
	{
		// export orders as CSV
		$this->load->dbutil();

		$query = $this->shop->export_orders();
		
		$csv = $this->dbutil->csv_from_result($query); 
		
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: application/force-download");
		header("Content-Type: application/octet-stream");
		header("Content-Length: " .(string)(strlen($csv)));
		header("Content-Disposition: attachment; filename=shop-orders-".date('U').".csv");
		header("Content-Description: File Transfer");
		
		$this->output->set_output($csv);
	}

	function ac_products()
	{	
		$q = strtolower($_POST["q"]);
		if (!$q) return;
		
		// form dropdown
		$results = $this->shop->get_products(NULL, $q);
		
		// go foreach
		foreach((array)$results as $row)
		{
			$items[$row['catalogueID']] = $row['productName'];
		}
		
		// output
		$output = '';
		foreach ($items as $key=>$value)
		{
			$output .= "$key|$value\n";
		}
		
		$this->output->set_output($output);
	}
	
	function ac_orders()
	{	
		$q = strtolower($_POST["q"]);
		if (!$q) return;
		
		// form dropdown
		$results = $this->shop->get_orders(NULL, NULL, $q);
		
		// go foreach
		foreach((array)$results as $row)
		{
			$items[$row['transactionCode']] = trim($row['firstName'].' '.$row['lastName']);
		}
		
		// output
		$output = '';
		foreach ($items as $key=>$value)
		{
			$output .= "$key|$value\n";
		}
		
		$this->output->set_output($output);
	}
	
	function ac_subscribers()
	{	
		$q = strtolower($_POST["q"]);
        if (!$q) return;

        // form dropdown
        $results = $this->shop->get_subscribers($q);

        // go foreach
        foreach((array)$results as $row)
        {
            $items[$row['referenceID']] = $row['email'];
        }

        foreach ($items as $key=>$value) {
			/* If you want to force the results to the query
			if (strpos(strtolower($key), $tags) !== false)
			{
				echo "$key|$id|$name\n";
			}*/
			$this->output->set_output("$key|$value\n");
        }
	}
    
    
}