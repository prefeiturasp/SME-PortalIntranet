<div class="banner-news">
    <h3>Receba as novidades no seu e-mail</h3>
    <?php
        if(get_sub_field('shortcode'))
            echo do_shortcode(get_sub_field('shortcode'));
    ?>
</div>