<?php
namespace Classes\TemplateHierarchy\ArchiveOrganograma\Mobile;

class ArchiveOrganogramaAssessoriasMobile extends ArchiveOrganogramaMobile
{

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		// Assessorias
		$divs_externas_assessorias = array('card-deck shadow-sm p-3 rounded justify-content-center mt-3');
		$divs_internas_assessorias = array('card shadow-sm border-0 bg-azul-organo mt-0', 'card-body d-flex justify-content-center');
		$classe_p_assessoria = 'card-text text-white text-center font-weight-bold align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_assessorias, $divs_internas_assessorias, $classe_p_assessoria, $this->montaQueryItensTaxonomias('assessorias'));

	}

}