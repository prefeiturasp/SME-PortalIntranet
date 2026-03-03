<?php

use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;

if ( is_user_logged_in() ){

    add_action('admin_head', 'meu_css_personalizado_cortesias');
    
    add_action('acf/input/admin_footer', 'cortesias_atualiza_endereco_com_observer');
    add_action('acf/input/admin_footer', 'valida_campos_obrigatorios');

	wp_enqueue_script('bootstrap-sorteio-js');
	wp_enqueue_style('datatables-css');
	wp_enqueue_script('datatables-js');

    add_shortcode('exibe_tab_participantes_cortesias', 'exibeTabParticipantesCortesias');
    add_shortcode('exibe_tab_resultado_cortesias', 'exibeTabResultadoCortesias');
    add_action('wp_ajax_remove_participante_cortesia', 'processa_ajax_remove_participante_cortesias');
    add_action('wp_ajax_enviar_email_cancelar_cortesia', 'enviar_email_cancelar_cortesia_ajax');
	add_action('admin_enqueue_scripts', 'scripts_exportacao_cortesias_admin');
	require_once get_template_directory() . '/includes/exportacao-cortesias.php';

    //Action para retornar a listagem de participantes exibida na tela de sanção.
    add_action('wp_ajax_exibir_lista_participantes_cortesia_sancao', 'exibir_lista_participantes_cortesia_sancao_callback');
}

function meu_css_personalizado_cortesias() {//** OK */
    $screen = get_current_screen();
    // Aplica só no post type desejado
    if ($screen->post_type !== 'cortesias') return;
	wp_enqueue_style('bootstrap-sorteio-css');
	wp_enqueue_style('style-sorteio-css');
	wp_enqueue_style('toggle-sorteio-css');
	wp_enqueue_style('sweetalert-sorteio-css');
	wp_enqueue_style('toastr-sorteio-css');

	//wp_enqueue_script('jquery-ui');
	wp_enqueue_script('sweetalert-sorteio-js');
	wp_enqueue_script('toggle-sorteio-js');
	wp_enqueue_script('toastr-sorteio-js');
	wp_enqueue_script('cortesia-js');
	wp_enqueue_script('bootstrap-sorteio-js');

	adicionaPostMetaExibirResultado();
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
    $programa_estagio = absint($params['programa_estagio']) ?? null;

    $limite_por_usuario = get_field( 'quantidade_ingressos_inscrito', $cortesia_id );

    if ( $data_id <= 0 ) {
        return new WP_Error( 'invalid_args', 'Parâmetros inválidos' );
    }

    if ( $quantidade > $limite_por_usuario ) {
        return new WP_Error( 'invalid_quantity', 'Quantidade inválida', ['limite_por_usuario' => $limite_por_usuario]);
    }

    if ( $quantidade <= 0 ) {
        return new WP_Error( 'zero_quantity', 'Quantidade inválida');
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

        if ( $data->estoque_atual == 0 ) {
            $wpdb->query('ROLLBACK');
            return new WP_Error( 'no_stock', 'Sem estoque disponível' );
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
            'programa_estagio'     => $programa_estagio,                 // int
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
            '%d', // programa_estagio
        ];

        $insert_inscricao = $wpdb->insert( $tabela_inscricoes, $dados, $formatos );

        //Registra o movimento na tabela de histórico
        $insert_movimento = $wpdb->insert(
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

/**
 * Realiza a devolução de uma cortesia previamente resgatada.
 *
 * @param int    $inscricao_id
 *        ID da inscrição de cortesia a ser devolvida.
 *
 * @param bool   $return_info
 *        Define se a função deve retornar informações detalhadas
 *        da operação em caso de sucesso.
 *        - false (default): retorna apenas true
 *        - true: retorna um array com dados da devolução
 *
 * @param string $chave
 *        Chave opcional utilizada para validação do fluxo de cancelamento
 *
 * @param bool   $remover_inscricao
 *        Define se o registro da inscrição deve ser removido após a devolução.
 *        - true (default): remove a inscrição
 *        - false: mantém a inscrição e apenas registra a devolução no histórico
 *
 * @return true|array|WP_Error
 *
 *         Retorna:
 *         - true: devolução realizada com sucesso (quando $return_info = false)
 *         - array: dados da devolução (quando $return_info = true)
 *         - WP_Error: em caso de falha ou validação inválida
 *
*/
function devolver_cortesia( int $inscricao_id, bool $return_info = false, string $chave = '', $remover_inscricao = true, string $data_selecionada = '', string $premio = '' ) {

    global $wpdb;

    if ( !$inscricao_id || $inscricao_id <= 0 ) {
        return new WP_Error( 'invalid_args', 'Parâmetros inválidos' );
    }

    $tabela_inscricoes = $wpdb->prefix . 'cortesias_inscricoes';
    $tabela_datas = $wpdb->prefix . 'cortesias_acf_datas';
    $tabela_mov = $wpdb->prefix . 'cortesias_movimentos';

    try {

        $wpdb->query('START TRANSACTION');

        $inscricao = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT *
                 FROM $tabela_inscricoes
                 WHERE id = %d
                 LIMIT 1
                 FOR UPDATE",
                $inscricao_id,
            )
        );

        if ( !$inscricao ) {
            $wpdb->query('ROLLBACK');
            return new WP_Error(
                'not_found',
                'Não foi possível encontrar a inscrição.'
            );
        }

        $data_id    = (int) $inscricao->acf_id;
        $quantidade = (int) $inscricao->qtd;
        $user_id    = !is_null($inscricao->user_id) ? (int) $inscricao->user_id : null;
        $cpf        = $inscricao->cpf;
        $post_id    = (int) $inscricao->post_id;

        $data = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT estoque_atual
                 FROM $tabela_datas
                 WHERE id = %d
                 FOR UPDATE",
                $data_id
            )
        );

        if ( !$data ) {
            throw new Exception('Não foi possível localizar a cortesia vinculada a esta inscrição.');
        }

        if ( $remover_inscricao ) {

            $inscricao_deletada = $wpdb->delete(
                $tabela_inscricoes,
                ['id' => $inscricao->id],
                ['%d']
            );

            if ( $inscricao_deletada !== 1 ) {
                throw new Exception('Falha ao remover a inscrição');
            }
        }

        $estoque_atualizado = $wpdb->update(
            $tabela_datas,
            [
                'estoque_atual' => $data->estoque_atual + $quantidade
            ],
            ['id' => $data_id],
            ['%d'],
            ['%d']
        );

        if ( $estoque_atualizado !== 1 ) {
            throw new Exception('Falha ao atualizar o estoque');
        }

        $registra_movimento = $wpdb->insert(
            $tabela_mov,
            [
                'cortesia_acf_data_id' => $data_id,
                'post_id'              => $post_id,
                'user_id'              => $user_id,
                'cpf'                  => $cpf,
                'tipo'                 => 'd', // devolução
                'quantidade'           => $quantidade
            ],
            ['%d', '%d', '%d', '%s', '%s', '%d']
        );

        if ( !$registra_movimento ) {
            throw new Exception('Falha ao registrar o movimento');
        }

        $wpdb->query('COMMIT');

        $response_data = [
            'sucesso'    => true,
            'quantidade' => $quantidade,
            'data_id'    => $data_id,
            'post_id'    => $post_id,
            'user_id'    => $user_id,
            'target' => "lista-sorteados-{$data_selecionada}",
        ];

        if($chave && $chave != ''){
            $wpdb->update(
                'int_inscricao_cancelamento',
                ['status' => 2],
                ['chave' => $chave]
            );
        }

        $response_data['total_inscritos'] = retorna_qtd_cortesias_ajax($post_id, $data_selecionada, false, false, '', '', $premio);

        if ( $return_info ) {
            $response_data['inscricao'] = $inscricao;
        }

        return $response_data;

    } catch ( Exception $e ) {

        $wpdb->query('ROLLBACK');

        return new WP_Error(
            'internal_error',
            'Erro ao processar devolução',
            [
                'error' => $e->getMessage(),
            ]
        );
    }
}

