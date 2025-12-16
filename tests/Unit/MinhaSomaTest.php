<?php

test('função minha_soma está disponível', function () {
    expect(function_exists('minha_soma'))->toBeTrue();
});

test('soma dois números corretamente', function () {
    expect(minha_soma(2, 3))->toBe(5);
});