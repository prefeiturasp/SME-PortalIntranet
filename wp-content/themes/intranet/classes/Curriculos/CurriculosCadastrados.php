<?php

namespace Classes\Curriculos;

class CurriculosCadastrados
{

	public function __construct()
	{
		add_action('admin_menu', array($this, 'admin_menu'));
	}

	public function admin_menu()
	{
		add_submenu_page(
            'edit.php?post_type=oportunidade', // menu pai (CPT)
            'Currículos Cadastrados',              // título da página
            'Currículos Cadastrados',                               // título do menu
            'edit_oportunidades',                     // capability necessária
            'listagem_oportunidades',                 // slug
            array( $this, 'render_page' ),            // callback
            2
        );
	}

	public function render_page()
	{
		?>
		<div class="wrap">
			<h1>Currículos Cadastrados</h1>
        </div>	
		<?php
	}
}

new CurriculosCadastrados;