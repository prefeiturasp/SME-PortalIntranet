<?php

namespace Classes\Usuarios\AdminPortal;

class AdminPortal
{

	public function __construct()
	{

		$this->addRole();
		//$this->removeRole();
	}

	public function removeRole(){
		remove_role('admin_portal');
	}

	public function addRole(){

		add_role(
            'admin_portal',
            'Admin do Portal',
            array(
                'read' => true,

                // gerenciamento de usuários
                'list_users'   => true,
                'edit_users'   => true,
                'create_users' => true,
                'promote_users' => true,
                'add_users' => true,
			    'enroll_users' => true,
			    'manage_network_users' => true,

                // acesso completo ao CPT
                'read_oportunidade'            => true,
                'edit_oportunidade'            => true,
                'delete_oportunidade'          => true,

                'edit_oportunidades'           => true,
                'publish_oportunidades'        => true,
                'delete_oportunidades'         => true,
                'edit_others_oportunidades'    => true,
                'delete_others_oportunidades'  => true,
                'edit_private_oportunidades'   => true,
                'read_private_oportunidades'   => true,

                'manage_locais'                => true,
                'edit_locais'                  => true,
                'delete_locais'                => true,
                'assign_locais'                => true,
                'read_locais'                  => true,
            )
        );
	}

}

new AdminPortal();