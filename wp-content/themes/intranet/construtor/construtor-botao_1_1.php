<?php
$colorbtn = get_sub_field('fx_cor_do_botao_coluna_1_1');
//conteudo flexivel Botão
if( have_rows('fx_botao_1_1') ):
    echo '<div class="container">';
        echo '<div class="row mt-3 mb-3">';
            while ( have_rows('fx_botao_1_1') ) : the_row();

                // Responsivo
                if( get_row_layout() == 'fx_cl1_botao_1_1' ):
                        //loop de botões responsivos
                        $align = get_sub_field('alinhamento');
                        echo '<div class="col-12 align-' . $align .'">';
                            echo '<a href="'.get_sub_field('fx_url_botao_1_1').'"><button type="button" class="btn bt_fx btn-'.$colorbtn['value'].' btn-lg">'.get_sub_field('fx_nome_botao_1_1').'</button></a>';
                        echo '</div>';
                endif;

                // Bloco
                if( get_row_layout() == 'fx_cl1_botao_bloco_1_1' ):
                    //loop de botões responsivos
                    echo '<div class="col-12 text-center">';
                        echo '<a href="'.get_sub_field('fx_url_botao_1_1').'"><button type="button" class="btn bt_fx btn-'.$colorbtn['value'].' btn-lg btn-block">'.get_sub_field('fx_nome_botao_1_1').'</button></a>';
                    echo '</div>';
                endif;

                // Fixo
                if( get_row_layout() == 'fx_cl1_botao_fixo_1_1' ):
                   //loop de botões fixo
                    $align = get_sub_field('alinhamento');
                    echo '<div class="col-12 align-' . $align .'">';
                        echo '<a href="'.get_sub_field('fx_url_botao_1_1').'"><button type="button" class="btn bt_fx btn-'.$colorbtn['value'].' btn-lg" style="width: ' . get_sub_field('fx_tamanho_botao_1_1') . 'px;">'.get_sub_field('fx_nome_botao_1_1').'</button></a>';
                    echo '</div>';
                endif;

            endwhile;
        echo '</div>';
    echo '</div>';
else :
endif;

// echo '<a href="'.get_sub_field('fx_url_botao_1_1').'"><button type="button" class="btn mb-3 btn-'.$colorbtn['value'].' btn-lg btn-block">'.get_sub_field('fx_nome_botao_1_1').'</button></a>';