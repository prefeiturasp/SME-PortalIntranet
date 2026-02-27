<?php
/*
Plugin Name: Emails Customizados
Plugin URI: http://educacao.sme.prefeitura.sp.gov.br/
Description: Personalização dos modelos de emails padrões do WordPress.
Version: 1.0
Author: Ollyver Ottoboni
Author URI: https://ottobonidesign.com.br/
*/


//modifica email padrão de recuperação de senha
add_filter('password_change_email', 'modelo_troca_senha', 10, 3);
function modelo_troca_senha( $pass_change_mail, $user, $userdata ) {
  $nova_messagem = __( 'Olá, ###USERNAME###.

Este aviso confirma que a sua senha foi alterada em ###SITENAME###.

Caso você não tenha alterado sua senha, contate a equipe da assessoria de comunicação em ascom.conteudo@sme.prefeitura.sp.gov.br.

Atenciosamente,

Equipe ###SITENAME###.' );
  $pass_change_mail[ 'message' ] = $nova_messagem;
  return $pass_change_mail;
}