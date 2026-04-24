<?php

namespace Classes\Usuarios\GestorUnidade;

class GestorUnidade
{

	public function __construct()
	{

		$this->addRole();
		//$this->removeRole();
	}

	public function removeRole(){
		remove_role('GestorUnidade');
	}

	public function addRole(){

		add_role(
            'gestor_unidade',
            'Gestor de Unidade',
            array(
                'read' => true,

                // capacidades do CPT (singular)
                'read_oportunidade'   => true,
                'edit_oportunidade'   => true,
                'delete_oportunidade' => true,

                // capacidades do CPT (plural)
                'edit_oportunidades'           => true,
                'publish_oportunidades'        => false,
                'delete_oportunidades'         => true,
                'edit_published_oportunidades' => true,
                'read_private_oportunidades'   => true,

                // restrições 
                'edit_others_oportunidades'    => true,
                'delete_others_oportunidades'  => true,
                'edit_private_oportunidades' => true,
                'read_private_oportunidades' => true,
            )
        );
	}

}

new GestorUnidade();