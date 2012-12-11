<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Entries_model extends CI_Model {
	
	public function __construct()
	{
		parent::__construct();
	}
	
	public function get_assigned_entries($field_name = FALSE, $channel, $member_id = FALSE)
	{
		if(!preg_match('/^\d*$/', $channel))
		{
			$channel = $this->channel_data->get_channel_by_name($channel)->row('channel_id');	
		}
		
		if(!$member_id)
		{
			$member_id = $this->session->userdata('member_id');
		}
				
		$sql = array();
		
		$sql[] = 'exp_channel_data.channel_id = '.$channel;
		$sql[] = 'AND exp_channel_data.site_id = '.config_item('site_id');
		
		if($field_name)
		{
			$field = $this->channel_data->get_field_by_name($field_name)->row();
			
			if(isset($field->field_id))
			{
				$sql[] = ' AND (field_id_'.$field->field_id.' LIKE \'%'.$member_id.'|%\' OR';
				$sql[] = ' field_id_'.$field->field_id.' LIKE \'%|'.$member_id.'\' OR';
				$sql[] = ' field_id_'.$field->field_id.' LIKE \''.$member_id.'\')';
				$sql[] = ' OR exp_channel_data.channel_id = '.$channel;
				$sql[] = ' AND exp_channel_data.site_id = '.config_item('site_id');
			}
		}
					
		$sql[] = 'AND author_id = '.$member_id;
		
		$sql   = rtrim(ltrim(implode(' ', $sql), 'OR'), 'AND');
		
		$this->db->select('channel_data.entry_id, author_id');
		$this->db->where($sql, NULL, FALSE);
		$this->db->join('channel_titles', 'channel_data.entry_id = channel_titles.entry_id');
		
		return $this->db->get('channel_data');
	}
	
	public function get_category_ids($type, $value)
	{
		$params = array(
			'select' => 'cat_id',
			'where'  => array(
				'or '.$type => explode('|', $value)
			)
		);
		
		$data = $this->channel_data->get_categories($params);
		
		$category_ids = array();
		
		foreach($data->result() as $row)
		{
			$category_ids[] = $row->cat_id;
		}
		
		return implode('|', $category_ids);
	}
	
}