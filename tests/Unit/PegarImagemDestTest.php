<?php

afterEach(function () {
    // Limpar todos os posts e attachments criados
    $posts = get_posts([
        'post_type' => ['post', 'attachment'],
        'numberposts' => -1,
        'post_status' => 'any'
    ]);
    
    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
    
    // Limpar arquivos de teste se existirem
    $upload_dir = wp_upload_dir();
    $files_to_clean = ['featured.jpg', 'conteudo.jpg'];
    
    foreach ($files_to_clean as $filename) {
        $file_path = $upload_dir['path'] . '/' . $filename;
        if (file_exists($file_path)) {
            @unlink($file_path);
        }
    }
});

it('retorna a featured image se existir', function () {
    global $wp_query;
    $wp_query = new class {
        public $in_the_loop = false;
        public $before_loop = false;
        public function __call($name, $args) { return false; }
    };

    $post_id = wp_insert_post([
        'post_title'  => 'Post com Featured',
        'post_status' => 'publish',
    ]);

    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/featured.jpg';
    file_put_contents($file_path, 'fake image content');

    $attachment_url = $upload_dir['url'] . '/featured.jpg';
    $attachment_id = wp_insert_attachment([
        'post_mime_type' => 'image/jpeg',
        'post_title'     => 'Featured Image',
        'post_content'   => '',
        'post_status'    => 'inherit',
        'guid'           => $attachment_url,
    ], $file_path);

    set_post_thumbnail($post_id, $attachment_id);

    require_once ABSPATH . 'wp-admin/includes/image.php';
    wp_generate_attachment_metadata($attachment_id, $file_path);

    $resultado = get_thumb($post_id);

    expect($resultado[0])->toContain('featured.jpg');
    expect($resultado[1])->toBe('Post com Featured');

    wp_delete_post($post_id, true);
    wp_delete_post($attachment_id, true);
    if (file_exists($file_path)) {
        @unlink($file_path);
    }
});

it('retorna a primeira imagem do conteúdo se não houver featured', function () {
    global $wp_query;
    $wp_query = new class {
        public $in_the_loop = false;
        public $before_loop = false;
        public function __call($name, $args) { return false; }
    };

    $post_id = wp_insert_post([
        'post_title'  => 'Post com imagem no conteúdo',
        'post_status' => 'publish',
    ]);

    $upload_dir = wp_upload_dir();
    $file_path = $upload_dir['path'] . '/conteudo.jpg';
    file_put_contents($file_path, 'fake image content');

    $attachment_url = $upload_dir['url'] . '/conteudo.jpg';
    $attachment_id = wp_insert_attachment([
        'post_mime_type' => 'image/jpeg',
        'post_title'     => 'Imagem Conteudo',
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

    $resultado = get_thumb($post_id);

    expect($resultado[0])->toContain('conteudo.jpg');
    expect($resultado[1])->toBe('Post com imagem no conteúdo');

    wp_delete_post($post_id, true);
    wp_delete_post($attachment_id, true);
    if (file_exists($file_path)) {
        @unlink($file_path);
    }
});

it('retorna placeholder se não houver nenhuma imagem', function () {
    global $wp_query;
    $wp_query = new class {
        public $in_the_loop = false;
        public $before_loop = false;
        public function __call($name, $args) { return false; }
    };

    $post_id = wp_insert_post([
        'post_title'  => 'Post sem imagem',
        'post_status' => 'publish',
    ]);

    $resultado = get_thumb($post_id);

    expect($resultado[0])->toBe('https://hom-educacao.sme.prefeitura.sp.gov.br/wp-content/uploads/2020/03/placeholder06.jpg');
    expect($resultado[1])->toBe('Post sem imagem');

    wp_delete_post($post_id, true);
});