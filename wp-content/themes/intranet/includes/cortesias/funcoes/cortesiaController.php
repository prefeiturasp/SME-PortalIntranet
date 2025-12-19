<?php

if ( is_user_logged_in() ){
    
    add_action('acf/input/admin_footer', 'cortesias_atualiza_endereco_com_observer');
    add_action('acf/input/admin_footer', 'valida_campos_obrigatorios');

	wp_enqueue_script('bootstrap-sorteio-js');
	wp_enqueue_style('datatables-css');
	wp_enqueue_script('datatables-js');
}

// Preenche o campo de endereço (ACF) com base no local (ACF) selecionado.
function cortesias_atualiza_endereco_com_observer() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'cortesias') return;

    $tags = get_terms([
        'taxonomy' => 'post_tag',
        'hide_empty' => false,
    ]);

    $dados = [];
    foreach ($tags as $tag) {
        $dados[$tag->term_id] = esc_js($tag->description);
    }

    ?>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const descricoes = <?php echo json_encode($dados); ?>;
        const select = document.querySelector('#local-evento select');
        const inputEndereco = document.querySelector('#endereco-evento input[type="text"]');

        function atualizarEndereco(termId) {
            const descricao = descricoes[termId];
            if (descricao) {
                inputEndereco.value = descricao;
                inputEndereco.setAttribute('readonly', 'readonly');
            } else {
                inputEndereco.value = '';
                inputEndereco.removeAttribute('readonly');
            }
        }

        function processarSelecao() {
            const termId = parseInt(select.value);
            if (!termId) return;

            if (!(termId in descricoes)) {
                // novo termo
                inputEndereco.value = '';
                inputEndereco.removeAttribute('readonly');
            } else {
                atualizarEndereco(termId);
            }
        }

        if (select && inputEndereco) {
            jQuery(select).on('select2:select', function () {
                processarSelecao();
            });

            // Executa a primeira vez
            processarSelecao();

            // Observa mudanças no select (ex: novo termo adicionado via botão "+")
            const observer = new MutationObserver(() => {
                processarSelecao();
            });

            observer.observe(select, {
                childList: true,
                subtree: true,
            });
        }
    });
    </script>
    <?php
}

// Adiciona a obrigatoriedade no campo de link quando a administração dos ingressos e feita pelo parceiro.
function valida_campos_obrigatorios() {
    $screen = get_current_screen();
    if ($screen->post_type !== 'cortesias') return;

    ?>
    <script>
    jQuery(function ($) {

        function validaCamposObrigatorios() {
            const admIngressos = $('[data-name="administracao_ingressos"] label.selected input').val();
            const $linkInfo = $('[data-name="link_infos"]');
            const $linkTitle = $('[data-name="texto_do_link"]');

            if ( admIngressos === 'parceiro' ) {
                $linkInfo.find('input[type="url"]').first().prop("required", true);
                $linkInfo.find('.acf-label label').append('<span class="acf-required"> *</span>');

                $linkTitle.find('input[type="text"]').first().prop("required", true);
                $linkTitle.find('.acf-label label').append('<span class="acf-required"> *</span>');

            } else {
                $linkInfo.find('input[type="url"]').first().prop("required", false);
                $linkInfo.find('.acf-input .acf-error-message').remove();
                $linkInfo.find('.acf-label label span.acf-required').remove();

                $linkTitle.find('input[type="text"]').first().prop("required", false);
                $linkTitle.find('.acf-input .acf-error-message').remove();
                $linkTitle.find('.acf-label label span.acf-required').remove()
            }
        }

        $('[data-name="administracao_ingressos"] input[type="radio"]').on('change', function () {
            validaCamposObrigatorios();
        });

        validaCamposObrigatorios();
    })
    </script>
    <?php
}

