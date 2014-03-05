<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Authenticate
 * 
 * @package		Authenticate
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Justin Kimbrell
 * @link 		http://www.objectivehtml.com/authenticate
 * @version		1.2.3
 * @build		20120911
 */
 
require_once PATH_THIRD . 'entries/config/config.php';

class Entries_ext {

    public $name       		= 'Entries';
    public $version        	= ENTRIES_VERSION;
    public $description    	= '';
    public $settings_exist 	= 'n';
  	public $docs_url       	= 'http://www.objectivehtml.com/';
	public $settings 		= array();
	public $required_by 	= array('module');
			
	public function __construct()
	{
	   	$this->EE =& get_instance();

        $this->settings = array();
	}
		
	public function channel_entries_row($obj, $row)
	{
		$entry_id = $row['entry_id'];
		$cats     = array();
		
		if(!empty($obj->categories[$entry_id]))
		{
			foreach($obj->categories[$entry_id] as $index => $cat)
			{
				$cats[] = $cat[0];
			}
		}
				
		$row['total_categories'] = count($cats);
		$row['category_ids']     = trim(implode('|', $cats));
		
		return $row;
	}
		 
	/**
	 * Activate Extension
	 *
	 * This function enters the extension into the exp_extensions table
	 *
	 * @return void
	 */
	function activate_extension()
	{	    
	    return TRUE;
	}
	
	/**
	 * Update Extension
	 *
	 * This function performs any necessary db updates when the extension
	 * page is visited
	 *
	 * @return  mixed   void on update / false if none
	 */
	function update_extension($current = '')
	{
	    if ($current == '' OR $current == $this->version)
	    {
	        return FALSE;
	    }
	
	    if ($current < '1.0')
	    {
	        // Update to version 1.0
	    }
	
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->update('extensions', array('version' => $this->version));
	}
	
	/**
	 * Disable Extension
	 *
	 * This method removes information from the exp_extensions table
	 *
	 * @return void
	 */
	function disable_extension()
	{
	    $this->EE->db->where('class', __CLASS__);
	    $this->EE->db->delete('extensions');
	}
	
}