function processar_devolucao_cortesia( int $inscricao_id, string $chave = '' ) {

    $resultado = devolver_cortesia($inscricao_id, true, $chave);

    if ( is_wp_error($resultado) ) {
        return $resultado;
    }

    if ( isset($resultado['inscricao']) ) {

        $extra_args = [
            'email_institucional' => $resultado['inscricao']->email_institucional,
            'email_secundario'    => $resultado['inscricao']->email_secundario,
        ];

        if ( is_plugin_active('envia-email-sme/envia-email-sme.php') ) {
            new Envia_Emails_Sorteio_SME(
                null,
                $resultado['user_id'],
                $resultado['post_id'],
                'cancelamento_cortesia',
                '',
                null,
                $extra_args
            );
        }
    }
    
    do_action('sme_cortesia_devolvida', $resultado);

    return $resultado;
}

add_action('wp_ajax_devolver_cortesia', 'devolver_cortesia_ajax_callback');
add_action('wp_ajax_nopriv_devolver_cortesia', 'devolver_cortesia_ajax_callback');

function devolver_cortesia_ajax_callback() {

    check_ajax_referer('devolver_cortesia_nonce', 'nonce');

    $inscricao_id = intval($_POST['inscricao_id']);

    $resultado = processar_devolucao_cortesia($inscricao_id);

    if ( is_wp_error($resultado) ) {
        wp_send_json_error([
            'error'   => $resultado->get_error_code(),
            'message' => $resultado->get_error_message(),
            'data'    => $resultado->get_error_data(),
        ], 400);
    }

    wp_send_json_success($resultado, 200);
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

    $tipo_evento  = get_field('tipo_evento', $post_id);
    $tabela_datas = $wpdb->prefix . 'cortesias_acf_datas';

    if ($tipo_evento === 'premio') {
        $linhas = get_field('evento_premios', $post_id);
    } elseif ($tipo_evento === 'data') {
        $linhas = get_field('evento_datas', $post_id);
    } elseif($tipo_evento === 'periodo') {
        $linhas = get_field( 'evento_periodo', $post_id );
        $encerramento_inscricoes = get_field( 'enc_inscri', $post_id );
    } else {
        return;
    }

    if (empty($linhas)) {
        $wpdb->delete($tabela_datas, ['post_id' => $post_id], ['%d']);
        return;
    }

    $existentes = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$tabela_datas} WHERE post_id = %d",
            $post_id
        )
    );

    $mapa_existentes = [];
    foreach ($existentes as $e) {
        $mapa_existentes[$e->data_evento] = $e;
    }

    $datas_acf = [];

    if ($tipo_evento === 'periodo') {

        $novo_total = (int) $linhas['qtd_cortesias'];

        if (!empty($existentes)) {

            $registro = $existentes[0];

            $consumido = (int) $registro->estoque_total - (int) $registro->estoque_atual;
            $novo_estoque_atual = max(0, $novo_total - $consumido);

            $wpdb->update(
                $tabela_datas,
                [
                    'estoque_total'           => $novo_total,
                    'estoque_atual'           => $novo_estoque_atual,
                    'encerramento_inscricoes' => $encerramento_inscricoes,
                    'premio'                  => null,
                ],
                ['id' => $registro->id],
                ['%d', '%d', '%s', '%s'],
                ['%d']
            );

        } else {

            $wpdb->insert(
                $tabela_datas,
                [
                    'post_id'                 => $post_id,
                    'data_evento'             => null,
                    'estoque_total'           => $novo_total,
                    'estoque_atual'           => $novo_total,
                    'encerramento_inscricoes' => $encerramento_inscricoes,
                    'premio'                  => null,
                ],
                ['%d', '%s', '%d', '%d', '%s', '%s']
            );
        }

        return;
    }

    foreach ($linhas as $linha) {

            
            $data = $linha['data'];
            $novo_total = (int) $linha['qtd_cortesias'];
            $encerramento = $linha['encerramento_inscricoes'];
            if($linha['premio']){
                $premio = $linha['premio'];
            } else {
                $premio = null;
            }


        $datas_acf[] = $data;

        if (isset($mapa_existentes[$data])) {

            $registro = $mapa_existentes[$data];

            $consumido = (int) $registro->estoque_total - (int) $registro->estoque_atual;
            $novo_estoque_atual = max(0, $novo_total - $consumido);

            $wpdb->update(
                $tabela_datas,
                [
                    'estoque_total'           => $novo_total,
                    'estoque_atual'           => $novo_estoque_atual,
                    'encerramento_inscricoes' => $encerramento,
                    'premio'                  => $premio,
                ],
                ['id' => $registro->id],
                ['%d', '%d', '%s', '%s'],
                ['%d']
            );

        } else {

            $wpdb->insert(
                $tabela_datas,
                [
                        'post_id'        => $post_id,
                        'data_evento'    => $data,
                        'estoque_total'  => $novo_total,
                        'estoque_atual'  => $novo_total,
                        'encerramento_inscricoes' => $encerramento,
                        'premio'         => $premio,
                ],
                ['%d', '%s', '%d', '%d', '%s', '%s']
            );
        }
    }

    // ===============================
    // Remove datas excluídas no ACF
    // ===============================
    foreach ($mapa_existentes as $data_evento => $registro) {
        if (!in_array($data_evento, $datas_acf, true)) {
            $wpdb->delete(
                $tabela_datas,
                ['id' => $registro->id],
                ['%d']
            );
        }
    }
}


