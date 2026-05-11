<?php
/*
Plugin Name: Emails Revisão Oportunidades
Plugin URI: http://educacao.sme.prefeitura.sp.gov.br
Description: Envia emails para admins do portal quando uma oportunidade vai para revisão.
Version: 1.0
Author: Spassu
*/

if (!defined('ABSPATH')) {
    exit;
}

function oportunidade_pending_email($new_status, $old_status, $post) {

    // Apenas CPT oportunidade
    if ($post->post_type !== 'oportunidade') {
        return;
    }

    // Apenas transições para pending
    if ($new_status !== 'pending') {
        return;
    }

    // Apenas:
    // new -> pending
    // publish -> pending
    if (
        $old_status !== 'new' &&
        $old_status !== 'publish'
    ) {
        return;
    }

    // Busca usuários admin_portal
    $usuarios = get_users([
        'role'   => 'admin_portal',
        'fields' => ['user_email']
    ]);

    if (empty($usuarios)) {
        return;
    }

    $emails = [];

    foreach ($usuarios as $user) {

        if (!empty($user->user_email)) {
            $emails[] = $user->user_email;
        }
    }

    // Remove duplicados
    $emails = array_unique($emails);

    // Sem emails válidos
    if (empty($emails)) {
        return;
    }

    // Dados do post
    $titulo = get_the_title($post->ID);

    $edit_link = get_edit_post_link($post->ID);
    $edit_link = str_replace('&amp;', '&', $edit_link);

    $view_link = get_permalink($post->ID);

    // Assunto
    $subject = 'Uma oportunidade aguarda aprovação';

    // Mensagem
    $message  = "A oportunidade \"{$titulo}\" foi enviada para revisão.\n\n";

    $message .= "Visualizar publicação:\n";
    $message .= $view_link . "\n\n";

    $message .= "Revisar publicação:\n";
    $message .= $edit_link;

    // Headers
    $headers = [
        'Content-Type: text/plain; charset=UTF-8'
    ];

    // Envia email
    wp_mail($emails, $subject, $message, $headers);
}

add_action(
    'transition_post_status',
    'oportunidade_pending_email',
    20,
    3
);