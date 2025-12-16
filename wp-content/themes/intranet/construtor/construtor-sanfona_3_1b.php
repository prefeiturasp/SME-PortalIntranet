<?php
if(get_sub_field('fx_sanfona_2_2'))://repeater
    //loop sanfona
    echo '<div id="accordionb" class="mt-3 mb-3">';
        $countbf=mt_rand(1,99);
        while(has_sub_field('fx_sanfona_2_2'))://verifica conteudo no repeater
            $countbf++;
            
              echo '<div class="card sanfona ">';
                echo '<div class="card-header">';
                  echo '<a class="collapsed card-link" data-toggle="collapse" href="#collapseb'.$countbf.'">';
                    echo '<strong>'.get_sub_field('fx_nome_sanfona_2_2').'</strong>';
                  echo '</a>';
                echo '</div>';
                echo '<div id="collapseb'.$countbf.'" class="collapse" data-parent="#accordionb">';
                  echo '<div class="card-body">';
                    echo get_sub_field('fx_editor_sanfona_2_2');
                  echo '</div>';
                echo '</div>';
              echo '</div>';
              
        endwhile;
    echo '</div>';		
endif;