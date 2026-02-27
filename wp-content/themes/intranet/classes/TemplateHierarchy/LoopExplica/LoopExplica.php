<?php

namespace Classes\TemplateHierarchy\LoopExplica;

use Classes\Lib\Util;

class LoopExplica extends Util
{

	public function __construct()
	{
		$this->init();
	}

	public function init(){
		$container_geral_tags = array('section', 'section');
		$container_geral_css = array('container-fluid', 'row');
		$this->abreContainer($container_geral_tags, $container_geral_css);

		new LoopExplicaCabecalho();
		new LoopExplicaNoticiaPrincipal();
		new LoopExplicaRelacionadas(get_the_ID());

		$this->fechaContainer($container_geral_tags);
	}



}