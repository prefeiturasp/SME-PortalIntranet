<?php 
    extract( $args );
    $agora = new \DateTime('now', new DateTimeZone('America/Sao_Paulo'));
?>

<?php if ( !$eventos ) : ?>
    <div id='lista-envios'>
        <h6 class='p-5 text-center'>Para visualizar o histórico dos participantes, informe o CPF.</h6>
    </div>
<?php endif; ?>

<?php
    //echo "<pre>";
    //print_r($eventos);
    //echo "</pre>";
?>

<?php if ( $eventos ) : ?>
    <div class="form-group mt-3 filtro-eventos-participante">
        <div class="row justify-content-between align-items-center">
            <div class="col-10">
                <label for="evento-input" class="fw-bold">Filtrar por evento</label>
                <input type="text" id="evento-input" class="form-control">
            </div>

            <div class="col-2 align-self-end">
                <button class="btn btn-outline-warning btn-block" id="btn-limpar-filtro">Limpar Filtro</button>
            </div>

        </div>
    </div>
    <div class="row">
        <div class="col">
            <p class="legenda-tabela">
                <img src="<?= get_template_directory_uri(); ?>/img/icon-telefone.svg" alt="icone Telefone" class="mr-1"> Contatado por telefone
                <img src="<?= get_template_directory_uri(); ?>/img/icon-email.svg" alt="icone Email" class="mr-1 ml-3"> Contatado por e-mail
                <img src="<?= get_template_directory_uri(); ?>/img/icon-whatsapp.svg" alt="icone Whatsapp" class="mr-1 ml-3"> Contatado por WhatsApp
            </p>
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
                        <?php echo esc_html( $evento->nome_evento ); ?><br>
                        <span class="badge badge-primary px-2"><?php echo esc_html( mb_strtoupper( $evento->tipo ) ); ?></span>
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
                            if( $evento->prazo_confirmacao && ($evento->sorteado || $evento->tipo == 'cortesia') ) {
                                $confirmacaoPresenca = $evento->confirmou_presenca;
                                $data_validar = new DateTime($evento->prazo_confirmacao, new DateTimeZone('America/Sao_Paulo'));
                                
                                if($confirmacaoPresenca == '1'){
                                    echo '<span class="dest-azul">SIM</span>';
                                } elseif($confirmacaoPresenca == '2'){
                                    echo '<span class="dest-azul">NÃO, CANCELOU</span>';
                                } else {			
                                    if($agora > $data_validar){
                                        echo '<span class="dest-vermelho">PRAZO EXPIRADO</span>';
                                    } else {
                                        echo '<span class="dest-azul">AINDA NÃO RESPONDEU</span>';
                                    }
                                }
                            } else {
                                echo '-';
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
                    <td>
                        <?php 
                            if($evento->sorteado || $evento->tipo == 'cortesia') {
                                switch ($evento->tipo_contato) {
                                    case '1':
                                        echo '<span data-togle="tooltip" data-placement="right" title="Contato por ligação"><img src="' . get_template_directory_uri() . '/img/icon-telefone.svg" alt="icone Telefone"></span>';
                                        break;
                                    case '2':
                                        echo '<span data-toggle="tooltip" data-placement="right" title="Contato por E-mail"><img src="' . get_template_directory_uri() . '/img/icon-email.svg" alt="icone Email"></span>';
                                        break;
                                    case '3':
                                        echo '<span data-toggle="tooltip" data-placement="right" title="Contato por WhatsApp"><img src="' . get_template_directory_uri() . '/img/icon-whatsapp.svg" alt="icone Whatsapp"></span>';
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
                            echo $evento->compareceu ? 'Sim' : '<span data-toggle="tooltip" data-placement="right" title="Nesta notícia foi registrada a sanção por ausência ou resgate do prêmio.">Não ⛔</span>';
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