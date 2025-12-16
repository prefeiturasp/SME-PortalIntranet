<?php 
    extract( $args );

    function monta_lista_sancoes_por_data($post_id, $data, $unica = false, $sancao = false, $participante = '') {

        global $wpdb;
        $agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
        $requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
        $escondePresenca = '';
        if(!$requerConfirmacao){
            $escondePresenca = 'd-none';
        }
        
        $tabela =  'int_inscricoes';
        if ($unica) {
            $resultados = $wpdb->get_results(
                "SELECT * FROM $tabela 
                WHERE post_id = $post_id AND sorteado = 1 
                ORDER BY data_hora_sorteado ASC", 
                ARRAY_A 
            );
        } else {
            if ($sancao) {
                $sql = "
                    SELECT * 
                    FROM $tabela 
                    WHERE post_id = %d 
                    AND sorteado = 1 
                    AND data_sorteada = %s
                ";

                $params = [ $post_id, $data ];

                // Se o participante veio preenchido, adiciona no WHERE
                if ( !empty($participante) ) {
                    $sql .= " AND nome_completo LIKE %s";
                    $params[] = '%' . $wpdb->esc_like($participante) . '%';
                }

                $sql .= " ORDER BY data_hora_sorteado ASC";

                $resultados = $wpdb->get_results( $wpdb->prepare($sql, $params), ARRAY_A );

            } else {
                $resultados = $wpdb->get_results(
                    $wpdb->prepare(
                        "SELECT * FROM $tabela 
                        WHERE post_id = %d AND sorteado = 1 AND data_sorteada = %s
                        ORDER BY data_hora_sorteado ASC",
                        $post_id,
                        $data
                    ),
                    ARRAY_A 
                );
            }
        }

        // Nenhum resultado → retorna false
        if (empty($resultados)) {
            return false;
        }

        $i = 1;
        $localInscri = '';
        $itens = '';

        $arrTodosConfirmados = [];

        $qtdIngressoSorteio = get_post_meta(get_the_id(), 'qtd_sorteada', true);

        foreach ($resultados as $linha) {
            $id = $linha['id'];            
            $confirmacaoPresenca = esc_html($linha['confirmou_presenca']);
            $prazo_confirmacao = esc_html($linha['prazo_confirmacao']);
            $data_validar = new DateTime($prazo_confirmacao);
            
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

            if($qtdIngressoSorteio < $i) {
                $esconde = '';
            }  
            
            if($confirmacaoPresenca == '0') {
                $esconde = '';
            }

            $hoje = current_time('Y-m-d');
            $tabela_sancoes = $wpdb->prefix . "inscricao_sancoes";

            // Verifica se já existe sanção ativa
            $sancao_existente = $wpdb->get_row(
                $wpdb->prepare(
                    "SELECT * FROM $tabela_sancoes WHERE cpf = %s",
                    $linha['cpf']
                ),
                ARRAY_A
            );

            $btn_debloqueio = '';
            $desabilitar = 0;

            if ( $sancao_existente && $sancao_existente['data_validade'] >= $hoje ) {
                $show_sancao = '<span class="dest-vermelho">SIM - SANÇÃO APLICADA ATÉ ' . date('d/m/Y', strtotime($sancao_existente['data_validade'])) . '</span>';
                $btn_debloqueio = '<button class="btn btn-outline-success btn-debloqueio" data-sancao-id="' . $sancao_existente['id'] . '">Permitir</button>';
                $desabilitar = 1;
            } else {
                $show_sancao = '<span class="dest-azul">NÃO</span>';
            }

            $arrHistorico = json_decode($linha['historico_emails']);
            $arrHistorico->vencedor->enviado == '1' ? $esconde = '' : $esconde = 'hidden';

            $dataConf = explode(' ', $arrHistorico->vencedor->data_hora_envio);
            $dataConfirmacao = explode('-', $dataConf[0]);
            $dataHoraEnvioSorteado = 'E-mail enviado dia: '.$dataConfirmacao[2].'/'.$dataConfirmacao[1].'/'.$dataConfirmacao[0].' às '.$dataConf[1];
            $statusNotificado = $arrHistorico->vencedor->enviado == '1' ? 'SIM' : 'NÃO';

            $item = file_get_contents(get_template_directory().'/includes/sorteio/sancao-tab-view.html');
            $item = str_replace('{ID}',                 	$id,                                  							$item);
            $desabilitar == 1 ? $item = str_replace('{DESABILITAR}', 'disabled', $item) : $item = str_replace('{DESABILITAR}', '', $item);
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
            $item = str_replace('{PRESENCA-CHECADA}',       $confPresenca,                                 					$item); 
            $item = str_replace('{ESCONDE-PRESENCA}',       $escondePresenca,                                				$item);
            $item = str_replace('{SANCAO-APLICADA}',        $show_sancao,                                 					$item);
            $item = str_replace('{UNIDADE}',                esc_html($linha['unidade_setor']),             		      		$item);
            $item = str_replace('{DEBLOQUEIO-SANCAO}',      $btn_debloqueio,                                 					$item);
            $itens .= $item;

            $i++;
        }

        in_array(false, $arrTodosConfirmados) ? $confirmaTodos = '' : $confirmaTodos = 'checked';
        
        $dataConf = str_replace([' ', ':'], '-', $data);
            
        $html = file_get_contents(get_template_directory().'/includes/sorteio/tab-view-sancao-lista.html');
        $html = str_replace('{CONFIRMA-TODOS}',   $confirmaTodos,      $html);
        $html = str_replace('{ATRIBUTO-ID}',      esc_attr($post_id),  $html);
        $html = str_replace('{CONTEUDO-TAB}',     $itens,              $html);
        $html = str_replace('{ATRIBUTO-DATA}',    esc_attr($data),     $html);
        $html = str_replace('{ATRIBUTO-DATA-CONF}', esc_attr($dataConf), $html);
        $html = str_replace('{EDITOR-ID}',   $linha['id'],             $html);
        $html = str_replace('{POST-ID}',   $post_id,                   $html);

        return $html;
    }
