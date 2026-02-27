<?php

$imagem_2_2 = get_sub_field('fx_imagem_2_2');//Pega todos os valores da imagem no array
if(get_sub_field('fx_imagem_url_2_2') != ''){
    ?>
        <a href="<?php echo the_sub_field('fx_imagem_url_2_2') ?>">
        <img class="mt-3 mb-3" src="<?php echo $imagem_2_2['url'] ?>" width="100%" alt="<?php echo $imagem_2_2['alt'] ?>">
        </a>
    <?php
}else{
    echo '<img class="mt-3 mb-3" src="'.$imagem_2_2['url'].'" width="100%" alt="'.$imagem_2_2['alt'].'">';
}