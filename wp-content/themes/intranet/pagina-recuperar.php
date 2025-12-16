<?php
/*
 * Template Name: Recuperar Senha
 * Description: Modelo para Login no CoreSSO
 */
wp_enqueue_script('pagina-login');
use Classes\ModelosDePaginas\Login\LoginRecuperar;

get_header('forms');
$Login = new LoginRecuperar();
get_footer('forms');