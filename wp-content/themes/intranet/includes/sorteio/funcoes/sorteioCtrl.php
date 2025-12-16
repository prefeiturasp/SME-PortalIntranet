<?php 
//###########################################################################//
//######################### FUNÇÕES DO SORTEIO ##############################//
//###########################################################################//
use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;

if (is_user_logged_in()){
    // add_filter('wp_cache_filter_request', 'disable_cache_for_admin_ajax', 10, 1);
    add_action('admin_head', 'meu_css_personalizado_no_admin');

    add_action('wp_ajax_sortear', 'processa_ajax_sortear');
    add_action('wp_ajax_confirmacoes_sorteio', 'processa_ajax_confirmacoes_sorteio');
    add_action('wp_ajax_confirma_todos', 'processa_ajax_conf_todos');
    add_action('wp_ajax_envia_email_sorteio', 'processa_ajax_envia_email_sorteio');
    add_action('wp_ajax_retorna_lista_sorteados', 'retornaListaSorteados');
    add_action('wp_ajax_enviaListaEmailSorteados', 'enviaListaEmailSorteados');
    add_action('wp_ajax_exibir_lista_pagina', 'processa_ajax_exibir_lista_pagina');
    add_action('wp_ajax_remove_participante_sorteado', 'processa_ajax_remove_participante_sorteado');
	add_action('wp_ajax_enviar_instrucoes', 'handle_enviar_instrucoes');
	add_action('wp_ajax_exibir_lista_sorteados_por_data', 'exibir_lista_sorteados_por_data_ajax');
	add_action('wp_ajax_aplicar_sancao', 'aplicar_sancao_ajax');
	add_action('wp_ajax_refresh_lista_sorteados', 'refresh_lista_sorteados_ajax');
	add_action('wp_ajax_debloquear_usuario', 'desbloquear_usuario_ajax');
	add_action('wp_ajax_enviar_email_cancelar', 'enviar_email_cancelar_ajax');
	add_action( 'wp_ajax_atualizar_confirm_presen', 'atualizar_confirm_presen_callback' );
	add_action( 'wp_ajax_exibir_historico_emails_instrucao', 'exibir_historico_envio_emails_callback' );

    add_shortcode('exibe_tab_resultado_pagina', 'exibeTabResultadoPagina');
    add_shortcode('exibe_tab_participantes_sorteio', 'exibeTabParticipantesSorteio');
    add_shortcode('exibe_tab_historico_emails_enviados', 'exibe_historico_emails_enviados');
    add_shortcode('listar_participantes_sorteados', 'exibir_lista_sorteados_por_data');
    add_shortcode('exibe_btn_sortear', 'exibir_btn_sortear');

    add_action('admin_enqueue_scripts', 'scripts_exportacao_inscritos_admin');
    require_once get_template_directory() . '/includes/exportacao-inscritos.php';
    add_action('admin_enqueue_scripts', 'scripts_exportacao_sorteados_admin');
    require_once get_template_directory() . '/includes/exportacao-sorteados.php';
	add_action('admin_enqueue_scripts', 'scripts_exportacao_sancao_admin');
	require_once get_template_directory() . '/includes/exportacao-sancionados.php';

	add_action('after_setup_theme', 'verifica_criacao_tabelas_sorteio');
	add_action('init', 'registrar_tabelas_personalizadas_no_wpdb');
	wp_enqueue_script('bootstrap-sorteio-js');
	wp_enqueue_style('datatables-css');
	wp_enqueue_script('datatables-js');
} 

function sanitiza_str_requisicoes($txtInfo){
    return sanitize_text_field($txtInfo);
}

function sanitiza_number_requisicoes($num){
    return absint($num);
}

function sanitize_array_requisicoes($arr) {
    return array_map(function($value) {
        if (is_array($value)) {
            return sanitize_array_requisicoes($value);
        } else if (is_string($value)) {
            return sanitiza_str_requisicoes($value);
        } else if (is_int($value)) {
            return sanitiza_number_requisicoes($value);
        } else {
            return $value; // Retorna o valor original para outros tipos de dados
        }
    }, $arr);
}

function disable_cache_for_admin_ajax() {
    if (defined('ADMIN_AJAX_HTTP')) {
        return false;
    }
}

function sortear_numeros($arrayNumeros){//** OK */
	$indiceAleatorio = random_int(0, count($arrayNumeros) - 1); // Gera um índice aleatório entre 0 e o último índice do array
	return $arrayNumeros[$indiceAleatorio];
}

function retornaNumerosSemRepetir($arrDados, $qtdSorteio){//** OK */
	$arrNumeros = [];
		$i = 0;
		foreach($arrDados as $item){
			$arrNumeros[$i] = $item['id'];
			$i++;
		}

		$numSorteados = [];
		while(count($numSorteados) < $qtdSorteio){
			$numSort = sortear_numeros($arrNumeros);
			if (!in_array($numSort, $numSorteados)) { 
				array_push($numSorteados, $numSort);
			}
		}

		return $numSorteados;
}

function retorna_lista_sorteados_html($post_id, $data, $unica = false, $sancao = false, $participante = '', $responsavel = '', $premio = '') {//** OK */

	global $wpdb;
	$agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
	$requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
	$escondePresenca = '';
	$tipo_evento = get_field('tipo_evento', $post_id);
	
    $tabela =  'int_inscricoes';
	if($unica){
		$resultados = $wpdb->get_results(
			"SELECT * FROM $tabela 
			WHERE post_id = $post_id AND sorteado = 1 
			ORDER BY data_hora_sorteado
			ASC", 
			ARRAY_A 
		);
	} else {
		if($sancao){

			$sql = "
				SELECT * 
				FROM $tabela 
				WHERE post_id = %d 
				AND sorteado = 1 
				AND data_sorteada = %s 
				AND confirmou_presenca = 1
			";

			$params = [ $post_id, $data ];

			// Se o participante veio preenchido, adiciona no WHERE
			if ( !empty($participante) ) {
				$sql .= " AND nome_completo LIKE %s";
				$params[] = '%' . $wpdb->esc_like($participante) . '%';
			}

			$sql .= " ORDER BY data_hora_sorteado ASC";

			// Executa consulta preparada
			$resultados = $wpdb->get_results( $wpdb->prepare($sql, $params), ARRAY_A );

		} else {
			$resultados = $wpdb->get_results(
				"SELECT * FROM $tabela 
				WHERE post_id = $post_id AND sorteado = 1 AND data_sorteada = '$data'
				ORDER BY data_hora_sorteado
				ASC", 
				ARRAY_A 
			);
		}
	}

    if (empty($resultados)) {
		if($sancao){
			return '<div class="conteudo-lista" data-data="'.$data.'"><p>Nenhum participante sancionado até o momento</p></div>';
		} else {
			return '<div class="conteudo-lista" data-data="'.$data.'"><p>Nenhum participante sorteado até o momento</p></div>';
		}
    }

	$i = 1;
	$localInscri = '';
	$itens = '';

	$arrTodosConfirmados = [];

	$qtdIngressoSorteio = get_post_meta(get_the_id(), 'qtd_sorteada', true);

	//dd($resultados);

	foreach ($resultados as $linha) {
		$id = $linha['id'];
		$contato = esc_html($linha['fez_contato']);
		$contato == '1' ? $contato = 'checked': $contato = '';
		$confirmacaoPresenca = esc_html($linha['confirmou_presenca']);
		$enviouEmailInstrucoes = esc_html($linha['enviou_email_instrucoes']);
		$prazo_confirmacao = esc_html($linha['prazo_confirmacao']);
		$data_validar = new DateTime($prazo_confirmacao, new DateTimeZone('America/Sao_Paulo'));
		
		
		if(!$requerConfirmacao){
			$escondePresenca = 'd-none';
		}

		if($confirmacaoPresenca == '1'){
			$confPresenca = '<span class="dest-azul">SIM</span>';
		} elseif($confirmacaoPresenca == '2'){
			$confPresenca = '<span class="dest-azul">NÃO, CANCELOU</span>';
		} else {			
			if($agora > $data_validar){
				$confPresenca = '<span class="dest-vermelho">PRAZO EXPIRADO</span>';
			} else {
				$confPresenca = '<span class="dest-azul">AINDA NÃO RESPONDEU</span>';
			}
		}

		if ( isset( $linha['prazo_confirmacao'] ) && !is_null( $linha['prazo_confirmacao'] ) ) {

			$data_formatada = date( 'd/m/Y', strtotime( $linha['prazo_confirmacao'] ) );
			$hora_formatada = obter_hora_formatada( $linha['prazo_confirmacao'] );

			$prazo_confirmacao = "<br><font class='status'>EXPIRAÇÃO:</font> 
			<span class='valor-status'>{$data_formatada} - {$hora_formatada}</span>";
		}


		$enviouEmailInstrucoes == '1' ? $enviouEmailInstrucoes = '<span class="dest-azul">ENVIADO</span>': $enviouEmailInstrucoes = 'NÃO ENVIADO';

		if (isset($linha['user_id'])) {
			$tipo = get_user_meta($linha['user_id'], 'parceira', true);
			if ($tipo == 1) {
				$localInscri = 'INTRANET - UE PARCEIRA';
				$tag = 'badge badge-success';
			} else if ($tipo == 0) {
				$localInscri = 'INTRANET - SERVIDOR';
				$tag = 'badge badge-primary';
			}
		} else { // Programa 1, 2 ou 3
			if (isset($linha['programa_estagio'])) {
				$localInscri = 'PORTAL - ESTAGIÁRIO';
				$tag = 'badge badge-warning';
			}
		} 

		$esconde = 'hidden';
		$escondeRemover = 'hidden';

		if($qtdIngressoSorteio < $i) {
			$esconde = '';
		}  
		
		if($confirmacaoPresenca == '0') {
			$esconde = '';
		}

		if($confirmacaoPresenca == '0') {
			$escondeRemover = '';
		}

		// $resHistorico = $wpdb->get_results("SELECT historico_emails FROM $tabela WHERE id = $id AND post_id = $post_id", ARRAY_A);
		$arrHistorico = json_decode($linha['historico_emails']);

		$arrHistorico->vencedor->enviado == '1' ? $esconde = '' : $esconde = 'hidden';

		$dataConf = explode(' ', $arrHistorico->vencedor->data_hora_envio);
		$dataConfirmacao = explode('-', $dataConf[0]);
		$dataHoraEnvioSorteado = 'E-mail enviado dia: '.$dataConfirmacao[2].'/'.$dataConfirmacao[1].'/'.$dataConfirmacao[0].' às '.$dataConf[1];
		$statusNotificado = $arrHistorico->vencedor->enviado == '1' ? 'SIM' : 'NÃO';

		$item = file_get_contents(get_template_directory().'/includes/sorteio/conteudo-tab-view.html');
		$item = str_replace('{ID}',                 	$id,                                  							$item);
		$confirmacaoPresenca == '2' ? $item = str_replace('{DESABILITAR}', 'disabled', $item) : $item = str_replace('{DESABILITAR}', '', $item);
		$item = str_replace('{TAG}',                	$tag,                                          					$item);
		$item = str_replace('{ORDEM}',              	$i,                                            					$item);
		$item = str_replace('{DIRETORIO_URI}',      	get_stylesheet_directory_uri(),                					$item);
		$item = str_replace('{NOME}',               	esc_html(strtoupper($linha['nome_completo'])), 					$item);
		$item = str_replace('{CPF}',                	esc_html($linha['cpf']),                       					$item);
		$item = str_replace('{PUBLICO}',            	esc_html($localInscri),                        					$item);
		$item = str_replace('{EMAILINSTITUCIONAL}', 	esc_html($linha['email_institucional']),       					$item);
		$item = str_replace('{EMAILSECUNDARIO}',    	esc_html($linha['email_secundario']),          					$item);
		$item = str_replace('{CELULAR}',            	esc_html($linha['celular']),                   					$item);
		$item = str_replace('{TELEFONE}',           	esc_html($linha['telefone_comercial']),        					$item);
		$item = str_replace('{DRE}',                	esc_html($linha['dre']),                       					$item);
		$item = str_replace('{CONTATO-CHECADO}',        $contato,                                      					$item); 
		$item = str_replace('{PRESENCA-CHECADA}',       $confPresenca,                                 					$item); 
		$item = str_replace('{EMAILINSTRUCAO-CHECADO}', $enviouEmailInstrucoes,                        			  		$item);
		$item = str_replace('{UNIDADE}',                esc_html($linha['unidade_setor']),             		      		$item);
		$item = str_replace('{IMG-CANCELA-SORTEADO}',   get_template_directory_uri().'/img/remove-participante.png',  	$item);
		$item = str_replace('{IMG-EMAIL-ENVIADO}',      get_template_directory_uri().'/img/email-enviado.png',  	    $item);
		$item = str_replace('{DESCRICAO-ENVIO-EMAIL}',  $dataHoraEnvioSorteado,  	    								$item);
		$item = str_replace('{ESCONDE}',            	$esconde,  														$item);
		$item = str_replace('{ESCONDE-REMOVER}',        $escondeRemover, 											    $item);
		$item = str_replace('{ESCONDE-PRESENCA}',       $escondePresenca, 											    $item);
		$item = str_replace('{STATUS_NOTIFICADO}',		$statusNotificado,												$item);
		$item = str_replace('{PRAZO_CONFIRMACAO}',		$prazo_confirmacao,												$item);
		$itens .= $item;

		$i++;
	}

	in_array(false, $arrTodosConfirmados) ? $confirmaTodos = '' : $confirmaTodos = 'checked';
	
	$dataConf = str_replace(' ', '-', $data);
	$dataConf = str_replace(':', '-', $dataConf);
		
	$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-view-conteudo-lista.html');
	if($tipo_evento == 'premio'){
		$html = str_replace('{OCULTAR-BOTAO}',   'd-none',  $html);
		$html = str_replace('{OCULTAR-CONFIRMADOS}',   'd-none',  $html);
	} else {
		$html = str_replace('{OCULTAR-BOTAO}',   '',  $html);
		$html = str_replace('{OCULTAR-CONFIRMADOS}',   '',  $html);
	}

	if($requerConfirmacao){
		$html = str_replace('{OCULTAR-TODOS}',   'd-none',  $html);
	}  else {
		$html = str_replace('{OCULTAR-TODOS}',   '',  $html);
	}
	$html = str_replace('{CONFIRMA-TODOS}',   $confirmaTodos,      $html);
	$html = str_replace('{ATRIBUTO-ID}',      esc_attr($post_id),  $html);
	$html = str_replace('{CONTEUDO-TAB}',     $itens,              $html);
	$html = str_replace('{ATRIBUTO-DATA}',    esc_attr($data),  $html);
	if($premio){
		$html = str_replace('{ATRIBUTO-TIPO}',    $premio,  $html);
	}
	$html = str_replace('{ATRIBUTO-DATA-CONF}',    esc_attr($dataConf),  $html);
	$html = str_replace('{EDITOR-ID}',   $linha['id'],        $html);
	$html = str_replace('{POST-ID}',   $post_id,        $html);
	$html = str_replace('{RESPONSAVEL}',   $responsavel,        $html);

	return $html;
	
}

