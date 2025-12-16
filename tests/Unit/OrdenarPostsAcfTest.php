<?php

use function PHPUnit\Framework\assertEquals;

it('modifica os argumentos do relacionamento', function () {
    $args = ['post_type' => 'post'];
    $field = [];
    $post_id = 123;

    $result = my_relationship_query($args, $field, $post_id);

    expect($result['orderby'])->toBe('date');
    expect($result['order'])->toBe('DESC');
    expect($result['post_type'])->toBe('post'); // garante que não sobrescreveu outros args
});