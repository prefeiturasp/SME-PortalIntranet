<?php

namespace Classes\TemplateHierarchy;


use Classes\Lib\Util;
use Classes\TemplateHierarchy\ArchiveContato\ExibirContatosTodasPaginas;

class Page extends Util
{
	protected $page_id;

	public function __construct()
	{
		$this->page_id = get_the_ID();
		$util = new Util($this->page_id);
		$util->montaHtmlLoopPadrao();
		$contato_todas_paginas = new ExibirContatosTodasPaginas($this->page_id);
		$contato_todas_paginas->init();
	}

}