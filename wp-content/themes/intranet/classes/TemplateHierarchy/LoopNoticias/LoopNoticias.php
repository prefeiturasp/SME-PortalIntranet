<?php

namespace Classes\TemplateHierarchy\LoopNoticias;

use Classes\Lib\Util;

class LoopNoticias extends Util
{

	public function __construct()
	{
		$this->init();
	}

	public function init(){
		$container_geral_tags = array('section', 'section');
		$container_geral_css = array('container-fluid', 'row');
		$this->abreContainer($container_geral_tags, $container_geral_css);

		new LoopNoticiasCabecalho();
		echo '<div class="container mt-5">';
			echo '<div class="row">';
				new LoopNoticiasNoticiaPrincipal();
				new LoopNoticiasMaisRecentes(get_the_ID());
				new LoopNoticiasComentarios(get_the_ID());
			echo '</div>';
		echo '</div>';

			

		$this->fechaContainer($container_geral_tags);
	}
}