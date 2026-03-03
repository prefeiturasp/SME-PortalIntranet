<?php extract( $args ); ?>

<div class="accordion-sorteio" id="lista-participantes-sorteados" data-post="<?php echo esc_html( get_the_ID() ); ?>">

    <?php
    $i = 10;
    $premio = '';
    foreach ( $datas as $item ) :

        if ($tipo_evento == 'premio') {

            $qtds = retorna_quantidades_disponiveis( $post_id, $item['data'], true );
            $label_collapse = 'Total de Inscritos para o Prêmio: ' . $item['premio'] . ' - <span class="total-inscritos">' . $qtds['inscritos'] . _n( ' inscrito', ' inscritos', (int) $qtds['inscritos'] ) . '</span> - <span class="total-disponiveis">' . $qtds['disponiveis'] . _n( ' disponível', ' disponíveis', (int) $qtds['disponiveis'] ) . '</span>';
            $premio = $item['premio'];
            $data_acord = date( 'Y-m-d-H-i-s', strtotime( $item['data'] ) );

        } elseif ( $tipo_evento == 'periodo' ) {
            
            $qtds = retorna_quantidades_disponiveis( $post_id, $item['data'], false );
            $info_periodo_evento = get_field( 'evento_periodo', $post_id );
            $data_sorteio = DateTime::createFromFormat( 'Y-m-d',  $info_periodo_evento['encerramento_inscricoes'] );
            $data_sorteio->setTime(0, 0, 0);
            $data_acord = $data_sorteio->format( 'Y-m-d-H-i-s' );
            $item['data'] = $data_sorteio->format( 'Y-m-d H:i:s' );
            $label_collapse = "Total de Inscritos para o Período: <strong>{$info_periodo_evento['descricao']}</strong>" . ' - <span class="total-inscritos">' . $qtds['inscritos'] . _n( ' inscrito', ' inscritos', (int) $qtds['inscritos'] ) . '</span> - <span class="total-disponiveis">' . $qtds['disponiveis'] . _n( ' disponível', ' disponíveis', (int) $qtds['disponiveis'] ) . '</span>';

        } else {
            $qtds = retorna_quantidades_disponiveis( $post_id, $item['data'], true );
            $label_collapse = date( 'd/m/Y H\hi', strtotime( $item['data'] ) );
            $label_collapse = 'Total de Inscritos para o <strong>evento do dia ' . str_replace( 'h00', 'h', $label_collapse ) . '</strong>' . ' - <span class="total-inscritos">' . $qtds['inscritos'] . _n( ' inscrito', ' inscritos', (int) $qtds['inscritos'] ) . '</span> - <span class="total-disponiveis">' . $qtds['disponiveis'] . _n( ' disponível', ' disponíveis', (int) $qtds['disponiveis'] ) . '</span>';
            $data_acord = date( 'Y-m-d-H-i-s', strtotime( $item['data'] ) );
            

        }        
        
        $current_user = wp_get_current_user();

        // ID do usuário
        $user_id = $current_user->ID;        
        $display_name = $current_user->display_name;
        $responsavel = "{$display_name} (ID: {$user_id})";

        ?>
        <div class="accordion-card">
            <div class="card-title p-2" id="cabecalho-sorteados-<?php echo esc_html( $data_acord ); ?>" data-inscritos="<?php echo esc_html( $qtds['inscritos'] ); ?>">
                <div class="mb-0">
                    <div
                        class="accordion-toggle d-flex justify-content-between align-items-center <?= $i === 1 ? '' : 'collapsed' ?>"
                        data-toggle="collapse"
                        data-target="#lista-sorteados-<?php echo esc_html( $data_acord ); ?>"
                        aria-expanded="<?= $i === 1 ? 'true' : 'false' ?>"
                        aria-controls="collapse-<?php echo esc_html( $data_acord ) ?>"
                    >
                        <span class="text-white">
                            <?= $label_collapse; ?></strong>
                        </span>
                        <span class="accordion-icon dashicons dashicons-controls-play ml-2"></span>
                    </div>
                </div>
            </div>

            <div id="lista-sorteados-<?php echo esc_html( $data_acord ); ?>"
                 class="collapse <?= $i === 1 ? 'show' : '' ?>"
                 aria-labelledby="cabecalho-sorteados-<?php echo esc_html( $data_acord ); ?>"
                 data-parent="#lista-participantes-sorteados">
                <div class="card-body">                    
                    <?php retorna_lista_cortesias_html( $post_id, $item['data'], $unica, $sancao, $participante, $responsavel, $premio ); ?>
                </div>
            </div>
        </div>
        <?php
        $i++;
    endforeach;
    ?>

</div>

