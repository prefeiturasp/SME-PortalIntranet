<?php 
    $user = wp_get_current_user();
    $rf = get_field('rf', 'user_' . $user->ID);
    
?>
<div class="whatsapp-banner">
    <h3>Receba as novidades no seu WhatsApp</h3>
    <form action="<?= get_sub_field('link_da_pagina'); ?>" method="post">
        
        <?php if($user->data->display_name): ?>
            <input type="hidden" name="nome" value="<?= $user->data->display_name; ?>">
        <?php endif;?>
        <?php if($rf): ?>
            <input type="hidden" name="rf" value="<?= $rf; ?>">
        <?php endif;?>

        <div class="whats-content">
            <div class="img-field">
                <img src="<?= get_template_directory_uri() . '/img/whatsapp-icon.png'?>" alt="Icone WhatsApp">
            </div>
            <div class="tel-field">
                <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000">
            </div>
            <input type="submit" value="Cadastrar" class="btn-whats">
        </div>

    </form>
</div>