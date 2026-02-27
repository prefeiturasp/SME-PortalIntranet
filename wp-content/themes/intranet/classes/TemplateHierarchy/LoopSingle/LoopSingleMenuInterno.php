<?php

namespace Classes\TemplateHierarchy\LoopSingle;


class LoopSingleMenuInterno extends LoopSingle
{
	public function __construct()
	{
		$this->getMenuInterno();
	}

	public function getMenuInterno(){

		$menu_interno = get_field('escolha_o_menu_que_deseja_exibir_no_detalhe_da_noticia', get_the_ID());

		if (!$menu_interno) {
			$menu_interno = 'menu mais noticias';
		};

		$menu_interno_tags = array('section');
		$menu_interno_css = array('mb-5');
		$this->abreContainer($menu_interno_tags, $menu_interno_css);
		wp_nav_menu(array(
			'menu' => $menu_interno,
			'theme_location' => 'primary',
			'depth' => 2,
			'container_id' => 'menu-interno-detalhe-noticia',
			'container_class' => 'categorias-nav',
			'menu_class' => 'nav nav-fill bg-cinza-dre text-uppercase',
			'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
			'walker'            => new \WP_Bootstrap_Navwalker(),
		));

		$this->fechaContainer($menu_interno_tags);
	}

}