<?php
function retorna_lista_cortesias_html($post_id, $data, $unica = false, $sancao = false, $participante = '', $responsavel = '', $premio = '') {//** OK */

	global $wpdb;
	$agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
	$requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
	$escondePresenca = '';
	$tipo_evento = get_field('tipo_evento', $post_id);
	
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
		if($sancao){
			echo '<div class="conteudo-lista" data-data="'.$data.'"><p>Nenhum participante sancionado até o momento</p></div>';
		} else {
			echo '<div class="conteudo-lista" data-data="'.$data.'"><p>Nenhum participante inscrito até o momento</p></div>';
		}
    }

	$i = 1;
	$localInscri = '';
	$itens = '';

	$arrTodosConfirmados = [];

	$qtdIngressoSorteio = get_post_meta(get_the_id(), 'qtd_sorteada', true);

	in_array(false, $arrTodosConfirmados) ? $confirmaTodos = '' : $confirmaTodos = 'checked';
	
	$dataConf = str_replace(' ', '-', $data);
	$dataConf = str_replace(':', '-', $dataConf);
		
	if($tipo_evento == 'premio'){
        $qtds = retorna_quantidades_disponiveis( $post_id, $data, true );
	} else {		
        $qtds = retorna_quantidades_disponiveis( $post_id, $data, true );
	}

    if($tipo_evento == 'periodo'){
        $qtds = retorna_quantidades_disponiveis( $post_id, $data, false );
    }

    $icones = [
        1 => 'icon-telefone.svg',
        2 => 'icon-email.svg',
        3 => 'icon-whatsapp.svg'
    ];

    $confimacao = $requerConfirmacao ? 'd-none' : '';	
    if(!empty($resultados)):
    ?>
        <div class="conteudo-lista" data-data="<?= esc_attr($data); ?>" data-tipo="<?= $premio; ?>">
            <div class="row">
                <div class="col accordion-buttons">
                    <div class="text-right">
                        <button data-data="<?= esc_attr($data); ?>" class="btn btn-notificar-sorteados" disabled>
                            <i class="fa fa-envelope" aria-hidden="true"></i> Confirmar Presença 
                        </button>
                        <button data-data="<?= esc_attr($data); ?>" class="btn btn-enviar-instrucoes" data-toggle="modal" data-target="#modal_<?= esc_attr($dataConf); ?>" data-postid="<?= $post_id; ?>" data-responsavel="<?= $responsavel; ?>">
                            <i class="fa fa-envelope-open" aria-hidden="true"></i> Enviar Instruções 
                        </button>
                    </div>

                    <div class="modal fade" id="modal_<?= esc_attr($dataConf); ?>" data-data="<?= esc_attr($dataConf); ?>" tabindex="-1" role="dialog" aria-labelledby="modal_<?= esc_attr($dataConf); ?>_label" aria-hidden="true">
                        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Enviar instruções sobre os ingressos</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">

                                <div class="custom-control custom-radio custom-control-inline radio-todos">
                                    <input type="radio" id="<?= esc_attr($dataConf); ?>_1" name="opcao_envio" value="todos" class="custom-control-input">
                                    <label class="custom-control-label" for="<?= esc_attr($dataConf); ?>_1">Enviar a todos com presença confirmada.</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline radio-geral <?= $confimacao; ?>">
                                    <input type="radio" id="<?= esc_attr($dataConf); ?>_3" name="opcao_envio" value="geral" class="custom-control-input">
                                    <label class="custom-control-label" for="<?= esc_attr($dataConf); ?>_3">Enviar a todos os participantes.</label>
                                </div>

                                <div class="custom-control custom-radio custom-control-inline radio-selecionados">
                                    <input type="radio" id="<?= esc_attr($dataConf); ?>_2" name="opcao_envio" value="selecionados" class="custom-control-input">
                                    <label class="custom-control-label" for="<?= esc_attr($dataConf); ?>_2">Enviar somente aos selecionados.</label>
                                </div>
                                <hr>
                                <div class="editorEmail" data-name="conteudo_email" id="<?= $linha['id']; ?>"></div>
                                <hr>
                                <div class="form-group">
                                    <label for="anexo_<?= esc_attr($dataConf); ?>">Anexar documento (PDF, DOC, etc.)</label>
                                    <input type="file" class="form-control-file input-anexo" 
                                        id="anexo_<?= esc_attr($dataConf); ?>" 
                                        name="anexo">
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn btn-primary btn-enviar">Enviar</button>
                            </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row"><div class="col">&nbsp;</div></div>
            <div class="row">
                <div class="col-3">
                    <p><span class="status">TOTAL DE CORTESIAS:</span> <strong><?= $qtds['total'] ;?></strong> </p>
                </div>
                <div class="col-3">
                    <p><span class="status">TOTAL DE SOLICITADOS:</span> <strong><span class="total-solicitados"><?= $qtds['total'] - $qtds['disponiveis'];?></span></strong></p>
                </div>
                <div class="col-3">
                    <p><span class="status">TOTAL DE REMANESCENTES:</span> <strong><span class="total-remanecentes"><?= $qtds['disponiveis'] ;?></span></strong></p>
                </div>
            </div>
            <div class="row"><div class="col">
                <p class="legenda-tabela">
                    <img src="<?= get_template_directory_uri(); ?>/img/icon-telefone.svg" alt="icone Telefone" class="mr-1"> Contatado por telefone
                    <img src="<?= get_template_directory_uri(); ?>/img/icon-email.svg" alt="icone Email" class="mr-1 ml-3"> Contatado por e-mail
                    <img src="<?= get_template_directory_uri(); ?>/img/icon-whatsapp.svg" alt="icone Whatsapp" class="mr-1 ml-3"> Contatado por WhatsApp
                </p>
            </div></div>
            <div id="sorteados_data_<?= esc_attr($dataConf); ?>">
                <div class="table-responsive">
                    <table class="table tabela-lista-sorteados">
                        <thead>
                        <tr>
                            <th style="width: 1%;"><input type="checkbox" class="check-sorteados check-all" name="marcar-todos"></th>
                            <th style="width:25%">Nome Completo</th>
                            <th>E-mails</th>
                            <th>Telefones</th>
                            <th>CPF</th>
                            <th>DRE/SME - UE/Setor</th>
                            <th>Opções</th>
                        </tr>
                        </thead>
                        <tbody>
                            
                            <?php
                                foreach ($resultados as $linha) :
                                    $id = $linha['id'];
                                    $contato = esc_html($linha['fez_contato']);
                                    $contato == '1' ? $contato = 'checked': $contato = '';
                                    $confirmacaoPresenca = esc_html($linha['confirmou_presenca']);
                                    $enviouEmailInstrucoes = esc_html($linha['enviou_email_instrucoes']);
                                    $prazo_confirmacao = esc_html($linha['prazo_confirmacao']);
                                    $data_validar = new DateTime($prazo_confirmacao, new DateTimeZone('America/Sao_Paulo'));
                                    $qtd_cortesias = $linha['qtd'];
                                    
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
                                    
                                    $confirmacaoPresenca == '2' ? $desabilitar ='disabled' : $desabilitar = '';                                   
                                ?>
                                
                                    <tr class="sorteado-<?= $id; ?> sorteado-item">
                                        <td><input type="checkbox" class="check-sorteados check-item" name="participantes-sorteados[]" value="<?= $id; ?>" <?= $desabilitar; ?>></td>
                                        <td>
                                            <span class="nome">
                                                <?= $i; ?>º 
                                                <?= esc_html(strtoupper($linha['nome_completo'])); ?>                                                
                                            </span>
                                            <span class="icone icone-user-<?= $id; ?>">
                                                <?php $linha['tipo_contato'] != 0 ? print ' <img src="' . get_stylesheet_directory_uri() . '/img/' . $icones[$linha['tipo_contato']] . '" class="icone-contato">' : ''; ?>
                                            </span>
                                            <br>
                                            <span class="<?= $tag; ?>">
                                                <?= esc_html($localInscri); ?>
                                            </span>
                                        </td>
                                        <td class="negrito"><span id="email-inst-<?= $id; ?>"><?= esc_html($linha['email_institucional']); ?></span><img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio" id="copiar-email-inst-<?= $id; ?>"><br><span id="email-sec-<?= $id; ?>"><?= esc_html($linha['email_secundario']); ?></span><img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio" id="copiar-email-sec-<?= $id; ?>"></td>
                                        <td class="negrito"><?= esc_html($linha['celular']); ?><br><?= esc_html($linha['telefone_comercial']); ?></td>
                                        <td class="negrito"><?= esc_html($linha['cpf']); ?></td>
                                        <td class="negrito"><?= esc_html($linha['dre']); ?><br><?= esc_html($linha['unidade_setor']); ?></td>
                                        <td>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-primary btn-contato"
                                                data-user-id="<?= $id; ?>"
                                                data-valor-atual="<?= $linha['tipo_contato']; ?>"
                                                data-tipo="cortesias"
                                                data-toggle="popover">
                                                <i class="fa fa-ellipsis-v" aria-hidden="true"></i>
                                            </button>

                                            <div class="d-none popover-template">
                                                <p>Contatado por</p>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="forma_contato" value="1" id="contato-telefone-<?= $id; ?>">
                                                    <label class="form-check-label" for="contato-telefone-<?= $id; ?>" data-toggle="tooltip" data-placement="top" title="Contatado por Telefone"><img src="<?= get_template_directory_uri(); ?>/img/icon-telefone.svg" alt="icone Telefone" class="mr-1"> Telefone</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="forma_contato" value="2" id="contato-email-<?= $id; ?>">
                                                    <label class="form-check-label" for="contato-email-<?= $id; ?>" data-toggle="tooltip" data-placement="top" title="Contatado por E-mail"><img src="<?= get_template_directory_uri(); ?>/img/icon-email.svg" alt="icone Email" class="mr-1"> Email</label>
                                                </div>

                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="forma_contato" value="3" id="contato-whatsapp-<?= $id; ?>">
                                                    <label class="form-check-label" for="contato-whatsapp-<?= $id; ?>" data-toggle="tooltip" data-placement="top" title="Contatado por WhatsApp"><img src="<?= get_template_directory_uri(); ?>/img/icon-whatsapp.svg" alt="icone Whatsapp" class="mr-1"> Whatsapp</label>
                                                </div>
                                                
                                            </div>
                                            <br>
                                            <img src="<?= get_template_directory_uri().'/img/remove-participante.svg'; ?>" id="remove-participante-sorteado-<?= $id; ?>-<?= esc_html(strtoupper($linha['nome_completo'])); ?>" class="remove-participante-sorteado <?= $escondeRemover; ?>" title="Remove o participante da lista de sorteados">
                                            <span id="remove-participante-sorteado-<?= $id; ?>-gif" class="spinner"></span>
                                            <br>
                                            <div class="d-flex justify-content-start align-items-center">
                                                <img id="reevia-email-sorteado-<?= $id; ?>-<?= esc_html(strtoupper($linha['nome_completo'])); ?>" src="<?= get_template_directory_uri().'/img/email-enviado.svg'; ?>" title="<?= $dataHoraEnvioSorteado; ?>" class="reevia-email-sorteado <?= $esconde; ?>" data-data="{ATRIBUTO-DATA}">
                                                <span id="reenvia-email-participante-sorteado-<?= $id; ?>-gif" class="spinner"></span>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr class="table-light sorteado-<?= $id; ?>">
                                        <td>&nbsp;</td>
                                        <td class="check-instrucoes">
                                            <font class="status">QTD CORTESIAS SOLICITADAS:</font> 
                                            <span class="valor-status"><?= $qtd_cortesias; ?></span>
                                        </td>
                                        <td class="check-contato <?= $escondePresenca; ?>">
                                            <font class="status">NOTIFICADO:</font> 
                                            <span class="valor-status"><?= $statusNotificado; ?></span>
                                            <?= $prazo_confirmacao; ?>
                                        </td>
                                        <td class="check-presenca <?= $escondePresenca; ?>">
                                            <font class="status">CONFIRMOU PRESENÇA:</font> 
                                            <span class="valor-status"><?= $confPresenca; ?></span>
                                        </td>
                                        <td class="check-instrucoes" colspan="2">
                                            <font class="status">E-MAIL COM INSTRUÇÕES:</font> 
                                            <span class="valor-status"><?= $enviouEmailInstrucoes; ?></span>
                                        </td>
                                    </tr>

                                <?php
                                    $i++;
                                endforeach;
                            ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
	endif;
}

