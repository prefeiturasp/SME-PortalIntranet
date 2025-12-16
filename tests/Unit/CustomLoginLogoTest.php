<?php
namespace Tests\Unit;

uses()->group('login-logo');

it('gera css customizado para login', function () {
    // injeta o template dir que queremos no teste
    \add_filter('custom_login_template_dir', function () {
        return 'https://meusite.com/wp-content/themes/meutema';
    });

    ob_start();
    \custom_login_logo();
    $css = ob_get_clean();

    expect($css)->toContain('background-image: url(https://meusite.com/wp-content/themes/meutema/img/logo_admin.png)');
    expect($css)->toContain('background-image: url(https://meusite.com/wp-content/themes/meutema/img/bg-background.png)');

    // limpa o filtro para não contaminar outros testes
    \remove_all_filters('custom_login_template_dir');
});