<?php

namespace Classes\TemplateHierarchy\Search;


class LoopSearchSingle extends LoopSearch
{

	public function __construct()
	{
		parent::__construct();
	}

	public function montaQuerySearch()
	{
		$this->argsSearch = array(
			'post_type' => array('post'),
			'post_parent' => 0,
			'paged' => get_query_var('paged'),
			's' => $this->search,
		);
		$this->querySearch = new \WP_Query($this->argsSearch);
	}

}