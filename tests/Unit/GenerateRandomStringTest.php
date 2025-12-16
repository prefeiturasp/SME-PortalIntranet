<?php

it('gera uma string com o tamanho correto', function () {
    $result = generateRandomString(16);
    expect(strlen($result))->toBe(16);
});

it('gera apenas caracteres válidos', function () {
    $result = generateRandomString(20);
    expect($result)->toMatch('/^[0-9a-zA-Z]+$/');
});

it('gera strings diferentes na maioria das vezes', function () {
    $first  = generateRandomString(10);
    $second = generateRandomString(10);
    expect($first)->not->toBe($second);
});