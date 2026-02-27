<?php
/*
Plugin Name: Avisos do Painel Admin
Plugin URI: http://educacao.sme.prefeitura.sp.gov.br/
Description: Habilita Campo de configuração de aviso dentro do Painel Admin.
Author: Rafael Henrique de Souza
Version: 1.0
Author URI: https://rafaelhsouza.com.br/
*/

/*INICIO AGENDA DO SECRETARIO*/

/////////////////////////////////////////////////////
///////Cria o Metabox para Agenda do Secretário//////
/////////////////////////////////////////////////////
function aviso_agenda_meta_box(){
	add_meta_box(
		'aviso-agenda',
		__('AVISO INTERNO', 'aviso-agenda'),
		'aviso_agenda',
		'agenda'
	);
}
//valor o metabox
function aviso_agenda(){
	echo get_option( 'aviso_secretario' );
}
add_action('admin_init', 'aviso_agenda_meta_box');
/////////////////////////////////////////////////////
//////Cria o Sub-Menu para Agenda do Secretário//////
/////////////////////////////////////////////////////
function criar_menu_aviso_secretario()
{
    add_submenu_page( 'edit.php?post_type=agenda','Aviso do Secretário', 'Aviso do Secretário', 10, 'aviso-secretario/aviso_do_secretario.php' );
}
add_action( 'admin_menu', 'criar_menu_aviso_secretario' );

/*FIM AGENDA DO SECRETARIO*/