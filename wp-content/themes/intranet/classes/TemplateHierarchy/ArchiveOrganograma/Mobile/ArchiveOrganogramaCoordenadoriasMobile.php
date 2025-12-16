<?php

namespace Classes\TemplateHierarchy\ArchiveOrganograma\Mobile;

class ArchiveOrganogramaCoordenadoriasMobile extends ArchiveOrganogramaMobile
{
	private $termo_slug;
	public function __construct()
	{
		$this->init();
	}

	public function init()
	{
		$this->getDadosCoordenadorias();
	}

	public function getDadosCoordenadorias(){

		$termos = get_terms(array(
            'taxonomy' => self::TAXONOMIA,
            'post_type' => self::CPT,
            'parent' => self::$id_taxonomia_coordenadorias,
			'hide_empty' => false,
        )); // ID Taxonomia Coordenadorias

		foreach ($termos as $termo){

			$this->setTaxonomyName($termo->slug);

			$classes_divs_coordenadorias_01 = array('coordenadoria position-relative');
			$this->abreDivs(null, $classes_divs_coordenadorias_01);

			$classes_divs_coordenadorias_02 = array('card-deck justify-content-center', 'card shadow-0 border-0 mb-3');
			$classe_h2_coordenadorias = 'mt-2 mb-2 text-center fonte-catorze';

			//$this->abreDivs('heading'.$this->getTaxonomyTermId(), $classes_divs_coordenadorias_02);
			$this->abreDivs(null, $classes_divs_coordenadorias_02);

			$classes_divs_coordenadorias_03 = array('card-header bg-cinza-escuro-organo rounded-top');
			$this->abreDivs('heading'.$this->getTaxonomyTermId(), $classes_divs_coordenadorias_03);

			$this->montaHtmlNameTaxonomyFilhas($classe_h2_coordenadorias);

			$this->fechaDivs($classes_divs_coordenadorias_03);

			$this->getContatosMobileCoordenadorias($termo->slug);

			$this->termo_slug = $termo->slug;

			$this->fechaDivs($classes_divs_coordenadorias_02);

			$this->fechaDivs($classes_divs_coordenadorias_01);
		}
	}

	public function getContatosMobileCoordenadorias($termo_slug){
		$deseja_exibir_contato = get_field('deseja_exibir_contato', self::TAXONOMIA . '_' . $this->getTaxonomyTermId());
		$escolha_o_contato_que_deseja_exibir = get_field('escolha_o_contato_que_deseja_exibir', self::TAXONOMIA . '_' . $this->getTaxonomyTermId());

		if ($deseja_exibir_contato == 'sim'){
			$this->montaHtmlContatosMobileCoordenadorias($escolha_o_contato_que_deseja_exibir, $termo_slug);
		}

	}

	public function montaHtmlContatosMobileCoordenadorias($escolha_o_contato_que_deseja_exibir, $termo_slug){
		?>
		<div id="id_<?=$this->getTaxonomyTermId()?>" class="collapse fade" aria-labelledby="<?= 'heading'.$this->getTaxonomyTermId() ?>">
			<div class="card-body bg-cinza-escuro-organo mb-3 rounded-bottom">
				<p class="card-text text-white text-center">
					<?php
					$this->exibeCamposCadastrados($escolha_o_contato_que_deseja_exibir, null, null, true);
					?>
				</p>

			</div>
			<?php
			$this->getItensCoordenadoriasMobile($termo_slug);
			?>
		</div>
		<?php
	}

	public function getItensCoordenadoriasMobile($termo_slug){
		$divs_externas_coordenadorias = array('card-deck justify-content-center');
		$divs_internas_coordenadorias = array('card shadow-sm border-0 bg-cinza-claro-organo', 'card-body d-flex justify-content-center');
		$classe_h2_coordenadorias = 'card-text font-weight-bold text-center align-self-center';
		$this->montaHtmlItensTaxonomias($divs_externas_coordenadorias, $divs_internas_coordenadorias, $classe_h2_coordenadorias, $this->montaQueryItensTaxonomias($termo_slug));
	}
}