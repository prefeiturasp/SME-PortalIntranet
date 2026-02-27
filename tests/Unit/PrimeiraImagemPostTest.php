<?php

it('retorna o ID da primeira imagem do conteúdo com post real', function () {
    global $wp_query;
    $wp_query = new class {
        public $in_the_loop = false;
        public $before_loop = false;
        public function __call($name, $args) { return false; }
    };

    $post_id = wp_insert_post([
        'post_title'  => 'Teste Post',
        'post_status' => 'publish',
    ]);

    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/teste.jpg';
    file_put_contents($file_path, 'fake image content');

    $attachment_url = $upload_dir['url'] . '/teste.jpg';
    $attachment_id = wp_insert_attachment([
        'post_mime_type' => 'image/jpeg',
        'post_title'     => 'Teste Image',
        'post_content'   => '',
        'post_status'    => 'inherit',
        'guid'           => $attachment_url,
    ], $file_path);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_generate_attachment_metadata($attachment_id, $file_path);

    wp_update_post([
        'ID'           => $post_id,
        'post_content' => '<img src="' . $attachment_url . '">',
    ]);

    $resultado = get_first_image($post_id);

    expect($resultado)->toBe($attachment_id);

    // Limpeza com verificação
    wp_delete_post($post_id, true);
    wp_delete_post($attachment_id, true);
    
    // Verifica se o arquivo existe antes de tentar excluir
    if (file_exists($file_path)) {
        @unlink($file_path);
    }
});

it('retorna false quando não há imagem no conteúdo', function () {
    global $wp_query;
    $wp_query = new class {
        public $in_the_loop = false;
        public $before_loop = false;
        public function __call($name, $args) { return false; }
    };

    $post_id = wp_insert_post([
        'post_title'   => 'Post sem imagem',
        'post_content' => 'nenhuma imagem aqui',
        'post_status'  => 'publish',
    ]);

    $resultado = get_first_image($post_id);

    expect($resultado)->toBeFalse();

    wp_delete_post($post_id, true);
});