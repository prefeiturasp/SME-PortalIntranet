<?php
$imagem_4_4 = get_sub_field('fx_imagem_4_4');//Pega todos os valores da imagem no array
//echo the_sub_field('fx_imagem_url_4_4');
if(get_sub_field('fx_imagem_url_4_4') != ''){
    ?>
      <a href="<?php echo the_sub_field('fx_imagem_url_4_4') ?>">
          <img class="mt-3 mb-3" src="<?php echo $imagem_4_4['url'] ?>" width="100%" alt="<?php echo $imagem_4_4['alt'] ?>">
      </a>
    <?php
}else{
    echo '<img class="mt-3 mb-3" src="'.$imagem_4_4['url'].'" width="100%" alt="'.$imagem_4_4['alt'].'">';
}