function resgatar_cortesia( array $params ) {

    global $wpdb;

    $user_id   = (int) $params['user_id'];
    $cpf = isset($params['cpf']) ? preg_replace('/\D/', '', $params['cpf']) : '';
    $data_id   = (int) $params['acf_id'];
    $quantidade = (int) $params['qtd'];
    $cortesia_id = (int) $params['post_id'];
    $nome_completo = sanitize_text_field($params['nome_completo']);
    $email_institucional = sanitize_email($params['email_institucional']);
    $email_secundario = sanitize_email($params['email_secundario']);
    $celular = sanitize_text_field($params['celular']);
    $dre = sanitize_text_field($params['dre']);
    $telefone_comercial = sanitize_text_field($params['telefone_comercial']);
    $cargo_principal = sanitize_text_field($params['cargo_principal']);
    $unidade_setor = sanitize_text_field($params['unidade_setor']);
    $disciplina = sanitize_text_field($params['disciplina']);
    $ciente = isset($params['ciente']) ? 1 : 0;

    $limite_por_usuario = get_field( 'quantidade_ingressos_inscrito', $cortesia_id );

    if ($user_id <= 0 || $data_id <= 0 || $quantidade <= 0) {
        return new WP_Error( 'invalid_args', 'Parâmetros inválidos' );
    }

    if ( $quantidade > $limite_por_usuario ) {
        return new WP_Error( 'invalid_quantity', 'Quantidade inválida', ['limite_por_usuario' => $limite_por_usuario]);
    }

    try {

        $wpdb->query('START TRANSACTION');

        $tabela_inscricoes = $wpdb->prefix . 'cortesias_inscricoes';
        $tabela_datas = $wpdb->prefix . 'cortesias_acf_datas';
        $tabela_movimentos = $wpdb->prefix . 'cortesias_movimentos';

        $data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT estoque_atual
                 FROM $tabela_datas
                 WHERE id = %d AND ativo = 1
                 FOR UPDATE",
                $data_id
            )
        );

        if ( !$data ) {
            $wpdb->query('ROLLBACK');
            return new WP_Error( 'invalid_date', 'Data inválida' );
        }

        if ( $data->estoque_atual < $quantidade ) {
            $wpdb->query('ROLLBACK');
            return new WP_Error( 'insufficient_stock', 'Estoque insuficiente', ['estoque_atual' => $data->estoque_atual] );
        }

        // Verifica se o usuário já resgatou cortesias do evento selecionado.
        $ja_resgatado = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 1
                 FROM $tabela_inscricoes
                 WHERE post_id = %d
                   AND cpf = %s
                 LIMIT 1",
                $cortesia_id,
                $cpf
            )
        );

        if ( $ja_resgatado ) {
            $wpdb->query( 'ROLLBACK' );
            return new WP_Error( 'blocked_for_user', 'Usuário já resgatou cortesias para este evento.', ['ja_resgatado' => $ja_resgatado] );
        }

        //Subtrai a quantidade selecionada pelo usuário do estoque atual
        $updated = $wpdb->query(
            $wpdb->prepare(
                "UPDATE $tabela_datas
                 SET estoque_atual = estoque_atual - %d
                 WHERE id = %d
                   AND estoque_atual >= %d",
                $quantidade,
                $data_id,
                $quantidade
            )
        );

        if ( $updated !== 1 ) {
            $wpdb->query('ROLLBACK');
            return new WP_Error( 'race_condition', 'Concorrência detectada' );
        }

        $dados = [
            'user_id'              => $user_id ?: null,                  // int | null
            'cpf'                  => $cpf,                              // string
            'post_id'              => $cortesia_id,                      // int
            'nome_completo'        => $nome_completo,                    // string
            'email_institucional'  => $email_institucional,              // string
            'email_secundario'     => $email_secundario,                 // string
            'celular'              => $celular,                          // string
            'dre'                  => $dre,                              // string
            'telefone_comercial'   => $telefone_comercial ?: null,       // string | null
            'cargo_principal'      => $cargo_principal,                  // string
            'unidade_setor'        => $unidade_setor,                    // string
            'disciplina'           => $disciplina ?: null,               // string | null
            'ciente'               => $ciente,                           // int (0/1)
            'acf_id'               => $data_id,                          // int
            'qtd'                  => $quantidade,                       // int
            'data_inscricao'       => current_time('mysql'),             // string (datetime)
        ];

        $formatos = [
            '%d', // user_id
            '%s', // cpf
            '%d', // post_id
            '%s', // nome_completo
            '%s', // email_institucional
            '%s', // email_secundario
            '%s', // celular
            '%s', // dre
            '%s', // telefone_comercial
            '%s', // cargo_principal
            '%s', // unidade_setor
            '%s', // disciplina
            '%d', // ciente (bool)
            '%d', // acf_id
            '%d', // qtd
            '%s', // data_inscricao (datetime)
        ];

        $insert_inscricao = $wpdb->insert( $tabela_inscricoes, $dados, $formatos );

        //Registra o movimento na tabela de histórico
        $insert_movimento =$wpdb->insert(
            $tabela_movimentos,
            [
                'cortesia_acf_data_id'  => $data_id,
                'post_id'               => $cortesia_id,
                'user_id'               => $user_id,
                'cpf'                   => $cpf,
                'tipo'                  => 'r',
                'quantidade'            => -$quantidade
            ],
            ['%d', '%d', '%d', '%s', '%s', '%d']
        );

        if ( !$insert_inscricao || !$insert_movimento ) {
            throw new Exception( 'Falha ao realizar o resgate.' );
        }

        $wpdb->query('COMMIT');

        return [
            'sucesso' => true,
            'user_id' => $user_id,
            'quantidade' => $quantidade,
            'data_id' => $data_id,
        ];

    } catch (Exception $e) {
        $wpdb->query('ROLLBACK');
        return new WP_Error( 'internal_error', 'Erro interno', [
            'error' => $e->getMessage(),
            'error_trace' => $e->getTrace()
        ] );
    }
}

