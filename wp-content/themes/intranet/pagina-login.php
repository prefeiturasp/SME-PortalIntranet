<?php
/*
 * Template Name: Login
 * Description: Modelo para Login no CoreSSO
 */
wp_enqueue_script('pagina-login');
use Classes\ModelosDePaginas\Login\Login;

get_header('forms');
$Login = new Login();
get_footer('forms');