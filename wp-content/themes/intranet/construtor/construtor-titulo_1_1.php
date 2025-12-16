<div class="container">
<?php

//echo '<h1 class="mt-3 mb-3 tx_fx_'.$color['value'].'">'.get_sub_field('fx_titulo_1_1').'</h1>';
$cab_h = get_sub_field('cabecalho_h_construtor_1_1');
$ali_h = get_sub_field('alinhar_h_construtor_1_1');
//echo $cab_h['value'];
if($cab_h['value'] == 'h1'){
    echo '<h1 class="text-'.$ali_h['value'].' mt-3 mb-3 tx_fx_'.$color['value'].'">'.get_sub_field('fx_titulo_1_1').'</h1>';
}elseif ($cab_h['value'] == 'h2') {
    echo '<h2 class="text-'.$ali_h['value'].' mt-3 mb-3 tx_fx_'.$color['value'].'">'.get_sub_field('fx_titulo_1_1').'</h2>';
}else{
    echo '<h1 class="text-left mt-3 mb-3 tx_fx_'.$color['value'].'">'.get_sub_field('fx_titulo_1_1').'</h1>';
}
?>
</div>