add_action( 'acf/save_post', 'sync_datas_cortesias', 20 );

//Retorna as datas disponíveis para inscrição
function get_datas_diponiveis( $post_id ) {
    global $wpdb;

    $tabela = $wpdb->prefix . 'cortesias_acf_datas';

    $hoje = obter_data_com_timezone( 'Y-m-d', 'America/Sao_Paulo' );

    $datas = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT 
                id,
                data_evento,
                estoque_atual,
                encerramento_inscricoes,
                premio
             FROM {$tabela}
             WHERE post_id = %d
               AND ativo = 1
               AND estoque_atual > 0
               AND encerramento_inscricoes > %s
             ORDER BY data_evento ASC",
            $post_id,
            $hoje
        )
    );

    if ( empty( $datas ) ) {
        return [];
    }

    return $datas;
}

function exibeTabParticipantesCortesias ($post_id = null) {//** OK */
	if (!$post_id) {
		$post_id = get_the_id();
	}

	$tipo_evento = get_field('tipo_evento', $post_id);
	$tipo_evento = ( is_array( $tipo_evento ) && !empty( $tipo_evento ) ) ? $tipo_evento[0] : $tipo_evento;

	if ( $tipo_evento == 'premio' ) {
		$datas = get_field('evento_premios', $post_id);
	} elseif ( $tipo_evento == 'data' ) {
		$datas = get_field('evento_datas', $post_id);
	} else {
		$datas = [];
	}
	
	$unica = false;

	if(!$datas){
        $datas[]['data'] = get_field('data_sorteio', $post_id);
		$unica = true;
    }

	if (!empty($datas)) {
		$args = [
			'post_id' => $post_id,
			'datas' => $datas,
			'unica' => $unica,
			'tipo_evento' => $tipo_evento,
		];

		echo "<div class='acf-label'><label>Lista de Participantes Inscritos:</label></div>";
		echo '<div class="row">';
			echo '<div class="col text-right">';
				echo '<button type="button" class="btn btn-outline-success btn-sm mb-3" id="exportar-cortesias" data-post-id="' . $post_id . '" data-responsavel="' . $responsavel . '">Baixar relatório de inscritos</button>';
			echo '</div>';
		echo '</div>';

		get_template_part( '/includes/cortesias/template-parts/lista-participantes-cortesias-container', null, $args );		
	}

	$exibeNaPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);
	if ($exibeNaPagina == '1') {
		$exibeNaPagina = 'checked';
	} else {
		$exibeNaPagina = '';
	}

    echo '<div id="conteudo-sortear-novamente">';
        echo '<div class="container margem-superior">';
            echo '<div class="row">';
                echo '<div class="col-35 divulgacao">';
                    echo '<font class="status">DIVULGAR RESULTADO NA PÁGINA:</font>';
                echo '</div>';
                echo '<div class="col divulgacao">';
                    echo '<input class="toggle-checkbox" name="divulgar-resultado" ' . $exibeNaPagina . ' id="divulgar-resultado" data-onstyle="success" type="checkbox" data-toggle="toggle" data-on="SIM" data-off="NÃO" data-style="ios" data-size="xs">';
                    echo '<span id="divulgar-resultado-gif" class="spinner"></span>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
    
}

