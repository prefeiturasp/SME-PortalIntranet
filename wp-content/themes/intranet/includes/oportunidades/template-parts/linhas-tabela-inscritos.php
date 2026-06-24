<?php

extract( $args );

// Mapeamento dos status (código => [descrição, classe_css])
$status_map = Inscricao::get_etapas_processo();

// Array de etapas que permitem comunicação
$etapas_comunicacao = array(
    'convocado_teste',
    'entrevista_agendada',
    'analise_documental',
    'entrega_documentos'
);

// Array de etapas que permitem desbloqueio
$permite_desbloqueio = array(
    'nao_avancou_triagem',
    'nao_avancou_pos_entrevista',
    'nao_selecionado'
);

// Array de etapas que usam botão desabilitado
$etapas_desabilitado = array(
    'analise_curricular',
    'fase_anuencia',
    'aprovado'
);


?>

<?php
foreach ( $participantes as $participante ) :

    // Pega os dados do status com base no código armazenado no banco
    $status_codigo = $participante['status']; // Ex: 'analise_curricular'
    $status_info = isset($status_map[$status_codigo]) ? $status_map[$status_codigo] : $status_map['inscrito'];
    $status_descricao = $status_info['descricao'];
    $status_classe = $status_info['classe'];

    // Verifica se o checkbox deve ser desabilitado
    $checkbox_disabled = in_array($status_codigo, $permite_desbloqueio) ? 'disabled' : '';

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
            <span class="card-etapa <?php echo esc_attr($status_classe); ?>">
                <?php echo esc_html($status_descricao); ?><br>
                <?php if ($status_classe != 'inscrito'): ?>
                    <span class="data-etapa"><?php echo date('d/m/Y \à\s H:i', strtotime($participante['updated_at'])); ?></span>
                <?php endif; ?>
            </span>
        </td>
        <td class="text-nowrap"><span class="copiar-texto" data-texto="<?php echo esc_html($participante['rf']); ?>" data-toggle="tooltip" title="Clique para copiar a informação"><?php echo esc_html($participante['rf']); ?> <img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio"></span></td>
        <td class="text-nowrap"><span class="copiar-texto" data-texto="<?php echo esc_html($participante['email_principal']); ?>" data-toggle="tooltip" title="Clique para copiar a informação"><?php echo esc_html($participante['email_principal']); ?> <img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio"></span></td>
        <td class="text-nowrap"><span class="copiar-texto" data-texto="<?php echo esc_html($participante['telefone_whatsapp']); ?>" data-toggle="tooltip" title="Clique para copiar a informação"><span class="celular-mask" data-texto="<?php echo esc_html($participante['telefone_whatsapp']); ?>"><?php echo esc_html($participante['telefone_whatsapp']); ?></span> <img src="<?= get_stylesheet_directory_uri(); ?>/img/icon_copy_16.png" class="copia-email-sorteio"></span></td>
        <td><a href="#"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> Ver CV</a></td>
        <td class="text-center">
            <?php 
                // Define o status atual do participante
                $status_atual = $participante['status'];                                        
                
                // Verifica qual botão mostrar
                if (in_array($status_atual, $etapas_comunicacao)) {                                               
                    echo '<button type="button" class="btn btn-success btn-comunicar-selecionados" data-id="' . esc_attr($participante['id']) . '" data-status="' . esc_attr($status_atual) . '">';
                    echo '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
                    echo '</button>';
                } elseif (in_array($status_atual, $permite_desbloqueio)) {                                                
                    echo '<button type="button" class="btn btn-voltar-status" data-id="' . esc_attr($participante['id']) . '" data-status="' . esc_attr($status_atual) . '">';
                    echo '<i class="fa fa-repeat" aria-hidden="true"></i>';
                    echo '</button>';
                } else {
                    if($status_atual != 'inscrito'){
                        echo '<button type="button" class="btn btn-secondary" data-toggle="tooltip" data-placement="top" title="A comunicação para esta etapa já foi enviada." disabled>';
                    } else {
                        echo '<button type="button" class="btn btn-secondary" data-toggle="tooltip" disabled>';
                    }                    
                    echo '<i class="fa fa-paper-plane" aria-hidden="true"></i>';
                    echo '</button>';
                }
            ?>
        </td>
    </tr>
    <?php
endforeach;
?>