function meu_css_personalizado_no_admin() {//** OK */
    $screen = get_current_screen();
    // Aplica só no post type desejado
    if ($screen->post_type !== 'post') return; // troque por seu CPT, ex: 'evento'
	wp_enqueue_style('bootstrap-sorteio-css');
	wp_enqueue_style('style-sorteio-css');
	wp_enqueue_style('toggle-sorteio-css');
	wp_enqueue_style('sweetalert-sorteio-css');
	wp_enqueue_style('toastr-sorteio-css');

	//wp_enqueue_script('jquery-ui');
	wp_enqueue_script('sweetalert-sorteio-js');
	wp_enqueue_script('toggle-sorteio-js');
	wp_enqueue_script('toastr-sorteio-js');
	wp_enqueue_script('sorteio-js');
	wp_enqueue_script('bootstrap-sorteio-js');

	adicionaPostMetaExibirResultado();
}

function exibir_lista_sorteados_por_data ($post_id = null) {//** OK */
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

		echo "<div class='acf-label'><label>Lista de Participantes Sorteados:</label></div>";
		echo '<div class="row">';
			echo '<div class="col text-right">';
				echo '<button type="button" class="btn btn-outline-success btn-sm mb-3" id="exportar-sorteados" data-post-id="' . $post_id . '" data-responsavel="' . $responsavel . '">Baixar relatório de sorteados</button>';
			echo '</div>';
		echo '</div>';

		get_template_part( '/includes/sorteio/template-parts/lista-participantes-sorteados-container', null, $args );		
	}

	$exibeNaPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);
	if ($exibeNaPagina == '1') {
		$exibeNaPagina = 'checked';
	} else {
		$exibeNaPagina = '';
	}


	$output = '';
	if ( $tipo_evento === 'periodo' ) {

		$info_periodo_evento = get_field( 'evento_periodo', get_the_ID() );
		$data_sorteio = DateTime::createFromFormat('d/m/Y', $info_periodo_evento['data_sorteio'])->format('Ymd');
		$label = 'Período';
		$opcao_padrao = 'Selecione o período para resortear';
		$opcoes_periodo = "<option value='{$data_sorteio}' selected>{$info_periodo_evento['descricao']}</option>";
		
		$output = '
			<label for="data-resorteada">' . $label . '</label>
			<select id="data-resorteada" class="form-control" disabled>
				<option value="">' . $opcao_padrao . '</option>
				' . $opcoes_periodo . '
			</select>';
		
	} else {

		if ($tipo_evento == 'premio') {
			$label = 'Prêmio:';
			$opcao_padrao = 'Selecione o prêmio para resortear';
		} else {
			$label = 'Datas do Evento';
			$opcao_padrao = 'Selecione uma data para resortear';
		}
		
		$opcoes_datas = '';
		if(!empty($datas)) {
			foreach ($datas as $data) {
				$data_formatada = date('d/m/Y H\hi', strtotime($data['data']));
				if($tipo_evento == 'premio') {
					$data_formatada = $data['premio'];
				}
				$opcoes_datas .= "<option value='{$data['data']}'>{$data_formatada} {$data['hora']}</option>";
			}
		}
		
		$output = '
			<label for="data-resorteada">' . $label . '</label>
			<select id="data-resorteada" class="form-control">
				<option value="" selected>' . $opcao_padrao . '</option>
				' . $opcoes_datas . '
			</select>';
	}
	
	$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-view-sortear-novamente.html');
	$html = str_replace('{EXIBE-NA-PAGINA}',  $exibeNaPagina, $html);
	$html = str_replace('{DATAS-RESORTEADAS}',  $output, $html);
	echo $html;

}

function exibir_lista_sorteados_por_data_ajax($post_id = null) {
    if (!isset($_POST['post_id']) && !$post_id) {
        wp_die('ID inválido');
    }

	$participante = '';

	if ($post_id) {
    	$post_id = intval($post_id);
	} else {
		$post_id = intval($_POST['post_id']);
	}
	
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
			'participante' => $participante
		];

		get_template_part( '/includes/sorteio/template-parts/lista-participantes-sorteados-sancao-container', null, $args );		
	}

    wp_die();
}

function monta_lista_sorteados_por_data ($post_id, $data, $unica = false, $sancao = false, $participante = '', $responsavel = '', $premio = '') {//** OK */
	echo retorna_lista_sorteados_html($post_id, $data, $unica, $sancao, $participante, $responsavel, $premio);
}

function aplicar_sancao_ajax() {
    global $wpdb;

    $tabela_inscricoes = $wpdb->prefix . "inscricoes";
    $tabela_sancoes    = $wpdb->prefix . "inscricao_sancoes";

    $participantes = isset($_POST['participantes']) ? (array) $_POST['participantes'] : [];
    $dias          = isset($_POST['dias']) ? intval($_POST['dias']) : 30;

    if (empty($participantes) || $dias <= 0) {
        wp_send_json_error(['message' => 'Dados inválidos']);
    }

    $hoje = current_time('Y-m-d');
    $validade = date('Y-m-d', strtotime("+{$dias} days", strtotime($hoje)));

    $aplicados = [];
    $ignorados = [];

    foreach ($participantes as $id_participante) {
        $id_participante = intval($id_participante);

        // Pega dados do participante
        $dados = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT cpf, post_id FROM $tabela_inscricoes WHERE id = %d",
                $id_participante
            ),
            ARRAY_A
        );

        if ($dados && !empty($dados['cpf'])) {
            $cpf = $dados['cpf'];

            // Verifica se já existe sanção ativa
            $sancao_existente = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $tabela_sancoes WHERE cpf = %s",
                    $cpf
                ),
                ARRAY_A
            );

            if ($sancao_existente) {
                if ($sancao_existente['data_validade'] >= $hoje) {
                    // Sanção ainda válida → ignora
                    $ignorados[] = $cpf;
                    continue;
                }
            }

            // Insere ou atualiza
            $wpdb->replace(
                $tabela_sancoes,
                [
                    'cpf'            => $cpf,
                    'evento_id'      => $dados['post_id'],
                    'data_aplicacao' => $hoje,
                    'data_validade'  => $validade,
					'id_inscricao'  => $id_participante,
                ],
                [
                    '%s',
                    '%d',
                    '%s',
                    '%s',
					'%d'
                ]
            );

            $aplicados[] = $cpf;
        }
    }

    wp_send_json_success([
        'message'   => 'Processo concluído',
        'aplicados' => $aplicados,
        'ignorados' => $ignorados,
    ]);
}

function desbloquear_usuario_ajax() {
    global $wpdb;

    if(!isset($_POST['sancao_id'])) {
        wp_send_json_error('ID da sanção não enviado');
    }

    $sancao_id = intval($_POST['sancao_id']);
    $tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';

    $delete = $wpdb->delete($tabela_sancoes, ['id' => $sancao_id], ['%d']);

    if($delete !== false) {
        wp_send_json_success();
    } else {
        wp_send_json_error('Falha ao remover a sanção');
    }
}

#Lista os participatens sorteados
function exibir_lista_sorteados($post_id = false) {//** OK */

	if (!$post_id) {
		$post_id = get_the_id();
	}

	$html = retorna_lista_sorteados_html($post_id);

	$exibeNaPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);
	if ($exibeNaPagina == '1') {
		$exibeNaPagina = 'checked';
	} else {
		$exibeNaPagina = '';
	}

	$html .= file_get_contents(get_template_directory().'/includes/sorteio/tab-view-sortear-novamente.html');
	$html = str_replace('{EXIBE-NA-PAGINA}',  $exibeNaPagina,      $html);

    return $html;
}

