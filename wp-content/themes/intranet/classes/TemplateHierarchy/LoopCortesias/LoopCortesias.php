<?php

namespace Classes\TemplateHierarchy\LoopCortesias;

use Classes\Lib\Util;
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

		new LoopCortesiasCabecalho();

		echo '<div class="container mt-5">';
			echo '<div class="row">';
				new LoopCortesiasNoticiaPrincipal();
				new LoopSingleMaisRecentes(get_the_ID());
				new LoopCortesiasRelacionadas(get_the_ID());
			echo '</div>';
		echo '</div>';
	
		$this->fechaContainer($container_geral_tags);
	}
}