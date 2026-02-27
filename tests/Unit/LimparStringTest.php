<?php

use function Pest\Faker\faker;

it('substitui espaços por hífens e remove caracteres especiais', function () {
    // Letras simples
    expect(clean('Hello World'))->toBe('Hello-World');

    // Letras com acentos e pontuação
    expect(clean('Olá, Mundo!'))->toBe('Ol-Mundo');

    // Apenas números
    expect(clean('123 456'))->toBe('123-456');

    // String vazia
    expect(clean(''))->toBe('');

    // Múltiplos espaços
    expect(clean('A  B'))->toBe('A--B');

    // Caracteres especiais misturados
    expect(clean('C@f#e$123'))->toBe('Cfe123');

    // Letras maiúsculas e minúsculas
    expect(clean('TeSt CaSe'))->toBe('TeSt-CaSe');

    // Espaços no início e no fim
    expect(clean('  inicio e fim  '))->toBe('--inicio-e-fim--');
});