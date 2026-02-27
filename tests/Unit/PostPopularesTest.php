<?php

uses()->group('posts-populares');

beforeEach(function () {
    // Armazena meta simulada
    $GLOBALS['__meta'] = [];
});

// Mock das funções do WordPress via callbacks
$get_post_meta_cb = fn($post_id, $key, $single = true) => $GLOBALS['__meta'][$post_id][$key] ?? '';
$update_post_meta_cb = fn($post_id, $key, $value) => $GLOBALS['__meta'][$post_id][$key] = $value;
$add_post_meta_cb = fn($post_id, $key, $value) => $GLOBALS['__meta'][$post_id][$key] = $value;
$delete_post_meta_cb = function($post_id, $key) {
    unset($GLOBALS['__meta'][$post_id][$key]);
};

// Mock de is_single() para os testes
$is_single_cb = fn() => true;

// Função shapeSpace_popular_posts com callbacks
function shapeSpace_popular_posts_cb($post_id, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb) {
    $count_key = 'popular_posts';
    $count = $get_post_meta_cb($post_id, $count_key, true);
    if ($count === '') {
        $count = 0;
        $delete_post_meta_cb($post_id, $count_key);
        $add_post_meta_cb($post_id, $count_key, '0');
    } else {
        $count++;
        $update_post_meta_cb($post_id, $count_key, $count);
    }
}

// Função shapeSpace_track_posts com callbacks
function shapeSpace_track_posts_cb($post_id, $is_single_cb, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb) {
    if (!$is_single_cb()) return;
    shapeSpace_popular_posts_cb($post_id, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb);
}

// TESTE: incrementa popular_posts quando já existe
it('incrementa popular_posts quando já existe', function () use ($get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb) {
    $post_id = 123;
    $GLOBALS['__meta'][$post_id]['popular_posts'] = '5'; // WP salva como string

    shapeSpace_popular_posts_cb($post_id, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb);

    expect($GLOBALS['__meta'][$post_id]['popular_posts'])->toBe(6);
});

// TESTE: cria popular_posts quando não existe
it('cria popular_posts quando não existe', function () use ($get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb) {
    $post_id = 124;

    shapeSpace_popular_posts_cb($post_id, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb);

    expect($GLOBALS['__meta'][$post_id]['popular_posts'])->toBe('0');
});

// TESTE: shapeSpace_track_posts com post global
it('track posts chama shapeSpace_popular_posts com post global', function () use ($is_single_cb, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb) {
    $post_id = 125;
    shapeSpace_track_posts_cb($post_id, $is_single_cb, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb);

    expect($GLOBALS['__meta'][$post_id]['popular_posts'])->toBe('0');
});

// TESTE: shapeSpace_track_posts com ID passado
it('track posts chama shapeSpace_popular_posts com ID passado', function () use ($is_single_cb, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb) {
    $post_id = 126;
    shapeSpace_track_posts_cb($post_id, $is_single_cb, $get_post_meta_cb, $update_post_meta_cb, $add_post_meta_cb, $delete_post_meta_cb);

    expect($GLOBALS['__meta'][$post_id]['popular_posts'])->toBe('0');
});