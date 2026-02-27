<?php

/**
 * Class for registering a new settings page under Settings.
 */
class WPDocs_Options_Page {
 
    /**
     * Constructor.
     */
    function __construct() {
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
    }
 
    /**
     * Registers a new settings page under Settings.
     */
    function admin_menu() {
        add_menu_page(
            __( 'Tutoriais', 'textdomain' ),//page title
            __( 'Tutoriais', 'textdomain' ),//menu title
            'read',//Capability
            'tutorial_slug',//slug
            array(
                $this,
                'conteudo_tutorial'
            ),
			'dashicons-laptop',//icon
			3//position	
        );
    }
 
    /**
     * Settings page display callback.
     */
    function conteudo_tutorial() {
		echo '<hr>';
		$user = wp_get_current_user();
		$funcao = $user->roles[0];		
		if( have_rows('cadastro_de_tutoriais','option') ):
			while ( have_rows('cadastro_de_tutoriais','option') ) : the_row();
				$perfis = get_sub_field('perfil');
				if( in_array($funcao, $perfis) || $perfis[0] == ''):
			?>
					<div style="width: 30%; display: inline-grid; padding: 10px; text-align: center; margin: 5px; border: solid 1px #000; height: 350px; overflow-y: scroll;">
							<h3><?php the_sub_field('nome_tutorial'); ?></h3>
						<?php
							if(get_sub_field('arquivo_tutorial') != ''){
								?>
								<video width="400" height="230" controls>
								<source src="<?php the_sub_field('arquivo_tutorial'); ?>" type="video/mp4">
								</video>
								<?php
							}
						?>
							
							<p><?php the_sub_field('descricao_tutorial'); ?></p>
						<?php
							if(get_sub_field('botao_tutorial') != ''){
								?>
									<p><a href="<?php the_sub_field('botao_tutorial'); ?>"><button>Ver tutorial</button></a></p>
								<?php
							}
						?>
							
					</div>
			<?php
				endif;
			endwhile;
		else :
		endif;
    }
}
 
new WPDocs_Options_Page;