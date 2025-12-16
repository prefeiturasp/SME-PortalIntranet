<?php

$chave = 'fx_abas_' . $args['key'];
$nomeAba = 'fx_nome_abas_' . $args['key'];
$contAba = 'fx_editor_abas_' . $args['key'];
$string = generateRandomString(5);

if(get_sub_field($chave))://repeater
										
    //loop menu aba
    echo '<ul class="nav nav-tabs mb-3 mt-3">';
        $count_1_2=0;
        while(has_sub_field($chave))://verifica conteudo no repeater
            $count_1_2++;
            //echo $count;
            $aba_title_1_2 = get_sub_field($nomeAba);
            $id_aba_1_2 = clean($aba_title);
            echo '<li class="nav-item">';
                echo '<a class="nav-link" data-toggle="tab" href="#abaa'. $count_1_2 . $string . $id_aba_1_2 .'"><strong>'.get_sub_field($nomeAba).'</strong></a>';
            echo '</li>';
        endwhile;
    echo '</ul>';

    //loop conteudo aba
    echo '<div class="tab-content mb-3 mt-3">';
            $count_1_2=0;
    while(has_sub_field($chave))://verifica se editor no repeater
            $count_1_2++;
            //echo $count;
            $aba_title_1_2 = get_sub_field($nomeAba);
            $id_aba_1_2 = clean($aba_title);
        echo '<div class="tab-pane container mt-3 mb-3" id="abaa'. $count_1_2 . $string . $id_aba_1_2 .'">'.get_sub_field($contAba).'</div>';

    endwhile;
    echo '</div>';

endif;