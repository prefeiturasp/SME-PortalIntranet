<?php

$imagem_1_2 = get_sub_field('fx_imagem_1_2');//Pega todos os valores da imagem no array
if(get_sub_field('fx_imagem_url_1_2') != ''){
    ?>
        <a href="<?php echo the_sub_field('fx_imagem_url_1_2') ?>">
        <img class="mt-3 mb-3" src="<?php echo $imagem_1_2['url'] ?>" width="100%" alt="<?php echo $imagem_1_2['alt'] ?>">
        </a>
    <?php
}else{
    echo '<img class="mt-3 mb-3" src="'.$imagem_1_2['url'].'" width="100%" alt="'.$imagem_1_2['alt'].'">';
}