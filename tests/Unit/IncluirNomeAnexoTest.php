<?php

uses()->group('anexos');

beforeEach(function () {
    // Variável global para armazenar os posts atualizados
    $GLOBALS['__updated_posts'] = [];

    // Mocks das funções do WordPress
    $this->mock_get_posts = fn($args) => [
        (object)['ID' => 201],
        (object)['ID' => 202]
    ];

    $this->mock_wp_update_post = fn($post_arr) => $GLOBALS['__updated_posts'][] = $post_arr;

    $this->mock_get_post = fn($post_id) => (object)[
        'ID' => $post_id,
        'post_excerpt' => 'Resumo do post'
    ];

    $this->mock_get_post_thumbnail_id = fn($post_id) => 111;
});

it('atualiza os anexos com nomes e resumo do post', function () {
    $xml_node = (object) [
        'Files_Nomes_Dos_Arquivos' => 'Arquivo1,Arquivo2'
    ];

    // Chama a função com os mocks
    incluir_nome_nos_anexos(
        999,
        $xml_node,
        false,
        $this->mock_get_posts,
        $this->mock_wp_update_post,
        $this->mock_get_post,
        $this->mock_get_post_thumbnail_id
    );

    // Verifica se dois attachments foram atualizados
    expect($GLOBALS['__updated_posts'])->toHaveCount(2);

    // Verifica se o primeiro attachment recebeu os valores corretos
    expect($GLOBALS['__updated_posts'][0])->toMatchArray([
        'ID' => 201,
        'post_title' => 'Arquivo1',
        'post_excerpt' => 'Resumo do post',
        'post_content' => 'Resumo do post'
    ]);

    // Verifica se o segundo attachment recebeu os valores corretos
    expect($GLOBALS['__updated_posts'][1])->toMatchArray([
        'ID' => 202,
        'post_title' => 'Arquivo2',
        'post_excerpt' => 'Resumo do post',
        'post_content' => 'Resumo do post'
    ]);
});

it('não atualiza nada se não houver attachments', function () {
    $xml_node = (object) [
        'Files_Nomes_Dos_Arquivos' => 'Arquivo1,Arquivo2'
    ];

    // Mock que retorna array vazio
    $mock_get_posts_empty = fn($args) => [];

    incluir_nome_nos_anexos(
        999,
        $xml_node,
        false,
        $mock_get_posts_empty,
        $this->mock_wp_update_post,
        $this->mock_get_post,
        $this->mock_get_post_thumbnail_id
    );

    expect($GLOBALS['__updated_posts'])->toHaveCount(0);
});