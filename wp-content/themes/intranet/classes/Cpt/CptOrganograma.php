<?php

namespace Classes\Cpt;


class CptOrganograma
{

	public function __construct(){
		add_action( 'admin_init', array($this,'removeThemeSupport' ), 10,2);
	}

	public function removeThemeSupport(){
		remove_post_type_support( 'organograma', 'editor' );
	}

}

new CptOrganograma();