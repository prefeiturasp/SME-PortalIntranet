<?php

$blocoTitulo = get_sub_field('fx_cl1_bloco_noticias_titulo');
$blocoColunas = get_sub_field('fx_cl1_bloco_noticias_colunas');
$blocoNoticias = get_sub_field('fx_cl1_bloco_noticias_selecione_noticias');

echo '<section class="container lista-noticias my-4">';
    echo '<div class="row">';
        echo '<div class="col-sm-12 lista-noticias-titulo">';
            echo '<p>' . $blocoTitulo . '</p>';
        echo '</div>';

        if($blocoNoticias) :

            foreach($blocoNoticias as $noticia):
                
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