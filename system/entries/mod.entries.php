<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Entries
 * 
 * @package		Entries
 * @author		Justin Kimbrell
 * @copyright	Copyright (c) 2012, Justin Kimbrell
 * @link 		http://www.objectivehtml.com/
 * @version		0.1.0
 * @build		20121114
 */
 
class Entries {
	
	private $lib;
	
	public function __construct()
	{
		$this->EE =& get_instance();	
		
		$this->EE->load->library('entries_lib');
				
		$this->lib =& $this->EE->entries_lib;
	}
		
	public function assigned_to_member()
	{
		$channel = $this->param('from_channel', FALSE, FALSE);
		$entries = $this->EE->entries_model->get_assigned_entries($this->param('from_field', FALSE, FALSE, TRUE), $channel, $this->param('from_author_id', $this->EE->session->userdata('member_id'), FALSE));
		
		if($entries->num_rows() == 0)
		{
			return $this->EE->TMPL->no_results();
		}
		
		$entry_ids = array();
		
		foreach($entries->result() as $entry)
		{
			$entry_ids[] = $entry->entry_id;	
		}
		
		return $this->lib->entries(array(
			'channel'  => $this->param('channel', ''),
			'entry_id' => implode($entry_ids, '|'),
			'dynamic'  => 'no',
			'disable'  => 'disable="member_data|categories|category_fields|pagination"'
		));
	}
	
	public function assigned_to_me()
	{
		$member_id = $this->EE->session->userdata('member_id');
		
		if(!$member_id)
		{
			return $this->EE->TMPL->no_results();
		}
		
		$this->lib->set_param('from_author_id', $member_id);
		
		return $this->assigned_to_member();
	}
	
	public function by_field()
	{
		$fields = $this->EE->channel_data->utility->reindex($this->EE->channel_data->get_fields()->result_array(), 'field_name');
		
		$where = array();
			
		foreach($this->lib->get_params() as $index => $value)
		{
			if($channel = $this->param('channel'))
			{
				$channel = $this->EE->channel_data->get_channel_by_name($channel);
				
				if($channel->num_rows() == 1)
				{
					$channel = $channel->row('channel_id');
				}
			}
			
			if(preg_match('/^field:/', $index))
			{
				$index = preg_replace('/^field:/', '', $index);
				
				if(isset($fields[$index]))
				{
					$index = 'field_id_'.$fields[$index]['field_id'];
				}
				
				$where[$index] = $value;
			}
		}
		
		$entries = $this->EE->channel_data->get_channel_entries($channel, array(
			'where' => $where
		));
		
		if($entries->num_rows() == 0)
		{
			return $this->EE->TMPL->no_results();
		}
		
		$entry_ids = array();
		
		foreach($entries->result() as $entry)
		{
			$entry_ids[] = $entry->entry_id;	
		}
		
		return $this->lib->entries(array(
			'entry_id' => implode('|', $entry_ids)
		));	
	}
	
	public function ids_assigned_to_member()
	{
		$channel = $this->param('channel', FALSE, FALSE, TRUE);
		$entries = $this->EE->entries_model->get_assigned_entries($this->param('field', FALSE, FALSE, TRUE), $channel, $this->param('member_id'));
	
		$entry_ids = array();
		
		foreach($entries->result() as $entry)
		{
			$entry_ids[] = $entry->entry_id;	
		}
		
		return implode($entry_ids, '|');
	}
	
	public function ids_assigned_to_me()
	{
		$member_id = $this->EE->session->userdata('member_id');
		
		if(!$member_id)
		{
			return $this->EE->TMPL->no_results();
		}
		
		$this->lib->set_param('member_id', $member_id);
		
		return $this->ids_assigned_to_member();
	}
	
	/*
	public function ids_from_field()
	{
		$channel = $this->param('from_channel', FALSE, FALSE);
		$entries = $this->EE->entries_model->get_assigned_entries($this->param('from_field', FALSE, FALSE, TRUE), $channel, '');
	
		$entry_ids = array();
		
		foreach($entries->result() as $entry)
		{
			$entry_ids[] = $entry->entry_id;	
		}
		
		return implode($entry_ids, '|');
	}
	*/
	
	public function get()
	{
		return $this->lib->entries();
	}
	
	public function by_category()
	{		
		$category = $this->_fetch_category_ids();
		
		if($category)
		{
			$this->lib->set_param('category', $category);
		}
		
		return $this->lib->entries();
	}	
	
