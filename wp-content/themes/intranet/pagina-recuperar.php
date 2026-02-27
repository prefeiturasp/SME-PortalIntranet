<?php
/*
 * Template Name: Recuperar Senha
 * Description: Modelo para Login no CoreSSO
 */

use Classes\ModelosDePaginas\Login\LoginRecuperar;

get_header('forms');
$Login = new LoginRecuperar();
get_footer('forms');