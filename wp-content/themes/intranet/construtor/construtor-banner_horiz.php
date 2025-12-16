<?php 
    $imagem = get_sub_field('imagem'); // link
    $titulo = get_sub_field('titulo');
    $subtitulo = get_sub_field('subtitulo');
    $descricao = get_sub_field('descricao');
    $tipoLink = get_sub_field('selecionar_url');
    $cor = get_sub_field('cor');
    $cor_texto = get_sub_field('cor_texto');

    if($tipoLink){
        $url = get_sub_field('url');
    } else {
        $url = get_sub_field('pagina');
    }

    //print_r($imagem);
?>
<?php if($imagem && $imagem != ''): ?>
    
    <div class="banner-horiz">
        <div class="row" style="background: <?= $cor; ?>;">
            <div class="col-12 col-md-7">

                <div class="d-flex flex-column h-100">

                    <div>
                        <?php if($titulo && $titulo != ''):?>
                            <h2 style="color: <?= $cor_texto; ?>;"><?= $titulo; ?></h2>
                        <?php endif; ?>
                        <?php if($url && $url != ''):?>
                            <a href="<?= $url; ?>" style="color: <?= $cor_texto; ?>;">
                        <?php endif; ?>
                            <?php if($subtitulo && $subtitulo != ''):?>
                                <p style="color: <?= $cor_texto; ?>;" class="titulo m-0"><?= $subtitulo; ?></p>
                            <?php endif; ?>
                            <?php if($descricao && $descricao != ''):?>
                                <p style="color: <?= $cor_texto; ?>;"><?= $descricao; ?></p>
                            <?php endif; ?>
                        <?php if($url && $url != ''):?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto">
                        <?php if($url && $url != ''):?>
                            <a href="<?= $url; ?>" style="color: <?= $cor_texto; ?>; text-decoration: underline" class="mt-auto">Ver mais</a>
                        <?php endif; ?>
                    </div>

                </div>                
                
            </div>
            <div class="col-12 col-md-5">
                <img src="<?= $imagem['url']; ?>" class="img-fluid" alt="<?= $imagem['alt']; ?>">
            </div>
        </div>        
    </div>
    
<?php else: ?>

    <div class="banner-horiz">
        <div class="row" style="background: <?= $cor; ?>;">
            <div class="col-12">

                <div class="d-flex flex-column h-100">
                    <div>
                        <?php if($titulo && $titulo != ''):?>
                            <h2 style="color: <?= $cor_texto; ?>;"><?= $titulo; ?></h2>
                        <?php endif; ?>
                        <?php if($url && $url != ''):?>
                            <a href="<?= $url; ?>" style="color: <?= $cor_texto; ?>;">
                        <?php endif; ?>
                            <?php if($subtitulo && $subtitulo != ''):?>
                                <p style="color: <?= $cor_texto; ?>;" class="titulo m-0"><?= $subtitulo; ?></p>
                            <?php endif; ?>
                            <?php if($descricao && $descricao != ''):?>
                                <p style="color: <?= $cor_texto; ?>;"><?= $descricao; ?></p>
                            <?php endif; ?>
                        <?php if($url && $url != ''):?>
                            </a>
                        <?php endif; ?>
                    </div>

                    <div class="mt-auto">
                        <?php if($url && $url != ''):?>
                            <a href="<?= $url; ?>" style="color: <?= $cor_texto; ?>; text-decoration: underline" class="mt-auto">Ver mais</a>
                        <?php endif; ?>
                    </div>
                </div>

            </div>            
        </div>        
    </div>

<?php endif; ?>