function exibeTabResultadoCortesias($idPost = false){//** OK */
	global $wpdb;

	!$idPost ? $post_id = get_the_id() : $post_id = $idPost;
	
	$exibicaoPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);

	$dataSorteio = get_post_meta($post_id, 'data_sorteio', true);
	$horaSorteio = get_post_meta($post_id, 'hora_sorteio', true);

	$dataSorteio = date('d/m/Y', strtotime($dataSorteio));
	$horaSorteio = date('H:i', strtotime($horaSorteio));
	$tipo_evento = get_field('tipo_evento', $post_id);
    $requerConfirmacao = get_field('confirm_presen', $post_id);

	$html = '';
	if ($exibicaoPagina == '1') {		
			
        echo '<div class="row mb-4 exibir-lista-sorteados">';
            echo '<div class="col">';
                echo '<span class="title-info">Lista de participantes</span>';
                echo '<p>Se o seu nome estiver na lista de participantes, acompanhe o e-mail cadastrado para verificar se há necessidade de confirmação da participação.</p>';
            echo '</div>';
        echo '</div>';

        if($tipo_evento == 'premio'){
			$datas_disponiveis = get_field('evento_premios', $post_id);			
		} else {

			if ( $tipo_evento == 'data' || !$tipo_evento ) {
				$datas_disponiveis = get_field('evento_datas', $post_id);
			}			
		}
		
		$tabela =  'int_cortesias_inscricoes';
        $tabela_acf    = 'int_cortesias_acf_datas';    
        
        echo '<div class="accordion" id="accordion-sorteados">';

		if($datas_disponiveis && $datas_disponiveis != ''){
			
			// Ordenar por data do evento
			usort($datas_disponiveis, function($a, $b) {
				return strtotime($a['data']) - strtotime($b['data']);
			});

			$exibir = 0;

			foreach ($datas_disponiveis as $key => $data) {

                $where_confirmacao = '';

                if ($requerConfirmacao) {
                    $where_confirmacao = ' AND i.confirmou_presenca = 1';
                }

                $sql = $wpdb->prepare(
                    "
                    SELECT i.*
                    FROM {$tabela} AS i
                    INNER JOIN {$tabela_acf} AS a
                        ON a.id = i.acf_id
                    WHERE i.post_id = %d
                    AND a.data_evento = %s
                    {$where_confirmacao}
                    ORDER BY i.data_inscricao ASC
                    ",
                    $post_id,
                    $data['data']
                );

                $resultados = $wpdb->get_results($sql, ARRAY_A);                  

				if (!empty($resultados)) {
					$dataSorteio = date('d/m/Y', strtotime($data['data_sorteio']));
					$dataEvento = date('d/m/Y H\hi', strtotime($data['data']));
					$dataEvento = str_replace('h00', 'h', $dataEvento);

					foreach ($resultados as $linha) {
						if (isset($linha['user_id'])) {
							$tipo = get_user_meta($linha['user_id'], 'parceira', true);
							if ($tipo == 1) {
								$tipo = 'PARCEIRO';
							} else if ($tipo == 0) {
								$tipo = 'SERVIDOR';
							}
						} else { // Programa 1, 2 ou 3
							if (isset($linha['programa_estagio'])) {
								$tipo = 'ESTAGIÁRIO';
							}
						} 

						$item = file_get_contents(get_template_directory().'/includes/sorteio/conteudo-tab-lista-sorteados.html');
						$item = str_replace('{NOME-SORTEADO}',     esc_html(mb_strtoupper($linha['nome_completo']), 'UTF-8'),   $item);
						$item = str_replace('{TIPO-SORTEADO}',    esc_html(mb_strtoupper($tipo), 'UTF-8'), $item);
						$itens .= $item;
					}
			
					$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-contemplados-view.html');
					$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);

					if ( $tipo_evento == 'premio' ) {

						$texto = 'Contemplados <strong>' . $data['premio'] . '</strong>';
						$html = str_replace('{TEXTO-COLLAPSE}', $texto, $html);

					} else {

						$texto = 'Contemplados para evento do dia <strong>' . $dataEvento . '</strong>';
						$html = str_replace('{TEXTO-COLLAPSE}', $texto, $html);
					}

					$html = str_replace('{ITEM-ID}', $data['data_sorteio'] . '-' . $key, $html);

					print $html;
					$itens = '';
					$exibir++;

				}
			}
		} elseif ( $tipo_evento === 'periodo' ) {
            
            $where_confirmacao = '';

            if ($requerConfirmacao) {
                $where_confirmacao = ' AND i.confirmou_presenca = 1';
            }

            $sql = $wpdb->prepare(
                "
                SELECT i.*
                FROM {$tabela} AS i
                INNER JOIN {$tabela_acf} AS a
                    ON a.id = i.acf_id
                WHERE i.post_id = %d
                {$where_confirmacao}
                ORDER BY i.data_inscricao ASC
                ",
                $post_id
            );

            $resultados = $wpdb->get_results($sql, ARRAY_A);

			$exibir = 0;

			if (!empty($resultados)) {
				$info_periodo_evento = get_field( 'evento_periodo', $post_id );
				$dataSorteio = $info_periodo_evento['data_sorteio'];

				foreach ($resultados as $linha) {
					if (isset($linha['user_id'])) {
						$tipo = get_user_meta($linha['user_id'], 'parceira', true);
						if ($tipo == 1) {
							$tipo = 'PARCEIRO';
						} else if ($tipo == 0) {
							$tipo = 'SERVIDOR';
						}
					} else { // Programa 1, 2 ou 3
						if (isset($linha['programa_estagio'])) {
							$tipo = 'ESTAGIÁRIO';
						}
					} 

					$item = file_get_contents(get_template_directory().'/includes/sorteio/conteudo-tab-lista-sorteados.html');
					$item = str_replace('{NOME-SORTEADO}',     esc_html(mb_strtoupper($linha['nome_completo']), 'UTF-8'),   $item);
					$item = str_replace('{TIPO-SORTEADO}',    esc_html(mb_strtoupper($tipo), 'UTF-8'), $item);
					$itens .= $item;
				}

				$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-contemplados-view.html');
				$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
				$html = str_replace('{DATA-EVENTO}', '', $html);
				$html = str_replace('{ITEM-ID}', $post_id, $html);
				$html = str_replace('{TEXTO-COLLAPSE}', 'Contemplados do evento', $html);

				print $html;
				$itens = '';
				$exibir++;
			}

		}

		if($exibir > 0){
			echo '<style>.exibir-lista-sorteados { display: block; }</style>';
		}

		echo '</div>';

		//return $html;
	} 
}

