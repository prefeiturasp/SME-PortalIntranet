<?php

wp_localize_script('scripts_js', 'ajax_obj', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonce'    => wp_create_nonce('acoes_inscricao')
]);

$historico_participante = new Historico_Participacoes();
$user_cpf = get_user_cpf();
$inscricoes_participante = [];
$filtros = [];

if ( $user_cpf ) {
    $filtros = get_filtros_minhas_inscricoes();
    $inscricoes_participante = $historico_participante->get_eventos_participante_com_filtros( $user_cpf, $filtros );
}

?>
<div class="container">
    <div class="row">
        <?php if ( !$user_cpf || ( !$inscricoes_participante && !$filtros ) ) : ?>
            <div class="alert alert-primary text-center w-100 mb-5" role="alert">
                Você ainda não tem inscrições realizadas.
            </div>
        <?php endif; ?>
        
        <?php if ( !$inscricoes_participante && $filtros ) : ?>
            <div class="no-results-inscricoes w-100 mb-5">
                <h2 class="search-title ml-3">
                    <span class="azul-claro-acervo"><strong class="text-primary">0</strong></span> <strong>resultados</strong>
                </h2>
                <div class="search-image d-flex flex-column justify-content-center align-items-center">
                    <img src="https://educacao.sme.prefeitura.sp.gov.br/wp-content/themes/sme-portal-institucional/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado">
                    <h2 class="text-primary mt-4">Nenhuma inscrição encontrada para os filtros selecionados.</h2>
                </div>
            </div>
        <?php endif; ?>
        
        <?php if ( $inscricoes_participante ) : ?>
            <div class="col-sm-12 mb-4 tabela-scroll" id="minhas-inscricoes">
                <table id="tabela-inscricoes-participante" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Evento</th>
                            <th>Modalidade</th>
                            <th>Ações</th>
                            <th>Situação da inscrição</th>
                            <th>Resultado</th>
                            <th>Confirmação de presença</th>
                            <th>Instruções</th>
                            <th>Presença</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ( $inscricoes_participante as $inscricao ) :
                            $pode_cancelar = check_inscricoes_abertas( $inscricao );
                            $pode_confirmar = check_necessidade_confirmacao( $inscricao );
                            $situacao_inscricao = get_situacao_inscricao( $inscricao );
                            $resultado_inscricao = get_resultado_inscricao( $inscricao );
                            $status_participacao = get_status_participacao( $inscricao );
                            $instrucoes_evento = get_envio_instrucoes_inscricao( $inscricao );
                            $participou_evento = check_comparecimento_evento( $inscricao );
                            ?>
                            <tr>
                                <td id="nome-evento">
                                    <a href="<?php echo esc_url( get_the_permalink( $inscricao->post_id ) ); ?>" target="_blank">
                                        <?php echo esc_html( $inscricao->nome_evento ); ?> <i class="fa fa-external-link" aria-hidden="true"></i>
                                    </a>
                                </td>
                                <td id="modalidade">
                                    <?php if($inscricao->tipo == 'sorteio') : ?>
                                        <span class="sorteio"><i class="fa fa-cube" aria-hidden="true"></i> Sorteio</span>
                                    <?php else : ?>
                                        <span class="cortesia"><i class="fa fa-bolt" aria-hidden="true"></i> Ordem de Inscrição</span>
                                    <?php endif; ?>
                                </td>
                                <td id="acoes" class="text-center">
                                    <?php if ( !$pode_confirmar && !$pode_cancelar ) : ?>
                                        <span>-</span>
                                    <?php else : ?>
                                        <div class="btn-group btn-acoes" role="group">
                                            <button type="button" class="btn btn-outline-secondary dropdown-toggle seletor-acoes" data-toggle="dropdown" aria-expanded="false">
                                                Selecione
                                            </button>
                                            <div class="dropdown-menu">
                                                <?php if ( $pode_cancelar ) : ?>
                                                    <a
                                                        class="dropdown-item btn-cancelar-inscricao"
                                                        data-post="<?php echo esc_html( $inscricao->post_id ); ?>"
                                                        data-modalidade="<?php echo esc_html( $inscricao->tipo ); ?>"
                                                        >
                                                        Cancelar inscrição
                                                    </a>
                                                <?php endif; ?>

                                                <?php if ( $pode_confirmar && $pode_cancelar ) : ?>
                                                    <hr class="m-0">
                                                <?php endif; ?>

                                                <?php
                                                if ( $pode_confirmar ) :
                                                    $prazo_formatado = $dataBrasil = date( 'd/m/Y H:i', strtotime( $inscricao->prazo_confirmacao ) );
                                                    ?>
                                                    <a
                                                        class="dropdown-item btn-confirmar-presenca"
                                                        data-post="<?php echo esc_html( $inscricao->post_id ); ?>"
                                                        data-tipo="<?php echo esc_attr(  get_field( 'tipo_evento', $inscricao->post_id ) ); ?>"
                                                        data-prazo="<?php echo esc_attr( $prazo_formatado ); ?>"
                                                        data-modalidade="<?php echo esc_html( $inscricao->tipo ); ?>"
                                                        data-inscricao="<?php echo esc_html( $inscricao->id ); ?>"
                                                        >
                                                        Confirmar/Cancelar participação
                                                    </a>
                                                    <?php
                                                endif;
                                                ?>
                                            </div> 
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td id="situacao-inscricao" class="text-center">
                                    <?php echo $situacao_inscricao; ?>
                                </td>
                                <td id="resultado-inscricao" class="text-center"><?php echo $resultado_inscricao; ?></td>
                                <td id="minha-participacao" class="text-center"><?php echo $status_participacao; ?></td>
                                <td id="instrucoes-evento" class="text-center"><?php echo $instrucoes_evento; ?></td>
                                <td id="participou-evento" class="text-center"><?php echo $participou_evento; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- MODAL DE INSTRUÇÕES -->
                <div class="modal fade" id="modalEmailInstrucao" tabindex="-1" role="dialog" aria-modal="true">
                    <div class="modal-dialog modal-lg" role="document">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">
                                    Detalhes do envio do e-mail
                                </h5>

                                <button type="button" class="close" data-dismiss="modal">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body">
                                <div class="container-fluid">
                                    <div class="row mb-3">

                                        <div class="col-md-6">
                                            <h6><strong>Participante</strong></h6>
                                            <span id="email-participante-nome" class="mb-0"></span><br>
                                            <span id="email-participante-email1" class="mb-0"></span><br>
                                            <span id="email-participante-email2" class="mb-0"></span><br>
                                        </div>

                                        <div class="col-12 col-md-6">
                                            <h6><strong>Evento</strong></h6>
                                            <span id="email-evento" class="mb-0"></span>
                                        </div>

                                        <div class="col-12 col-md-6 mt-2">
                                            <h6><strong>Remetente</strong></h6>
                                            <span id="email-admin" class="mb-0">intranet.beneficios@sme.prefeitura.sp.gov.br</span>
                                        </div>

                                        <div class="col-12 col-md-6 mt-2">
                                            <h6><strong>Data/Hora</strong></h6>
                                            <span id="email-data" class="mb-0"></span>
                                        </div>

                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-md-12">
                                            <h6><strong>Descrição do e-mail de instrução</strong></h6>
                                            <div id="email-mensagem" class="border p-3 bg-light"></div>
                                        </div>
                                    </div>

                                    <div class="content-attachment border-bottom mb-2">
                                        <a href="" download>
                                            <strong><i class="fa fa-paperclip" aria-hidden="true"></i> Baixar anexo</strong>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-dismiss="modal">
                                    Fechar
                                </button>

                            </div>

                        </div>

                    </div>
                </div>
                <!-- FIM DO MODAL DE INSTRUÇÕES -->
            </div>
        <?php endif; ?>
    </div>
