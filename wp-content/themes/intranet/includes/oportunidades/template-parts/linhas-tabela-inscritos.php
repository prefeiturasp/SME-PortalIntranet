<?php

extract( $args );

// Mapeamento dos status (código => [descrição, classe_css])
$status_map = Inscricao::get_etapas_processo();

?>

<?php
foreach ( $participantes as $participante ) :

    // Pega os dados do status com base no código armazenado no banco
    $status_codigo = $participante['status']; // Ex: 'analise_curricular'
    $status_info = isset($status_map[$status_codigo]) ? $status_map[$status_codigo] : $status_map['inscrito'];
    $status_descricao = $status_info['descricao'];
    $status_classe = $status_info['classe'];
    $status_atual = $participante['status']; 

    $permite_comunicacao = Inscricao::permite_comunicacao($status_codigo);
    $permite_desbloqueio = Inscricao::permite_desbloqueio($status_codigo);

    // Verifica se o checkbox deve ser desabilitado
    $checkbox_disabled = $permite_desbloqueio ? 'disabled' : '';

    ?>
    <tr data-inscricao-id="<?php echo esc_attr($participante['id']); ?>">
        <td class="text-center">
            <input type="checkbox" class="check-sorteados check-item" name="participantes-sorteados[]" value="<?php echo esc_attr($participante['id']); ?>" <?php echo $checkbox_disabled; ?>>
        </td>
        <td>
            <?php if ($participante['nome_social']): ?>
                <span class="nome-candidato"><?php echo esc_html($participante['nome_social']); ?> <br><small>(<?php echo esc_html($participante['nome_completo']); ?>)</small></span><br>
            <?php else: ?>
                <span class="nome-candidato"><?php echo esc_html($participante['nome_completo']); ?></span><br>
            <?php endif; ?>
            <span class="card-etapa data-inscricao">
                Inscrição Recebida<br>
                <span class="data-etapa"><?php echo date('d/m/Y \à\s H:i', strtotime($participante['created_at'])); ?></span>
            </span>
        </td>
        <td data-situacao="<?php echo esc_attr($status_descricao); ?>">

            <?php
                $classe = '';
                $prazo_confirmacao = strtotime($participante['prazo_confirmacao']);
                $agora = current_time('timestamp');
                if($prazo_confirmacao <= $agora && $participante['confirmou_presenca'] == 0 && $participante['prazo_confirmacao'] && $participante['status_confirm'] == $status_atual){
                    $classe = 'expirado';
                }
            ?>

            <span class="card-etapa <?= esc_attr($status_classe) . ' ' . esc_attr($classe); ?>">
                <?php echo esc_html($status_descricao); ?><br>
                <?php if ($status_classe != 'inscrito'): ?>
                    <span class="data-etapa"><?php echo date('d/m/Y \à\s H:i', strtotime($participante['updated_at'])); ?></span>
                <?php endif; ?>
            </span>
            <?php if ($permite_comunicacao) : ?>
                <br><br>
                <span class="notificacao-confirmacao">
                    <?php
                        if ( $participante['prazo_confirmacao'] && $participante['status_confirm'] == $status_atual ) {

                            echo 'Notificado: sim';

                            // Prazo ainda válido e ainda não respondeu
                            if ( 
                                $participante['confirmou_presenca'] == 0 
                                && $prazo_confirmacao > $agora
                            ) {

                                echo ' - Expira: <span class="data-etapa">' 
                                    . date('d/m/Y \à\s H:i', $prazo_confirmacao) 
                                    . '</span>';

                                echo '<br>
                                <span class="confirmar-interesse mt-2 badge badge-warning">
                                    Confirmou interesse: A confirmar
                                </span>';


                            // Prazo expirado e ainda não respondeu
                            } elseif ( 
                                $participante['confirmou_presenca'] == 0 
                                && $prazo_confirmacao <= $agora
                            ) {

                                echo '<br>
                                <span class="confirmar-interesse mt-2 badge badge-secondary">
                                    Confirmou interesse: Prazo expirado
                                </span>';


                            // Confirmou presença
                            } elseif ( $participante['confirmou_presenca'] == 1 ) {

                                echo '<br>
                                <span class="confirmar-interesse badge badge-success">
                                    Confirmou interesse: Sim
                                </span>';


                            // Não confirmou presença
                            } elseif ( $participante['confirmou_presenca'] == 2 ) {

                                echo '<br>
                                <span class="confirmar-interesse badge badge-danger">
                                    Confirmou interesse: Não
                                </span>';

                            }


                        } else {

                            echo 'Notificado: não';

                        }
                    ?>
                </span>                
                
            <?php endif; ?>
        </td>
        <td class="text-nowrap"><span class="copiar-texto" data-texto="<?php echo esc_html($participante['rf']); ?>" data-toggle="tooltip" title="Clique para copiar a informação"><?php echo esc_html($participante['rf']); ?> <img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio"></span></td>
        <td class="text-nowrap"><span class="copiar-texto" data-texto="<?php echo esc_html($participante['email_principal']); ?>" data-toggle="tooltip" title="Clique para copiar a informação"><?php echo esc_html($participante['email_principal']); ?> <img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio"></span></td>
        <td class="text-nowrap"><span class="copiar-texto" data-texto="<?php echo esc_html($participante['telefone_whatsapp']); ?>" data-toggle="tooltip" title="Clique para copiar a informação"><span class="celular-mask" data-texto="<?php echo esc_html($participante['telefone_whatsapp']); ?>"><?php echo esc_html($participante['telefone_whatsapp']); ?></span> <img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio"></span></td>
        <td><a href="#"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver CV</a></td>
        <td class="text-center">
            <?php
                
                // Verifica qual botão mostrar
                if ($permite_desbloqueio) {
                    echo '<button type="button" class="btn btn-voltar-status" data-placement="top" title="Desfazer alteração de etapa" data-toggle="tooltip" data-id="' . esc_attr($participante['id']) . '" data-status="' . esc_attr($status_atual) . '">';
                    echo '<i class="fa fa-repeat" aria-hidden="true"></i>';
                    echo '</button>';
                } elseif ($permite_comunicacao) {

                    if ( $participante['prazo_confirmacao'] && $participante['status_confirm'] ==  $status_atual ) { 

                        if ( $participante['confirmou_presenca'] == 0 ) {
                            echo '<button type="button" data-toggle="tooltip" data-placement="top" title="Enviar comunicação ao candidato." class="btn btn-success btn-comunicar-selecionados" data-id="' . esc_attr($participante['id']) . '" data-status="' . esc_attr($status_atual) . '">';
                            echo '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
                            echo '</button>';
                        } else {
                            echo '<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="A comunicação para esta etapa já foi enviada." disabled>';
                            echo '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
                            echo '</button>';
                        }
                        
                    } else {
                        echo '<button type="button" data-toggle="tooltip" data-placement="top" title="Enviar comunicação ao candidato." class="btn btn-success btn-comunicar-selecionados" data-id="' . esc_attr($participante['id']) . '" data-status="' . esc_attr($status_atual) . '">';
                        echo '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
                        echo '</button>';
                    }
                    
                } else {
                    if($status_atual != 'inscrito'){
                        echo '<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="A comunicação para esta etapa já foi enviada." disabled>';
                        echo '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
                        echo '</button>';
                    } else {
                        echo '—';
                    }                    
                    
                }
            ?>
        </td>
    </tr>
    <?php
endforeach;
?>