function processa_ajax_remove_participante_cortesias(){//** OK */

    $data_selecionada = sanitize_text_field($_POST['date']);
    $premio = sanitize_text_field($_POST['premio']);
	
    $retorno = devolver_cortesia( $_POST['idPart'], false, '', true, $data_selecionada, $premio );
    wp_send_json_success($retorno);
}

// Função AJAX para buscar as informações da inscrição
add_action( 'wp_ajax_buscar_dados_inscricao', 'buscar_dados_inscricao' );
function buscar_dados_inscricao() {

    global $wpdb;

    $post_id = intval($_POST['postId']);
	$tipo_evento = get_field( 'tipo_evento', $post_id );
    $user_id = get_current_user_id();

    if ( boolval( get_user_meta( $user_id, 'parceira', true ) ) ) {
        $tipo_usuario = 'parceira';
    } else {
        $tipo_usuario = 'servidor';
    }

    $tabela_inscricoes = $wpdb->prefix . 'cortesias_inscricoes';
    $tabela_datas = $wpdb->prefix . 'cortesias_acf_datas';

    $inscricao_info = [];

    if ( $tipo_usuario === 'servidor' ) {

        $resultado = $wpdb->get_row( $wpdb->prepare("
            SELECT i.*, d.data_evento, d.premio
            FROM {$tabela_inscricoes} i
            INNER JOIN {$tabela_datas} d ON d.id = i.acf_id
            WHERE i.post_id = %d
            AND i.user_id = %d
            ",
            $post_id, $user_id )
        );
    } else {

        $cpf = isset( $_POST['cpf'] ) ? preg_replace( '/\D/', '',$_POST['cpf'] ) : '';
        $resultado = $wpdb->get_row( $wpdb->prepare("
            SELECT i.*, d.data_evento, d.premio
            FROM {$tabela_inscricoes} i
            INNER JOIN {$tabela_datas} d ON d.id = i.acf_id
            WHERE i.post_id = %d
            AND i.cpf = %s
            ",
            $post_id, $cpf )
        );
    }

    if ( !$resultado ) {
        wp_send_json_error();
    }

    $inscricao_info = [
        'id' => $resultado->id,
        'cpf' => $resultado->cpf,
        'qtd_resgatada' => $resultado->qtd,
        'data_inscricao' => $resultado->data_inscricao,
        'tipo_evento' => $tipo_evento
    ];

    if ( $tipo_evento === 'periodo' ) {
        $periodo = get_field( 'evento_periodo', $post_id );
        $inscricao_info['periodo'] = $periodo['descricao'];
	}

    if ( $tipo_evento === 'data' ) {
        $data_formatada = DateTime::createFromFormat( 'Y-m-d H:i:s', $resultado->data_evento );
        $hora_formatada = formatar_hora( $data_formatada->format( 'H:i:s' ) );
        $inscricao_info['data_evento'] = $resultado->data_evento;
        $inscricao_info['data_formatada'] = $data_formatada->format( 'd/m/Y') . ' às ' . $hora_formatada;
	}

    if ( $tipo_evento === 'premio' ) {
        $inscricao_info['premio'] = $resultado->premio;
	}

    wp_send_json_success( $inscricao_info );
}

function enviar_email_cancelar_cortesia_ajax() {
	global $wpdb;

	if(!isset($_POST['post_id']) || !isset($_POST['cpf'])) {
		wp_send_json_error('ID do evento ou data inválida.');
	}

	$post_id = intval($_POST['post_id']);
	$cpf = sanitize_text_field($_POST['cpf']);

	$tabela =  'int_cortesias_inscricoes';
	$resultados = $wpdb->get_results("SELECT id FROM $tabela WHERE post_id = $post_id AND cpf = $cpf", ARRAY_A);

	if (empty($resultados)) {
		wp_send_json_error('Nenhuma inscrição encontrada.');
	}

	foreach($resultados as $item) {
		if (is_plugin_active('envia-email-sme/envia-email-sme.php')) {
			new Envia_Emails_Sorteio_SME($item['id'], null, $post_id, 'desistencia');
		}
	}

    wp_send_json_success();
}

