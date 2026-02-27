<?php

namespace Classes\TemplateHierarchy\ArchiveContato;


use Classes\Lib\Util;

class ExibirContatosTodasPaginas extends ArchiveContato
{
	protected $page_id;

	public function __construct($page_id)
	{
		$this->page_id = $page_id;

	}

	public function init(){

		$deseja_exibir_contatos_nesta_pagina = get_field('deseja_exibir_contatos_nesta_pagina', $this->page_id);
		$quais_contatos_deseja_exibir = get_field('quais_contatos_deseja_exibir', $this->page_id);


		if ($deseja_exibir_contatos_nesta_pagina === 'sim'){

			if ($quais_contatos_deseja_exibir === 'todos'){
				new ArchiveContato('p', 'titulo-outras-paginas mb2');
			}else{
				$container_html_tags = array('section','section', 'section');
				$container_html_css = array('col-12','container', 'container-contatos');
				$this->abreContainer($container_html_tags,$container_html_css);

				if ($quais_contatos_deseja_exibir === 'categoria'){
					$categorias_de_contatos = get_field('escolha_as_categorias_de_contato_que_deseja_exibir', $this->page_id);
					$this->getContatosPorCategoria($categorias_de_contatos);
				}elseif ($quais_contatos_deseja_exibir === 'especificos'){
					$contatos_especificos = get_field('escolhas_os_contatos_individuais', $this->page_id);
					$this->getContatosEspecificos($contatos_especificos);
				}
				$this->fechaContainer($container_html_tags);
			}
		}
	}

	public function getContatosEspecificos($contatos_especificos){
		foreach ($contatos_especificos as $contato){
			echo '<p class="titulo-nivel-nao-superior mt-2 pt-2 border-top">'.$contato->post_title.'</p>';
			$this->exibeCamposCadastrados($contato->ID);
		}

	}

	public function getContatosPorCategoria($categorias_de_contatos){
		foreach ($categorias_de_contatos as $categoria){
			$term = get_term( $categoria );
			echo '<p class="titulo-nivel-superior mt-2 pt-2">'.$term->name.'</p>';
			$this->exibeCamposCadastrados($categoria);
			$this->getContatosTaxonomia($categoria);
		}

	}

}