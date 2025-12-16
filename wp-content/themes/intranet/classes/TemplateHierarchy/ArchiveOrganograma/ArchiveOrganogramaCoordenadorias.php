<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma;


class ArchiveOrganogramaCoordenadorias extends ArchiveOrganograma
{
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$divs_coordenadoria = array('lista-coordenadorias');
		$this->abreDivs(null, $divs_coordenadoria);
		$this->getDadosCoordenadorias();
		$this->fechaDivs($divs_coordenadoria);
	}

	public function getDadosCoordenadorias(){

		$termos = get_terms(array(
			'taxonomy' => self::TAXONOMIA,
			'post_type' => self::CPT,
			'parent' => self::$id_taxonomia_coordenadorias,
			'hide_empty' => false
		)); // ID Taxonomia Coordenadorias

		foreach ($termos as $termo){

			$this->setTaxonomyName($termo->slug);

			$classes_divs_coordenadorias_01 = array('coordenadoria position-relative');
			$this->abreDivs(null, $classes_divs_coordenadorias_01);

			$classes_divs_coordenadorias_02 = array('card-deck justify-content-center mt-5', 'card shadow-sm border-0 bg-cinza-escuro-organo btn-block mb-2');
			$classe_h2_coordenadorias = 'mt-2 mb-2 text-center fonte-catorze';
			$this->abreDivs(null, $classes_divs_coordenadorias_02);

			$classes_divs_coordenadorias_03 = array('card-header');
			$this->abreDivs('heading'.$this->getTaxonomyTermId(), $classes_divs_coordenadorias_03);

			$this->montaHtmlNameTaxonomyFilhas($classe_h2_coordenadorias);

			$this->fechaDivs($classes_divs_coordenadorias_03);

			$this->getContatos();

			$this->fechaDivs($classes_divs_coordenadorias_02);

			$this->getItensCoordenadorias($termo->slug);

			$this->fechaDivs($classes_divs_coordenadorias_01);
		}
	}

	public function getItensCoordenadorias($termo_slug){

		if ($this->verificaSeExistemItensTaxonomias($termo_slug) == 1) {
			$divs_externas_coordenadorias = array('card-deck margin-right-negativa-16 centraliza-itens mt-5 borda-itens borda-cinza-claro-organo');

		}elseif ($this->verificaSeExistemItensTaxonomias($termo_slug) == 3){
			$divs_externas_coordenadorias = array('card-deck margin-right-negativa-13 centraliza-itens mt-5 borda-itens borda-cinza-claro-organo');

		}elseif($this->verificaSeExistemItensTaxonomias($termo_slug) == 0){
			$divs_externas_coordenadorias = array('card-deck centraliza-itens pb-5 borda-cinza-claro-organo');
		}else{
			$divs_externas_coordenadorias = array('card-deck centraliza-itens mt-5 borda-itens borda-cinza-claro-organo');

		}
		$divs_internas_coordenadorias = array('card shadow-sm border-0 bg-cinza-claro-organo borda-conexao', 'card-body d-flex justify-content-center');
		$classe_h2_coordenadorias = 'card-text font-weight-bold text-center align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_coordenadorias, $divs_internas_coordenadorias, $classe_h2_coordenadorias, $this->montaQueryItensTaxonomias($termo_slug));
	}
}