//Define o prazo limite para confirmação de presença de um inscrito em eventos do tipo gratuidade e cortesias
function definir_prazo_expiracao_email_confirmacao_cortesia( array $participantes, string $tipo_prazo, $prazo, bool $forcar_atualizacao = false ) {

	global $wpdb;
	$timezone = new DateTimeZone( 'America/Sao_Paulo' );
	$data_atual = new DateTime( 'now', $timezone );

	// Aplica o prazo de validade conforme o tipo selecionado
	if ( $tipo_prazo === 'dias' ) {
		$data_atual->modify( "+{$prazo} days" );

	} elseif ( $tipo_prazo === 'horas' ) {
		$partes = explode( ':', $prazo );

		$horas   = isset( $partes[0] ) ? (int) $partes[0] : 0;
		$minutos = isset( $partes[1] ) ? (int) $partes[1] : 0;

		$intervalo = new DateInterval( sprintf( 'PT%dH%dM', $horas, $minutos ) );
		$data_atual->add( $intervalo );
	}

	$data_final = $data_atual->format( 'Y-m-d H:i:s' );

	//Força a atualização do prazo de confirmação para o caso de reenvio
	$extra_args = $forcar_atualizacao ? '' : 'AND (prazo_confirmacao IS NULL OR prazo_confirmacao = "")';

	$lista_ids = implode( ',', array_map( 'intval', $participantes ) );
	$query = $wpdb->prepare(
		"UPDATE {$wpdb->prefix}cortesias_inscricoes
		SET prazo_confirmacao = %s
		WHERE id IN ($lista_ids)
		$extra_args",
		$data_final
	);

	return $wpdb->query( $query );
}

