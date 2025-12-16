<?php 
    $imagem = get_sub_field('imagem'); // link
    $titulo = get_sub_field('titulo');
    $tipoLink = get_sub_field('selecionar_url');
    $cor = get_sub_field('cor');

    if($tipoLink){
        $url = get_sub_field('url');
    } else {
        $url = get_sub_field('pagina');
    }

    //print_r($imagem);
?>
<a href="<?= $url; ?>" title="<?= $titulo; ?>">
    <div class="card-subpagina">
        <img src="<?= $imagem['url']; ?>" class="img-fluid" alt="<?= $imagem['alt']; ?>">
        <h2 style="background: <?= $cor; ?>;"><?= $titulo; ?></h2>                    
    </div>
</a>