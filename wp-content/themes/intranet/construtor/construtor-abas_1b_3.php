<?php
if(get_sub_field('fx_abas_2_2'))://repeater
	$contentAbas = array();
    $str = 'abcdef';
    						
    //loop menu aba
    echo '<ul class="nav nav-tabs mb-3 mt-3">';
        $count_2_2=0;
        while(has_sub_field('fx_abas_2_2'))://verifica conteudo no repeater
            $count_2_2++;
            $misturada = str_shuffle($str);
            $contentAbas[$count_2_2] = $misturada;
            //echo $count;
            $aba_title_2_2 = get_sub_field('fx_nome_abas_2_2');
            $id_aba_2_2 = clean($aba_title);
            echo '<li class="nav-item">';
                echo '<a class="nav-link" data-toggle="tab" href="#abab' . $misturada . $id_aba_2_2 . '"><strong>'.get_sub_field('fx_nome_abas_2_2').'</strong></a>';
            echo '</li>';
        endwhile;
    echo '</ul>';

    //loop conteudo aba
    echo '<div class="tab-content mb-3 mt-3">';
            $count_2_2=0;
    while(has_sub_field('fx_abas_2_2'))://verifica se editor no repeater
            $count_2_2++;
            //echo $count;
            $aba_title_2_2 = get_sub_field('fx_nome_abas_2_2');
            $id_aba_2_2 = clean($aba_title);
        echo '<div class="tab-pane container mt-3 mb-3" id="abab' . $contentAbas[$count_2_2] . $id_aba_2_2 . '">'.get_sub_field('fx_editor_abas_2_2').'</div>';

    endwhile;
    echo '</div>';

 endif;