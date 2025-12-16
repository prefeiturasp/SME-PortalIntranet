<?php

it('substitui caracteres acentuados simples', function () {
    expect(convert_chars_url('àáâãèéê'))->toBe('(.*)(.*)(.*)(.*)(.*)(.*)(.*)');
});

it('substitui caracteres acentuados com codificação UTF-8', function () {
    expect(convert_chars_url('a%CC%80a%CC%81'))->toBe('(.*)(.*)');
});

it('substitui caracteres mistos e múltiplos', function () {
    $input = 'Olá, você está testando í e ú!';
    $expected = 'Ol(.*), voc(.*) est(.*) testando (.*) e (.*)!';
    expect(convert_chars_url($input))->toBe($expected); // Ol(.*), voc(.*) est(.*) testando (.*) e (.*)!
});

it('não altera strings sem acentos', function () {
    $input = 'Testando sem acentos';
    expect(convert_chars_url($input))->toBe('Testando sem acentos');
});

it('substitui o ç corretamente', function () {
    $input = 'coração';
    $expected = 'cora(.*)(.*)o'; // correto
    expect(convert_chars_url($input))->toBe($expected);
});

it('funciona com string vazia', function () {
    expect(convert_chars_url(''))->toBe('');
});