function resgatar_cortesia_callback( WP_REST_Request $request ) {

        $resultado = resgatar_cortesia( $request->get_params() );

        if ( is_wp_error($resultado ) ) {
            return new WP_REST_Response([
                'error'   => $resultado->get_error_code(),
                'message' => $resultado->get_error_message(),
                'data'    => $resultado->get_error_data()
            ], 400);
        }

        return new WP_REST_Response( $resultado, 200 );
}

// Sincroniza os valores da tabela int_cortesias_acf_datas com os valores cadastrados no repetidor de datas do post.
function sync_datas_cortesias($post_id) {

    if ($post_id === 'options') {
        return;
    }

    if (!is_numeric($post_id)) {
        return;
    }

    if (get_post_type($post_id) !== 'cortesias') {
        return;
    }

    if (wp_is_post_autosave($post_id) || wp_is_post_revision($post_id)) {
        return;
    }

	global $wpdb;

	$tipo_evento = get_field( 'tipo_evento', $post_id );
	$tabela_datas = $wpdb->prefix . 'cortesias_acf_datas';

	if ( $tipo_evento === 'premio' ) {
		$linhas = get_field('evento_premios', $post_id);
	}

    if ( $tipo_evento === 'data' ) {
		$linhas = get_field('evento_datas', $post_id);
	}

    if (empty($linhas)) {
        return;
    }

    global $wpdb;

    $existentes = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$tabela_datas} WHERE post_id = %d",
            $post_id
        )
    );

    $mapa_existentes = [];
    foreach ( $existentes as $e ) {
        $mapa_existentes[$e->data_evento] = $e;
    }

    $datas_acf = [];

    foreach ($linhas as $linha) {

        $data = $linha['data'];
        $novo_total = (int) $linha['qtd_cortesias'];

        $datas_acf[] = $data;

        if ( isset( $mapa_existentes[$data] ) ) {

            $registro = $mapa_existentes[$data];
            $diferenca = $novo_total - (int) $registro->estoque_total;

            $update = [
                'estoque_total' => $novo_total,
            ];

            if ( $diferenca > 0 ) {
                $update['estoque_atual'] = (int) $registro->estoque_atual + $diferenca;
            }

            $wpdb->update(
                $tabela_datas,
                $update,
                ['id' => $registro->id],
                array_fill(0, count($update), '%d'),
                ['%d']
            );

        } else {

            $wpdb->insert(
                $tabela_datas,
                [
                    'post_id'   => $post_id,
                    'data_evento'   => $data,
                    'estoque_total' => $novo_total,
                    'estoque_atual' => $novo_total,
                    'ativo'         => 1,
                ],
                ['%d', '%s', '%d', '%d', '%d']
            );
        }
    }
}

add_action( 'acf/save_post', 'sync_datas_cortesias', 20 );