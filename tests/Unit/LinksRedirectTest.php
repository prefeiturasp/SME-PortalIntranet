<?php

use function Pest\Faker\fake;

beforeEach(function () {
    // Mock simples de convert_chars_url, substituindo acentos por regex
    if (!function_exists('convert_chars_url')) {
        function convert_chars_url($string) {
            $map = ['á'=>'(.*)','à'=>'(.*)','ç'=>'(.*)'];
            return str_replace(array_keys($map), array_values($map), $string);
        }
    }
});

it('gera redirects corretamente com barra final', function () {
    $links = [
        ['origem' => 'https://educacao.sme.prefeitura.sp.gov.br/pagina/', 'destino' => '/novo/'],
    ];

    $expected = "RedirectMatch 301 ^/pagina(\/|)$ /novo/" . PHP_EOL;

    expect(generate_redirects_htaccess($links))->toBe($expected);
});

it('gera redirects corretamente sem barra final', function () {
    $links = [
        ['origem' => 'http://educacao.sme.prefeitura.sp.gov.br/pagina', 'destino' => '/novo/'],
    ];

    $expected = "RedirectMatch 301 ^/pagina(\/|)$ /novo/" . PHP_EOL;

    expect(generate_redirects_htaccess($links))->toBe($expected);
});

it('não adiciona regex para /uploads/', function () {
    $links = [
        ['origem' => 'https://educacao.sme.prefeitura.sp.gov.br/uploads/imagem.jpg', 'destino' => '/novo/'],
    ];

    $expected = "RedirectMatch 301 /uploads/imagem.jpg /novo/" . PHP_EOL;

    expect(generate_redirects_htaccess($links))->toBe($expected);
});

it('substitui caracteres acentuados', function () {
    $links = [
        ['origem' => 'https://educacao.sme.prefeitura.sp.gov.br/coração', 'destino' => '/novo/'],
    ];

    $expected = "RedirectMatch 301 ^/cora(.*)(.*)o(\/|)$ /novo/" . PHP_EOL;

    expect(generate_redirects_htaccess($links))->toBe($expected);
});