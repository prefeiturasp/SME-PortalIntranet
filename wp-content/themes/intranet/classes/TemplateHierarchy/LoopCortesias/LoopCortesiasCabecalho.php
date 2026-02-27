<?php

namespace Classes\TemplateHierarchy\LoopCortesias;

use Classes\TemplateHierarchy\Search\SearchFormSingle;

class LoopCortesiasCabecalho extends LoopCortesias
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
				<div class="container"><h1>Gratuidade e Cortesias</h1></div>
			</div>
        <?php
        
	}
}