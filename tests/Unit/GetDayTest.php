<?php

it('retorna o dia da semana correto para cada numero', function ($numero, $diaEsperado) {
    expect(getDay($numero))->toBe($diaEsperado);
})->with([
    [0, 'Domingo'],
    [1, 'Segunda-feira'],
    [2, 'Terça-feira'],
    [3, 'Quarta-feira'],
    [4, 'Quinta-feira'],
    [5, 'Sexta-feira'],
    [6, 'Sábado'],
]);

it('retorna null para um numero de dia invalido', function () {
    expect(getDay(7))->toBeNull();
    expect(getDay(-1))->toBeNull();
});

it('retorna null para entrada nao numerica', function () {
    expect(getDay('abc'))->toBeNull();
    expect(getDay(null))->toBeNull();
});
