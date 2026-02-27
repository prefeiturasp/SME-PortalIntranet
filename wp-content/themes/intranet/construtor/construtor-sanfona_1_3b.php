<?php
if(get_sub_field('fx_sanfona_1_2'))://repeater
    //loop sanfona
    echo '<div id="accordiona" class="mt-3 mb-3">';
        $count_a=mt_rand(1,99);
        while(has_sub_field('fx_sanfona_1_2'))://verifica conteudo no repeater
            $count_a++;
            //echo $count;
              echo '<div class="card sanfona ">';
                echo '<div class="card-header">';
                  echo '<a class="collapsed card-link" data-toggle="collapse" href="#collapsea'.$count_a.'">';
                    echo '<strong>'.get_sub_field('fx_nome_sanfona_1_2').'</strong>';
                  echo '</a>';
                echo '</div>';
                echo '<div id="collapsea'.$count_a.'" class="collapse" data-parent="#accordiona">';
                  echo '<div class="card-body">';
                    echo get_sub_field('fx_editor_sanfona_1_2');
                  echo '</div>';
                echo '</div>';
              echo '</div>';
        endwhile;
    echo '</div>';		
endif;