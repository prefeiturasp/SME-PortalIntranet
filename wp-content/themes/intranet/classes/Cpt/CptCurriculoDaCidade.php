<?php

namespace Classes\Cpt;


class CptCurriculoDaCidade extends Cpt
{

	public function __construct()
	{
		$this->cptSlug = self::getCptSlugExtend();
		add_action('init', array($this, 'removePostTypeSupport'));
	}

	public function removePostTypeSupport(){
		remove_post_type_support( $this->cptSlug, 'editor' );
	}

}