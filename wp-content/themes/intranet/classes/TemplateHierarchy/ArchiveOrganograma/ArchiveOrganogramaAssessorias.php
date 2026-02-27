<?php
namespace Classes\TemplateHierarchy\ArchiveOrganograma;


class ArchiveOrganogramaAssessorias extends ArchiveOrganograma
{

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		// Assessorias
		$divs_externas_assessorias = array('card-deck justify-content-center mt-5 borda-itens borda-azul-organo');
		$divs_internas_assessorias = array('card shadow-sm border-0 bg-azul-organo borda-conexao', 'card-body d-flex justify-content-center');
		$classe_p_assessoria = 'card-text text-white text-center font-weight-bold align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_assessorias, $divs_internas_assessorias, $classe_p_assessoria, $this->montaQueryItensTaxonomias('assessorias'));

	}

}