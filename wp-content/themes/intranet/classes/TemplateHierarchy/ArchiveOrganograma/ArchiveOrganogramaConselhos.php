<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma;


class ArchiveOrganogramaConselhos extends ArchiveOrganograma
{

	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		// Conselhos
		$divs_externas_conselhos = array('card-deck');
		$divs_internas_conselhos = array('card shadow-sm', 'card-body d-flex justify-content-center');
		$classe_p_conselhos = 'card-text font-weight-bold text-center align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_conselhos, $divs_internas_conselhos, $classe_p_conselhos, $this->montaQueryItensTaxonomias('conselhos'));

	}

}