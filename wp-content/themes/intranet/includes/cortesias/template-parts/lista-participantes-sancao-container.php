<?php extract( $args ); ?>

<div class="accordion-sorteio" id="lista-participantes-sorteados" data-post="<?php echo esc_html( $post_id ); ?>">

    <?php
    $i = 10;
    $premio = '';
    $tem_resultados = false;

    foreach ( $datas as $item ) :

        if ($tipo_evento == 'premio') {

            $label_collapse = 'Inscritos do prêmio: <strong>' . $item['premio'] . '</strong>';
            $premio = $item['premio'];

        } elseif ( $tipo_evento == 'periodo' ) {
            
            $info_periodo_evento = get_field( 'evento_periodo', $post_id );
            $data_sorteio = DateTime::createFromFormat( 'Y-m-d',  $info_periodo_evento['encerramento_inscricoes'] );
            $data_sorteio->setTime(0, 0, 0);
            $data_acord = $data_sorteio->format( 'dmYHi' );
            $item['data'] = $data_sorteio->format( 'Y-m-d H:i:s' );
            $label_collapse = "Inscritos do Período: <strong>{$info_periodo_evento['descricao']}</strong>";

        } else {

            $label_collapse = date( 'd/m/Y H\hi', strtotime( $item['data'] ) );
            $label_collapse = 'Inscritos para a <strong>sessão de ' . str_replace( 'h00', 'h', $label_collapse ) . '</strong>';
            $data_acord = date( 'dmYHi', strtotime( $item['data'] ) );
        }        
              
        $conteudo = retorna_lista_inscritos_cortesia_html( $post_id, $item['data'], $unica, $participante);

        if ( $conteudo ) :
            $tem_resultados = true;
            ?>
            <div class="accordion-card">
                <div class="card-title p-2" id="cabecalho-sorteados-<?php echo esc_html( $data_acord ) . $i; ?>">
                    <div class="mb-0">
                        <div
                            class="accordion-toggle d-flex justify-content-between align-items-center <?= $i === 1 ? '' : 'collapsed' ?>"
                            data-toggle="collapse"
                            data-target="#lista-sorteados-<?php echo esc_html( $data_acord ) . $i; ?>"
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

                <div id="lista-sorteados-<?php echo esc_html( $data_acord ) . $i; ?>"
                    class="collapse <?= $i === 1 ? 'show' : '' ?>"
                    aria-labelledby="cabecalho-sorteados-<?php echo esc_html( $data_acord ) . $i; ?>"
                    data-parent="#lista-participantes-sorteados">
                    <div class="card-body">                    
                        <?php echo $conteudo; ?>
                    </div>
                </div>
            </div>
            <?php
        endif;
        $i++;
    endforeach;

    if ( !$tem_resultados ) :
        ?>
        <div class="sem-resultados p-4 text-center">
            <p>Não foram localizados participantes com os filtros informados!</p>
        </div>
        <?php
    endif;
    ?>
</div>

