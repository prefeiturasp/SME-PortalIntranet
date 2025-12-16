<?php

$blocoTitulo = get_sub_field('fx_cl1_bloco_noticias_titulo');
$blocoColunas = get_sub_field('fx_cl1_bloco_noticias_colunas');
$noticias = get_sub_field('selecione_noticias');
            

$date = date('Y-m-d H:i:s', time());
$dT = new DateTime($date);
$hoursToSubtract = 3; // Subtrair 3h
$dT->sub(new DateInterval("PT{$hoursToSubtract}H"));
$newTime = $dT->format('Y-m-d H:i:s');

$noticiaExibir = array();

if($noticias && $noticias != ''){
    foreach($noticias as $noticia){
        
        if($noticia['limitar_tempo'] && $noticia['ocultar']){
            if($noticia['exibir'] < $newTime && $newTime < $noticia['ocultar']){
                $noticiaExibir[] = $noticia['noticia'];
            } 
        } elseif ($noticia['limitar_tempo'] && !$noticia['ocultar']){
            if($noticia['exibir'] < $newTime){
                $noticiaExibir[] = $noticia['noticia'];
            }
        } else {
            $noticiaExibir[] = $noticia['noticia'];
        }
    }
}

echo '<section class="container lista-noticias my-4">';
    echo '<div class="row">';
        echo '<div class="col-sm-12 lista-noticias-titulo">';
            echo '<p>' . $blocoTitulo . '</p>';
        echo '</div>';

        if($noticiaExibir) :

            foreach($noticiaExibir as $noticia):
                
                // Busca a imagem destaca / primeira imagem / imagem padrao -- functions.php
                $thumbs = get_thumb($noticia);
        
            ?>
                <div class="col-sm-12 col-md-6 col-lg-<?php echo $blocoColunas; ?> lista-noticia">
                    <a href="<?php echo get_the_permalink($noticia); ?>">
                        <img src="<?php echo $thumbs[0]; ?>" alt="<?php echo $thumbs[1]; ?>" class="img-fluid">
                        <p><?php echo get_the_title($noticia); ?></p>
                    </a>
                </div>

            <?php
            endforeach;
        
        endif;

    echo '</div>';
echo '</section>';