<?php
$titulo = get_sub_field('titulo');
$destaque = get_sub_field('destaque');
$link = get_sub_field('link');



if($destaque){
    $informativo = get_sub_field('informativo');
    $subtitulo = get_field('insira_o_subtitulo', $informativo);
} else {
    $args = array(
        'fields' => 'ids',
        'numberposts' => 1,
        'post_type'   => 'info-sme-explica'
    );
    $posts = get_posts($args);
    $informativo = $posts[0];
    $subtitulo = get_field('insira_o_subtitulo', $informativo);
}

$tipo = get_field('tipo_de_destaque', $informativo);
if($tipo == 'audio'){
    $audio = get_field('audio', $informativo);
}

$habilitar_destaque = get_field('habilitar_destaque', $informativo);

?>

<div class="card-dest">
    <div class="row">
        <?php if($tipo == 'video' && $habilitar_destaque): ?>

            <div class="col-12 col-md-7">
                <h3><?= $titulo; ?></h3>
                <a href="<?= get_the_permalink($informativo); ?>"><p class="titulo m-0"><?= get_the_title($informativo); ?></p>
                <p><?= $subtitulo; ?></p></a>
                <?php if($link): ?>
                    <p class="m-0"><a class="link_saiba" href="<?= $link; ?>">Ver outras explicações</a></p>
                <?php endif; ?>
            </div>

            <div class="col-12 col-md-5">
                <div class="img-dest">
                    <a href="#" data-toggle="modal" data-target="#videoModal">
                        <?= get_the_post_thumbnail($informativo, 'medium', array( 'class' => 'img-fluid' )); ?>
                        <i class="fa fa-play-circle" aria-hidden="true"></i>
                    </a>
                </div>
            </div>

        <?php else: ?>

            <div class="col-12">
                <h3><?= $titulo; ?></h3>
                <a href="<?= get_the_permalink($informativo); ?>"><p class="titulo m-0"><?= get_the_title($informativo); ?></p>
                <p><?= $subtitulo; ?></p></a>                
                <?php
                    if($tipo == 'audio' && $audio != '' && $habilitar_destaque){
                        echo do_shortcode('[audio mp3=' . $audio . ']');
                    }
                ?>
                <?php if($link): ?>
                    <p class="m-0"><a class="link_saiba" href="<?= $link; ?>">Ver outras explicações</a></p>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
    
</div>

<?php if($tipo == 'video'): ?>
    <?php
        $tipo_video = get_field('tipo_de_video', $informativo);
        $video_file = get_field('video', $informativo);
        $video_embed = get_field('video_embed', $informativo);
        $video_format = pathinfo($video_file, PATHINFO_EXTENSION);       
        //echo "<pre>";
        //print_r($tipo_video);
        //echo "</pre>";
    ?>
    <div class="modal fade" id="videoModal" tabindex="-1" role="dialog" aria-labelledby="videoModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"><strong><?= get_the_title($informativo); ?></strong></h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                <div>
                    <?php 
                        if($tipo_video && $video_file != ''){
                            echo do_shortcode( '[video ' . $video_format . '=' . $video_file . ']' );
                        } elseif($video_embed) {
                            echo '<div class="video-container">';
                                echo $video_embed;
                            echo '</div>';
                        }
                    ?>
                </div>
            </div>
            <div class="modal-footer">                
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
    </div>
<?php endif; ?>