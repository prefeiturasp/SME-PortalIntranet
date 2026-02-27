<?php
use Classes\BuscaDeEscolas\BuscaDeEscolas;

get_header();

$url_personalizada = new BuscaDeEscolas();
$url_personalizada->buscaEscola();

get_footer();