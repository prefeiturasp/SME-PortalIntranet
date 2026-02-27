<?php
$chave = 'fx_sanfona_' . $args['key'];
$nome = 'fx_nome_sanfona_' . $args['key'];
$editor = 'fx_editor_sanfona_' . $args['key'];

$fundo = get_sub_field('fundo_sanfona');
$link = get_sub_field('cor_link');
$texto = get_sub_field('cor_texto');
$botao = get_sub_field('cor_botao');

if(get_sub_field($chave))://repeater
    //loop sanfona
    echo '<div id="accordiona"';
        $count_a = mt_rand(1,9999);
        while(has_sub_field($chave))://verifica conteudo no repeater
            $count_a++;
            //echo $count;
            echo '<div class="card mb-3 sanfona bg-' . $fundo['value'] . ' link-' . $link['value'] . ' text-' . $texto['value'] . ' btn-' . $botao['value'] . '">';
                echo '<div class="card-header">';
                  echo '<a class="collapsed card-link" data-toggle="collapse" href="#collapsea'.$count_a.'">';
                    echo '<strong>'.get_sub_field($nome).'</strong>';
                  echo '</a>';
                echo '</div>';
                echo '<div id="collapsea'.$count_a.'" class="collapse" data-parent="#accordiona">';
                  echo '<div class="card-body">';
                    echo get_sub_field($editor);
                  echo '</div>';
                echo '</div>';
              echo '</div>';
        endwhile;
    echo '</div>';		
endif;