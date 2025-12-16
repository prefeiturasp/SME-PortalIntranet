<?php

use Brain\Monkey\Functions;

beforeEach(function () {
    // Mocks de funções globais usadas pelo acf_reciprocal_relationship
    Functions\when('acf_get_field')->alias(function ($key) {
        return [
            'name' => $key === 'field_5fecb928c7571' ? 'grupos' : 'paginas',
            'key'  => $key,
        ];
    });

    // Vamos simular um "banco" de metadados em memória
    global $mock_meta;
    $mock_meta = [];

    Functions\when('get_post_meta')->alias(function ($post_id, $key) {
        global $mock_meta;
        return $mock_meta[$post_id][$key] ?? [];
    });

    Functions\when('update_post_meta')->alias(function ($post_id, $key, $value) {
        global $mock_meta;
        $mock_meta[$post_id][$key] = $value;
        return true;
    });
});

it('cria e remove relacionamentos recíprocos corretamente', function () {
    global $mock_meta;

    $post_a = 1;
    $post_b = 2;

    // Executa a função para criar relacionamento
    acf_reciprocal_relationship(
        [$post_b],
        $post_a,
        ['key' => 'field_5fecb928c7571'],
        'field_5fecb928c7571',
        'field_616875a8b6c80'
    );

    // Verifica se os relacionamentos foram criados nos dois lados
    expect($mock_meta[$post_a]['grupos'])->toContain($post_b);
    expect($mock_meta[$post_b]['paginas'])->toContain($post_a);

    // Executa a função para remover relacionamento
    acf_reciprocal_relationship(
        [],
        $post_a,
        ['key' => 'field_5fecb928c7571'],
        'field_5fecb928c7571',
        'field_616875a8b6c80'
    );

    // Verifica se ambos os lados foram limpos
    expect($mock_meta[$post_a]['grupos'] ?? [])->toBeEmpty();
    expect($mock_meta[$post_b]['paginas'] ?? [])->toBeEmpty();
});