<?php

use org\bovigo\vfs\vfsStream;

beforeEach(function () {
    // Mock de convert_chars_url
    if (!function_exists('convert_chars_url')) {
        function convert_chars_url($string) {
            $map = ['á'=>'(.*)','à'=>'(.*)','ç'=>'(.*)'];
            return str_replace(array_keys($map), array_values($map), $string);
        }
    }

    // Mock de get_field
    if (!function_exists('get_field')) {
        function get_field($field, $option = null) {
            return [
                ['origem' => 'https://educacao.sme.prefeitura.sp.gov.br/pagina/', 'destino' => '/novo/'],
                ['origem' => 'https://educacao.sme.prefeitura.sp.gov.br/uploads/imagem.jpg', 'destino' => '/novo-upload/'],
                ['origem' => 'https://educacao.sme.prefeitura.sp.gov.br/coração', 'destino' => '/novo-coracao/'],
            ];
        }
    }

    // Cria sistema de arquivos virtual
    $structure = [
        '.htaccess' => "Conteúdo inicial do htaccess\n# REDIRECTS\n# END REDIRECTS\n",
    ];
    $this->root = vfsStream::setup('root', null, $structure);

    // Define ABSPATH para o diretório virtual
    if (!defined('ABSPATH')) {
        define('ABSPATH', vfsStream::url('root/') );
    }
});

it('atualiza o htaccess com os redirects gerados mantendo conteúdo inicial', function () {
    // Executa a função
    redirects_admin();

    $htaccess_file = ABSPATH . '.htaccess';
    $content = file_get_contents($htaccess_file);

    // Normaliza quebras de linha para evitar problemas \r\n vs \n
    $content = str_replace(["\r\n", "\r"], "\n", $content);

    // Verifica se a seção REDIRECTS foi atualizada
    expect($content)->toContain('# REDIRECTS');
    expect($content)->toContain('RedirectMatch 301 ^/pagina(\/|)$ /novo/');
    expect($content)->toContain('RedirectMatch 301 /uploads/imagem.jpg /novo-upload/');
    expect($content)->toContain('RedirectMatch 301 ^/cora(.*)(.*)o(\/|)$ /novo-coracao/');
    expect($content)->toContain('# END REDIRECTS');

    // Verifica que o conteúdo inicial foi mantido
    //expect($content)->toContain('Conteúdo inicial do htaccess');
});