</div>

<?php

function check_inscricoes_abertas( object $inscricao ) {

    $hoje = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );
    $encerramento_inscricoes = get_field( 'enc_inscri', $inscricao->post_id );
    
    return $encerramento_inscricoes >= $hoje;
}

function get_situacao_inscricao( object $inscricao ) {
    $inscricoes_abertas = check_inscricoes_abertas( $inscricao );

    if ( $inscricao->tipo === 'cortesia' ) {
        $encerramento_inscricoes = get_field( 'enc_inscri', $inscricao->post_id );

        if ( !$encerramento_inscricoes ) {
            return null;
        }

        $data_formatada = DateTime::createFromFormat( 'Ymd', $encerramento_inscricoes )->format( 'd/m/Y' );
        $data_formatada = formatar_data_por_extenso( $data_formatada, false, false );

        if ( !$inscricoes_abertas ) {
            return "Encerrada <br> {$data_formatada}";
        }

        return "Aberta até <br> {$data_formatada}";
    }

    if ( !$inscricoes_abertas ) {
        return "Sorteio realizado <br>" . obter_ultima_data_sorteio( $inscricao->post_id, false );
    }

    return "Próximo sorteio <br>" . obter_proxima_data_sorteio( $inscricao->post_id, false );
}

function get_resultado_inscricao( object $inscricao ) {

    if ( $inscricao->resultado_inscricao === 'contemplado' ) {
        return '<strong>Contemplado</strong>';
    }

    if ( $inscricao->resultado_inscricao === 'nao_sorteado' ) {
        return 'Não foi dessa vez &#128546;';
    }

    return '-';
}

