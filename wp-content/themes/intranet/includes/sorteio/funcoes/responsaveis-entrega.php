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

function obter_tipo_evento_responsavel_entrega( int $post_id ) {

    $tipo_evento = get_field( 'tipo_evento', $post_id );
    $tipo_evento = is_array( $tipo_evento ) ? ( $tipo_evento[0] ?? '' ) : $tipo_evento;

    return in_array( $tipo_evento, [ 'data', 'premio', 'periodo' ], true ) ? $tipo_evento : '';
}

function obter_chave_responsavel_entrega_sessao( int $post_id, ?string $data_sessao = null ) {

    $tipo_evento = obter_tipo_evento_responsavel_entrega( $post_id );
    if ( !$tipo_evento || ( 'periodo' !== $tipo_evento && empty( $data_sessao ) ) ) {
        return '';
    }
    $chave = "_responsavel_entrega_{$tipo_evento}";

    if ( $tipo_evento != 'periodo' ) {
        $chave .= "_{$data_sessao}";
    }

    return $chave;
}

function obter_uuid_responsavel_entrega_sessao( int $post_id, ?string $data_sessao = null ) {
    $data_sessao = normalizar_data_sessao( $data_sessao );
    $chave = obter_chave_responsavel_entrega_sessao( $post_id, $data_sessao );

    if ( !$chave ) {
        return '';
    }

    return get_post_meta( $post_id, $chave, true );
}

function obter_responsavel_entrega_sessao( int $post_id, ?string $data_sessao = null ) {
    $uuid = obter_uuid_responsavel_entrega_sessao( $post_id, $data_sessao );
    $responsaveis = obter_lista_responsaveis_entrega();

    return $responsaveis[$uuid] ?? null;
}

function exibir_seletor_responsavel_entrega_sessao( int $post_id, ?string $data_sessao = null ) {

    if ( !is_admin() || !current_user_can( 'edit_post', $post_id ) ) {
        return;
    }

    $tipo_evento = obter_tipo_evento_responsavel_entrega( $post_id );
    if ( !obter_chave_responsavel_entrega_sessao( $post_id, $data_sessao ) ) {
        return;
    }

    if ( 'periodo' === $tipo_evento ) {
        $data_sessao = null;
    }
    $responsaveis = obter_lista_responsaveis_entrega();
    $uuid = obter_uuid_responsavel_entrega_sessao( $post_id, $data_sessao );
    $id = 'responsavel-entrega-' . $post_id . '-' . md5( $tipo_evento . '_' . ( $data_sessao ?? '' ) );
    
    ?>
    <div
        class="responsavel-entrega-sessao col-12 mb-3 text-left"
        data-post-id="<?php echo esc_attr( $post_id ); ?>"
        data-sessao="<?php echo esc_attr( $tipo_evento !== 'periodo' ? $data_sessao : '' ); ?>"
        data-nonce="<?php echo esc_attr( wp_create_nonce( 'responsavel_entrega_' . $post_id ) ); ?>"
        >

        <label for="<?php echo esc_attr( $id ); ?>">
            <strong>Atribuir Responsável pela Entrega dos Ingressos/Prêmios</strong>
        </label>

        <select
            id="<?php echo esc_attr( $id ); ?>"
            class="form-control select-responsavel-entrega"
            data-valor-salvo="<?php echo esc_attr( $uuid ); ?>"
            >

            <option value="">Selecione um responsável</option>
            <?php foreach ( $responsaveis as $identificador => $responsavel ) : ?>
                <option value="<?php echo esc_attr( $identificador ); ?>" <?php selected( $uuid, $identificador ); ?>>
                    <?php echo esc_html( $responsavel['nome'] ); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php
}

add_action( 'wp_ajax_salvar_responsavel_entrega_sessao', 'salvar_responsavel_entrega_sessao' );
function salvar_responsavel_entrega_sessao() {

    $post_id = absint( $_POST['post_id'] ?? 0 );
    check_ajax_referer( 'responsavel_entrega_' . $post_id );

    $post_types_permitidos = ['post'];

    if ( !in_array( get_post_type( $post_id ), $post_types_permitidos ) || !current_user_can( 'edit_post', $post_id ) ) {
        wp_send_json_error( [ 'message' => 'Sem permissão para realizar esta ação.' ], 403 );
    }

    $sessao = isset( $_POST['sessao'] ) && is_string( $_POST['sessao'] ) ? sanitize_text_field( wp_unslash( $_POST['sessao'] ) ) : null;
    $uuid = isset( $_POST['uuid'] ) && is_string( $_POST['uuid'] ) ? sanitize_text_field( wp_unslash( $_POST['uuid'] ) ) : '';

    $tipo_evento = obter_tipo_evento_responsavel_entrega( $post_id );

    if ( !$tipo_evento ) {
        wp_send_json_error( [ 'message' => 'Tipo de uso inválido.' ], 400 );
    }

    if ( 'periodo' === $tipo_evento ) {
        // Um unico responsavel para o periodo, sem vinculo com datas.
        $sessao = null;
    } else {
        $repetidor = $tipo_evento === 'premio' ? 'evento_premios' : 'evento_datas';
        $sessoes = get_field( $repetidor, $post_id );

        if ( empty( $sessao ) || !is_array( $sessoes ) || !in_array( $sessao, array_column( $sessoes, 'data' ), true ) ) {
            wp_send_json_error( [ 'message' => 'Sessão inválida. Atualize a página e tente novamente.' ], 400 );
        }
    }

    $responsaveis = obter_lista_responsaveis_entrega();

    if ( $uuid !== '' && !isset( $responsaveis[$uuid] ) ) {
        wp_send_json_error( [ 'message' => 'Responsável não encontrado.' ], 400 );
    }

    $chave = obter_chave_responsavel_entrega_sessao( $post_id, $sessao );

    if ( $uuid === '' ) {
        delete_post_meta( $post_id, $chave );
    } else {
        update_post_meta( $post_id, $chave, $uuid );
    }

    if ( obter_uuid_responsavel_entrega_sessao( $post_id, $sessao ) !== $uuid ) {
        wp_send_json_error( [ 'message' => 'Não foi possível salvar. Tente novamente.' ], 500 );
    }

    wp_send_json_success( [ 'message' => 'Responsável salvo.', 'uuid' => $uuid ] );
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

function normalizar_data_sessao( ?string $valor = null ): ?string {

    if ( !$valor ) {
        return null;
    }
    
    foreach ( [ 'Y-m-d H:i:s', 'Y-m-d-H-i-s' ] as $formato ) {
        $data = DateTimeImmutable::createFromFormat( '!' . $formato, $valor );

        if ( $data && $data->format( $formato ) === $valor ) {
            return $data->format( 'Y-m-d H:i:s' );
        }
    }

    return null;
}

add_action( 'acf/input/admin_enqueue_scripts', 'load_scripts_responsaveis_entrega' );
function load_scripts_responsaveis_entrega() {
    wp_enqueue_style( 'sweetalert-sorteio-css' );
    wp_enqueue_style( 'toastr-sorteio-css' );
    wp_enqueue_script( 'responsaveis-entrega' );
}
