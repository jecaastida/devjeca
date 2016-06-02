<?php 

class Banners_model extends CI_Model {
	
	function __construct()
	{
		parent::__construct();

		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}


	}

	function shqytdchcbdjk()
	{		
		// default where
		$this->db->where(array('siteID' => $this->siteID, 'deleted' => 0));

		// where parent is set
		// $this->db->where('parentID', 0); 
		
		$this->db->order_by('offerCode', 'asc');
		
		$query = $this->db->get('introoffers_offers');
		
		if ($query->num_rows())
		{
			return $query->result_array();
		}
		else
		{
			return FALSE;
		}		
	}

}
