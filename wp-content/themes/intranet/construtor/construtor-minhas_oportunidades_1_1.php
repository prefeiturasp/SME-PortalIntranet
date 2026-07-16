<?php

wp_localize_script('scripts_js', 'ajax_obj', [
    'ajax_url' => admin_url('admin-ajax.php'),
    'nonces'    => [
        'visualizar_envio' => wp_create_nonce( 'visualizar_envio' ),
        'confirmar_participacao' => wp_create_nonce( 'confirmar_participacao' )
    ]
]);

$oportunidade_destaque = absint( $_GET['highlight'] ) ?? null;

$inscricoes = Inscricao::get_inscricoes_by_user_id( $oportunidade_destaque );
$etapas_processo = Inscricao::get_etapas_processo();
$info_cadidato = Inscricao::obter_curriculo_usuario( get_current_user_id() );

?>
<div class="container mt-4">
    <div class="row">
        <?php if ( !$inscricoes ) : ?>
            <div class="alert alert-primary text-center w-100 mb-5" role="alert">
                Você ainda não se inscreveu para nenhuma oportunidade.
            </div>
        <?php endif; ?>
        
        <div class="container mb-5 d-none" id="sem-resultado">
            <div class="alerta-sem-oportunidades">
                <div class="alerta-info">
                    <i class="fa fa-search fa-3x mb-3" aria-hidden="true"></i>
                    <p>Nenhuma Oportunidade encontrada para os filtros selecionados.</p>
                </div>
            </div>
        </div>
        
        <?php if ( $inscricoes ) : ?>
            <div class="col-sm-12 mb-4 tabela-scroll" id="minhas-candidaturas">
                <table id="tabela-minhas-oportunidades" class="table table-striped">
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
                        foreach ( $inscricoes as $inscricao ) {
                            get_template_part( 'includes/oportunidades/template-parts/minhas-oportunidades/linha-inscricao', null, [
                                'inscricao' => $inscricao,
                                'etapas_processo' => $etapas_processo,
                                'oportunidade_destaque' => $oportunidade_destaque
                            ]);
                        }
                        ?>
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

                <!-- MODAL DE CONFIRMAÇÕES -->
                <div class="modal fade" id="modal-confirmacao" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content border-0 shadow">

                            <div class="modal-header d-flex">
                                <h2 class="modal-title fw-semibold">
                                    Confirmação de Participação no Processo Seletivo
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
                                    <p>Você está participando do processo seletivo da oportunidade:</p>
                                </div>

                                <div class="alert bg-light border rounded js-titulo" role="alert"></div>

                                <div class="mb-3">
                                    <p>Sua candidatura recebeu uma atualização e o Gestor responsável solicita que você confirme seu interesse em continuar participando do processo seletivo.</p>
                                </div>

                                <div class="mb-3 prazo-confirmacao">
                                    <span>Prazo para confirmação: </span>
                                    <span class="js-prazo-confirmacao"></span>
                                </div>

                                <div class="alert alert-warning mb-3">
                                    <strong><i class="fa fa-exclamation-triangle mr-2" aria-hidden="true"></i> Importante</strong>
                                    <p class="my-1">Após o encerramento do prazo, a confirmação não poderá mais ser realizada e sua participação nesta etapa poderá ser encerrada.</p>
                                    <p class="my-1">Caso não tenha interesse em prosseguir, recomendamos que realize o cancelamento dentro do prazo informado.</p>
                                    <p class="my-1">Sua resposta ajuda a organização do processo seletivo e contribui para uma gestão mais eficiente das oportunidades.</p>
                                </div>

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
                            </div>

                            <div class="modal-footer">
                            <button class="btn btn-outline-danger btn-cancelar-interesse-etapa">
                                    Cancelar Participação
                                </button>
                                <button class="btn btn-primary btn-confirmar-interesse-etapa">
                                    Confirmar Participação
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>
    </div>
</div>

<?php if ( $oportunidade_destaque ) : ?>
    <script>
        jQuery(function($) {

            const $tabela = $('#tabela-minhas-oportunidades');

            if ($tabela.length) {
                $('html, body').animate({
                    scrollTop: $tabela.offset().top - 400
                }, 800);

            }
        });
    </script>
<?php endif; ?>