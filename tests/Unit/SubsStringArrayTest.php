<?php

it('substitui corretamente várias strings no subject', function () {
    $replace = [
        'foo' => 'bar',
        'hello' => 'world'
    ];
    $subject = 'foo and hello';
    expect(str_replace_assoc($replace, $subject))->toBe('bar and world');
});

it('não altera o subject se as chaves não existirem', function () {
    $replace = [
        'x' => 'y'
    ];
    $subject = 'foo and hello';
    expect(str_replace_assoc($replace, $subject))->toBe('foo and hello');
});

it('retorna string vazia se o subject for vazio', function () {
    $replace = [
        'foo' => 'bar'
    ];
    $subject = '';
    expect(str_replace_assoc($replace, $subject))->toBe('');
});

it('retorna o subject original se o array de substituição estiver vazio', function () {
    $replace = [];
    $subject = 'foo and hello';
    expect(str_replace_assoc($replace, $subject))->toBe('foo and hello');
});

it('substitui múltiplas ocorrências da mesma string', function () {
    $replace = [
        'foo' => 'bar'
    ];
    $subject = 'foo foo foo';
    expect(str_replace_assoc($replace, $subject))->toBe('bar bar bar');
});

it('substitui strings que contenham caracteres especiais', function () {
    $replace = [
        'f@o!' => 'bar$'
    ];
    $subject = 'Hello f@o! world';
    expect(str_replace_assoc($replace, $subject))->toBe('Hello bar$ world');
});