// Callback ajax para definir o prazo de confirmação para os inscritos selecionados
add_action('wp_ajax_definir_prazo_selecionados_cortesia', 'definir_prazo_selecionados_cortesia_callback');
function definir_prazo_selecionados_cortesia_callback() {
    global $wpdb;

    $participantesSelecionados = isset( $_POST['selecionados'] ) ? $_POST['selecionados'] : null;
	$tipo_prazo_confirmacao = isset( $_POST['tipo_prazo'] ) ? sanitize_text_field( $_POST['tipo_prazo'] ) : 'dias';
	$prazo_confirmacao = isset( $_POST['prazo'] ) ? sanitize_text_field( $_POST['prazo'] ) : 1;
    $reenvio = boolval( $_POST['reenvio'] );

    $arrEmails = array();
    $tabela =  $wpdb->prefix .'cortesias_inscricoes';
    $placeholders = implode( ',', array_fill( 0, count( $participantesSelecionados ), '%d' ) );

    $sql = $wpdb->prepare("
        SELECT id, post_id
        FROM $tabela
        WHERE id IN ($placeholders)
    ", $participantesSelecionados);

    $arrDados = $wpdb->get_results( $sql, ARRAY_A );

    if(count($arrDados) < 1){
        wp_send_json(array("res" => false));
    } else {
        foreach($arrDados as $item){
            $item['tipoEmail'] = 'confirmar_presenca_cortesia';
            array_push($arrEmails, $item);
        }

        if(is_plugin_active('envia-email-sme/envia-email-sme.php')){

            definir_prazo_expiracao_email_confirmacao_cortesia( $participantesSelecionados, $tipo_prazo_confirmacao, $prazo_confirmacao, $reenvio );

            foreach($arrEmails as $item){
                new Envia_Emails_Sorteio_SME($item['id'], null, $item['post_id'], $item['tipoEmail'], null, null, ['reenvio' => $reenvio]);
            }
        }
    }

    die();
}

//Retorna as informações da data/premio a partir do acf_id (acf_cortesias_datas)
function get_acf_info_by_id( int $id ) {
    global $wpdb;

    $tabela = $wpdb->prefix . 'cortesias_acf_datas';

    $informacoes_data = $wpdb->get_row( $wpdb->prepare("
        SELECT *
        FROM {$tabela}
        WHERE id = %d
        ", $id )
    );

    if ( !$informacoes_data ) {
        return null;
    }

    $dados = [
        'id' => $informacoes_data->id,
        'tipo' => 'data',
        'post_id' => $informacoes_data->post_id,
        'info' => $informacoes_data->data_evento
    ];

    if ( !is_null( $informacoes_data->premio ) ) {
        $dados['tipo'] = 'premio';
        $dados['info'] = $informacoes_data->premio;
    }

    if ( is_null( $informacoes_data->data_evento ) ) {
        $dados['tipo'] = 'periodo';
        $dados['info'] = get_field( 'evento_periodo', $informacoes_data->post_id )['descricao'] ?? '';
    }

    return $dados;
}

add_shortcode('exibe_tab_historico_emails_enviados_cortesias', 'exibe_historico_emails_enviados_cortesias');
function exibe_historico_emails_enviados_cortesias(){
	global $wpdb;
	$post_id = get_the_id();
    $tabela =  $wpdb->prefix . 'cortesias_inscricoes';
    $resultados = $wpdb->get_results("
        SELECT nome_completo, email_institucional, email_secundario, historico_emails
        FROM $tabela
        WHERE post_id = $post_id
        ORDER BY data_inscricao ASC
    ", ARRAY_A);

	$requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
	$escondePresenca = '';
	if(!$requerConfirmacao){
		$escondePresenca = 'd-none';
	}

    if (empty($resultados)) {
        echo 'Nenhum participante inscrito até o momento';
    } else {

		$itens = [];
		foreach ($resultados as $i => $linha) {

			$historico = json_decode($linha['historico_emails']);

			$notificado = $historico->vencedor->enviado;
			$instrucoesEvento = $historico->instrucoes->enviado;
			
			$dataEmailVencedor = $historico->vencedor->data_hora_envio;
			$dataEmailInstrucoes = $historico->instrucoes->data_hora_envio;

			$dataVenc = explode(' ', $dataEmailVencedor);
			$dataEnvio =  explode('-', $dataVenc[0]);
			$dataInst = explode(' ', $dataEmailInstrucoes);
			$dataInstrucoes = explode('-', $dataInst[0]);

			$notificado    == 1 ? $notificado = '<strong>SIM - '.$dataEnvio[2].'/'.$dataEnvio[1].'/'.$dataEnvio[0].' às '.$dataVenc[1].'</strong>'  : $notificado = 'NÃO';
			$instrucoesEvento == 1 ? $instrucoesEvento = '<strong>SIM - '.$dataInstrucoes[2].'/'.$dataInstrucoes[1].'/'.$dataInstrucoes[0].' às '.$dataInst[1].'</strong>' : $instrucoesEvento = 'NÃO';
			
            $itens[] = [
                'ordem' => $i + 1,
                'nome' => $linha['nome_completo'],
                'emails' => $linha['email_institucional'].'<br>'.$linha['email_secundario'],
                'notificado' => $notificado,
                'escondePresenca' => $escondePresenca,
                'instrucoes_enviadas' => $instrucoesEvento,
            ];
		}

		get_template_part( '/includes/cortesias/template-parts/historico-emails-enviados', null, [ 'itens' => $itens, 'escondePresenca' => $escondePresenca] );
	}
}

function exibir_lista_participantes_cortesia_sancao_callback() {

    if ( !isset( $_POST['post_id'] ) ) {
        wp_die('ID inválido');
    }

	$participante = '';
	$post_id = intval($_POST['post_id']);
	
	if($_POST['participante']){
		$participante = $_POST['participante'];
	}

	$tipo_evento = get_field('tipo_evento', $post_id);
	if ($tipo_evento == 'premio') {
		$datas = get_field('evento_premios', $post_id);
	} else {
		$datas = get_field('evento_datas', $post_id);
	}

	$unica = false;

	if(!$datas){
        $datas[]['data'] = get_field('data_sorteio', $post_id);
		$unica = true;
    }

	if ($tipo_evento == 'periodo') {
		$unica = true;
	}

	if (!empty($datas)) {
		$args = [
			'post_id' => $post_id,
			'datas' => $datas,
			'unica' => $unica,
			'sancao' => true,
			'participante' => $participante,
            'tipo_evento' => $tipo_evento
		];

		get_template_part( '/includes/cortesias/template-parts/lista-participantes-sancao-container', null, $args );		
	}

    wp_die();
}

// Exportar inscritos das cortesias para Excel (OS: 140149)
function scripts_exportacao_cortesias_admin() {
    global $pagenow;
	$screen = get_current_screen();
    
    if ( $pagenow === 'post.php' && isset($_GET['post']) && $screen->post_type === 'cortesias' ) {
        
        // Registra o script
        wp_enqueue_script(
            'exportar-cortesias',
            get_template_directory_uri() . '/includes/js/exportar-cortesias-admin.js',
            ['jquery'],
            filemtime(get_template_directory() . '/includes/js/exportar-cortesias-admin.js'),
            true
        );
        
        // Passa variáveis para o JS
        wp_localize_script('exportar-cortesias', 'exportVarsCorte', [
            'nonce' => wp_create_nonce('exportar_cortesias_nonce'),
            'dataAtual' => obter_data_com_timezone( 'd_m_y_H_i_s', 'America/Sao_Paulo' )
        ]);

    }
}

function retorna_qtd_cortesias_ajax($post_id, $data, $unica = false, $sancao = false, $participante = '', $responsavel = '', $premio = '') {

	global $wpdb;
	$agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
	$requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
	$escondePresenca = '';
	$tipo_evento = get_field('tipo_evento', $post_id);
    if($tipo_evento == 'periodo'){
        $unica = true;
    }
	
    $tabela_inscri = 'int_cortesias_inscricoes';
    $tabela_acf    = 'int_cortesias_acf_datas';

	if($unica){
		$sql = $wpdb->prepare(
            "
            SELECT i.*
            FROM {$tabela_inscri} AS i
            INNER JOIN {$tabela_acf} AS a
                ON a.id = i.acf_id
            WHERE i.post_id = %d
            ORDER BY i.data_inscricao ASC
            ",
            $post_id
        );

        $resultados = $wpdb->get_results($sql, ARRAY_A);
	} else {
		
        $sql = $wpdb->prepare(
            "
            SELECT i.*
            FROM {$tabela_inscri} AS i
            INNER JOIN {$tabela_acf} AS a
                ON a.id = i.acf_id
            WHERE i.post_id = %d
            AND a.data_evento = %s
            ORDER BY i.data_inscricao ASC
            ",
            $post_id,
            $data
        );

        $resultados = $wpdb->get_results($sql, ARRAY_A);

		
	}

    if (empty($resultados)) {
		$total = 0;
    } else {
        $total = count($resultados);
    }

    return $total;
}

//Retorna as informações da data/premio a partir da data (acf_cortesias_datas)
function get_acf_info_by_key( int $post_id, ?string $data = null) {
    global $wpdb;

    $tabela = $wpdb->prefix . 'cortesias_acf_datas';
    $tipo_evento = get_field( 'tipo_evento', $post_id );

    if ( $tipo_evento === 'periodo' ) {
        return $wpdb->get_row( $wpdb->prepare("
            SELECT *
            FROM {$tabela}
            WHERE post_id = %d
            AND data_evento IS NULL
            AND ativo = 1
            ", $post_id)
        ); 
    }

    return $wpdb->get_row( $wpdb->prepare("
        SELECT *
        FROM {$tabela}
        WHERE post_id = %d
        AND data_evento = %s
        AND ativo = 1
        ", $post_id, $data )
    );    
}

/**
 * Obtem informações sobre as datas diponíveis em um post de Gratuidade e cortesias
 *
 * Obtem informações relacionadas ao repetidor de datas do evento.
 *
 * @param  string  $post_id ID do evento.
 * @param  string  $filto   hoje (dia atual), data (uma data especifica Y-m-d).
 * @param  string  $data 	Se o filtro tiver o valor = data, então o parametro "data" deve ser iformado.
 */
function obter_informacoes_datas_cortesia( $post_id, ?string $filtro = null, ?string $data = null ) {
	
    $hoje = obter_data_com_timezone( 'Y-m-d', 'America/Sao_Paulo' );
	$tipo_evento = get_field( 'tipo_evento', $post_id );
    $datas_info = [];

	if($tipo_evento == 'premio'){
		if ( $evento_datas = get_field( 'evento_premios', $post_id ) ) {
			
            $datas = $evento_datas;

			if ( $filtro === 'hoje' || $filtro === 'data' ) {

				$data = is_null( $data ) ? $hoje : $data;
				
				$datas = array_filter( $evento_datas, function( $item ) use ( $data ) {
					return isset( $item['encerramento_inscricoes'] ) && $item['encerramento_inscricoes'] == $data;
				} );

				if ( empty( $datas ) ) {
					return null;
				}

				usort( $datas, function( $a, $b ) {
					return strcmp( $a['data'], $b['data'] );
				} );

			}

			foreach ( $datas as $item ) {

                $acf_data = get_acf_info_by_key( $post_id,  $item['data'] );
				$instrucoes = check_envio_email_instrucoes_cortesia( $post_id, $acf_data->id );
                $estoque_atual = intval( $acf_data->estoque_atual );

				array_push( $datas_info, [
					'data' => $item['premio'],
                    'estoque_atual' => $estoque_atual > 0
                        ? $estoque_atual . ' ' . _n( 'cortesia', 'cortesias', $estoque_atual ) . ' ' . _n( 'disponível', 'disponíveis', $estoque_atual )
                        : 'Cortesias esgotadas',
					'instrucoes' => $instrucoes ? 'Instruções enviadas 📧' : 'Instruções pendentes ⚠️'
				] );
			}

			return $datas_info;
		}

	} elseif ( $tipo_evento === 'periodo' ) {

		if ( $data_sorteio = get_field( 'evento_periodo_descricao', $post_id ) ) {
			
            $acf_data = get_acf_info_by_key( $post_id );
			$instrucoes = check_envio_email_instrucoes_cortesia( $post_id, $acf_data->id );
            $estoque_atual = intval( $acf_data->estoque_atual );

            array_push( $datas_info, [
                'data' => $data_sorteio,
                'estoque_atual' => $estoque_atual > 0
                    ? $estoque_atual . ' ' . _n( 'cortesia', 'cortesias', $estoque_atual ) . ' ' . _n( 'disponível', 'disponíveis', $estoque_atual )
                    : 'Cortesias esgotadas',
                'instrucoes' => $instrucoes ? 'Instruções enviadas 📧' : 'Instruções pendentes ⚠️'
            ] );

			return $datas_info;
		}
	} else {
		
		if ( $evento_datas = get_field( 'evento_datas', $post_id ) ) {
			
            $datas = $evento_datas;

			if ( $filtro === 'hoje' || $filtro === 'data' ) {

				$data = is_null( $data ) ? $hoje : $data;
				
				$datas = array_filter( $evento_datas, function( $item ) use ( $data ) {
					return isset( $item['encerramento_inscricoes'] ) && $item['encerramento_inscricoes'] == $data;
				} );

				if ( empty( $datas ) ) {
					return null;
				}

				usort( $datas, function( $a, $b ) {
					return strcmp( $a['data'], $b['data'] );
				} );
			}

			foreach ( $datas as $item ) {

                $acf_data = get_acf_info_by_key( $post_id,  $item['data'] );
                $instrucoes = check_envio_email_instrucoes_cortesia( $post_id, $acf_data->id );
                $estoque_atual = intval( $acf_data->estoque_atual );

                array_push( $datas_info, [
					'data' => date( 'd/m/Y H:i', strtotime( $item['data'] ) ),
                    'estoque_atual' => $estoque_atual > 0
                        ? $estoque_atual . ' ' . _n( 'cortesia', 'cortesias', $estoque_atual ) . ' ' . _n( 'disponível', 'disponíveis', $estoque_atual )
                        : 'Cortesias esgotadas',
					'instrucoes' => $instrucoes ? 'Instruções enviadas 📧' : 'Instruções pendentes ⚠️'
				] );
			}

			return $datas_info;
		}
	}	
}

/**
 * Verifica se já houve algum envio de e-mail de instruções para o evento/data
 *
 * @param  int  $post_id ID do evento.
 * @param  $acf_data_id ID da data de rferencia na tabela int_cortesias_acf_datas.

 */
function check_envio_email_instrucoes_cortesia( int $post_id, $acf_data_id ) {

    global $wpdb;

    $tabela = $wpdb->prefix . 'cortesias_inscricoes';

    $instrucoes = $wpdb->get_var($wpdb->prepare("
        SELECT 1
        FROM $tabela 
        WHERE post_id = %d
        AND enviou_email_instrucoes = 1
        AND acf_id = %d 
        LIMIT 1
        ",
        $post_id,
        $acf_data_id
    ));

    return boolval( $instrucoes );
}