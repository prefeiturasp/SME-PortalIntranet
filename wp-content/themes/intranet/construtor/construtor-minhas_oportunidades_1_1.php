<?php

wp_localize_script('scripts_js', 'ajax_obj', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonces'    => [
        'visualizar_envio' => wp_create_nonce( 'visualizar_envio' )
    ]
]);


$filtros = [];

$inscricoes = Inscricao::get_inscricoes_by_user_id();
$etapas_processo = Inscricao::get_etapas_processo();
$info_cadidato = Inscricao::obter_curriculo_usuario( get_current_user_id() );

?>
<div class="container mt-4">
    <div class="row">
        <?php if ( !$inscricoes && !$filtros ) : ?>
            <div class="alert alert-primary text-center w-100 mb-5" role="alert">
                Você ainda não tem inscrições realizadas.
            </div>
        <?php endif; ?>
        
        <?php if ( !$inscricoes && $filtros ) : ?>
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
        
        <?php if ( $inscricoes ) : ?>
            <div class="col-sm-12 mb-4 tabela-scroll" id="minhas-candidaturas">
                <table id="tabela-inscricoes-participante" class="table table-striped">
                    <thead>
                        <tr>
                            <th>OPORTUNIDADE</th>
                            <th>LOCAL DE TRABALHO</th>
                            <th>ETAPA DO PROCESSO SELETIVO</th>
                            <th>MINHAS MENSAGENS</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ( $inscricoes as $inscricao ) :
                            $etapa = $etapas_processo[$inscricao->status];
                            ?>
                            <tr data-data-inscricao="<?php echo date( 'd/m/Y \à\s H:i', strtotime( $inscricao->created_at ) ); ?>">
                                <td id="titulo-oportunidade">
                                    <a href="<?php echo esc_url( get_the_permalink( $inscricao->oportunidade_id ) ); ?>" target="_blank">
                                        <?php echo esc_html( get_the_title( $inscricao->oportunidade_id ) ); ?>
                                    </a>
                                    <?php if ( $tipos_oportindade = get_field( 'tipo_oportunidade', $inscricao->oportunidade_id ) ) : ?>
                                        <div class="subtitulo-oportunidade">
                                            <?php foreach ( $tipos_oportindade as $tipo ) : ?>
                                                <p><?php echo esc_html( $tipo['label'] ); ?></p>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td id="local-trabalho">
                                    <?php
                                    $local_id = get_field( 'local_trabalho', $inscricao->oportunidade_id );
                                    $local_trabalho = get_term_by( 'term_id', $local_id, 'locais' );
                                    $endereco_trabalho = !empty( $local_trabalho->description ) ? $local_trabalho->description : get_field( 'endereco_trabalho', $inscricao->oportunidade_id );

                                    echo esc_html( $endereco_trabalho );
                                    ?>
                                </td>
                                <td id="etapa-processo-seletivo">
                                    <span class="badge-oportunidade <?php echo esc_html( $etapa['classe'] ); ?>">
                                        <i class="fa fa-circle" aria-hidden="true"></i>
                                        <?php echo esc_html( $inscricao->status === 'inscrito' ? 'Inscrição Realizada' : $etapa['descricao'] ); ?>

                                        <?php if ( $inscricao->prazo_confirmacao && $inscricao->status === $inscricao->status_confirm ) : ?>
                                            <?php
                                            if ( $inscricao->confirmou_presenca == '1' ) {
                                                echo esc_html( '- Confirmou' );
                                            } elseif ( $inscricao->confirmou_presenca == '2' ) {
                                                echo esc_html( '- Cancelou' );
                                            } else {
                                                $agora = current_time( 'timestamp' );
                                                $prazo_confirmacao = strtotime( $inscricao->prazo_confirmacao );

                                                if ( $agora > $prazo_confirmacao ) {
                                                    echo esc_html( '- Prazo Expirado' );
                                                }
                                            }
                                            ?>
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td id="minhas-mensagens" class="text-center">
                                    <?php if ( $inscricao->comunicado_public_id ) : ?>
                                        <button
                                            class="btn btn-outline-primary btn-visualizar-comunicado"
                                            data-id="<?php echo esc_attr( $inscricao->comunicado_public_id ); ?>"
                                            >
                                            <i class="fa fa-eye" aria-hidden="true"></i> Ver
                                        </button>
                                    <?php else : ?>
                                        <span class="text-secondary">
                                            <i class="fa fa-eye-slash" aria-hidden="true"></i> Sem mensagem
                                        </span>
                                    <?php endif; ?>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <!-- MODAL DE COMUNICAÇÕES/ATUALIZAÇÕES ETAPA -->
                <div class="modal fade" id="modal-comunicado" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow">

                            <div class="modal-header d-flex">
                                <h2 class="modal-title fw-semibold">
                                    Detalhes do e-mail recebido
                                </h2>
                                
                                <div class="modal-close ml-auto" data-dismiss="modal">
                                    <i class="fa fa-times" aria-hidden="true"></i>
                                </div>

                                <hr>
                            </div>

                            <div class="modal-body">
                                <div class="row g-3 mb-4">
                                    <div class="col-md-6 d-flex flex-column justify-content-between">
                                        <div class="envio-info mb-2">
                                            <small class="text-muted d-block">
                                                Candidato
                                            </small>
                                            <?php if ( $info_cadidato->nome_social ) : ?>
                                                <span>
                                                    <?php echo esc_html( $info_cadidato->nome_social ); ?>
                                                    <br>
                                                    <small>(<?php echo esc_html( $info_cadidato->nome_completo ); ?>)</small>
                                                </span>
                                            <?php else: ?>
                                                <span class="nome-candidato">
                                                    <?php echo esc_html( $info_cadidato->nome_completo ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="envio-info">
                                            <small class="text-muted d-block">
                                                Data/Hora da inscrição
                                            </small>
                                            <div class="fw-semibold js-data-inscricao"></div>
                                        </div>
                                        
                                    </div>

                                    <div class="col-md-6 d-flex flex-column justify-content-between">
                                        <div class="envio-info mb-2">
                                            <small class="text-muted d-block">
                                                Remetente
                                            </small>
                                            <div>institucional@sme.prefeitura.sp.gov.br</div>
                                        </div>

                                        <div class="envio-info">
                                            <small class="text-muted d-block">
                                                Data/Hora do Último retorno
                                            </small>
                                            <div class="fw-semibold js-data-envio"></div>
                                        </div>
                                    </div>
                                </div>

                                <hr>
                                <div class="mb-3">
                                    <p>Olá,</p>
                                    <p>Sua candidatura para a oportunidade abaixo recebeu uma atualização:</p>
                                </div>

                                <div class="alert bg-light border rounded js-titulo" role="alert"></div>

                                <div id="info-complementar" class="mb-3 d-none">
                                    <h6 class="text-uppercase text-secondary small mb-3">Orientações Complementares</h6>
                                    <div class="border rounded p-3 bg-light js-mensagem"></div>
                                </div>
                                
                                <div class="js-bloco-anexos d-none">
                                    <hr>
                                    <h6 class="text-uppercase text-secondary small mb-3">Anexos</h6>
                                    <div class="list-group js-anexos mb-3"></div>
                                </div>

                                <div>
                                    <p>Atenciosamente,</p>
                                    <strong>Equipe do Portal de Oportunidades SME</strong>
                                </div>

                                <div class="alert alert-warning mt-4 mb-0">
                                    <strong><i class="fa fa-exclamation-triangle mr-2" aria-hidden="true"></i> Importante</strong>
                                    <p class="m-0">As atualizações ocorrem conforme o andamento do processo seletivo. Fique atento à etapa da sua candidatura.</p>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary" data-dismiss="modal">
                                    Fechar
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>