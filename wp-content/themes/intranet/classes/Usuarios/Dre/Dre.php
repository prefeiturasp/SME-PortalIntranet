<?php


namespace Classes\Usuarios\Dre;

class Dre
{

	public function __construct()
	{

		//$this->addRole();
		$this->removeRole();
	}

	public function removeRole(){
		remove_role('dre');
	}

	public function addRole(){

		add_role(
			'dre',
			__( 'Usuarios DREs' ),
			array(
				'read'         => true,  // true allows this capability
				'edit_posts'   => true,
				'publish_posts' => false
			)
		);
	}

}

new Dre();