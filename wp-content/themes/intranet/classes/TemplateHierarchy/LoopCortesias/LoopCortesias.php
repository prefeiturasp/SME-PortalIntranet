<?php

namespace Classes\TemplateHierarchy\LoopCortesias;

use Classes\Lib\Util;
use Classes\TemplateHierarchy\LoopSingle\LoopSingleCabecalho;
use Classes\TemplateHierarchy\LoopSingle\LoopSingleInformacoesEvento;
use Classes\TemplateHierarchy\LoopSingle\LoopSingleMaisRecentes;

class LoopCortesias extends Util
{

	public function __construct()
	{
		$this->init();
	}

	public function init(){
		$container_geral_tags = array('section', 'section');
		$container_geral_css = array('container-fluid', 'row');
		$this->abreContainer($container_geral_tags, $container_geral_css);

		new LoopSingleCabecalho();

		echo '<div class="container mt-5">';
			echo '<div class="row">';
				new LoopCortesiasNoticiaPrincipal();

				// Sidebar (somente desktop)
                echo '<div class="d-none d-lg-block col-lg-4 order-2">';                    
					$informacoes = new LoopSingleInformacoesEvento();
					$informacoes->getInformacoesEvento();
                echo '</div>';

				new LoopCortesiasRelacionadas(get_the_ID());		
			echo '</div>';
		echo '</div>';

		echo '<div class="container-fluid pt-5 posts-recentes">';
			echo '<div class="row">';				
				new LoopSingleMaisRecentes(get_the_ID());		
			echo '</div>';
		echo '</div>';
	
		$this->fechaContainer($container_geral_tags);
	}
}