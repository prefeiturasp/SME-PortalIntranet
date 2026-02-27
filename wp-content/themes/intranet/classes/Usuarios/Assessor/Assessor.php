<?php


namespace Classes\Usuarios\Assessor;

class Assessor
{

	public function __construct()
	{

		$this->addRole();
		//$this->removeRole();
	}

	public function removeRole(){
		remove_role('assessor');
	}

	public function addRole(){

		add_role(
			'assessor',
			__( 'Assessores' ),
			array(
				'read'         => true,  // true allows this capability
				'edit_posts'   => true,
				'publish_posts' => false
			)
		);
	}

}

new Assessor();