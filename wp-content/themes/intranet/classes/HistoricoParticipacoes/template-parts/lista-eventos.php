<?php extract( $args ); ?>

<?php if ( !$eventos ) : ?>
    <div id='lista-envios'>
        <h6 class='p-5 text-center'>Para visualizar o histórico dos participantes, informe o CPF.</h6>
    </div>
<?php endif; ?>

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
                    <td><?php echo esc_html( $evento->id ); ?></td>
                    <td class="nome-evento">
                        <?php echo esc_html( $evento->nome_evento ); ?><br>
                        <span class="badge badge-primary px-2"><?php echo esc_html( mb_strtoupper( $evento->tipo ) ); ?></span>
                    </td>
                    <td><?= $evento->sorteado ? 'Sim' : 'Não' ?></td>
                    <td><?= $evento->confirmou_presenca ? 'Sim' : 'Não' ?></td>
                    <td><?php echo $evento->enviou_email_instrucoes ? 'Sim <i class="fa fa-eye fa-lg ver-email-instrucao" aria-hidden="true"></i>' : 'Não'; ?></td>
                    <td><?= esc_html( $evento->contato ?? '-' ) ?></td>
                    <td><?= $evento->compareceu ? 'Sim' : 'Não' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>