function get_status_resultado_inscricao( object $inscricao ) {

        /* Cortesia */
        if ( $inscricao->tipo === 'cortesia' ) {
            return 'contemplado';
        }

        if ( $inscricao->sorteado == '1' ) {
            return 'contemplado';
        }

        $divulgar_resultado = boolval(
            get_post_meta(
                $inscricao->post_id,
                'exibe_resultado_pagina',
                true
            )
        );

        $inscricoes_abertas = check_inscricoes_abertas( $inscricao );

        if ( $inscricoes_abertas && $inscricao->sorteado == '0' && !$divulgar_resultado ) {
            return 'aguardando_sorteio';
        }
        
        /* Não foi sorteado */
        if ( !$inscricoes_abertas && $inscricao->sorteado == '0' && $divulgar_resultado ) {
            return 'nao_sorteado';
        }

        return 'aguardando_sorteio';
    }

function get_status_participacao( object $inscricao ) {

    if ( $inscricao->tipo === 'sorteio' && !boolval( $inscricao->sorteado ) ) {
        return '-';
    }

    $precisa_confirmar = get_field( 'confirm_presen', $inscricao->post_id );

    if ( !$precisa_confirmar ) {
        return '<span data-toggle="tooltip" title="Não é necessário confirmar presença.">N/A</span>';
    }

    if ( empty( $inscricao->prazo_confirmacao ) ) {
        return '-';
    }

    $prazo = new DateTime( $inscricao->prazo_confirmacao, new DateTimeZone( 'America/Sao_Paulo' ) );
    $agora = new DateTime('now', new DateTimeZone( 'America/Sao_Paulo' ) );

    if ( $inscricao->confirmou_presenca == 1 ) {
        return '
            <strong class="presenca-confirmada" data-toggle="tooltip" title="Sua presença foi confirmada.">
                Confirmada
            </strong>
        ';
    }

    if ( $inscricao->confirmou_presenca == 2 ) {
        return '
            <strong class="text-secondary" data-toggle="tooltip" title="Você cancelou sua participação.">
                Cancelou participação
            </strong>
        ';
    }

    if ( $prazo < $agora ) {
        return '
            <strong class="prazo-expirado" data-toggle="tooltip" title="O prazo para confirmar presença já foi encerrado.">
                Prazo expirado
            </strong>
        ';
    }

    return '-';
}

function get_envio_instrucoes_inscricao( object $inscricao ) {

    if ( $inscricao->tem_historico ) {
        return ' 
            <span class="ver-email-instrucao" data-inscricao="'.$inscricao->id.'" style="cursor: pointer;">
                Ver Instruções <i class="fa fa-eye fa-lg text-secondary"></i>
            </span>
        ';
    }

    return '-';
}

