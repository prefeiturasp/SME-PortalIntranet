<?php extract( $args ); ?>

<?php
if ( !empty( $historico_envios ) ) :
    foreach ( array_values( $historico_envios ) as $key => $envio ) :
        $usuario = get_userdata( $envio['user_id'] );
        $data_envio = new DateTime( $envio['data_envio'] );
        ?>
        <div
            class="accordion accordion-sorteio"
            id="historico-envios-wrapper"
            data-post="<?php echo esc_html( $post_id ); ?>"
            >
            <div class="accordion-card">
                <div class="card-title p-2" id="historico-<?php echo esc_html( $envio['envio_id'] ) ?>">
                    <div class="mb-0">
                        <div class="accordion-toggle <?php echo esc_html( $key != 0 ? 'collapsed' : '' ); ?> d-flex justify-content-between align-items-center"
                            data-toggle="collapse"
                            data-target="#lista-destinatarios-envio-<?php echo esc_html( $envio['envio_id'] ); ?>"
                            aria-expanded="<?php echo esc_html( $key != 0 ? 'false' : 'true' ); ?>"
                            aria-controls="collapseOne">
                            <span class="text-white">
                                <?php echo esc_html( "E-mail de Instruções enviado {$data_envio->format('d/m/Y \à\s\ H:i:s')} por {$usuario->display_name}" ); ?>
                            </span>
                            <span class="accordion-icon dashicons dashicons-controls-play ml-2"></span>
                        </div>
                    </div>
                </div>

                <div
                    id="lista-destinatarios-envio-<?php echo esc_html( $envio['envio_id'] ); ?>"
                    class="collapse <?php echo esc_html( $key == 0 ? 'show' : '' ); ?>"
                    aria-labelledby="headingOne"
                    data-parent="#lista-envios"
                    >
                    <div class="card-body">
                        <div class="conteudo-lista">
                            <div class="row">
                                <div class="col mb-3 d-flex align-items-center">
                                    <h6>Listagem de participantes</h6>
                                    <button class="btn btn-laranja ml-auto" data-toggle="modal" data-target="#modal-envio-<?php echo esc_html( $envio['envio_id'] ); ?>">
                                        Ver E-mail de Instrução
                                    </button>                           
                                </div>
                            </div>
                            <div>
                                <div class="table-responsive">
                                    <table class="table tabela-lista-sorteados">
                                        <thead>
                                        <tr>
                                            <th style="width:25%">Nome Completo</th>
                                            <th>E-mails</th>
                                            <th>Telefones</th>
                                            <th>CPF</th>
                                            <th>DRE/SME - UE/Setor</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ( $envio['destinatarios'] as $destinatario ) : ?>
                                                <tr class="sorteado-item">
                                                    <td>
                                                        <span class="nome"><?php echo esc_html( $destinatario['nome_completo'] ); ?></span>

                                                        <?php if ( $destinatario['tipo_vinculo'] == 1 ) : ?>
                                                            <span class="badge badge-primary">INTRANET - SERVIDOR</span>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ( $destinatario['tipo_vinculo'] == 2 ) : ?>
                                                            <span class="badge badge-success">INTRANET - UE PARCEIRA</span>
                                                        <?php endif; ?>

                                                        <?php if ( $destinatario['tipo_vinculo'] == 3 ) : ?>
                                                            <span class="badge badge-warning">PORTAL - ESTAGIÁRIO</span>
                                                        <?php endif; ?>

                                                    </td>
                                                    <td class="negrito">
                                                        <?php if ( !empty( $destinatario['email_institucional'] ) ) : ?>
                                                            <span id="email-inst-{ID}">
                                                                <?php echo esc_html( $destinatario['email_institucional'] ); ?> 
                                                                <i class="fa fa-files-o copiar-email" style="cursor: pointer;" aria-hidden="true"></i>
                                                            </span>
                                                            
                                                        <?php endif; ?>
                                                        <?php if ( !empty( $destinatario['email_secundario'] ) ) : ?>
                                                            <br>
                                                            <span id="email-sec-{ID}">
                                                                <?php echo esc_html( $destinatario['email_secundario'] ); ?> 
                                                                <i class="fa fa-files-o copiar-email" style="cursor: pointer;" aria-hidden="true"></i>
                                                        </span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td class="negrito">
                                                        <?php echo esc_html( $destinatario['celular'] ); ?>
                                                        <br>
                                                        <?php echo esc_html( $destinatario['telefone_comercial'] ); ?>
                                                    </td>
                                                    <td class="negrito"><?php echo esc_html( $destinatario['cpf'] ); ?></td>
                                                    <td class="negrito">
                                                        <?php echo esc_html( $destinatario['dre'] ); ?>
                                                        <br>
                                                        <?php echo esc_html( $destinatario['unidade_setor'] ); ?>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal com as informações do envio -->
        <div
            class="modal fade"
            id="modal-envio-<?php echo esc_html( $envio['envio_id'] ); ?>"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
            >
            <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">Detalhes do e-mail de instrução</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="content-header">
                            <div class="row">
                                <div class="col">
                                    <span class="text-secondary">Remetente</span>
                                    <p><?php echo esc_html( $usuario->display_name ); ?></p>
                                </div>
                                <div class="col">
                                    <span class="text-secondary">Data/Hora</span>
                                    <p><?php echo esc_html( $data_envio->format('d/m/Y \à\s\ H:i:s') ); ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="content-body">
                            <?php echo wp_kses_post( $envio['mensagem'] ); ?>
                        </div>

                        <?php
                        if ( isset( $envio['anexo'] ) && !empty( $envio['anexo'] ) ) :
                            $upload_dir = wp_upload_dir();
                            $url_arquivo = str_replace( $upload_dir['basedir'], $upload_dir['baseurl'], $envio['anexo'] );
                            ?>
                            <div class="content-attachment d-flex border-top">
                                <a href="<?php echo esc_url( $url_arquivo ); ?>" class="ml-auto mt-3" target="_blank">
                                    <strong><i class="fa fa-paperclip" aria-hidden="true"></i> Baixar anexo</strong>
                                </a>
                            </div>
                            <?php
                        endif;
                        ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-laranja btn-copiar-conteudo"><i class="fa fa-files-o" aria-hidden="true"></i> Copiar dados</button>
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endforeach;
endif;
?>

<?php if ( empty( $historico_envios ) ) : ?>
    <?php if ( $envios_sem_historico ) : ?>
        <div class="alert alert-warning text-center m-5" role="alert">
            <h6><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> Já existem envios para o evento selecionado, mas não há registro no histórico.</h6>
        </div>
    <?php else : ?>
        <div class="alert alert-primary text-center m-5" role="alert">
            <h6>Nenhum e-mail de instrução foi enviado para este evento.</h6>
        </div>
    <?php endif; ?>
<?php endif; ?>


<script>
    //Script para cópia do e-mail do destinatário e cópia do conteúdo do envio
    jQuery(function ($) {
        $('.btn-copiar-conteudo').on('click', function () {
            const conteudo = $(this).closest('.modal-content').find('.content-body').text().trim();
            navigator.clipboard.writeText( conteudo );
            toastr["success"]("Conteúdo copiado com sucesso.")
        })

        $('.copiar-email').on('click', function () {
            const conteudo = $(this).parent('span').text().trim();
            navigator.clipboard.writeText( conteudo );
            toastr["success"]("E-mail copiado com sucesso.")
        })
    })
</script>