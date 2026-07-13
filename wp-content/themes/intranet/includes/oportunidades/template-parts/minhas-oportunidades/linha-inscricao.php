<?php
extract( $args );

$etapa = $etapas_processo[$inscricao->status];
$etapa_classe = $etapa['classe'];

$destacar_linha = isset( $oportunidade_destaque ) && $oportunidade_destaque == $inscricao->oportunidade_id;

?>
<tr
    data-data-inscricao="<?php echo date( 'd/m/Y \à\s H:i', strtotime( $inscricao->created_at ) ); ?>"
    data-inscricao-id="<?php echo esc_html( $inscricao->id ); ?>"
    class="<?php echo esc_html( $destacar_linha ? 'linha-destacada' : 'linha-inscricao' ); ?>"
    >
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

    <?php if ( $inscricao->prazo_confirmacao && $inscricao->status === $inscricao->status_confirm ) : ?>
        <?php
        $exibir_botao_confirmacao = false;
        $sufixo_status = '';

        if ( $inscricao->confirmou_presenca == '1' ) {
           $sufixo_status = '- Confirmou';

        } elseif ( $inscricao->confirmou_presenca == '2' ) {
           $sufixo_status = '- Cancelou';
           $etapa_classe = 'inscricoes-encerradas';

        } else {
            $agora = current_time( 'timestamp' );
            $prazo_confirmacao = strtotime( $inscricao->prazo_confirmacao );

            if ( $agora > $prazo_confirmacao ) {
               $sufixo_status = '- Prazo Expirado';
               $etapa_classe = 'inscricoes-encerradas';
            } else {
                $exibir_botao_confirmacao = true;
            }
        }
        ?>
    <?php endif; ?>

    <td id="etapa-processo-seletivo" class="<?php echo $exibir_botao_confirmacao ? 'd-flex flex-column' : ''; ?>">
        <span class="badge-oportunidade <?php echo esc_html( $etapa_classe ); ?>">
            <i class="fa fa-circle" aria-hidden="true"></i>
            <?php echo esc_html( $inscricao->status === 'inscrito' ? 'Inscrição Realizada' : $etapa['descricao'] ); ?>
            <?php echo esc_html( $sufixo_status ); ?>
        </span>

        <?php if ( isset( $exibir_botao_confirmacao ) && $exibir_botao_confirmacao ) : ?>
            <button
                type="button"
                class="btn btn-outline-info btn-sm btn-visualizar-confirmacao"
                data-id="<?php echo esc_attr( $inscricao->confirmacao_public_id ); ?>"
                >
                <i class="fa fa-check-square-o" aria-hidden="true"></i> Confirmar interesse
            </span>
        <?php endif; ?>
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
