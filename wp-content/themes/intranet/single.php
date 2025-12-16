<?php
$user = wp_get_current_user();
$parceira = get_field('parceira', 'user_'. $user->ID );
$email = $user->user_email;
if(function_exists('email_validate_patterns_in_monitored_domains_php7')){
    $resultado = email_validate_patterns_in_monitored_domains_php7($email);
    if($resultado && !$parceira){
        wp_redirect( home_url('index.php/perfil?atualizar=1') );
        exit;
    }
}

use Classes\TemplateHierarchy\LoopSingle\LoopSingle;
get_header();
$loop_single = new LoopSingle();
//contabiliza visualizações de noticias
setPostViews(get_the_ID()); /*echo getPostViews(get_the_ID());*/
get_footer();
?>