	public function profile($override = array(), $reset_params = FALSE)
	{
		if($reset_params)
		{
			$this->lib->reset_params();
		}
		
		$params = array(
			'channel'   => $this->param('channel', 'members'),
			'author_id' => $this->param('author_id', 'CURRENT_USER'),
			'limit'     => $this->param('limit', 1),
			'dynamic'   => $this->param('dynamic', 'no')
		);
		
		$this->lib->set_params($params);
		
		$return = $this->lib->entries();
		
		if($reset_params)
		{
			$this->lib->restore_params();
		
		}
		
		return $return;
	}	
	
	public function profile_category_ids()
	{
		return $this->my_category_ids();
	}
	
	public function reverse_related()
	{
		$this->lib->set_param('reverse', TRUE);
		
		return $this->related();
	}
	
	public function related()
	{
		$entry_id   = $this->param('rel_entry_id', FALSE, FALSE, TRUE);
		
		if(!$reverse = $this->param('reverse', $this->param('reverse_relationships'), TRUE, FALSE))
		{
			$related_entries = $this->EE->channel_data->get_related_entries($entry_id);
		}
		else
		{
			$related_entries = $this->EE->channel_data->get_related_child_entries($entry_id);
		}
		
		if($related_entries->num_rows() == 0)
		{
			return $this->EE->TMPL->no_results();
		}	
		
		$entry_ids = array();
		
		foreach($related_entries->result() as $row)
		{
			if(!$reverse)
			{
				$entry_ids[] = $row->rel_child_id;
			}
			else
			{
				$entry_ids[] = $row->rel_parent_id;
			}
		}		
				
		$params = array(
			'channel'  => $this->param('channel'),
			'entry_id' => implode($entry_ids, '|'),
			'dynamic'  => 'no',
			'orderby'  => 'start',
			'disable'  => 'disable="member_data|categories|category_fields|pagination"'
		);
		
		$this->lib->set_params($params);
		
		return $this->lib->entries();
	}
		
	public function my_category_ids($override = array())
	{
		$this->lib->reset_no_results();
		$this->lib->reset_tagdata();
		
		$this->lib->set_tagdata('{category_ids}');
		
		$category = $this->_fetch_category_ids();
		
		if($category)
		{
			$return = $category;
		}
		else
		{
			$return = $this->profile($override, TRUE);
		}
		
		$this->lib->restore_no_results();
		$this->lib->restore_tagdata();
		
		return $return;
	}
		
	public function my_category_entries()
	{	
		$category_ids = $this->my_category_ids();
			
		$this->lib->set_param('dynamic', $this->param('dynamic', 'no'));
		
		return $this->lib->entries(array(
			'category' => $category_ids
		));	
	}
	
	public function my_entries()
	{
		if(!$this->EE->session->userdata('member_id'))
		{
			return $this->EE->TMPL->no_results();
		}
		
		return $this->lib->entries(array(
			'author_id' => 'CURRENT_USER'
		));	
	}
	
	private function _fetch_category_ids()
	{
		$category = FALSE;
		
		if($category_id = $this->param('category_id', $this->param('cat_id')))
		{
			$category = $category_id;
		}
		
		$cols = array(
			'cat_name'      => 'category_name',
			'cat_url_title' => 'category_url_title',
			'parent_id'     => 'category_parent_id'
		);
		
		foreach($cols as $col => $alias)
		{
			if($var = $this->param($col, $this->param($alias)))
			{
				$category = $this->EE->entries_model->get_category_ids($col, $var);
			}
		}
		
		return $category;
	}
		
	private function parse($vars, $tagdata = FALSE)
	{
		if($tagdata === FALSE)
		{
			$tagdata = $this->EE->TMPL->tagdata;
		}
			
		return $this->EE->TMPL->parse_variables($tagdata, $vars);
	}
	
	private function param($param, $default = FALSE, $boolean = FALSE, $required = FALSE)
	{
		$name	= $param;
		$param 	= $this->EE->TMPL->fetch_param($param);
		
		if($required && !$param) show_error('You must define a "'.$name.'" parameter in the '.__CLASS__.' tag.');
			
		if($param === FALSE && $default !== FALSE)
		{
			$param = $default;
		}
		else
		{				
			if($boolean)
			{
				$param = strtolower($param);
				$param = ($param == 'true' || $param == 'yes') ? TRUE : FALSE;
			}			
		}
		
		return $param;			
	}
}