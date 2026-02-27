<?php
//if (!isset($_SESSION['teste'])) {
//    
//    $_SESSION['teste'] = 'Sessão Criada';
    
    $query_promocoes = new WP_Query('cat=4&showposts=-1&order=DESC');
    if ($query_promocoes->have_posts()) {

        while ($query_promocoes->have_posts()): $query_promocoes->the_post();
            $do_not_duplicate = $post->ID;

            if (is_sticky()) {
                ?>
                <div class="modal fade bs-example-modal-lg" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                <h4 class="modal-title" id="myModalLabel"><i class="fa fa-money"></i> <?php the_title() ?>.</h4>
                            </div>
                            <div class="modal-body">
                                <?php if (has_post_thumbnail()) { ?>
                                    <?php the_post_thumbnail('home-thumb', array('class' => 'img-fluid alignleft img-thumbnail')); ?>
                                <?php } ?>
                                <?php
                                echo the_content();
                                ?>
                            </div>
                            <div class="modal-footer">
                                
                            </div>
                        </div>
                    </div>
                </div>
            <?php }

        endwhile
        ?>

        <?php
    }

    wp_reset_query();
//} else {
//    // session_cache_expire(1);
//    session_destroy();
//}
?>
<?php ?>