function exibir_btn_sortear() {//** OK */
	echo '<input type="hidden" value="'.get_the_id().'" id="postID">';
	echo '<button type="button" class="btn btn-success btn-block btn-sm" id="btn-sortear">Sortear</button>';
}

function processa_ajax_sortear(){//** OK */

	date_default_timezone_set('America/Sao_Paulo');

    $acao = isset($_POST['action']) ? sanitiza_str_requisicoes($_POST['action']) : '';
	$tipo_evento = get_field( 'tipo_evento', sanitiza_number_requisicoes( $_POST['idPost'] ) );
	
	if ($acao == 'sortear'){

		global $wpdb;

		$idPost = sanitiza_number_requisicoes($_POST['idPost']);
		$qtdSorteio = sanitiza_number_requisicoes($_POST['qtdSorteio']);
		$tipo = sanitiza_str_requisicoes($_POST['tipo']);
		$data_selecionada_sorteio = sanitize_text_field( $_POST['data_selecionada_sorteio'] );
		$premio = sanitize_text_field( $_POST['premio'] );
		
		if (preg_match('/^\d{8}$/', $data_selecionada_sorteio)) {
			// Formato aaaammdd → converter para aaaa-mm-dd
			//$data_formatada = substr($data_selecionada_sorteio, 0, 4) . '-' . substr($data_selecionada_sorteio, 4, 2) . '-' . substr($data_selecionada_sorteio, 6, 2);
			$data_formatada = $data_selecionada_sorteio;
		} else {
			// Assume que já está no formato aaaa-mm-dd
			$data_formatada = $data_selecionada_sorteio;
		}
		//$data_selecionada_sorteio = '2025-07-11';

		if ( $tipo_evento === 'periodo' ) {
			$data_formatada = date( 'Y-m-d H:i:s', strtotime( $data_selecionada_sorteio ) );
		}

		$tabela =  'int_inscricoes';
		$sql = $wpdb->prepare("
			SELECT i.*
			FROM $wpdb->evento_inscricoes i
			INNER JOIN $wpdb->evento_inscricao_datas d ON i.id = d.inscricao_id
			WHERE i.post_id = %d
			AND i.sorteado = 0
			AND d.data_evento = %s
			ORDER BY i.id
		", $idPost, $data_formatada);

		if ( $tipo_evento === 'periodo' ) {
			$sql = $wpdb->prepare("
				SELECT *
				FROM $wpdb->evento_inscricoes
				WHERE post_id = %d
				AND sorteado = 0
				ORDER BY id
			", $idPost);
		}

		$resultados = $wpdb->get_results( $sql, ARRAY_A );
		$query = $wpdb->last_query;

		if($tipo == 1){
			if($premio){
				$msg =  'A quantidade a ser sorteada para o prêmio ' . $premio . ' é maior que o número de inscrições válidas para este sorteio!';
			} else {
				$msg =  'A quantidade a ser sorteada é maior que o número de inscrições válidas para este evento!';
			}
		} else if ($tipo == 2){
			if($premio){
				$msg =  'A quantidade a ser resorteada para o prêmio ' . $premio . ' é maior que o número de inscrições válidas para este sorteio!';
			} else {
				$msg =  'A quantidade a ser resorteada é maior que o número de inscrições válidas para este evento!';
			}
		}

		$total = count($resultados);

		if(count($resultados) < $qtdSorteio){
			$total = count($resultados);
			wp_send_json(array(
				"res" => false,
				"msg" => $msg,
				"qtd" => $qtdSorteio,
				"query" => $query,
				"total" => $total,
				"post_id" => $idPost,
			));
			die();
		}

		if($tipo == 1){

			$sql = "SELECT 1 FROM $tabela WHERE post_id = $idPost AND sorteado = 1 AND data_sorteada = '$data_formatada' LIMIT 1";
			$mensagem = "Não é possível realizar o sorteio: já existe um sorteio concluído para esta data e horário.";

			if ( $tipo_evento === 'periodo' ) {
				$sql = "SELECT 1 FROM $tabela WHERE post_id = $idPost AND sorteado = 1 LIMIT 1";
				$mensagem = "Não é possível realizar o sorteio: O sorteio já foi realizado.";
			}

			if ( $premio ) {
				$mensagem = "Não é possível realizar o sorteio: já existe um sorteio concluído para este prêmio.";
			}

			$existe = $wpdb->get_var( $sql );

			if ($existe) {
				wp_send_json(array(
					"res" => false,
					"msg" => $mensagem,
				));
				die();
			}
		}

		$numSorteados = retornaNumerosSemRepetir($resultados, $qtdSorteio);

		foreach($resultados as $item){
			foreach($numSorteados as $num){
				if($num == $item['id']){
					$data = array(
						'sorteado' => '1',
						'data_hora_sorteado' => microtime(true),
						'data_sorteada' => $tipo_evento === 'periodo' ? null : $data_formatada
					);
					$where = array('id' => $num);
					$res = $wpdb->update( $tabela, $data, $where);
				}
			}
		}

		$data_unica = $tipo_evento === 'periodo';
		$tabelaSorteados = retorna_lista_sorteados_html($idPost, $data_formatada, $data_unica);

		if ( !$data_unica ) {
			$data_exibir = DateTime::createFromFormat('Y-m-d H:i:s', $data_formatada)->format('d/m/Y');
		}

		if ( $tipo == 1 ) {

			if ( $premio ) {
				$msg =  'Sorteio para o prêmio ' . $premio . ' realizado com sucesso!';

			} elseif ( isset( $data_exibir ) ) {
				$msg =  'Sorteio para a data '.$data_exibir.' realizado com sucesso!';

			} else {
				$msg = 'Sorteio realizado com sucesso!';
			}

		} else if ( $tipo == 2 ) {

			if ( $premio ) {
				$msg =  'Resorteio para o prêmio ' . $premio . ' realizado com sucesso!';

			} elseif ( isset( $data_exibir ) ) {
				$msg =  'Resorteio para a data '.$data_exibir.' realizado com sucesso!';
				
			} else {
				$msg = 'Resorteio realizado com sucesso!';
			}
		}

		$tabelaInscritos = monta_tabela_participantes_por_data($idPost, $data_formatada);

		$total = count($resultados);
		$total = $total - count($numSorteados);

		wp_send_json(array(
			"res" => $res,
			"query" => $query,
			"resultados" => $resultados,
			"sorteados" => $numSorteados,
			"qtdSorteio" => $qtdSorteio,
			"msg" => $msg,
			"html" => $tabelaSorteados,
			"htmlInscritos" => $tabelaInscritos,
			"data" => $data_formatada,
			"target" => "sorteados_data_{$data_selecionada_sorteio}",
			"total" => $total
		));

		die();
	}
}

function processa_ajax_confirmacoes_sorteio(){//** OK */

	global $wpdb;

    $acao = isset($_POST['action']) ? sanitiza_str_requisicoes($_POST['action']) : '';

	if ($acao == 'confirmacoes_sorteio'){

		$idSorteado = sanitiza_number_requisicoes($_POST['idSorteado']);
		$opcao = sanitiza_str_requisicoes($_POST['opcao']);
		$confirmacao = sanitiza_str_requisicoes($_POST['confirmacao']);

		$tabela =  'int_inscricoes';

		switch ($opcao) {
			case 'contato':
				$data = array('fez_contato' => $confirmacao === "true" ? '1' : '0');
				if ($confirmacao === "true") {
					$msg = 'Contato realizado com sucesso!';
				} else {
					$msg = 'Contato não realizado!';
				}
			break;
			case 'presenca':
				$data = array('confirmou_presenca' => $confirmacao === "true" ? '1' : '0');
				if ($confirmacao === "true") {
					$msg = 'Confirmação de presenca realizada com sucesso!';
				} else {
					$msg = 'Confirmação de presenca cancelada!';
				}
			break;
			case 'emailinstrucoes':
				$data = array('enviou_email_instrucoes' => $confirmacao === "true" ? '1' : '0');
				if ($confirmacao === "true") {
					$msg = 'Email de instruções enviado com sucesso!';
				} else {
					$msg = 'Email de instruções não enviado!';
				}
			break;
		}

		   $where = array('id' => $idSorteado);
		   $res = $wpdb->update( $tabela, $data, $where);

		   wp_send_json(array(
				"res" => $res,
				"msg" => $msg
			));

		die();
	}
	
}

function processa_ajax_envia_email_sorteio(){//** OK */
	
    $acao = isset($_POST['action']) ? sanitiza_str_requisicoes($_POST['action']) : '';
	$data = isset($_POST['data_sorteada']) ? sanitize_text_field($_POST['data_sorteada']) : '';
	$tipo_prazo_confirmacao = isset( $_POST['tipo_prazo'] ) ? sanitize_text_field( $_POST['tipo_prazo'] ) : 'dias';
	$prazo_confirmacao = isset( $_POST['prazo'] ) ? sanitize_text_field( $_POST['prazo'] ) : 1;

	if ($acao == 'envia_email_sorteio'){
        global $wpdb;

		$idPart = isset($_POST['idPart']) ? sanitiza_number_requisicoes($_POST['idPart']) : '';
		$postId = isset($_POST['postId']) ? sanitiza_str_requisicoes($_POST['postId']) : '';
		$tipoEmail = sanitiza_str_requisicoes($_POST['tipoEmail']);

        $tabela =  'int_inscricoes';
        $result = $wpdb->get_results("SELECT historico_emails FROM $tabela WHERE post_id = $postId AND id = $idPart AND sorteado = 1", ARRAY_A);

        if(!empty($result)){
            $result = json_decode($result[0]['historico_emails']);
            if($result->vencedor->enviado == '1'){
                    $result->vencedor->enviado = '0';
                    $result->vencedor->data_hora_envio = date('Y-m-d H:i:s');
                    $data = array('historico_emails' => json_encode($result));
                    $where = array('id' => $idPart, 'post_id' => $postId);
                    $wpdb->update( $tabela, $data, $where);
            }
            if(is_plugin_active('envia-email-sme/envia-email-sme.php')){
				definir_prazo_expiracao_email_confirmacao( [$idPart], $tipo_prazo_confirmacao, $prazo_confirmacao, true );
				new Envia_Emails_Sorteio_SME($idPart, null, $postId, $tipoEmail);
			}
        } else {
            return array('res' => false, 'msg' => 'Não foi possível reenviar este email!');
        }
	}

    return array('res' => true, 'html' => retorna_lista_sorteados_html($postId, $data));
}

function retornaListaSorteados(){//** OK */

    $acao = isset($_POST['action']) ? sanitiza_str_requisicoes($_POST['action']) : '';
	$participantesSelecionados = isset( $_POST['selecionados'] ) ? $_POST['selecionados'] : null;
	$tipo_prazo_confirmacao = isset( $_POST['tipo_prazo'] ) ? sanitize_text_field( $_POST['tipo_prazo'] ) : 'dias';
	$prazo_confirmacao = isset( $_POST['prazo'] ) ? sanitize_text_field( $_POST['prazo'] ) : 1;

	if ( $acao == 'retorna_lista_sorteados' ) {
		global $wpdb;

		$arrEmails = array();
		$tabela =  'int_inscricoes';
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
				$item['tipoEmail'] = 'vencedor';
				array_push($arrEmails, $item);
			}

			if(is_plugin_active('envia-email-sme/envia-email-sme.php')){

				definir_prazo_expiracao_email_confirmacao( $participantesSelecionados, $tipo_prazo_confirmacao, $prazo_confirmacao );

				foreach($arrEmails as $item){
					new Envia_Emails_Sorteio_SME($item['id'], null, $item['post_id'], $item['tipoEmail']);
				}
			}
		}

		die();
	}
}

