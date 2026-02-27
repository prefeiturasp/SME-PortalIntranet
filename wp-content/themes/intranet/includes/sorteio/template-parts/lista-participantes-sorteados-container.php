<?php extract( $args ); ?>

<div class="accordion-sorteio" id="lista-participantes-sorteados" data-post="<?php echo esc_html( get_the_ID() ); ?>">

    <?php
    $i = 10;
    $premio = '';
    foreach ( $datas as $item ) :

        if ($tipo_evento == 'premio') {
            $label_collapse = 'Sorteados do prêmio: ' . $item['premio'];
            $premio = $item['premio'];
        } elseif ( $tipo_evento == 'periodo' ) {

            $info_periodo_evento = get_field( 'evento_periodo', $post_id );
            $data_sorteio = DateTime::createFromFormat( 'd/m/Y',  $info_periodo_evento['data_sorteio'] );
            $data_sorteio->setTime(0, 0, 0);
            $data_acord = $data_sorteio->format( 'dmYHi' );
            $item['data'] = $data_sorteio->format( 'Y-m-d H:i:s' );
            $label_collapse = "Sorteados do Período: <strong>{$info_periodo_evento['descricao']}</strong>";

        } else {
            $label_collapse = date( 'd/m/Y H\hi', strtotime( $item['data'] ) );
            $label_collapse = 'Sorteados para a <strong>sessão de ' . str_replace( 'h00', 'h', $label_collapse ) . '</strong>';
            $data_acord = date( 'dmYHi', strtotime( $item['data'] ) );
        }
        
        $current_user = wp_get_current_user();

        // ID do usuário
        $user_id = $current_user->ID;        
        $display_name = $current_user->display_name;
        $responsavel = "{$display_name} (ID: {$user_id})";

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
                    <?php monta_lista_sorteados_por_data( $post_id, $item['data'], $unica, $sancao, $participante, $responsavel, $premio ); ?>
                </div>
            </div>
        </div>
        <?php
        $i++;
    endforeach;
    ?>

</div>