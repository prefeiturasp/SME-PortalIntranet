<?php 
    extract( $args );
    $agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
?>

<?php if ( !$eventos ) : ?>
    <div id='lista-envios'>
        <h6 class='p-5 text-center'>Para visualizar o histórico dos participantes, informe o CPF, e-mail ou telefone celular.</h6>
    </div>
<?php endif; ?>

<?php if ( $eventos ) : ?>
    <div class="form-group mt-3 filtro-eventos-participante">
        <div class="row align-items-center">

            <div class="col-3 mb-3">
                <label for="foi-sorteado" class="fw-bold">Foi sorteado</label>
                <select id="foi-sorteado" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                    <option value="N/A">N/A</option>
                </select>
            </div>

            <div class="col-3 mb-3">
                <label for="modalidade" class="fw-bold">Modalidade</label>
                <select id="modalidade" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Sorteio">Sorteio</option>
                    <option value="Ordem de Inscrição">Ordem de Inscrição</option>
                </select>
            </div>

            <div class="col-3 mb-3">
                <label for="evento-input" class="fw-bold">Filtrar por evento</label>
                <input type="text" id="evento-input" class="form-control" placeholder="Digite o nome do evento">
            </div>

            <div class="col-3 mb-3">
                <label for="presenca" class="fw-bold">Confirmou Presença</label>
                <select id="presenca" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Confirmada">Confirmada</option>
                    <option value="Cancelou Participação">Cancelou Participação</option>
                    <option value="Prazo Expirado">Prazo Expirado</option>
                    <option value="Aguardando">Aguardando</option>
                    <option value="Não Requer">Não Requer</option>
                </select>
            </div>

            <div class="col-3 mb-3">
                <label for="instrucoes" class="fw-bold">Instruções Enviadas</label>
                <select id="instrucoes" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                </select>
            </div>

            <div class="col-3 mb-3">
                <label for="contato" class="fw-bold">Contato Extra</label>
                <select id="contato" class="form-control">
                    <option value="">Selecione</option>
                    <option value="ligacao">Contatado por Telefone</option>
                    <option value="email">Contatado por E-mail</option>
                    <option value="whatsapp">Contatado por WhatsApp</option>
                </select>
            </div>

            <div class="col-3 mb-3">
                <label for="compareceu" class="fw-bold">Compareceu ou Resgatou:</label>
                <select id="compareceu" class="form-control">
                    <option value="">Selecione</option>
                    <option value="Sim">Sim</option>
                    <option value="Não">Não</option>
                    <option value="Bloqueado por Falta">Bloqueado por Falta</option>
                </select>
            </div>

            <div class="col-3 mb-3 align-self-end">
                <button class="btn btn-outline-warning btn-block" id="btn-limpar-filtro">Limpar Filtro</button>
            </div>

        </div>
    </div>
    <div class="row mb-3">
        <div class="col-8 d-flex align-items-end">
            <p class="legenda-tabela m-0">
                <img src="<?= get_template_directory_uri(); ?>/img/icon-telefone.svg" alt="icone Telefone" class="mr-1"> Contatado por telefone
                <img src="<?= get_template_directory_uri(); ?>/img/icon-email.svg" alt="icone Email" class="mr-1 ml-3"> Contatado por e-mail
                <img src="<?= get_template_directory_uri(); ?>/img/icon-whatsapp.svg" alt="icone Whatsapp" class="mr-1 ml-3"> Contatado por WhatsApp
            </p>
        </div>
        <div class="col-4 text-right">
            <button type="button" id="btn-exportar" class="btn btn-laranja">
                <i class="fa fa-file-excel-o" aria-hidden="true"></i> Exportar Excel
            </button>
        </div>
    </div>
    <table id="tabela-eventos" class="historico-participantes widefat striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Evento</th>
                <th>Foi Sorteado?</th>
                <th>Confirmou Presença?</th>
                <th>Instruções Enviadas?</th>
                <th>Contato Extra</th>
                <th>Compareceu ou Resgatou?</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ( $eventos as $evento ): ?>
                <tr data-inscricao="<?php echo esc_html( $evento->id ); ?>" data-tipo="<?php echo esc_html( $evento->tipo ); ?>">
                    <td><?php echo esc_html( $evento->post_id ); ?></td>
                    <td class="nome-evento">
                        <a class="text-dark" href="<?php echo esc_url( get_edit_post_link( $evento->post_id ) ); ?>" target="_blank">
                            <?php echo esc_html( $evento->nome_evento ); ?><br>
                        </a>
                        
                        <?php if ( $evento->tipo == 'cortesia' ) : ?>
                            <span class="post-type-tag cortesia-tag px-2"><i class="fa fa-bolt" aria-hidden="true"></i> Ordem de Inscrição</span>
                        <?php else: ?>
                            <span class="post-type-tag px-2"><i class="fa fa-cube" aria-hidden="true"></i> Sorteio</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php
                            if($evento->tipo == 'sorteio') {
                                echo $evento->sorteado ? 'Sim' : 'Não';
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            $requerConfirmacao = get_post_meta($evento->post_id, 'confirm_presen', true);
                            if( $evento->prazo_confirmacao && ($evento->sorteado || $evento->tipo == 'cortesia') ) {
                                $confirmacaoPresenca = $evento->confirmou_presenca;
                                $data_validar = new DateTime($evento->prazo_confirmacao, new DateTimeZone('America/Sao_Paulo'));                                
                                
                                if($confirmacaoPresenca == '1'){
                                    echo '<span class="dest-azul">Confirmada</span>';
                                } elseif($confirmacaoPresenca == '2'){
                                    echo '<span class="dest-azul">Cancelou participação</span>';
                                } else {			
                                    if($agora > $data_validar){
                                        echo '<span class="dest-vermelho">Prazo expirado</span>';
                                    } else {
                                        echo '<span class="dest-azul">Aguardando</span>';
                                    }
                                }
                            } elseif (!$requerConfirmacao) {
                                echo '<span class="dest-vermelho">Não requer</span>';
                            } else {
                                echo '<span class="dest-azul">Aguardando</span>';
                            }
                        ?>
                    </td>
                    <td>
                        <?php
                            if($evento->sorteado || $evento->tipo == 'cortesia') {
                                if($evento->enviou_email_instrucoes) {
                                    if($evento->tem_historico) {
                                        echo 'Sim <button data-toggle="tooltip" data-placement="right" title="Ver instruções" class="ver-email-instrucao btn btn-sm btn-link" data-inscricao="'.$evento->id.'"><i class="fa fa-eye fa-lg"></i></button>';
                                    } else {
                                        echo '<span data-toggle="tooltip" data-placement="right" title="Sem registro no histórico">
                                                Sim 
                                                <button class="btn btn-sm btn-link" disabled>
                                                    <i class="fa fa-eye-slash fa-lg" aria-hidden="true"></i>
                                                </button>
                                            </span>';
                                    }
                                } else {
                                    echo '<span data-toggle="tooltip" data-placement="right" title="Instruções pendentes">Não ⚠️</span>';
                                }
                            } else {
                                echo '-';
                            }
                        ?>
                        </td>
                    <td class="tipos-contato">
                        <?php 
                            if($evento->sorteado || $evento->tipo == 'cortesia') {
                                switch ($evento->tipo_contato) {
                                    case '1':
                                        echo '<span data-togle="tooltip" data-filtro="ligacao" data-placement="right" title="Contato por ligação"><img src="' . get_template_directory_uri() . '/img/icon-telefone.svg" alt="icone Telefone"></span>';
                                        //echo '<span data-toggle="tooltip" data-placement="right" title="Contato por ligação">Contato por telefone</span>';
                                        break;
                                    case '2':
                                        echo '<span data-toggle="tooltip" data-filtro="email" data-placement="right" title="Contato por E-mail"><img src="' . get_template_directory_uri() . '/img/icon-email.svg" alt="icone Email"></span>';
                                        //echo '<span data-toggle="tooltip" data-placement="right" title="Contato por E-mail">Contato por e-mail</span>';
                                        break;
                                    case '3':
                                        echo '<span data-toggle="tooltip" data-filtro="whatsapp" data-placement="right" title="Contato por WhatsApp"><img src="' . get_template_directory_uri() . '/img/icon-whatsapp.svg" alt="icone Whatsapp"></span>';
                                        //echo '<span data-toggle="tooltip" data-placement="right" title="Contato por WhatsApp">Contato por WhatsApp</span>';
                                        break;
                                    default:
                                        echo '-';
                                }
                            } else {
                                echo '-';
                            }
                        ?>
                    </td>
                    <td><?php
                        if($evento->sorteado || $evento->tipo == 'cortesia') {
                            if ($evento->compareceu) {
                                echo 'Sim';
                            } else if (is_array($dados) && $dados['id_inscricao'] == $evento->id) {
                                echo 'Bloqueado por Falta';
                            } else {
                                echo '<span data-toggle="tooltip" data-placement="right" title="Nesta notícia foi registrada a sanção por ausência ou resgate do prêmio.">Não ⛔</span>';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="modal fade" id="modalEmailInstrucao" tabindex="-1" role="dialog">
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

                        <div class="col-md-3">
                            <h6><strong>Participante</strong></h6>

                            <p id="email-participante-nome" class="mb-0"></p>
                            <p id="email-participante-email1" class="mb-0"></p>
                            <p id="email-participante-email2" class="mb-0"></p>
                        </div>

                        <div class="col-md-3">
                            <h6><strong>Evento</strong></h6>

                            <p id="email-evento" class="mb-0"></p>
                        </div>

                        <div class="col-md-3">
                            <h6><strong>Administrador responsável</strong></h6>

                            <p id="email-admin" class="mb-0"></p>
                        </div>

                        <div class="col-md-3">
                            <h6><strong>Data/Hora</strong></h6>

                            <p id="email-data" class="mb-0"></p>
                        </div>

                    </div>

                    <div class="row mb-3">
                        <div class="col-md-12">

                            <h6>
                                <strong>Descrição do e-mail de instrução</strong>
                            </h6>

                            <div id="email-mensagem" class="border p-3 bg-light"></div>

                        </div>
                    </div>

                </div>

            </div>

            <div class="modal-footer">

                <button class="btn btn-outline-warning" id="copiar-dados-email">
                    <i class="fa fa-clone" aria-hidden="true"></i> Copiar dados
                </button>

                <button class="btn btn-laranja" data-dismiss="modal">
                    Fechar
                </button>

            </div>

        </div>

    </div>
</div>