function check_comparecimento_evento( object $inscricao ) {

    $historico_participante = new Historico_Participacoes();    
    $user_cpf = get_user_cpf();
    $sancao_ativa = $historico_participante->check_sancao_ativa_participante( $user_cpf );

    if ( isset( $sancao_ativa['id_inscricao'] ) && $sancao_ativa['id_inscricao'] == $inscricao->id ) {
        $data_formatada = date( 'd/m/Y', strtotime( $sancao_ativa['data_validade'] ) );
        return '
            <span class="text-danger" data-toggle="tooltip" title="Você está temporariamente impedido de realizar novas inscrições devido à sua ausência. Você poderá se inscrever novamente a partir de ' . $data_formatada . '">
                &#9888; Bloqueado por falta
            </span>
        ';
    }

    if ( $inscricao->compareceu == 0 ) {
        return '<span class="text-danger">&#9888; Faltou</span>';
    }
    
    return '-';
}

function get_user_cpf() {

    $user_id = get_current_user_id();
    $perfil = get_perfil_usuario_logado();

    if( $perfil === 'parceiro' ) {
        return null;
    }

    $cpf = get_field( 'cpf', 'user_' . $user_id );
    
    return preg_replace( '/[^0-9]/', '', $cpf );
}

function check_necessidade_confirmacao( object $inscricao ) {

    $precisa_confirmar = get_field( 'confirm_presen', $inscricao->post_id );

    if (!$precisa_confirmar) {
        return false;
    }

    if ( $inscricao->tipo === 'sorteio' && !boolval( $inscricao->sorteado ) ) {
        return false;
    }

    if ( intval( $inscricao->confirmou_presenca ) !== 0 ) {
        return false;
    }

    if ( empty( $inscricao->prazo_confirmacao ) ) {
        return false;
    }

    $prazo = new DateTime( $inscricao->prazo_confirmacao, new DateTimeZone('America/Sao_Paulo') );
    $agora = new DateTime( 'now', new DateTimeZone( 'America/Sao_Paulo' ) );

    return $prazo > $agora;
}

function get_filtros_minhas_inscricoes(): array {

    $filtros = [];

    $modalidades_validas = get_valores_filtro_inscricoes('modalidade');

    $modalidade = isset($_GET['modalidade'])
        ? sanitize_text_field($_GET['modalidade'])
        : '';

    if (
        !empty($modalidade)
        && array_key_exists($modalidade, $modalidades_validas)
    ) {

        $filtros['modalidade'] = $modalidade;

    }

    $evento = isset($_GET['evento'])
        ? sanitize_text_field($_GET['evento'])
        : '';

    if (!empty($evento)) {

        $filtros['evento'] = $evento;

    }

    $acoes_validas = get_valores_filtro_inscricoes('acoes_pendentes');

    $acao = isset($_GET['acoes'])
        ? sanitize_text_field($_GET['acoes'])
        : '';

    if (
        !empty($acao)
        && array_key_exists($acao, $acoes_validas)
    ) {

        $filtros['acoes'] = $acao;

    }

    $participacoes_validas = get_valores_filtro_inscricoes('minha_participacao');

    $participacao = isset($_GET['participacao'])
        ? sanitize_text_field($_GET['participacao'])
        : '';

    if (
        !empty($participacao)
        && array_key_exists($participacao, $participacoes_validas)
    ) {

        $filtros['participacao'] = $participacao;

    }

    $resultados_validos = get_valores_filtro_inscricoes('resultado_inscricao');

    $resultado = isset($_GET['resultado'])
        ? sanitize_text_field($_GET['resultado'])
        : '';

    if (
        !empty($resultado)
        && array_key_exists($resultado, $resultados_validos)
    ) {

        $filtros['resultado'] = $resultado;

    }

    $local = isset( $_GET['local-evento'] ) ? absint( $_GET['local-evento'] ) : 0;

    if ( !empty( $local ) ) {
        $filtros['local'] = $local;
    }

    return $filtros;
}