function processa_ajax_exibir_lista_pagina(){//** OK */
	if ($_POST['action'] == 'exibir_lista_pagina'){

		$idPost = $_POST['postId'];
		$confirmacao = $_POST['opcao'];

		if ($confirmacao === 'true') {	
			update_post_meta($idPost, 'exibe_resultado_pagina', '1'); 
			$msg = 'Exibindo lista de resultados na pagina do sorteio';
		} else if ($confirmacao === 'false') {
			update_post_meta($idPost, 'exibe_resultado_pagina', '0'); 
			$msg = 'Escondendo lista de resultados na pagina do sorteio';
		}
		
		wp_send_json(
			array("res" => true, "msg" => $msg)
		);

	}
}

function processa_ajax_remove_participante_sorteado(){//** OK */
	global $wpdb;

	if ($_POST['action'] == 'remove_participante_sorteado'){

		$tabela =  'int_inscricoes';
		$idParticipante = $_POST['idPart'];
		$post_id = $_POST['postId'];
		$data_selecionada = sanitize_text_field($_POST['date']);

		$data = array('sorteado' => '0', 'data_hora_sorteado' => null, 'historico_emails' => null);
		$where = array('id'=> $idParticipante, 'post_id' => $post_id);
		$res = $wpdb->update( $tabela, $data, $where);

		wp_send_json(
			array(
				"res" => $res,
				"msg" =>
				'Participante removido com sucesso da lista de sorteados!',
				"target" => "lista-sorteados-{$data_selecionada}",
				"html" => retorna_lista_sorteados_html($post_id, $data_selecionada)
			)
		);
	}
}

