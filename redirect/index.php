<?php
require_once('../wp-load.php');

$chave = isset($_GET['token']) ? $_GET['token'] : '';

// Verifica se a variável $chave está vazia antes de realizar a consulta
if (!empty($chave)) {
    // Parâmetros da consulta
    $args = array(
        'meta_key'     => 'chave_temp', // Substitua pelo nome do seu campo meta_key
        'meta_value'   => $chave,
        'meta_compare' => '=', 
    );

    // Obtém os usuários que correspondem aos parâmetros da consulta
    $usuarios = get_users($args);

    // Verifica se há usuários correspondentes
    if (!empty($usuarios)) {
        
        // O ID do primeiro usuário correspondente
        $user_id = $usuarios[0]->ID;
        
        // Exclui o campo 'chave_temp' para o usuário autenticado
        delete_user_meta($user_id, 'chave_temp');

        // Autentica o usuário definindo um cookie de autenticação
        wp_set_auth_cookie($user_id);

        // Redireciona o usuário para a home do site
        wp_redirect(home_url());
        exit;

    } else {
        $login_page = home_url();
        wp_redirect( $login_page . '?pass=expired' );
        exit;
    }
} else {
    $login_page = home_url();
	wp_redirect( $login_page . '?pass=expired' );
	exit;
}