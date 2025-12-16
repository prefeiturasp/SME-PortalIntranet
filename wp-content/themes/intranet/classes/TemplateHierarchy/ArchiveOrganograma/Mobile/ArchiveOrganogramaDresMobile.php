<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma\Mobile;


class ArchiveOrganogramaDresMobile extends ArchiveOrganogramaMobile
{
	public function __construct()
	{
		$this->init();
	}

	public function init(){
		$divs_dres = array('card-deck justify-content-center');
		$this->abreDivs(null, $divs_dres);
		$this->getDadosDresTaxonomiaMae();
		$this->fechaDivs($divs_dres);
	}

	public function getDadosDresTaxonomiaMae(){

		$this->setTaxonomyName(self::SLUG_TAXONOMIA_DRES);

		$divs_externas_dres_nivel_1 = array('card shadow-none border-0 mb-2');
		$this->abreDivs(null, $divs_externas_dres_nivel_1);


		$div_card_header_taxonomia_mae = array('card-header rounded bg-cinza-escuro-organo');
		$this->abreDivs('heading'.$this->getTaxonomyTermId(), $div_card_header_taxonomia_mae);

		$classe_h2_dres= 'mt-2 mb-2 text-center fonte-catorze';
		$this->montaHtmlNameTaxonomyDreMae($classe_h2_dres);

		$this->fechaDivs($div_card_header_taxonomia_mae);

		$this->getTaxonomiasFilhas();


		$this->fechaDivs($divs_externas_dres_nivel_1);

	}

	public function getTaxonomiasFilhas(){

		$termos = get_terms(array('taxonomy' => self::TAXONOMIA, 'post_type' => self::CPT, 'parent' => self::$id_taxonomia_dres )); // ID Taxonomia DRE's

		foreach ($termos as $termo){
			$this->setTaxonomyName($termo->slug);

			$divs_externas_itens_dres = array('fade bg-transparent collapse');
			$this->abreDivs('dres', $divs_externas_itens_dres);

			$divs_internas_itens_dres = array('card-body pt-0 pb-0 pl-0 pr-0', 'dre', 'card-deck justify-content-center mt-2', 'card shadow-sm border-0 bg-cinza-dre btn-block mb-2');
			$this->abreDivs(null, $divs_internas_itens_dres);

			$div_card_header = array('card-header bg-cinza-dre rounded-top');
			$this->abreDivs('headingDre'.$this->getTaxonomyTermId(), $div_card_header);
			$classe_h2_taxonomias_filhas = 'mt-2 mb-2 text-center fonte-catorze';
			$this->montaHtmlNameTaxonomyFilhas($classe_h2_taxonomias_filhas);
			$this->fechaDivs($div_card_header);

			$this->getContatosDresMobile($termo->slug);

			$this->fechaDivs($divs_internas_itens_dres);

			$this->fechaDivs($divs_externas_itens_dres);

			
		}

	}

	public function getContatosDresMobile($termo_slug){
		$deseja_exibir_contato = get_field('deseja_exibir_contato', self::TAXONOMIA . '_' . $this->getTaxonomyTermId());
		$escolha_o_contato_que_deseja_exibir = get_field('escolha_o_contato_que_deseja_exibir', self::TAXONOMIA . '_' . $this->getTaxonomyTermId());

		if ($deseja_exibir_contato == 'sim'){
			$this->montaHtmlContatosDresMobile($escolha_o_contato_que_deseja_exibir, $termo_slug);
		}

	}

	public function montaHtmlContatosDresMobile($escolha_o_contato_que_deseja_exibir, $termo_slug){
		?>
		<div id="id_<?=$this->getTaxonomyTermId()?>" class="collapse fade" aria-labelledby="<?= 'heading'.$this->getTaxonomyTermId() ?>">
			<div class="card-body bg-cinza-dre rounded-bottom">
				<p class="card-text text-dark text-center">
					<?php
					$this->exibeCamposCadastrados($escolha_o_contato_que_deseja_exibir, null, null, false);
					?>
				</p>
			</div>
			<?php
			$this->getItensTaxonomiasFilhas($termo_slug);
			?>
		</div>
		<?php
	}

	public function montaHtmlNameTaxonomyFilhas($classe_h2)
	{
		echo '<h2 class="'.$classe_h2.'">';
		echo '<a class="text-dark font-weight-bold text-decoration-none collapsed" data-toggle="collapse" data-target="#id_'.$this->getTaxonomyTermId().'" aria-expanded="false" aria-controls="id_'.$this->getTaxonomyTermId().'" href="">';
		echo $this->getTaxonomyName();
		echo '</a>';
		echo '</h2>';
	}


	public function getItensTaxonomiasFilhas($termo_slug){
		$divs_externas_coordenadorias = array('card-deck justify-content-center mt-3');
		$divs_internas_coordenadorias = array('card shadow-sm border-0 bg-cinza-claro-organo', 'card-body d-flex justify-content-center');
		$classe_h2_coordenadorias = 'card-text text-center font-weight-bold align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_coordenadorias, $divs_internas_coordenadorias, $classe_h2_coordenadorias, $this->montaQueryItensTaxonomias($termo_slug));
	}

	public function montaHtmlNameTaxonomyDreMae($classe_h2)
	{
		echo '<h2 class="'.$classe_h2.'">';
		echo '<a class="text-white font-weight-bold text-decoration-none collapsed" data-toggle="collapse" data-target="#dres" aria-expanded="false" aria-controls="dres" href="">';
		echo $this->getTaxonomyName();
		echo '</a>';
		echo '</h2>';
	}

}