function enviar_email_cancelar_ajax() {
	global $wpdb;

	if(!isset($_POST['post_id']) || !isset($_POST['cpf'])) {
		wp_send_json_error('ID do evento ou data inválida.');
	}

	$post_id = intval($_POST['post_id']);
	$cpf = sanitize_text_field($_POST['cpf']);

	$tabela =  'int_inscricoes';
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


function exibeTabResultadoPagina($idPost = false){//** OK */
	global $wpdb;

	!$idPost ? $post_id = get_the_id() : $post_id = $idPost;
	
	$exibicaoPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);

	$dataSorteio = get_post_meta($post_id, 'data_sorteio', true);
	$horaSorteio = get_post_meta($post_id, 'hora_sorteio', true);

	$dataSorteio = date('d/m/Y', strtotime($dataSorteio));
	$horaSorteio = date('H:i', strtotime($horaSorteio));
	$tipo_evento = get_field('tipo_evento', $post_id);

	$html = '';
	if ($exibicaoPagina == '1') {

		if($tipo_evento == 'premio'){
			$datas_disponiveis = get_field('evento_premios', $post_id);
			echo '<div class="row mb-4 exibir-lista-sorteados">';
				echo '<div class="col">';
					echo '<span class="title-info">Lista de contemplados do sorteio</span>';
					echo '<p>Se o seu nome estiver entre os contemplados, acesse o e-mail cadastrado e verifique o local de retirada do seu prêmio.</p>';
				echo '</div>';
			echo '</div>';
		} else {

			if ( $tipo_evento == 'data' || !$tipo_evento ) {
				$datas_disponiveis = get_field('evento_datas', $post_id);
			}
			
			echo '<div class="row mb-4 exibir-lista-sorteados">';
				echo '<div class="col">';
					echo '<span class="title-info">Lista de contemplados do sorteio</span>';
					echo '<p>Se o seu nome estiver entre os contemplados, acesse o e-mail cadastrado e veja se é necessário confirmar sua presença.</p>';
				echo '</div>';
			echo '</div>';
		}
		$tabela =  'int_inscricoes';

		

		echo '<div class="accordion" id="accordion-sorteados">';

		if($datas_disponiveis && $datas_disponiveis != ''){
			
			// Ordenar por data do evento
			usort($datas_disponiveis, function($a, $b) {
				return strtotime($a['data']) - strtotime($b['data']);
			});

			$exibir = 0;

			foreach ($datas_disponiveis as $key => $data) {
				$resultados = $wpdb->get_results(
					"SELECT * FROM $tabela 
					WHERE post_id = $post_id AND sorteado = 1 AND data_sorteada = '" . $data['data'] . "'
					ORDER BY data_hora_sorteado
					ASC", 
					ARRAY_A 
				);

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
			
					$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-sorteados-view.html');
					$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
					$html = str_replace('{DATA-SORTEIO}', $dataSorteio, $html);

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
			$resultados = $wpdb->get_results(
				"SELECT * FROM $tabela 
				WHERE post_id = $post_id AND sorteado = 1
				ORDER BY data_hora_sorteado
				ASC", 
				ARRAY_A 
			);

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

				$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-sorteados-view.html');
				$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
				$html = str_replace('{DATA-SORTEIO}', $dataSorteio, $html);
				$html = str_replace('{DATA-EVENTO}', '', $html);
				$html = str_replace('{ITEM-ID}', $post_id, $html);
				$html = str_replace('{TEXTO-COLLAPSE}', 'Contemplados do evento', $html);

				print $html;
				$itens = '';
				$exibir++;
			}

		} else {
			$resultados = $wpdb->get_results(
				"SELECT * FROM $tabela 
				WHERE post_id = $post_id AND sorteado = 1
				ORDER BY data_hora_sorteado
				ASC", 
				ARRAY_A 
			);

			$exibir = 0;

			if (!empty($resultados)) {
				$dataSorteio = date( 'd/m/Y', strtotime( get_field( 'data_sorteio', $post_id ) ) );

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

				$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-lista-sorteados-view.html');
				$html = str_replace('{CONTEUDO-LISTA-SORTEADOS}', $itens, $html);
				$html = str_replace('{DATA-SORTEIO}', $dataSorteio, $html);
				$html = str_replace('{DATA-EVENTO}', '', $html);
				$html = str_replace('{ITEM-ID}', $post_id, $html);
				$html = str_replace('{TEXTO-COLLAPSE}', 'Contemplados', $html);

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

function exibeTabParticipantesSorteio() {
	global $wpdb;

	$post_id = get_the_id();
	$tipo_evento = get_field( 'tipo_evento', $post_id );

	echo '<div class="row legenda-participantes">';
		echo '<div class="col">';
			echo '<p><span class="dashicons dashicons-star-empty"></span> Participantes já sorteados</p>';
		echo '</div>';
		echo '<div class="col text-right">';
			echo '<button type="button" class="btn btn-outline-success btn-sm mb-3" id="exportar-inscritos" data-post-id="' . $post_id . '" data-responsavel="' . $responsavel . '">Baixar relatório de inscritos</button>';
		echo '</div>';
	echo '</div>';

	if ( $tipo_evento === 'periodo' ) {
		return monta_tab_parcipantes_sorteio_periodo( $post_id );
	}

	// Verifica primeiro se existe array de múltiplas datas
	$tipo_evento = get_field('tipo_evento', $post_id);
	if ($tipo_evento == 'premio') {
		$datas = get_field('evento_premios', $post_id);
	} else {
		$datas = get_field('evento_datas', $post_id);
	}

	// Se não houver, tenta buscar a data antiga única
	if (empty($datas)) {
		$data_unica = get_field('data_sorteio', $post_id);
		if ($data_unica) {
			$datas = [ [ 'data' => $data_unica ] ];
		}
	}

	if (!empty($datas)) : ?>
		<div id="accordion">
			<?php foreach ($datas as $key => $data) :
				$data_evento = $data['data'];

				// Verifica se estrutura nova				
				if ($tipo_evento == 'premio') {
					$usa_join = get_field('evento_premios', $post_id);
				} else {
					$usa_join = get_field('evento_datas', $post_id);
				}

				// Formata data para exibição:
				if ($usa_join) {
					// Exibe data e hora: 25/08/2025 14h30
					$label_collapse = date('d/m/Y H\hi', strtotime($data_evento));
					$label_collapse = 'Total de inscritos para o evento do dia: ' . str_replace('h00', 'h', $label_collapse);
				} else {
					// Só data: 25/08/2025
					$label_collapse = 'Total de inscritos para o evento do dia: ' . date('d/m/Y', strtotime($data_evento));
				}

				if ($tipo_evento == 'premio') {
					$label_collapse = 'Total de inscritos para o prêmio: ' . $data['premio'];
				}

				$heading_id = 'heading' . $key;
				$collapse_id = 'collapse' . $key;

				// Verifica se o evento usa JOIN (múltiplas datas) ou consulta simples (data única)
				//$usa_join = get_field('evento_datas', $post_id); // se esse campo existe, é estrutura nova

				if ($usa_join) {
					$qtdInscritos = $wpdb->get_var($wpdb->prepare("
						SELECT COUNT(*)
						FROM {$wpdb->evento_inscricoes} i
						JOIN {$wpdb->evento_inscricao_datas} d ON d.inscricao_id = i.id
						WHERE i.post_id = %d
						AND d.data_evento = %s
					", $post_id, $data_evento));

					$qtdElegiveis = $wpdb->get_var($wpdb->prepare("
						SELECT COUNT(*)
						FROM {$wpdb->evento_inscricoes} i
						JOIN {$wpdb->evento_inscricao_datas} d ON d.inscricao_id = i.id
						WHERE i.post_id = %d 
						AND i.sorteado = 0 
						AND d.data_evento = %s
					", $post_id, $data_evento));
				} else {
					$qtdInscritos = $wpdb->get_var($wpdb->prepare("
						SELECT COUNT(*)
						FROM {$wpdb->evento_inscricoes}
						WHERE post_id = %d
					", $post_id));

					$qtdElegiveis = $wpdb->get_var($wpdb->prepare("
						SELECT COUNT(*)
						FROM {$wpdb->evento_inscricoes}
						WHERE post_id = %d
						AND sorteado = 0
					", $post_id));
				}
				?>
				<div class="card-inscritos">
					<div class="card-title" id="<?php echo $heading_id; ?>">
						<h5 class="mb-0">
							<button type="button" class="btn" data-toggle="collapse" data-target="#<?php echo $collapse_id; ?>" aria-expanded="false" aria-controls="<?php echo $collapse_id; ?>">
								<?= $label_collapse . ' - ' . $qtdInscritos . ' (Elegíveis: <span data-ele="' . $data_evento . '">' . $qtdElegiveis . '</span>)'; ?>
								<span class="accordion-icon dashicons dashicons-controls-play ml-2"></span>
							</button>
						</h5>
					</div>

					<div id="<?php echo $collapse_id; ?>" class="collapse" aria-labelledby="<?php echo $heading_id; ?>" data-parent="#accordion">
						<div class="card-content">
							<?= monta_tabela_participantes_por_data($post_id, $data_evento); ?>
						</div>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
	<?php endif;
}

function monta_tabela_participantes_por_data($post_id, $data_evento = null) {
	global $wpdb;

	$tipo_evento = get_field( 'tipo_evento', $post_id );

	// Verifica se estrutura nova				
	if ($tipo_evento == 'premio') {
		$usa_join = get_field('evento_premios', $post_id);
	} elseif ($tipo_evento == 'periodo') {
		$usa_join = false;
	} else {
		$usa_join = get_field('evento_datas', $post_id);
	}

	if ($usa_join) {
		// Estrutura nova com JOIN e múltiplas datas
		$resultados = $wpdb->get_results($wpdb->prepare("
			SELECT i.*
			FROM {$wpdb->evento_inscricoes} i
			JOIN {$wpdb->evento_inscricao_datas} d ON d.inscricao_id = i.id
			WHERE i.post_id = %d
			AND d.data_evento = %s
		", $post_id, $data_evento), ARRAY_A);
	} else {
		// Estrutura antiga (sem JOIN), ignora $data_evento pois só tem uma
		$resultados = $wpdb->get_results($wpdb->prepare("
			SELECT *
			FROM {$wpdb->evento_inscricoes}
			WHERE post_id = %d
		", $post_id), ARRAY_A);
	}

	$qtdSorteados = count($resultados);

	if ($qtdSorteados < 1) {
		return '<p>Nenhum participante inscrito até o momento.</p>';
	} else {

		$programas_estagio = [
			'1' => 'Aprender sem limite',
			'2' => 'Parceiros da aprendizagem',
			'3' => 'Diversos',
		];

		$itens = '';
		foreach ($resultados as $linha) {

			$programaEstagio = $programas_estagio[$linha['programa_estagio']] ?? null;

			if ($programaEstagio) {
				$cargo = "ESTAGIÁRIO";
				$disciplina = $programaEstagio;
			} else {
				$cargo = $linha['cargo_principal'];
				$disciplina = $linha['disciplina'];
			}

			$classe = '';

			if ($linha['sorteado'] == '1') {
				$classe = 'class="sorteado"';
				if($linha['data_sorteada']){
					if ( $tipo_evento === 'premio' ) {
						$premios = obter_array_premios_sorteio( $post_id );
						$premio = $premios[$linha['data_sorteada']];
						$linha['nome_completo'] = '<span class="dashicons dashicons-star-empty"></span> <span data-toggle="tooltip" title="Sorteado do prêmio ' . $premio . '">' . strtoupper($linha['nome_completo']) . '</span>';
					} else {
						$dataSorteio = date('d/m/Y H:i', strtotime($linha['data_sorteada']));
						$linha['nome_completo'] = '<span class="dashicons dashicons-star-empty"></span> <span data-toggle="tooltip" title="Sorteado dia ' . $dataSorteio . '">' . strtoupper($linha['nome_completo']) . '</span>';
					}
				} else {
					$linha['nome_completo'] = '<span class="dashicons dashicons-star-empty"></span> ' . strtoupper($linha['nome_completo']);
				}
			}

			$item = file_get_contents(get_template_directory() . '/includes/sorteio/conteudo-tab-participantes-sorteio-view.html');
			$item = str_replace('{NOME-COMPLETO}',        $linha['nome_completo'], 						$item);
			$item = str_replace('{EMAIL-INSTITUCIONAL}',  esc_html($linha['email_institucional']),      $item);
			$item = str_replace('{EMAIL-SECUNDARIO}',     esc_html($linha['email_secundario']),         $item);
			$item = str_replace('{TELEFONE-CELULAR}',     esc_html($linha['celular']),                  $item);
			$item = str_replace('{TELEFONE-COMERCIAL}',   esc_html($linha['telefone_comercial']),       $item);
			$item = str_replace('{CPF}',                  esc_html($linha['cpf']),                       $item);
			$item = str_replace('{DRE-SME}',              esc_html(strtoupper($linha['dre'])),          $item);
			$item = str_replace('{CARGO-ATUAL}',          esc_html(strtoupper($cargo)),                 $item);
			$item = str_replace('{ESCOLA-SETOR}',         esc_html(strtoupper($linha['unidade_setor'])),$item);
			$item = str_replace('{DISCIPLINA}',           esc_html(strtoupper($disciplina)),            $item);
			$item = str_replace('{CLASSE}',               $classe,                                      $item);

			$itens .= $item;
		}

		$html = file_get_contents(get_template_directory() . '/includes/sorteio/tab-participantes-sorteio-view.html');
		$html = str_replace('{ATRIBUTO-ID}',        esc_attr($post_id),       $html);
		$html = str_replace('{TOTAL-PARTICIPANTES-SORTEADOS}', esc_attr($qtdSorteados), $html);
		$html = str_replace('{CONTEUDO-PARTICIPANTES-SORTEADOS}', $itens, $html);
		$html = str_replace('{ATRIBUTO-DATA}',      esc_attr($data_evento),   $html);

		return $html;
	}
}

// Exportar inscritos dos sorteios para Excel (OS: 127107)
function scripts_exportacao_inscritos_admin() {//** OK */
    global $pagenow;
    
    if ($pagenow === 'post.php' && isset($_GET['post'])) {
        
        // Registra o script
        wp_enqueue_script(
            'exportar-inscritos',
            get_template_directory_uri() . '/includes/js/exportar-inscritos-admin.js',
            ['jquery'],
            filemtime(get_template_directory() . '/includes/js/exportar-inscritos-admin.js'),
            true
        );
        
        // Passa variáveis para o JS
        wp_localize_script('exportar-inscritos', 'exportVarsInscri', [
            'nonce' => wp_create_nonce('exportar_inscritos_nonce'),
            'dataAtual' => obter_data_com_timezone( 'd_m_y_H_i_s', 'America/Sao_Paulo' )
        ]);

    }
}

// Exportar sorteados para Excel (OS: 127107)
function scripts_exportacao_sorteados_admin() {//** OK */
    global $pagenow;
    
    if ($pagenow === 'post.php' && isset($_GET['post'])) {
        
        // Registra o script
        wp_enqueue_script(
            'exportar-sorteados',
            get_template_directory_uri() . '/includes/js/exportar-sorteados-admin.js',
            ['jquery'],
            filemtime(get_template_directory() . '/includes/js/exportar-sorteados-admin.js'),
            true
        );
        
        // Passa variáveis para o JS
        wp_localize_script('exportar-sorteados', 'exportVars', [
            'nonce' => wp_create_nonce('exportar_sorteados_nonce'),
            'dataAtual' => obter_data_com_timezone( 'd_m_y_H_i_s', 'America/Sao_Paulo' )
        ]);

    }
}

// Exportar usuarios com sanções ativas
function scripts_exportacao_sancao_admin() {
	wp_enqueue_script(
		'exportar-sancao',
		get_template_directory_uri() . '/includes/js/exportar-sancao-admin.js',
		['jquery'],
		filemtime(get_template_directory() . '/includes/js/exportar-sancao-admin.js'),
		true
	);

	// Passa variáveis para o JS
	wp_localize_script('exportar-sancao', 'exportVarsSancao', [
		'nonce' => wp_create_nonce('exportar_sancao_nonce'),
		'dataAtual' => obter_data_com_timezone( 'd_m_y_H_i_s', 'America/Sao_Paulo' )
	]);
}

function exibe_historico_emails_enviados(){
	global $wpdb;
	$post_id = get_the_id();
    $tabela =  'int_inscricoes';
    $resultados = $wpdb->get_results("SELECT nome_completo, email_institucional, email_secundario, historico_emails FROM $tabela WHERE post_id = $post_id AND sorteado = 1 ORDER BY data_hora_sorteado ASC", ARRAY_A);

	$requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
	$escondePresenca = '';
	if(!$requerConfirmacao){
		$escondePresenca = 'd-none';
	}

    if (empty($resultados)) {
        echo 'Nenhum participante sorteado até o momento';
    } else {

		$itens = '';
		$i = 1;
		foreach ($resultados as $linha) {

			$historico = json_decode($linha['historico_emails']);

			$vencedorSorteio = $historico->vencedor->enviado;
			$instrucoesSorteio = $historico->instrucoes->enviado;

			$dataEmailConfirmacao = $historico->confirmacao->data_hora_envio;			
			$dataEmailVencedor = $historico->vencedor->data_hora_envio;
			$dataEmailInstrucoes = $historico->instrucoes->data_hora_envio;

			$dataConf = explode(' ', $dataEmailConfirmacao);
			$dataConfirmacao = explode('-', $dataConf[0]);
			$dataVenc = explode(' ', $dataEmailVencedor);
			$dataVencimento =  explode('-', $dataVenc[0]);
			$dataInst = explode(' ', $dataEmailInstrucoes);
			$dataInstrucoes = explode('-', $dataInst[0]);

			$vencedorSorteio    == 1 ? $vencedorSorteio = '<strong>SIM - '.$dataVencimento[2].'/'.$dataVencimento[1].'/'.$dataVencimento[0].' às '.$dataVenc[1].'</strong>'  : $vencedorSorteio = 'NÃO';
			$instrucoesSorteio == 1 ? $instrucoesSorteio = '<strong>SIM - '.$dataInstrucoes[2].'/'.$dataInstrucoes[1].'/'.$dataInstrucoes[0].' às '.$dataInst[1].'</strong>' : $instrucoesSorteio = 'NÃO';
			
			$item = file_get_contents(get_template_directory() . '/includes/sorteio/conteudo-tab-historico-emails-enviados.html');
			$item = str_replace('{ORDEM}',               $i,                                      							$item);
			$item = str_replace('{NOME}',                $linha['nome_completo'],                                      		$item);
			$item = str_replace('{EMAILS}',              $linha['email_institucional'].'<br>'.$linha['email_secundario'], 	$item);
			$item = str_replace('{VENCEDOR-SORTEIO}',    $vencedorSorteio,                                             		$item);
			$item = str_replace('{ESCONDE-PRESENCA}',    $escondePresenca,                                           		$item);			
			$item = str_replace('{INSTRUCOES-SORTEIO}',  $instrucoesSorteio,                                           		$item);
			$itens .= $item;
			$i++;
		}

		$html = file_get_contents(get_template_directory().'/includes/sorteio/tab-historico-emails-enviados.html');
		$html = str_replace('{CONTEUDO-HISTORICO-EMAILS-ENVIADOS}',           $itens,                       $html);
		$html = str_replace('{ESCONDE-PRESENCA}',    $escondePresenca,                                		$html);			

		print $html;

	}
}

function enviaListaEmailSorteados(){

    $acao = isset($_POST['action']) ? sanitiza_str_requisicoes($_POST['action']) : '';

	if ($acao == 'envia_email_lista_sorteados'){

		global $wpdb;

		$idPost = sanitiza_number_requisicoes($_POST['postId']);
		$arrEmails = array();

		$tabela =  'int_inscricoes';
		$arrDados = $wpdb->get_results("SELECT id, post_id FROM $tabela WHERE post_id = $idPost AND sorteado = 1 ORDER BY data_hora_sorteado", ARRAY_A);

		if(count($arrDados) < 1){
			wp_send_json(array("res" => false));
		} else {
			foreach($arrDados as $item){
				$item['tipoEmail'] = 'vencedor';
				array_push($arrEmails, $item);
			}

			if(is_plugin_active('envia-email-sme/envia-email-sme.php')){
				foreach($arrEmails as $item){
					new Envia_Emails_Sorteio_SME($item['id'], null, $item['post_id'], $item['tipoEmail']);
				}
			}
		}
		die();
	}
}

//Preenche o seletor de datas com base nas datas cadastradas para o evento
add_filter('acf/load_field/name=datas_disponiveis_evento', function($field) {
    $post_id = get_the_ID();
    $datas = get_field('evento_datas', $post_id);

    $field['choices'] = [];

    if ($datas) {
        foreach ($datas as $item) {
            $status = $item['status'] ?? 'disponivel';
            if ($status !== 'disponivel') {
                continue;
            }

            $data_raw = $item['data'];
            $data_formatada = date('d/m/Y', strtotime($data_raw)) . ' - ' . $item['hora'];

            $field['choices'][$data_raw] = $data_formatada;
        }
    }

    return $field;
});

//cria as tabelas necessárias para o sorteio com múltiplas datas.
function criar_tabelas_sorteio() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $datas_table = $wpdb->prefix . 'inscricao_datas';

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');   

    dbDelta("
        CREATE TABLE $datas_table (
            id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
            inscricao_id BIGINT UNSIGNED NOT NULL,
            data_evento DATE NOT NULL,
            PRIMARY KEY (id),
            INDEX (inscricao_id),
            INDEX (data_evento)
        ) $charset_collate;
    ");
}

function verifica_criacao_tabelas_sorteio() {
    if ( !get_option( 'tabelas_sorteio_criadas' ) ) {
        criar_tabelas_sorteio();
        update_option( 'tabelas_sorteio_criadas', true );
    }
}

//registra as tabelas pra conseguir acessar pelo $wpdb
function registrar_tabelas_personalizadas_no_wpdb() {
    global $wpdb;

    $wpdb->evento_inscricoes = $wpdb->prefix . 'inscricoes';
    $wpdb->evento_inscricao_datas = $wpdb->prefix . 'inscricao_datas';
}

//Realiza as configurações necessárias nas tabelas relacionadas ao sorteio
function configura_tabelas_sorteio() {
	global $wpdb;

	$coluna_existe = $wpdb->get_var("
		SHOW COLUMNS FROM int_inscricoes LIKE 'data_sorteada'
	");

	if (!$coluna_existe) {
		$wpdb->query("
			ALTER TABLE int_inscricoes
			ADD COLUMN data_sorteada DATE NULL
		");
	}
}

// Verifica disponibilidade de data para inscrição
function verifica_disponibilidade_data_inscricao($idPost, $data_evento, $tipo_evento = false) {
    // Define timezone -3
    $tz = new DateTimeZone('America/Sao_Paulo');

    // Pega data e hora atual exata no fuso correto
    $hoje = new DateTime('now', $tz);

    // Tenta criar objeto DateTime com data e hora do evento
    $dataEventoObj = DateTime::createFromFormat('Y-m-d H:i:s', $data_evento, $tz);

    // Se a data for inválida ou estiver no passado, retorna indisponível
    if ($tipo_evento == false && (!$dataEventoObj || $dataEventoObj < $hoje)) {
        return false;
    }

    global $wpdb;
    $tabela = $wpdb->prefix . 'inscricoes';

    // Verifica se já existe sorteio feito para essa data+hora e post
    $existe = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT 1 FROM $tabela WHERE post_id = %d AND sorteado = 1 AND data_sorteada = %s LIMIT 1",
            $idPost,
            $data_evento
        )
    );

    if ($existe) {
        return false; // Sorteio já realizado para essa data e hora
    }

    return true; // Data e hora disponível
}


if ( !get_option( 'tabelas_sorteio_configuradas' ) ) {

	configura_tabelas_sorteio();
	update_option( 'tabelas_sorteio_configuradas', true );
}

//Posiciona os metaboxes da maneira correta.
add_action('admin_footer', function () {
    $screen = get_current_screen();
    if ($screen->post_type !== 'post') return;
    ?>
    <script>
    jQuery(function($) {
        const destino = $('#normal-sortables');

        $('[id^="inscritos_data_"]').each(function() {
        	const box = $(this).closest('.postbox');
        	box.insertBefore($('#acf-group_684ae6e1601bf')); // Acima do metabox de histórico de emails
        });

		$('[id^="sorteados_data_"]').each(function() {
        	const box = $(this).closest('.postbox');
        	box.insertBefore($('#acf-group_684ae6e1601bf')); // Acima do metabox de histórico de emails
        });
    });
    </script>
    <?php
});

// Obtem adata do próximo sorteio de um evento com multiplas datas
function obter_proxima_data_sorteio( $post_id ) {

    $hoje = obter_data_com_timezone('Y-m-d', 'America/Sao_Paulo');
	$tipo_evento = get_field( 'tipo_evento', $post_id );
	$evento_datas = get_field( 'evento_datas', $post_id );

	if ( $tipo_evento === 'premio' ) {
		$evento_datas = get_field( 'evento_premios', $post_id );
	}
   
    if ( $evento_datas ) {

		$datas_futuras = array_filter( $evento_datas, function( $item ) use ( $hoje ) {
			return isset( $item['data_sorteio'] ) && $item['data_sorteio'] >= $hoje;
		} );

		usort( $datas_futuras, function( $a, $b ) {
			return strcmp( $a['data_sorteio'], $b['data_sorteio'] );
		} );

		return !empty( $datas_futuras ) ? formatar_data_por_extenso( str_replace( '-', '', $datas_futuras[0]['data_sorteio'] ), false ) : null;
    }
	
	if ( $data_sorteio = get_field( 'data_sorteio', $post_id ) ) {

		return  formatar_data_por_extenso(  str_replace( '-', '', $data_sorteio ), false );
	}

	if ( $tipo_evento === 'periodo' ) {
		$info_periodo_evento = get_field( 'evento_periodo', $post_id );

		return formatar_data_por_extenso( str_replace( '-', '', $info_periodo_evento['data_sorteio'] ), false );
	}

	return null;
}

function obter_ultima_data_sorteio( $post_id ) {

	$tipo_evento = get_field( 'tipo_evento', $post_id );
	$evento_datas = get_field( 'evento_datas', $post_id );

	if ( $tipo_evento === 'premio' ) {
		$evento_datas = get_field( 'evento_premios', $post_id );
	}

    if ( $evento_datas ) {
        
		usort( $evento_datas, function( $a, $b ) {
			return strcmp( $b['data_sorteio'], $a['data_sorteio'] );
		} );

    	return !empty( $evento_datas ) ? formatar_data_por_extenso( str_replace( '-', '', $evento_datas[0]['data_sorteio'] ), false ) : null;
    }

	if ( $data_sorteio = get_field( 'data_sorteio', $post_id ) ) {

		return formatar_data_por_extenso( str_replace( '-', '', $data_sorteio ), false );
	}

	if ( $tipo_evento === 'periodo' ) {
		$info_periodo_evento = get_field( 'evento_periodo', $post_id );

		return formatar_data_por_extenso( str_replace( '-', '', $info_periodo_evento['data_sorteio'] ), false );
	}

	return null;
}

/**
 * Obtem informações sobre as datas dos sorteios
 *
 * Obtem informações relacionadas ao repetidor de datas do evento,
 * incluindo uma consulta no banco que verifica se já houve sorteios
 *
 * @param  string  $post_id ID do evento.
 * @param  string  $filto   hoje (dia atual), data (uma data especifica Y-m-d).
 * @param  string  $data 	Se o filtro tiver o valor = data, então a data deve ser passada ness parametro.
 */
function obter_informacoes_datas_sorteio( $post_id, ?string $filtro = null, ?string $data = null ) {
	global $wpdb;

	$tabela = 'int_inscricoes';
    $hoje = obter_data_com_timezone( 'Y-m-d', 'America/Sao_Paulo' );
	$datas_info = [];
	$tipo_evento = get_field( 'tipo_evento', $post_id );

	if($tipo_evento == 'premio'){
		if (  $evento_datas = get_field( 'evento_premios', $post_id )) {

			$datas = $evento_datas;
			
			if ( $filtro === 'hoje' || $filtro === 'data' ) {

				$data = is_null( $data ) ? $hoje : $data;
				
				$datas = array_filter( $evento_datas, function( $item ) use ( $data ) {
					return isset( $item['data_sorteio'] ) && $item['data_sorteio'] == $data;
				} );

				if ( empty( $datas ) ) {
					return null;
				}

				usort( $datas, function( $a, $b ) {
					return strcmp( $a['data'], $b['data'] );
				} );
			}

			foreach ( $datas as $item ) {
				$status = $wpdb->get_var($wpdb->prepare("
					SELECT id
					FROM $tabela 
					WHERE post_id = %d
					AND sorteado = 1
					AND data_sorteada = %s 
					LIMIT 1
				",
				$post_id,
				$item['data']
				));

				$instrucoes = $wpdb->get_var($wpdb->prepare("
					SELECT id
					FROM $tabela 
					WHERE post_id = %d
					AND enviou_email_instrucoes = 1
					AND data_sorteada = %s 
					LIMIT 1
				",
				$post_id,
				$item['data']
				));

				array_push( $datas_info, [
					'data' => $item['premio'],
					'sorteio_realizado' => boolval( $status ),
					'status' =>  boolval( $status ) ? 'Sorteio Realizado' : 'Aguardando Sorteio',
					'instrucoes' => boolval( $instrucoes ) ? 'Instruções enviadas 📧' : 'Instruções pendentes ⚠️'
				] );
			}

			return $datas_info;
		}

	} elseif ( $tipo_evento === 'periodo' ) {

		if ( $data_sorteio = get_field( 'evento_periodo_descricao', $post_id ) ) {

			$status = $wpdb->get_var($wpdb->prepare("
				SELECT id
				FROM $tabela 
				WHERE post_id = %d
				AND sorteado = 1 
				LIMIT 1
			",
			$post_id,
			));

			array_push( $datas_info, [
				'data' => $data_sorteio,
				'sorteio_realizado' => boolval( $status ),
				'status' =>  boolval( $status ) ? 'Sorteio Realizado' : 'Aguardando Sorteio'
			] );

			return $datas_info;
		}
	} else {
		if ( $data_sorteio = get_field( 'data_sorteio', $post_id ) ) {

			$hora_sorteio = get_field( 'hora_sorteio', $post_id );
			$status = $wpdb->get_var($wpdb->prepare("
				SELECT id
				FROM $tabela 
				WHERE post_id = %d
				AND sorteado = 1 
				LIMIT 1
			",
			$post_id,
			));

			array_push( $datas_info, [
				'data' => date( 'd/m/Y H:i', strtotime( "{$data_sorteio} {$hora_sorteio}" ) ),
				'sorteio_realizado' => boolval( $status ),
				'status' =>  boolval( $status ) ? 'Sorteio Realizado' : 'Aguardando Sorteio'
			] );

			return $datas_info;
		}
	
		if (  $evento_datas = get_field( 'evento_datas', $post_id ) ) {

			$datas = $evento_datas;
			
			if ( $filtro === 'hoje' || $filtro === 'data' ) {

				$data = is_null( $data ) ? $hoje : $data;
				
				$datas = array_filter( $evento_datas, function( $item ) use ( $data ) {
					return isset( $item['data_sorteio'] ) && $item['data_sorteio'] == $data;
				} );

				if ( empty( $datas ) ) {
					return null;
				}

				usort( $datas, function( $a, $b ) {
					return strcmp( $a['data'], $b['data'] );
				} );
			}

			foreach ( $datas as $item ) {
				$status = $wpdb->get_var($wpdb->prepare("
					SELECT id
					FROM $tabela 
					WHERE post_id = %d
					AND sorteado = 1
					AND data_sorteada = %s 
					LIMIT 1
				",
				$post_id,
				$item['data']
				));

				$instrucoes = $wpdb->get_var($wpdb->prepare("
					SELECT id
					FROM $tabela 
					WHERE post_id = %d
					AND enviou_email_instrucoes = 1
					AND data_sorteada = %s 
					LIMIT 1
				",
				$post_id,
				$item['data']
				));

				array_push( $datas_info, [
					'data' => date( 'd/m/Y H:i', strtotime( $item['data'] ) ),
					'sorteio_realizado' => boolval( $status ),
					'status' =>  boolval( $status ) ? 'Sorteio Realizado' : 'Aguardando Sorteio',
					'instrucoes' => boolval( $instrucoes ) ? 'Instruções enviadas 📧' : 'Instruções pendentes ⚠️'
				] );
			}

			return $datas_info;
		}
	}	
}

/**
 * Filtro necessário para viabilizar consultas utilizando o repetidor de datas do evento
 * Para garantir o funcionamento correto das consultas, o filtro deve ser aplicado
 * sempre que for necessário utilizar o repetidor na WP_Query
 * 
 * Ref.: https://www.advancedcustomfields.com/resources/query-posts-custom-fields/#4-sub-custom-field-values
 */
function filtro_posts_where_evento_datas( $where ) {
    return str_replace("meta_key = 'evento_datas_$", "meta_key LIKE 'evento_datas_%", $where );
}

function filtro_posts_where_evento_premios( $where ) {
    return str_replace("meta_key = 'evento_premios_$", "meta_key LIKE 'evento_premios_%", $where );
}

/**
 * Envia os emails para os ganhadores de sorteio
 * Envia para todos que confirmaram presença
 * ou envia somente para os selecionados
 */
function handle_enviar_instrucoes() {    

    // Dados vindos do Ajax
    $opcao = isset($_POST['opcao']) ? sanitize_text_field($_POST['opcao']) : '';
    $data  = isset($_POST['data']) ? sanitize_text_field($_POST['data']) : '';
    $participantes = isset($_POST['participantes']) ? (array) $_POST['participantes'] : [];
	$post_id = isset($_POST['post_id']) ? sanitize_text_field($_POST['post_id']) : '';
	$responsavel = isset($_POST['responsavel']) ? sanitize_text_field($_POST['responsavel']) : '';
	$conteudo_email = isset($_POST['conteudo_email']) ? wp_kses_post($_POST['conteudo_email']) : '';
	$anexo = '';

    if (empty($opcao) || empty($data)) {
        wp_send_json_error(['msg' => 'Parâmetros inválidos']);
    }
    
    if ($opcao === 'selecionados') { // Somente para selecionados
        
        foreach ($participantes as $id) {
            $id = intval($id);
            
			// Verifica se foi enviado arquivo
			if (isset($_FILES['anexo']) && !empty($_FILES['anexo']['name'])) {
				require_once(ABSPATH . 'wp-admin/includes/file.php');

				// Configurações para o upload
				$upload_overrides = array('test_form' => false);

				// Processa o upload
				$movefile = wp_handle_upload($_FILES['anexo'], $upload_overrides);

				if ($movefile && !isset($movefile['error'])) {
					// Caminho completo no servidor
					$file_path = $movefile['file'];

					// Ou a URL para uso externo
					$file_url = $movefile['url'];

					$anexo = $file_path; // é isso que você envia para a classe
				} else {
					// Falha no upload
					$erro = $movefile['error'];
					error_log("Erro no upload do anexo: $erro");
				}
			}

			if (is_plugin_active('envia-email-sme/envia-email-sme.php')) {
				
				new Envia_Emails_Sorteio_SME(
					$id, 
					null, 
					$post_id, 
					'instrucoes', 
					$conteudo_email,
					$anexo
				);

			}
        }

		$titulo = get_the_title( $post_id );
		$responsavel = sanitize_text_field( $responsavel );
		$assunto = 'Instruções para o evento!';
		$assunto = $assunto . ' - Responsavel: ' . $responsavel;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$emailEnvio = get_field('email_copia','conf-rodape');
		$conteudo_envio = '<stron>Evento: ' . $titulo . ' - Post ID: ' . $post_id . '</strong><br><br>' . $conteudo_email;

		if($anexo){
			wp_mail($emailEnvio, $assunto, $conteudo_envio, $headers, array($anexo));
		} else {
			wp_mail($emailEnvio, $assunto, $conteudo_envio, $headers);
		}

		if ( !empty( $participantes ) && !empty( $conteudo_email ) ) {
			registrar_historico_envio_email( $post_id, $conteudo_email, $participantes, $anexo );
		}
		
        wp_send_json_success([
            'msg' => 'Instruções enviadas para os selecionados',
            'ids' => $participantes
        ]);

    } elseif ($opcao === 'todos' || $opcao === 'geral') { // Todos que confirmaram presença
		global $wpdb;

		// Converte a data recebida
		$data_mysql = DateTime::createFromFormat('Y-m-d-H-i-s', $data)->format('Y-m-d H:i:s');

		$tabela = $wpdb->prefix . 'inscricoes';
		$tabela_datas = $wpdb->prefix . 'inscricao_datas';

		if ($opcao === 'todos') {
			$arrDados = $wpdb->get_results(
				$wpdb->prepare("
					SELECT i.id, i.post_id
					FROM $tabela i				
					WHERE i.post_id = %d
					AND i.sorteado = 1
					AND i.confirmou_presenca = 1 
					AND i.data_sorteada = %s
					ORDER BY i.id
				", $post_id, $data_mysql),
				ARRAY_A
			);
		} elseif($opcao === 'geral') {
			$arrDados = $wpdb->get_results(
				$wpdb->prepare("
					SELECT i.id, i.post_id
					FROM $tabela i				
					WHERE i.post_id = %d
					AND i.sorteado = 1 
					AND i.data_sorteada = %s
					ORDER BY i.id
				", $post_id, $data_mysql),
				ARRAY_A
			);
		}

		if (count($arrDados) < 1) {
			wp_send_json([
				"res" => false,
				"msg" => "Nenhum inscrito encontrado para esta data."
			]);
		}

		$arrEmails = [];

		foreach ($arrDados as $item) {
			$item['tipoEmail'] = 'instrucoes';
			array_push($arrEmails, $item);
		}

		if ( ! function_exists( 'is_plugin_active' ) ) {
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		// Verifica se foi enviado arquivo
		if (isset($_FILES['anexo']) && !empty($_FILES['anexo']['name'])) {
			require_once(ABSPATH . 'wp-admin/includes/file.php');

			// Configurações para o upload
			$upload_overrides = array('test_form' => false);

			// Processa o upload
			$movefile = wp_handle_upload($_FILES['anexo'], $upload_overrides);

			if ($movefile && !isset($movefile['error'])) {
				// Caminho completo no servidor
				$file_path = $movefile['file'];

				// Ou a URL para uso externo
				$file_url = $movefile['url'];

				$anexo = $file_path; // é isso que você envia para a classe
			} else {
				// Falha no upload
				$erro = $movefile['error'];
				error_log("Erro no upload do anexo: $erro");
			}
		}

		if (is_plugin_active('envia-email-sme/envia-email-sme.php')) {
			foreach ($arrEmails as $item) {				
				new Envia_Emails_Sorteio_SME(
					$item['id'], 
					null, 
					$item['post_id'], 
					$item['tipoEmail'], 
					$conteudo_email,
					$anexo
				);				
			}			
		}

		if ( !empty( $arrEmails ) && !empty( $conteudo_email ) ) {
			registrar_historico_envio_email( $post_id, $conteudo_email, wp_list_pluck( $arrEmails, 'id' ) );
		}

		$titulo = get_the_title( $post_id );
		$responsavel = sanitize_text_field( $responsavel );
		$assunto = 'Instruções para o evento!';
		$assunto = $assunto . ' - Responsavel: ' . $responsavel;
		$headers = array('Content-Type: text/html; charset=UTF-8');
		$emailEnvio = get_field('email_copia','conf-rodape');
		$conteudo_envio = '<stron>Evento: ' . $titulo . ' - Post ID: ' . $post_id . '</strong><br><br>' . $conteudo_email;

		if($anexo){
			wp_mail($emailEnvio, $assunto, $conteudo_envio, $headers, array($anexo));
		} else {
			wp_mail($emailEnvio, $assunto, $conteudo_envio, $headers);
		}

		wp_send_json_success([
			'msg' => 'Instruções enviadas para todos na data ' . $data_mysql,
			'conteudo_email' => $conteudo_email,
			'total' => count($arrEmails),
			'anexo' => $anexo,
		]);
	}

    // fallback
    wp_send_json_error(['msg' => 'Opção inválida']);
}

function definir_prazo_expiracao_email_confirmacao( array $participantes, string $tipo_prazo, $prazo, bool $forcar_atualizacao = false ) {

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
		"UPDATE {$wpdb->prefix}inscricoes
		SET prazo_confirmacao = %s
		WHERE id IN ($lista_ids)
		$extra_args",
		$data_final
	);

	return $wpdb->query( $query );
}

// Atualiza o valor do campo de confirmação de presença.
function atualizar_confirm_presen_callback() {
    if ( ! current_user_can('edit_posts') ) {
        wp_send_json_error('Permissão negada.');
    }

    $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : null;
    $valor = isset( $_POST['valor'] ) ? intval( $_POST['valor'] ) : 0;

    if ( !$post_id ) {
        wp_send_json_error('ID do post inválido.');
    }

    update_field( 'confirm_presen', $valor, $post_id );

    wp_send_json_success([
        'post_id' => $post_id,
        'valor'   => $valor,
    ]);
}

/**
 * Monta a listagem de participantes incritos em sorteios do tipo "Período" 
*/
function monta_tab_parcipantes_sorteio_periodo( int $post_id ) {
	global $wpdb;

	$info_periodo_evento = get_field( 'evento_periodo', $post_id );
	?>
	<div id="accordion">
		<?php
		$qtdInscritos = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*)
			FROM {$wpdb->evento_inscricoes}
			WHERE post_id = %d
		", $post_id));

		$qtdElegiveis = $wpdb->get_var($wpdb->prepare("
			SELECT COUNT(*)
			FROM {$wpdb->evento_inscricoes}
			WHERE post_id = %d
			AND sorteado = 0
		", $post_id));
		?>
		<div class="card-inscritos" data-count="<?php echo esc_html( $qtdInscritos ); ?>">
			<div class="card-title" id="<?php echo "participantes-{$post_id}" ?>">
				<h5 class="mb-0">
					<button type="button" class="btn" data-toggle="collapse" data-target="#post-<?php echo esc_html( $post_id ); ?>" aria-expanded="false" aria-controls="<?php echo $post_id; ?>">
						Total de inscritos para o sorteio do Período: <?php echo "{$info_periodo_evento['descricao']} - {$qtdInscritos} (Elegíveis: <span data-ele='{$post_id}'>{$qtdElegiveis}</span>)"; ?>
						<span class="accordion-icon dashicons dashicons-controls-play ml-2"></span>
					</button>
				</h5>
			</div>

			<div id="post-<?php echo esc_html( $post_id ); ?>" class="collapse" aria-labelledby=<?php echo "participantes-{$post_id}" ?>" data-parent="#accordion">
				<div class="card-content">
					<?= monta_tabela_participantes_por_data($post_id); ?>
				</div>
			</div>
		</div>
	</div>
	<?php
}

/**
 * Retorna um array chave valor com os prêmios do sorteio do tipo Premiação
 * O indice é a data que é utilizada como chave e o valor é o texto de descrição do prêmio 
*/
function obter_array_premios_sorteio( int $post_id, $data_sorteio = false ) {
    $tipo_evento = get_field( 'tipo_evento', $post_id );
    $premios = [];
 
    if ( $tipo_evento != 'premio' ) {
        return [];
    }
 
    $lista_premios = get_field( 'evento_premios', $post_id );
 
    if ( !is_array( $lista_premios ) ) {
        return [];
    }
 
    if ( $data_sorteio ){
        foreach ( $lista_premios as $item ) {
            $premios[$item['data']]['premio'] = $item['premio'];
            $premios[$item['data']]['data'] = $item['data_sorteio'];
        }
    } else {
        foreach ( $lista_premios as $item ) {
            $premios[$item['data']] = $item['premio'];
        }
    }
 
    return $premios;
}

// Cria as tabelas necessárias para o fluxo de histórico de envio de e-mails dos sorteios
function criar_tabelas_historico_envios() {
    global $wpdb;

    $criadas = get_option( 'tabelas_historico_envios_criadas', 0 );
    if ( $criadas == 1 ) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $charset_collate = $wpdb->get_charset_collate();

    $tabela_envios = $wpdb->prefix . 'historico_envios';
    $tabela_destinatarios = $wpdb->prefix . 'historico_envios_destinatarios';

    $sql = "
    CREATE TABLE $tabela_envios (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NOT NULL,
		post_id BIGINT UNSIGNED NOT NULL,
        mensagem LONGTEXT NOT NULL,
		anexo VARCHAR(255),
        data_envio DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

        PRIMARY KEY (id),
		KEY idx_user_id (user_id),
    	KEY idx_post_id (post_id),
    	KEY idx_user_post (user_id, post_id)
    ) $charset_collate;

    CREATE TABLE $tabela_destinatarios (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        envio_id BIGINT UNSIGNED NOT NULL,
        inscricao_id BIGINT UNSIGNED NULL,
        nome_completo VARCHAR(255) NOT NULL,
        email_institucional VARCHAR(150) NOT NULL,
        email_secundario VARCHAR(150),
		celular VARCHAR(20),
		telefone_comercial VARCHAR(20),
		cpf VARCHAR(14),
		dre VARCHAR(100),
		unidade_setor VARCHAR(100),
        tipo_vinculo TINYINT COMMENT '1=Servidor, 2=Parceiro, 3=Estagiário',

        PRIMARY KEY (id),
        KEY envio_id (envio_id)
    ) $charset_collate;
    ";

    dbDelta($sql);

    // Atualiza a option que verifica se as tabelas já foram criadas.
    update_option( 'tabelas_historico_envios_criadas', 1 );
}

add_action( 'after_setup_theme', 'criar_tabelas_historico_envios' );

function exibir_historico_envio_emails_callback() {
	global $wpdb;

    if ( !isset( $_POST['post_id'] ) ) {
        wp_die('ID inválido');
    }

	$post_id = intval( $_POST['post_id'] );
	$envios_table = $wpdb->prefix . 'historico_envios';
	$dest_table   = $wpdb->prefix . 'historico_envios_destinatarios';
	$users_table  = $wpdb->users;

	$post_id = intval($_POST['post_id']);

	$query = $wpdb->prepare("
		SELECT
			e.id AS envio_id,
			e.user_id,
			u.display_name AS nome_remetente,
			e.post_id,
			e.mensagem,
			e.anexo,
			e.data_envio,

			d.id AS destinatario_id,
			d.inscricao_id,
			d.nome_completo,
			d.email_institucional,
			d.email_secundario,
			d.celular,
			d.telefone_comercial,
			d.cpf,
			d.dre,
			d.unidade_setor,
			d.tipo_vinculo

		FROM $envios_table e
		LEFT JOIN $users_table u
			ON u.ID = e.user_id
		LEFT JOIN $dest_table d
			ON d.envio_id = e.id

		WHERE e.post_id = %d

		ORDER BY e.data_envio DESC, e.id DESC
	", $post_id);

	$results = $wpdb->get_results( $query, ARRAY_A );
	$envios = [];
	$envios_sem_historico = false;

	/**
	 * Verifica se, apesar de não estar no histórico, já existem envio de notificações
	 * para adicionar a validação informando que o disparo foi realizado antes da implementação
	 * do histórico de envios.
	 */
	if ( !$results ) {
		$tabela_inscricoes = $wpdb->prefix . 'inscricoes';

		$existe = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT 1 
				FROM $tabela_inscricoes 
				WHERE post_id = %d 
				AND enviou_email_instrucoes = 1 
				LIMIT 1",
				$post_id
			)
		);

		$envios_sem_historico = $existe ? true : false;
	}

	if ( $results ) {
		foreach ( $results as $row ) {

			$envio_id = $row['envio_id'];

			if ( !isset( $envios[$envio_id] ) ) {
				$envios[$envio_id] = [
					'envio_id'      => $envio_id,
					'user_id'       => $row['user_id'],
					'usuario_nome'  => $row['usuario_nome'], // <-- AQUI!
					'post_id'       => $row['post_id'],
					'mensagem'      => $row['mensagem'],
					'anexo'			=> $row['anexo'],
					'data_envio'    => $row['data_envio'],
					'destinatarios' => []
				];
			}

			if ( !empty( $row['destinatario_id'] ) ) {
				$envios[$envio_id]['destinatarios'][] = [
					'destinatario_id'     	=> $row['destinatario_id'],
					'inscricao_id'        	=> $row['inscricao_id'],
					'nome_completo'       	=> $row['nome_completo'],
					'email_institucional' 	=> $row['email_institucional'],
					'email_secundario'    	=> $row['email_secundario'],
					'celular'				=> $row['celular'],
					'telefone_comercial'	=> $row['telefone_comercial'],
					'cpf'					=> $row['cpf'],
					'dre'					=> $row['dre'],
					'unidade_setor'			=> $row['unidade_setor'],
					'tipo_vinculo'			=> $row['tipo_vinculo']
				];
			}
		}
	}

	get_template_part( '/includes/sorteio/template-parts/lista-historico-envios-container', null, [
		'post_id' => $post_id,
		'historico_envios' => $envios,
		'envios_sem_historico' => $envios_sem_historico
	] );
		
    wp_die();
}

/**
 * Registra o histórico de um envio de e-mail no banco de dados.
 *
 * @param int    $post_id      O ID do post (evento) relacionado ao envio.
 * @param string $mensagem     O conteúdo do e-mail que foi enviado.
 * @param string $anexo        O caminho do arquivo anexado.
 * @param array  $destinatarios_ids Array com os IDs de inscrição dos destinatários.
 * @return int|false O ID do registro de envio em caso de sucesso, ou false em caso de falha.
 */
function registrar_historico_envio_email( int $post_id, string $mensagem, array $destinatarios_ids, ?string $anexo = null ) {
    global $wpdb;

    $user_id = get_current_user_id();
    if ( !$user_id ) {
        // Não é possível registrar o histórico sem um usuário logado
        return false;
    }

    if ( empty( $destinatarios_ids ) ) {
        return false;
    }

	if ( $anexo ) {
		$dir = WP_CONTENT_DIR . "/uploads/historico-envios/{$post_id}/" . obter_data_com_timezone( 'YmdHis', 'America/Sao_Paulo' );

		if ( !file_exists( $dir ) ) {
			wp_mkdir_p( $dir );
		}
		$destino = $dir . '/' . basename( $anexo );

		rename( $anexo, $destino );
	}

    $tabela_envios = $wpdb->prefix . 'historico_envios';
    $tabela_destinatarios = $wpdb->prefix . 'historico_envios_destinatarios';
    $tabela_inscricoes = $wpdb->prefix . 'inscricoes';

    //Faz o insert na tabela int_historico_envios
    $wpdb->insert(
        $tabela_envios,
        [
            'user_id'		=> $user_id,
            'post_id'		=> $post_id,
            'mensagem'		=> $mensagem,
			'anexo'			=> ( $anexo && !empty( $anexo ) ) ? $destino : null,
            'data_envio' => obter_data_com_timezone( 'Y-m-d H:i:s', 'America/Sao_Paulo' ),
        ],
        [ '%d', '%d', '%s', '%s', '%s' ]
    );

    $envio_id = $wpdb->insert_id;

    if ( !$envio_id ) {
        error_log( 'Falha ao inserir na tabela historico_envios: ' . $wpdb->last_error );
        return false;
    }

    $ids_placeholders = implode( ',', array_fill( 0, count( $destinatarios_ids ), '%d' ) );    
    $query_destinatarios = $wpdb->prepare(
        "SELECT id, nome_completo, user_id, email_institucional, email_secundario, celular, telefone_comercial, cpf, dre, unidade_setor 
         FROM {$tabela_inscricoes} WHERE id IN ($ids_placeholders)",
        $destinatarios_ids
    );
    
    $destinatarios_data = $wpdb->get_results( $query_destinatarios, ARRAY_A );

    if ( empty( $destinatarios_data ) ) {
        error_log( "Nenhum dado de destinatário encontrado para os IDs: " . implode(',', $destinatarios_ids) );
        return $envio_id;
    }

    //Insere cada destinatário na tabela int_historico_envios_destinatarios vinculando ao envio
    foreach ( $destinatarios_data as $destinatario ) {
		if ( !isset( $destinatario['user_id'] ) || empty( $destinatario['user_id'] ) ) {
			$tipo_vinculo = 3; // Estagiário
		} else {
			$tipo_usuario = get_user_meta( $destinatario['user_id'], 'parceira', true );
			$tipo_vinculo = ( $tipo_usuario == 1 ) ? 2/*Parceiro*/ : 1/*Servidor*/;
		}

        $wpdb->insert(
            $tabela_destinatarios,
            [
                'envio_id'            => $envio_id,
                'inscricao_id'        => $destinatario['id'],
                'nome_completo'       => $destinatario['nome_completo'],
                'email_institucional' => $destinatario['email_institucional'],
                'email_secundario'    => $destinatario['email_secundario'],
                'celular'             => $destinatario['celular'],
                'telefone_comercial'  => $destinatario['telefone_comercial'],
                'cpf'                 => $destinatario['cpf'],
                'dre'                 => $destinatario['dre'],
                'unidade_setor'       => $destinatario['unidade_setor'],
                'tipo_vinculo'        => $tipo_vinculo,
            ]
        );
    }

    return $envio_id;
}