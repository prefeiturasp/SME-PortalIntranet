<?php
namespace Classes\TemplateHierarchy\ArchiveOrganograma\Mobile;

use Classes\TemplateHierarchy\ArchiveOrganograma\ArchiveOrganograma;

class ArchiveOrganogramaMobile extends ArchiveOrganograma
{

	public function __construct()
	{

	}

	public function init()
	{
		$this->getTituloSubtitulo();
		$divs_geral = array('container mb-5', 'row', 'col-lg-12 mb-5', 'organograma-mobile fonte-catorze d-lg-none d-sm-none d-block');
		$this->abreDivs(null, $divs_geral);

		$divs_conselhos_e_secretario = array('card-deck shadow-sm p-3 rounded justify-content-center');
		$this->abreDivs(null, $divs_conselhos_e_secretario);
		new ArchiveOrganogramaConselhosMobile();
		new ArchiveOrganogramaSecretarioMobile();
		$this->fechaDivs($divs_conselhos_e_secretario);

		new ArchiveOrganogramaAssessoriasMobile();

		$divs_coordenadorias_dres = array('coordenadorias-dres shadow-sm p-3 rounded justify-content-center mt-0');
		$this->abreDivs(null, $divs_coordenadorias_dres);

		new ArchiveOrganogramaCoordenadoriasMobile();

		new ArchiveOrganogramaDresMobile();

		$this->fechaDivs($divs_coordenadorias_dres);

		new ArchiveOrganogramaRodape();

		$this->fechaDivs($divs_geral);
	}

	public function montaHtmlItensTaxonomias(array $divs_externas, array $divs_internas, $classe_p, $query)
	{
		$this->abreDivs(null,$divs_externas);

		foreach ($query as $index => $term) {

			$this->abreDivs(null,$divs_internas);
			echo '<p class="'.$classe_p.'">' . $term->post_title . '</p>';
			$this->fechaDivs($divs_internas);
		}

		$this->fechaDivs($divs_externas);
	}





}