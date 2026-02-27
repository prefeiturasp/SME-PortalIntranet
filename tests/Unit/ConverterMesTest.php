<?php

it('converte cada mes para sua abreviacao correta', function ($mes, $esperado) {
    expect(converter_mes($mes))->toBe($esperado);
})->with([
    ['01', 'Jan'],
    ['02', 'Fev'],
    ['03', 'Mar'],
    ['04', 'Abr'],
    ['05', 'Mai'],
    ['06', 'Jun'],
    ['07', 'Jul'],
    ['08', 'Ago'],
    ['09', 'Set'],
    ['10', 'Out'],
    ['11', 'Nov'],
    ['12', 'Dez'],
]);

it('retorna string vazia para mes invalido', function () {
    expect(converter_mes('13'))->toBe('');
    expect(converter_mes('00'))->toBe('');
});

it('retorna string vazia para entrada nao numerica', function () {
    expect(converter_mes('abc'))->toBe('');
});

it('retorna string vazia para entrada vazia', function () {
    expect(converter_mes(''))->toBe('');
});
