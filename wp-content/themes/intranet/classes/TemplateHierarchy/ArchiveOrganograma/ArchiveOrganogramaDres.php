<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma;


class ArchiveOrganogramaDres extends ArchiveOrganograma
{
	public function __construct()
	{
		$this->init();
	}

	public function init(){
		$divs_dres = array('lista-dres');
		$this->abreDivs(null, $divs_dres);
		$this->getDadosDresTaxonomiaMae();
		$this->fechaDivs($divs_dres);
	}

	public function getDadosDresTaxonomiaMae(){

		$this->setTaxonomyName(self::SLUG_TAXONOMIA_DRES);

		$divs_externas_dres_nivel_1 = array('card-deck justify-content-center mt-5');
		$this->abreDivs(null, $divs_externas_dres_nivel_1);

		$divs_externas_dres_taxonomia_mae = array('card shadow-none border-0 bg-transparent btn-block mb-2');
		$this->abreDivs(null, $divs_externas_dres_taxonomia_mae);

		$div_card_header_taxonomia_mae = array('card-header bg-cinza-escuro-organo dres');
		$this->abreDivs('heading'.$this->getTaxonomyTermId(), $div_card_header_taxonomia_mae);

		$classe_h2_dres= 'mt-2 mb-2 text-center fonte-catorze';
		$this->montaHtmlNameTaxonomyDreMae($classe_h2_dres);

		$this->fechaDivs($div_card_header_taxonomia_mae);

		$this->getTaxonomiasFilhas();

		$this->fechaDivs($divs_externas_dres_taxonomia_mae);

		$this->fechaDivs($divs_externas_dres_nivel_1);

	}

	public function getTaxonomiasFilhas(){

		$termos = get_terms(array(
			'taxonomy' => self::TAXONOMIA,
			'post_type' => self::CPT,
			'parent' => self::$id_taxonomia_dres,
			'hide_empty' => false
		)
		); // ID Taxonomia DRE's

		foreach ($termos as $termo){
			$this->setTaxonomyName($termo->slug);

			$divs_externas_itens_dres = array('fade bg-transparent collapse');
			$this->abreDivs('dres', $divs_externas_itens_dres);

			$divs_internas_itens_dres = array('card-body', 'dre', 'card-deck justify-content-center mt-2', 'card shadow-sm border-0 bg-cinza-escuro-organo btn-block mb-2');
			$this->abreDivs(null, $divs_internas_itens_dres);

			$div_card_header = array('card-header');
			$this->abreDivs('headingDre'.$this->getTaxonomyTermId(), $div_card_header);
			$classe_h2_taxonomias_filhas = 'mt-2 mb-2 text-center fonte-catorze';
			$this->montaHtmlNameTaxonomyFilhas($classe_h2_taxonomias_filhas);
			$this->fechaDivs($div_card_header);

			$this->getContatos();

			$this->fechaDivs($divs_internas_itens_dres);

			$this->getItensTaxonomiasFilhas($termo->slug);

			$this->fechaDivs($divs_externas_itens_dres);
		}

	}

	public function getItensTaxonomiasFilhas($termo_slug){
		$divs_externas_coordenadorias = array('card-deck justify-content-center mt-4 borda-itens borda-cinza-claro-organo');
		$divs_internas_coordenadorias = array('card shadow-sm border-0 bg-cinza-claro-organo borda-conexao', 'card-body d-flex justify-content-center');
		$classe_h2_coordenadorias = 'card-text font-weight-bold text-center align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_coordenadorias, $divs_internas_coordenadorias, $classe_h2_coordenadorias, $this->montaQueryItensTaxonomias($termo_slug));
	}

	public function montaHtmlNameTaxonomyDreMae($classe_h2)
	{
		echo '<div id="borda-em-l"></div>';

		echo '<h2 class="'.$classe_h2.'">';
		echo '<a class="text-white font-weight-bold text-decoration-none collapsed" data-toggle="collapse" data-target="#dres" aria-expanded="false" aria-controls="dres" href="">';

    	echo $this->getTaxonomyName();
		echo '</a>';
		echo '</h2>';
	}

}