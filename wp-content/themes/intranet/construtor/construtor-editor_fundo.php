<?php
$chave = 'fx_editor_' . $args['key'];
echo '<div style="background: url('.get_sub_field('imagem_de_fundo').')" class="mt-3 mb-3 p-3 bg_img_fix">'.get_sub_field($chave).'</div>';
