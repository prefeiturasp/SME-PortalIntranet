<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma\Mobile;

class ArchiveOrganogramaSecretarioMobile extends ArchiveOrganogramaMobile
{
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		// Secretario
		$this->setTaxonomyName(self::SLUG_TAXONOMIA_SECRETARIO);
		// Adjunto
		$this->setTaxonomyAdj(self::SLUG_TAXONOMIA_ADJUNTO);
		// card shadow-sm border-0 bg-azul-escuro text-white text-center font-weight-bold mb-0
		$classes_divs_secretario = array('card shadow-sm border-0 bg-azul-escuro text-white text-center font-weight-bold mb-0', 'card-body');
		$classe_p_secretario = 'card-text';
		$this->abreDivs(null, $classes_divs_secretario);
		$this->montaHtmlNameTaxonomy($classe_p_secretario);
		$this->fechaDivs($classes_divs_secretario);
	}
}