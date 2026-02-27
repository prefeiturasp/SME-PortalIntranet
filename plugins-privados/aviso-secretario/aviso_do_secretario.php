<div class="wrap">
<h2>Configurações do Aviso da Agenda do Secretário</h2>
    <form method="post" action="options.php">
        <?php wp_nonce_field( 'update-options' ); ?>
		<?php
		
		$content = get_option('aviso_secretario');
    	wp_editor( 
			$content, 
			'aviso_secretario',
			array(
				'media_buttons' => true, 
				'quicktags' => false,
				'textarea_rows' => 10
			)
		);
		?>
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="aviso_secretario" />
        <p class="submit">
            <input type="submit" class="button-primary" value="Salvar" />
        </p>
    </form>
</div>