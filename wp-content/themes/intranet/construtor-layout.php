<?php
/*
 * Template Name: Construtor de páginas
 * Description: Modelo para construção de páginas dinamicas
 */

use Classes\ModelosDePaginas\Layout\Construtor;

$user = wp_get_current_user();
$rf = get_field('rf', 'user_' . $user->ID);
$email = $user->user_email;
$verifyEmail = explode('@', $email);
$parceira = get_field('parceira', 'user_'. $user->ID );

if(function_exists('email_validate_patterns_in_monitored_domains_php7')){
    $resultado = email_validate_patterns_in_monitored_domains_php7($email);
    if($resultado && !$parceira){
        wp_redirect( home_url('index.php/perfil?atualizar=1') );
        exit;
    }
}

if($rf == $verifyEmail[0]){
    wp_redirect( home_url('index.php/perfil?atualizar=1') ); //exit;
    exit;
}

get_header();
$Construtor = new Construtor();
//contabiliza visualizações de noticias
setPostViews(get_the_ID());  //echo getPostViews(get_the_ID());
get_footer();