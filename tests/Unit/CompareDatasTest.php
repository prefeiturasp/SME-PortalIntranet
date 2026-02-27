<?php

it('retorna 0 quando as datas são iguais', function () {
    $a = (object) ['post_date' => '2025-09-15 10:00:00'];
    $b = (object) ['post_date' => '2025-09-15 10:00:00'];

    expect(sort_objects_by_date($a, $b))->toBe(0);
});

it('retorna -1 quando a data de a é maior que b', function () {
    $a = (object) ['post_date' => '2025-09-16 10:00:00'];
    $b = (object) ['post_date' => '2025-09-15 10:00:00'];

    expect(sort_objects_by_date($a, $b))->toBe(-1);
});

it('retorna 1 quando a data de a é menor que b', function () {
    $a = (object) ['post_date' => '2025-09-14 10:00:00'];
    $b = (object) ['post_date' => '2025-09-15 10:00:00'];

    expect(sort_objects_by_date($a, $b))->toBe(1);
});