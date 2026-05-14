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
    // auto-draft -> pending
    // new -> pending
    // publish -> pending
    if ( !in_array( $old_status, ['new', 'auto-draft', 'publish'], true ) ) {
        return;
    }

    // Busca usuários admin_portal
    $usuarios = get_users([
        'role'   => 'admin_portal',
        'fields' => ['id', 'user_email']
    ]);

    if (empty($usuarios)) {
        return;
    }

    $emails = [];

    foreach ($usuarios as $user) {

        $receber_notificacoes = get_user_meta( $user->ID, 'receber_emails_oportunidades_pendentes', true );

        if ( !empty( $user->user_email ) && $receber_notificacoes != '0' ) {
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

    // Assunto
    $assunto = ( $old_status === 'publish' )
        ? "OPORTUNIDADE EDITADA PENDENTE DE APROVAÇÃO - {$titulo}"
        : "NOVA OPORTUNIDADE PENDENTE DE APROVAÇÃO";

    // Mensagem
    $mensagem  = ( $old_status === 'publish' )
        ? 'A oportunidade abaixo foi editada pelo Gestor da Unidade e precisa de nova análise antes da publicação. Acesse o link da oportunidade para revisar as alterações realizadas e, caso estejam corretas, realizar a publicação.'
        : 'A oportunidade abaixo foi criada no sistema e precisa de análise para publicação. Acesse o WP-Admin para revisar as informações cadastradas e, caso estejam corretas, realizar a publicação.';

    $tipo_oportunidade = '';
    if ( $array_tipos = get_field( 'tipo_oportunidade', $post->ID ) ) {
        $tipo_oportunidade = wp_list_pluck( $array_tipos, 'label' ) ?? [];
        $tipo_oportunidade = implode(' | ', $tipo_oportunidade );
    }

    // Dados do autor do post
    $nome_gestor = get_the_author_meta( 'display_name', $post->post_author );
    $email_gestor = get_the_author_meta( 'email', $post->post_author );

    $conteudo_email = get_email_oportunidade_template('aprovacao-oportunidade', [
        'mensagem' => $mensagem,
        'link' => $edit_link,
        'tipo_oportunidade' => $tipo_oportunidade,
        'nome_gestor' => $nome_gestor,
        'email_gestor' => $email_gestor
    ]);

    // Headers
    $headers = ['Content-Type: text/html; charset=UTF-8'];

    // Envia email
    wp_mail( $emails, $assunto, $conteudo_email, $headers );
}

add_action(
    'transition_post_status',
    'oportunidade_pending_email',
    20,
    3
);

function get_email_oportunidade_template( string $template, $data = [] )
{
    $template_path = plugin_dir_path( __FILE__ ) . "templates/{$template}.php";

    if ( !file_exists( $template_path ) ) {
        return '';
    }

    ob_start();

    extract( $data );

    include $template_path;

    return ob_get_clean();
}
