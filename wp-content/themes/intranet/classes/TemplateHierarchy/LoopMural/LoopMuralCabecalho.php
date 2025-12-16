<?php

namespace Classes\TemplateHierarchy\LoopMural;

class LoopMuralCabecalho extends LoopMural
{

	public function __construct()
	{
		$this->cabecalhoDetalheNoticia();
	}

	public function cabecalhoDetalheNoticia(){
		?>
			<div class="bn_fx_banner" style="background-image: url(https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2023/03/topo-mural.png);">
				<div class="container"><h1>Mural dos Professores</h1></div>
			</div>
        <?php        
	}
}