function retorna_quantidades_disponiveis($post_id, $data = null, $multi = false) {

    global $wpdb;

    $tabela_inscri = 'int_cortesias_inscricoes';
    $tabela_acf    = 'int_cortesias_acf_datas';

    $whereData = ($multi && $data) ? 'AND a.data_evento = %s' : '';
    $params    = ($multi && $data) ? [$post_id, $data] : [$post_id];

    $sql = $wpdb->prepare(
        "
        SELECT COUNT(*)
        FROM {$tabela_inscri} AS i
        INNER JOIN {$tabela_acf} AS a
            ON a.id = i.acf_id
        WHERE i.post_id = %d
        {$whereData}
        ",
        ...$params
    );

    $qtdInscritos = (int) $wpdb->get_var($sql);
    
    $whereDataEstoque = ($multi && $data) ? 'AND data_evento = %s' : '';
    $paramsEstoque    = ($multi && $data) ? [$post_id, $data] : [$post_id];

    $sql = $wpdb->prepare(
        "
        SELECT estoque_atual, estoque_total
        FROM {$tabela_acf}
        WHERE post_id = %d
        {$whereDataEstoque}
        LIMIT 1
        ",
        ...$paramsEstoque
    );

    $estoque = $wpdb->get_row($sql, ARRAY_A);

    return [
        'inscritos'   => $qtdInscritos,
        'disponiveis' => (int) ($estoque['estoque_atual'] ?? 0),
        'total'       => (int) ($estoque['estoque_total'] ?? 0),
    ];
}