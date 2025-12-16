<?php
if(get_sub_field('fx_sanfona_2_2'))://repeater
    //loop sanfona
    echo '<div id="accordionb" class="mt-3 mb-3">';
        $countb=0;
        while(has_sub_field('fx_sanfona_2_2'))://verifica conteudo no repeater
            $countb++;
            //echo $count;
              echo '<div class="card sanfona ">';
                echo '<div class="card-header">';
                  echo '<a class="collapsed card-link" data-toggle="collapse" href="#collapseb'.$countb.'">';
                    echo '<strong>'.get_sub_field('fx_nome_sanfona_2_2').'</strong>';
                  echo '</a>';
                echo '</div>';
                echo '<div id="collapseb'.$countb.'" class="collapse" data-parent="#accordionb">';
                  echo '<div class="card-body">';
                    echo get_sub_field('fx_editor_sanfona_2_2');
                  echo '</div>';
                echo '</div>';
              echo '</div>';
        endwhile;
    echo '</div>';		
endif;