<?php

namespace Classes\TemplateHierarchy\Search;


class GetTipoDePost
{
	public function __construct()
	{
		$this->getTipoDePost();
	}

	public function getTipoDePost(){
		if (isset($_GET['tipo']) && $_GET['tipo'] == 'post'){
			new LoopSearchSingle();
		}else{
			new LoopSearch();
		}
	}

}