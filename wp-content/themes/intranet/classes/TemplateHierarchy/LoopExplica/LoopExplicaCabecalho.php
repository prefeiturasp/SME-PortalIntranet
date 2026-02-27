<?php

namespace Classes\TemplateHierarchy\LoopExplica;

class LoopExplicaCabecalho extends LoopExplica
{

	public function __construct()
	{
		$this->cabecalhoDetalheNoticia();
	}

	public function cabecalhoDetalheNoticia(){
		?>
			<div class="bn_fx_banner" style="background-image: url(https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2023/01/topo-sme-explica.png);">
				<div class="container"><h1>SME Explica</h1></div>
			</div>
        <?php        
	}
}