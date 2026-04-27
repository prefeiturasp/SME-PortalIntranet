<?php

namespace Classes\TemplateHierarchy\LoopSingle;

use Classes\Lib\Util;

class LoopSingle extends Util
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
		//new LoopSingleMenuInterno();
		echo '<div class="container mt-5">';
			echo '<div class="row">';
				new LoopSingleNoticiaPrincipal();
				new LoopSingleInformacoesEvento();
				new LoopSingleRelacionadas(get_the_ID());				
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