<div class="container">
    <?php
    $string = generateRandomString(5);
    if(get_sub_field('fx_abas_1_1'))://repeater

        //loop menu aba
        echo '<ul class="nav nav-tabs">';
            $count=0;
            
            while(has_sub_field('fx_abas_1_1'))://verifica conteudo no repeater
                $count++;
                //echo $count;
                $aba_title = get_sub_field('fx_nome_abas_1_1');
                $id_aba = clean($aba_title);
                echo '<li class="nav-item">';
                    echo '<a class="nav-link" data-toggle="tab" href="#aba'. $count . $string . $id_aba. '"><strong>'.get_sub_field('fx_nome_abas_1_1').'</strong></a>';
                echo '</li>';
            endwhile;
        echo '</ul>';

        //loop conteudo aba
        echo '<div class="tab-content">';
                $count=0;
        while(has_sub_field('fx_abas_1_1'))://verifica se editor no repeater
                $count++;
                //echo $count;
                $aba_title = get_sub_field('fx_nome_abas_1_1');
                $id_aba = clean($aba_title);
            echo '<div class="tab-pane container mt-3 mb-3" id="aba'. $count . $string . $id_aba. '">'.get_sub_field('fx_editor_abas_1_1').'</div>';

        endwhile;
        echo '</div>';

    endif;
    ?>
</div>