<?php

namespace Classes\TemplateHierarchy\LoopNoticias;

use Classes\TemplateHierarchy\Search\SearchFormSingle;

class LoopNoticiasCabecalho extends LoopNoticias
{

	public function __construct()
	{
		$this->cabecalhoDetalheNoticia();
	}

	public function cabecalhoDetalheNoticia(){
		$capa = get_field('capa_noticias', 'conf-rodape');
		if(!$capa)
			$capa = 'https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2023/01/topo-sme-explica.png';
		?>
			<div class="bn_fx_banner" style="background-image: url(<?= $capa; ?>);">
				<div class="container"><h1>Notícias</h1></div>
			</div>
        <?php
        
	}
}