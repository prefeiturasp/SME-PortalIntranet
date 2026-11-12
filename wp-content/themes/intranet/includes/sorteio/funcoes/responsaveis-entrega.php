<?php

function obter_lista_responsaveis_entrega() {
    $lista = get_field( 'lista_responsaveis_entrega', 'options' ) ?: [];
    $responsaveis = [];

    foreach ( $lista as $responsavel ) {
        if ( !empty( $responsavel['uuid'] ) && !empty( $responsavel['nome'] ) ) {
            $responsaveis[$responsavel['uuid']] = $responsavel;
        }
    }

    return $responsaveis;
}

function obter_chave_responsavel_entrega_sessao( string $data_sessao ) {
    return '_responsavel_entrega_sessao_' . $data_sessao;
}

function obter_uuid_responsavel_entrega_sessao( int $post_id, string $data_sessao ) {
    $chave = obter_chave_responsavel_entrega_sessao( $data_sessao );

    return get_post_meta( $post_id, $chave, true );
}

function obter_responsavel_entrega_sessao( int $post_id, string $data_sessao ) {
    $uuid = obter_uuid_responsavel_entrega_sessao( $post_id, $data_sessao );
    $responsaveis = obter_lista_responsaveis_entrega();

    return $responsaveis[$uuid] ?? null;
}

// Adiciona a validação de tamanho no arquivo de imagem do repetidor de responsáveis pela entrega.
add_filter( 'acf/validate_value/name=foto', 'validar_tamanho_foto_responsavel_entrega', 10, 4 );
function validar_tamanho_foto_responsavel_entrega( $valid, $value, $field, $input ) {

    if ( $valid !== true || empty( $value ) ) {
        return $valid;
    }

    $repetidor = acf_get_field( $field['parent'] );

    if ( !$repetidor || 'lista_responsaveis_entrega' !== $repetidor['name'] ) {
        return $valid;
    }

    $anexo_id = absint( $value );
    $arquivo = wp_get_original_image_path( $anexo_id );

    if ( !$arquivo ) {
        $arquivo = get_attached_file( $anexo_id );
    }

    if ( $arquivo && is_file( $arquivo ) && filesize( $arquivo ) > 5 * MB_IN_BYTES ) {
        return 'A imagem deve possuir tamanho máximo de 5 MB.';
    }

    return $valid;
}

// Oculta o campo de identificação do responsável (uuid).
add_filter( 'acf/prepare_field/name=uuid', 'ocultar_uuid_responsavel_entrega' );
function ocultar_uuid_responsavel_entrega( $field ) {

    $repetidor = acf_get_field( $field['parent'] );

    if ( $repetidor && 'lista_responsaveis_entrega' === $repetidor['name'] ) {
        $field['readonly'] = 1;
        $field['required'] = 0;
        $field['wrapper']['class'] = trim( ( $field['wrapper']['class'] ?? '' ) . ' acf-hidden' );
    }

    return $field;
}

// Gera identificadores para as linhas sem UUID depois que o ACF salvar o cadastro.
add_action( 'acf/save_post', 'gerar_uuids_responsaveis_entrega', 20 );
function gerar_uuids_responsaveis_entrega( $post_id ) {
    $campo = get_field_object( 'lista_responsaveis_entrega', $post_id, false );

    if ( !$campo || !isset( $_POST['acf'][$campo['key']] ) ) {
        return;
    }

    $responsaveis = get_field( $campo['key'], $post_id ) ?: [];

    foreach ( $responsaveis as $key => $responsavel ) {
        $uuid = $responsavel['uuid'] ?? '';

        if ( !$uuid ) {

            $uuid = wp_generate_uuid4();
            update_sub_field( [$campo['key'], $key + 1, 'uuid'], $uuid, $post_id );
        }

    }
}

add_action( 'acf/input/admin_enqueue_scripts', 'load_scripts_responsaveis_entrega' );
function load_scripts_responsaveis_entrega() {
    wp_enqueue_style( 'sweetalert-sorteio-css' );
    wp_enqueue_script( 'responsaveis-entrega' );
}
