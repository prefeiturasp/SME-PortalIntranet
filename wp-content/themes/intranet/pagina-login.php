<?php
/*
 * Template Name: Login
 * Description: Modelo para Login no CoreSSO
 */

use Classes\ModelosDePaginas\Login\Login;

get_header('forms');
$Login = new Login();
get_footer('forms');