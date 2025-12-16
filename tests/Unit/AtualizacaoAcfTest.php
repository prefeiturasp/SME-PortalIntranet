<?php
use Mockery;

it('gera a query correta para atualizar o post_modified', function () {
    $wpdb = Mockery::mock(stdClass::class);
    $wpdb->posts = 'wp_posts';

    $wpdb->shouldReceive('query')
        ->once()
        ->withArgs(function ($query) {
            return $query === "UPDATE wp_posts
              SET post_modified = '2025-01-01 00:00:00'
              WHERE ID = '123'";
        })
        ->andReturn(1);

    $resultado = atualizar_post_modified($wpdb, 123, '2025-01-01 00:00:00');

    expect($resultado)->toBe(1);
});