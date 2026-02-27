<?php

namespace Classes\TemplateHierarchy\LoopSingle;

use Classes\TemplateHierarchy\Search\SearchFormSingle;

class LoopSingleCabecalho extends LoopSingle
{

	public function __construct()
	{
		$this->cabecalhoDetalheNoticia();
	}

	public function cabecalhoDetalheNoticia(){
		$capa = get_field('capa_sorteios', 'conf-rodape');
		if(!$capa)
			$capa = 'https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2023/01/topo-sme-explica.png';
		?>
			<div class="bn_fx_banner" style="background-image: url(<?= $capa; ?>);">
				<div class="container"><h1>Sorteios</h1></div>
			</div>
        <?php
        
	}
}