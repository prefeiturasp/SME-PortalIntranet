<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma\Mobile;

class ArchiveOrganogramaConselhosMobile extends ArchiveOrganogramaMobile
{

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		// Conselhos
		$divs_externas_conselhos = array(null);
		$divs_internas_conselhos = array('card shadow-sm border-secondary', 'card-body d-flex justify-content-center');
		$classe_p_conselhos = 'card-text font-weight-bold text-center align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_conselhos, $divs_internas_conselhos, $classe_p_conselhos, $this->montaQueryItensTaxonomias('conselhos'));

	}

}