?>


<div class="accordion accordion-sorteio" id="lista-participantes-sorteados" data-post="<?= $post_id; ?>">
  
    <?php
    $html_final = '';
    $tipo_evento = get_field('tipo_evento', $post_id);
    foreach ( $datas as $item ) {
        $conteudo = monta_lista_sancoes_por_data( $post_id, $item['data'], $unica,  $sancao, $participante );

        if ( $conteudo !== false ) {
            $data_formatada = date( 'd/m/Y H\hi', strtotime( $item['data'] ) );
            $data_formatada = str_replace( 'h00', 'h', $data_formatada );
            $data_acord = date( 'dmYHis', strtotime( $item['data'] ) );
            if ($tipo_evento == 'premio') {
                $texto_collapse = 'Sorteados do prêmio: <strong>' . esc_html( $item['premio'] ) . '</strong>';
            } elseif( $tipo_evento === 'periodo'){
                $info_periodo_evento = get_field( 'evento_periodo', $post_id );
                $texto_collapse = 'Sorteados do Período: <strong>' . esc_html( $info_periodo_evento['descricao'] ) . '</strong>';
            }else {
                $texto_collapse = 'Sorteados para a <strong>sessão de ' . esc_html( $data_formatada ) . '</strong>';
            }

            $html_final .= '
            <div class="accordion-card">
                <div class="card-title p-2" id="cabecalho-sorteados-'.esc_html( $data_acord ).'">
                    <div class="mb-0">
                        <div class="accordion-toggle collapsed d-flex justify-content-between align-items-center"
                            data-toggle="collapse"
                            data-target="#lista-sorteados-'.esc_html( $data_acord ).'"
                            aria-expanded="false"
                            aria-controls="collapseOne">
                            <span class="text-white">
                                ' . $texto_collapse . '
                            </span>
                            <span class="accordion-icon dashicons dashicons-controls-play ml-2"></span>
                        </div>
                    </div>
                </div>

                <div id="lista-sorteados-'.esc_html( $data_acord ).'" class="collapse" aria-labelledby="headingOne" data-parent="#lista-participantes-sorteados">
                    <div class="card-body">
                        '.$conteudo.'
                    </div>
                </div>
            </div>';
        }
    }

    if ( empty( $html_final ) ) {
        echo '<div class="sem-resultados"><p>Não foram localizados participantes com os filtros informados!</p></div>';
    } else {
        echo $html_final;
    }
    ?>
</div>