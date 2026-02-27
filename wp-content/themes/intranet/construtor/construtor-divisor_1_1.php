<div class="container">
    <div class="mt-3 mb-3 hr-divisor">
        <?php
            if(get_sub_field('divisor_hr') == 'linhaazul'){
                echo '<hr class=" hr-divisor-azul">';
            }elseif (get_sub_field('divisor_hr') == 'linhagrafite') {
                echo '<hr class=" hr-divisor-grafite">';
            }else{
                echo '<hr class=" hr-divisor-branca">';
            }
        ?>
    </div>
</div>