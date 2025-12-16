<?php

use function PHPUnit\Framework\assertEquals;

beforeEach(function () {
    // Reset do redirect global antes de cada teste
    $GLOBALS['__redirect_url'] = null;
});

it('redireciona para pagina em atualizacao quando slug bate com pagina pendente', function () {

    // Mock do wp_redirect
    $mock_redirect = function($url) {
        $GLOBALS['__redirect_url'] = $url;
    };

    // Mock do $wpdb
    $mock_wpdb = new class {
        public $posts;
        public function get_results($query, $output) {
            return [(object)['post_title' => 'Página Teste']];
        }
    };

    // Chamamos a função com mocks
    redireciona_paginas_pendentes(
        fn() => true,                  // is_404
        $mock_redirect,                // wp_redirect
        '/pagina-teste',               // REQUEST_URI
        $mock_wpdb                      // $wpdb
    );

    expect($GLOBALS['__redirect_url'])->toBe(STM_URL . '/conteudo-em-atualizacao/');
});

it('nao redireciona quando slug nao bate', function () {

    // Mock do wp_redirect
    $mock_redirect = function($url) {
        $GLOBALS['__redirect_url'] = $url;
    };

    // Mock do $wpdb
    $mock_wpdb = new class {
        public $posts;
        public function get_results($query, $output) {
            return [(object)['post_title' => 'Outra Página']];
        }
    };

    // Chamamos a função com mocks
    redireciona_paginas_pendentes(
        fn() => true,                  // is_404
        $mock_redirect,                // wp_redirect
        '/pagina-teste',               // REQUEST_URI
        $mock_wpdb                      // $wpdb
    );

    expect($GLOBALS['__redirect_url'])->toBeNull();
});

it('nao redireciona se nao for 404', function () {

    $mock_redirect = function($url) {
        $GLOBALS['__redirect_url'] = $url;
    };

    $mock_wpdb = new class {
        public $posts;
        public function get_results($query, $output) {
            return [(object)['post_title' => 'Página Teste']];
        }
    };

    // Chamamos a função simulando que não é 404
    redireciona_paginas_pendentes(
        fn() => false,                 // is_404 falso
        $mock_redirect,
        '/pagina-teste',
        $mock_wpdb
    );

    expect($GLOBALS['__redirect_url'])->toBeNull();
});