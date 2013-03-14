<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class My_category_entries_channel_search_rule extends Base_rule {
	
	protected $title = 'My Category Entries Search';
	
	protected $description = 'If you store member profiles in channel entries and assign a categories to those member profiles, this search modifier will ensure only entries assigned to those same categories return in the results.';
	
	protected $name = 'category_entries_search';
	
	protected $fields = array(
		'member_channel' => array(
			'label'       => 'Members Channel',
			'description' => 'Enter the name of the members channel',
			'id'          => 'member_channel',
		),
		'order_by' => array(
			'label'       => 'Order By',
			'description' => 'If the member has multiple profiles, you may select the field to order the profiles so the correct one is used.',
			'id'          => 'order_by',
			'type'		  => 'select',
			'settings'	  => array(
				'options' => array(
					'title'           => 'Title',
					'entry_id'        => 'Entry ID',
					'entry_date'      => 'Entry Date',
					'expiration_date' => 'Expiration Date',
				) 
			)
		),
		'sort' => array(
			'label'       => 'Sort',
			'description' => 'If the member has multiple profiles, you may change the way they are sorted.',
			'id'          => 'sort',
			'type'		  => 'select',
			'settings'	  => array(
				'options' => array(
					'asc'  => 'ASC',
					'desc' => 'DESC',
				) 
			)
		),
		'exclude_categories' => array(
			'label' => 'Exclude Categories',
			'description' => 'Enter the names of the categories you wish to exclude from the search.',
			'id'    => 'exclude_categories',
			'type'	=> 'matrix',
			'settings' => array(
				'columns' => array(
					0 => array(
						'name'  => 'category_name',
						'title' => 'Category Name'
					)
				),
				'attributes' => array(
					'class'       => 'mainTable padTable',
					'border'      => 0,
					'cellpadding' => 0,
					'cellspacing' => 0
				)
			)
		)
	);
	
	public function __construct($properties = array())
	{
		parent::__construct($properties);
	}
	
	public function get_from()
	{
		$EE =& get_instance();
		
		$rules     = $this->settings->rules;
		$channel   = $this->channels[$rules->member_channel];
		$exclude   = $rules->exclude_categories;
		$member_id = $EE->session->userdata('member_id');
		
		if(!isset($this->channels[$rules->member_channel]))
		{
			return;
		}
		
		$profile = $EE->channel_data->get_channel_entries($channel->channel_id, array(
			'where' => array(
				'author_id' => $member_id
			),
			'order_by' => $rules->order_by,
			'sort'     => $rules->sort
		));
		
		if($profile->num_rows() == 0)
		{
			return;
		}
		
		$profile = $profile->row();
			
		$category_posts = $EE->channel_data->get_category_posts(array(
			'where' => array(
				'entry_id' => $profile->entry_id
			)
		))->result();
		
		$cat_where = array();
		
		foreach($category_posts as $index => $row)
		{
			$cat_where[$row->cat_id] = $row->cat_id;
			
			//$cat_where[$row->cat_id] = 'cat_id = '.$EE->db->escape($row->cat_id);
		}
		
		if(count($cat_where) == 0)
		{
			return;
		}
		
		$category_post_array;
		
		$category_posts = $EE->channel_data->get_category_posts(array(
			'where' => array(
				'entry_id' => $profile->entry_id
			)
		))->result();
		
		foreach($category_posts as $row)
		{
			$category_post_array[] = $row->cat_id;
		}		
		
		$category_data = $EE->channel_data->get_categories(array(
			'where' => array(
				'group_id' => $channel->cat_group,
				'site_id'  => config_item('site_id')
			)
		));
		
		$categories = array();
		
		if($category_data->num_rows() > 0)
		{
			$categories = $EE->channel_data->utility->reindex('cat_name', $category_data->result());
		}
		
		foreach($exclude as $cat)
		{
			if(isset($categories[$cat->category_name]))
			{
				if(array_key_exists($categories[$cat->category_name]->cat_id, $cat_where))
				{
					unset($cat_where[$categories[$cat->category_name]->cat_id]);
				}
			}
		}
		
		return array(
			'(SELECT distinct entry_id, COUNT(cat_id) AS cat_count, cat_id, cat_id as \'category_id\', GROUP_CONCAT(cat_id SEPARATOR \'|\') as \'cat_ids\', GROUP_CONCAT(cat_id SEPARATOR \'|\') as \'category_ids\', exp_categories.cat_name, exp_categories.cat_name as \'category_name\', exp_categories.cat_url_title, exp_categories.cat_url_title as \'category_url_title\', exp_categories.parent_id as \'cat_parent_id\', exp_categories.parent_id as \'category_parent_id\', exp_categories.site_id as \'cat_site_id\', exp_categories.site_id as \'category_site_id\', exp_categories.group_id as \'cat_group_id\', exp_categories.group_id as \'category_group_id\', exp_categories.cat_description as \'cat_description\', exp_categories.cat_description as \'category_description\', exp_categories.cat_image as \'cat_image\', exp_categories.cat_image as \'category_image\', GROUP_CONCAT(exp_categories.cat_name  SEPARATOR \'|\') as \'cat_names\', GROUP_CONCAT(exp_categories.cat_name  SEPARATOR \'|\') as \'category_names\',  GROUP_CONCAT(exp_categories.cat_url_title  SEPARATOR \'|\') as \'cat_url_titles\', GROUP_CONCAT(exp_categories.cat_url_title  SEPARATOR \'|\') as \'category_url_titles\', GROUP_CONCAT(exp_categories.parent_id  SEPARATOR \'|\') as \'cat_parent_ids\', GROUP_CONCAT(exp_categories.parent_id  SEPARATOR \'|\') as \'category_parent_ids\', GROUP_CONCAT(exp_categories.cat_description  SEPARATOR \'|\') as \'cat_descriptions\', GROUP_CONCAT(exp_categories.cat_description  SEPARATOR \'|\') as \'category_descriptions\',  GROUP_CONCAT(exp_categories.group_id SEPARATOR \'|\') as \'cat_group_ids\', GROUP_CONCAT(exp_categories.group_id SEPARATOR \'|\') as \'category_group_ids\', GROUP_CONCAT(exp_categories.site_id  SEPARATOR \'|\') as \'cat_site_ids\', GROUP_CONCAT(exp_categories.site_id  SEPARATOR \'|\') as \'category_site_ids\', GROUP_CONCAT(exp_categories.cat_image  SEPARATOR \'|\') as \'cat_images\',  GROUP_CONCAT(exp_categories.cat_image  SEPARATOR \'|\') as \'category_images\'
			    FROM exp_category_posts 
			    INNER JOIN exp_categories USING (cat_id)
			    WHERE cat_id IN ('. implode(', ', $cat_where) .')
			    GROUP BY entry_id
			    HAVING cat_count >= 1
			) cc
			INNER JOIN
		    	exp_channel_data
		  	USING (entry_id)'
		);
	}
	
	public function get_select()
	{
		return array(
			'cc.*'
		);
	}
	
	public function get_where()
	{
	}
}