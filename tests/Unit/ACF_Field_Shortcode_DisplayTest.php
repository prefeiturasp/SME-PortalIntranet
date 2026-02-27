<?php

use Brain\Monkey;
use Brain\Monkey\Functions;

beforeEach(function () {

    Monkey\setUp();

    /*
     |------------------------------------------------------------------
     | Mock mínimo da classe base do ACF
     |------------------------------------------------------------------
     */
    if (!class_exists('acf_field')) {
        abstract class acf_field {
            public $name;
            public $label;
            public $category;
            public $defaults;

            public function __construct() {}
        }
    }

    /*
     |------------------------------------------------------------------
     | Mock da função de tradução
     |------------------------------------------------------------------
     | NÃO usamos expect aqui para evitar DefinedTooEarly
     */
    if (!function_exists('__')) {
        function __($text) {
            return $text;
        }
    }

    /*
     |------------------------------------------------------------------
     | Carrega o arquivo REAL da classe
     |------------------------------------------------------------------
     */
    require_once __DIR__ . '/../../wp-content/themes/intranet/acf/acf-field-shortcode-display.php';
});

afterEach(function () {
    Monkey\tearDown();
});

/*
|--------------------------------------------------------------------------
| Testes
|--------------------------------------------------------------------------
*/

test('classe é instanciada corretamente', function () {
    $field = new ACF_Field_Shortcode_Display();

    expect($field)
        ->toBeInstanceOf(ACF_Field_Shortcode_Display::class)
        ->and($field->name)->toBe('shortcode_display')
        ->and($field->label)->toBe('Exibição de Shortcode')
        ->and($field->category)->toBe('layout');
});

test('render_field exibe mensagem quando shortcode está vazio', function () {
    $field = new ACF_Field_Shortcode_Display();

    ob_start();
    $field->render_field(['shortcode' => '']);
    $output = ob_get_clean();

    expect($output)->toContain('Nenhum shortcode configurado.');
});

test('render_field imprime o shortcode quando informado', function () {
    $field = new ACF_Field_Shortcode_Display();

    ob_start();
    $field->render_field(['shortcode' => '[meu_shortcode]']);
    $output = ob_get_clean();

    // Não testamos o WordPress, apenas o comportamento da classe
    expect($output)->toBe('[meu_shortcode]');
});

test('format_value retorna o shortcode configurado', function () {
    $field = new ACF_Field_Shortcode_Display();

    $result = $field->format_value(null, 1, [
        'shortcode' => '[outro_shortcode]',
    ]);

    expect($result)->toBe('[outro_shortcode]');
});

test('render_field_settings registra configuração corretamente', function () {
    Functions\expect('acf_render_field_setting')
        ->once()
        ->with(
            \Mockery::type('array'),
            \Mockery::subset([
                'label'        => 'Shortcode',
                'instructions' => 'Insira o shortcode que será executado',
                'type'         => 'text',
                'name'         => 'shortcode',
            ])
        );

    $field = new ACF_Field_Shortcode_Display();
    $field->render_field_settings(['key' => 'campo_test']);
});