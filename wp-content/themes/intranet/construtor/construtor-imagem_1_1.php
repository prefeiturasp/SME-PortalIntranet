<?php
echo '<div class="container">';
$imagem_1_1 = get_sub_field('fx_imagem_1_1');//Pega todos os valores da imagem no array
if(get_sub_field('fx_imagem_url_1_1') != ''){
    ?>
        <a href="<?php echo the_sub_field('fx_imagem_url_1_1') ?>">
        <img class="mt-3 mb-3" src="<?php echo $imagem_1_1['url'] ?>" width="100%" alt="<?php echo $imagem_1_1['alt'] ?>">
        </a>
    <?php
}else{
    echo '<img class="mt-3 mb-3" src="'.$imagem_1_1['url'].'" width="100%" alt="'.$imagem_1_1['alt'].'">';
}

echo "</div>";