<?php
function retorna_lista_inscritos_cortesia_html($post_id, $data, $unica = false, $participante = '') {

	global $wpdb;
	$agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
	$requerConfirmacao = get_post_meta($post_id, 'confirm_presen', true);
	$escondePresenca = '';
	
    $tabela_inscri = 'int_cortesias_inscricoes';
    $tabela_acf    = 'int_cortesias_acf_datas';

	if($unica){

		$sql = "
            SELECT i.*
            FROM {$tabela_inscri} AS i
            INNER JOIN {$tabela_acf} AS a
                ON a.id = i.acf_id
            WHERE i.post_id = %d
        ";

        $params = [$post_id];

	} else {
		
        $sql = "
            SELECT i.*
            FROM {$tabela_inscri} AS i
            INNER JOIN {$tabela_acf} AS a
                ON a.id = i.acf_id
            WHERE i.post_id = %d
            AND a.data_evento = %s
        ";

        $params = [$post_id, $data];
	}

    //Se o participante veio preenchido, adiciona no WHERE
    if ( !empty( $participante ) ) {
        $sql .= " AND nome_completo LIKE %s";
        $params[] = '%' . $wpdb->esc_like( $participante ) . '%';
    }

    $sql .= " ORDER BY i.data_inscricao ASC";
    $resultados = $wpdb->get_results( $wpdb->prepare( $sql, $params ), ARRAY_A );

    if (empty($resultados)) {
        return false;
    }

	$i = 1;
	$localInscri = '';
	
	$dataConf = str_replace(' ', '-', $data);
	$dataConf = str_replace(':', '-', $dataConf);	

    ob_start();

    if(!empty($resultados)):
        ?>
        <div class="conteudo-lista" data-data="<?= esc_attr($data); ?>">
            <div class="row">
                <div class="col accordion-buttons">
                    <div class="text-right">
                        <button class="btn btn-outline-danger btn-aplicar-sancao">
                            <i class="fa fa-ban" aria-hidden="true"></i> Aplicar Sanção 
                        </button>                
                    </div>            
                </div>
            </div>
            <div class="row"><div class="col">&nbsp;</div></div>
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
                        </tr>
                        </thead>
                        <tbody>
                            
                            <?php
                                foreach ($resultados as $linha) :

                                    $id = $linha['id'];
                                    $confirmacaoPresenca = esc_html($linha['confirmou_presenca']);
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

                                    $arrHistorico = json_decode($linha['historico_emails']);

                                    $dataConf = explode(' ', $arrHistorico->vencedor->data_hora_envio);
                                    $confirmacaoPresenca == '2' ? $desabilitar ='disabled' : $desabilitar = '';   
                                    $sancao_existente = vefificar_sancao_ativa( $linha['cpf'] );                                
                                    ?>
                                
                                    <tr class="sorteado-<?= $id; ?> sorteado-item">
                                        <td><input type="checkbox" class="check-sorteados check-item" name="participantes-sorteados[]" value="<?= $id; ?>" <?= $desabilitar; ?>></td>
                                        <td><span class="nome"><?= $i; ?>º <?= esc_html(strtoupper($linha['nome_completo'])); ?></span><span class="<?= $tag; ?> ml-1"><?= esc_html($localInscri); ?></span></td>
                                        <td class="negrito"><span id="email-inst-<?= $id; ?>"><?= esc_html($linha['email_institucional']); ?></span><img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio" id="copiar-email-inst-<?= $id; ?>"><br><span id="email-sec-<?= $id; ?>"><?= esc_html($linha['email_secundario']); ?></span><img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio" id="copiar-email-sec-<?= $id; ?>"></td>
                                        <td class="negrito"><?= esc_html($linha['celular']); ?><br><?= esc_html($linha['telefone_comercial']); ?></td>
                                        <td class="negrito"><?= esc_html($linha['cpf']); ?></td>
                                        <td class="negrito"><?= esc_html($linha['dre']); ?><br><?= esc_html($linha['unidade_setor']); ?></td>
                                    </tr>
                                    <tr class="table-light sorteado-<?php echo esc_html( $id ); ?>">
                                        <td>&nbsp;</td>
                                        <td class="check-contato <?php echo esc_html( $escondePresenca ); ?>" colspan="2">
                                            <font class="status">CONFIRMOU PRESENÇA:</font> 
                                            <span class="valor-status"><?php echo wp_kses_post( $confPresenca ); ?></span>
                                        </td>
                                        <td class="check-presenca" colspan="2">
                                            <font class="status">APLICADA SANÇÃO AO PARTICIPANTE:</font> 
                                            <span class="valor-status">
                                                <?php if ( $sancao_existente && $sancao_existente['data_validade'] >= $agora->format( 'Y-m-d' ) ) : ?>
                                                    <span class="dest-vermelho">
                                                        SIM - SANÇÃO APLICADA ATÉ <?php echo esc_html( date('d/m/Y', strtotime( $sancao_existente['data_validade'] ) ) ); ?>
                                                    </span>
                                                <?php else : ?>
                                                    <span class="dest-azul">NÃO</span>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td class="check-debloqueio">
                                            <?php if ( $sancao_existente && $sancao_existente['data_validade'] >= $agora->format( 'Y-m-d' ) ) : ?>
                                                <button
                                                    class="btn btn-outline-success btn-debloqueio"
                                                    data-sancao-id="<?php echo esc_html( $sancao_existente['id'] ); ?>"
                                                    >
                                                    Permitir
                                                </button>
                                            <?php endif; ?>
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

    return ob_get_clean();
}

function vefificar_sancao_ativa( string $cpf ) {
    global $wpdb;

    $tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';
    return $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM $tabela_sancoes WHERE cpf = %s",
            $cpf
        ),
        ARRAY_A
    );
}