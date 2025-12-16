<br>
<section class="row container-taxonomias">
    <article class='col-12'>
        <h2 class="titulo"> <i class="fa fa-exclamation-triangle"></i> <?php echo OOOPS ?></h2>

        <p><?php echo PEDIMOSDESCULPAS ?></p>
        <p><?php echo UTILIZEOMENUACIMA ?></p>
        <?php
        $s = str_replace("-", " ", $s);
        if (count($posts) == 0) {
            // $posts = query_posts("&showposts=-1");
            $posts = query_posts(array('post_type' => 'post', 'name' => $s, 'showposts=3'));
        }
        if (count($posts) > 0) {
            echo "<p>".ESTAPROCURANDO."</p>";
        ?>

            
            <?php
            echo '<div class="list-group">';
            foreach ($posts as $post) {
                echo '<button type="button" class="list-group-item">';
                echo '<a href="' . get_permalink($post->ID) . '">' . $post->post_title . '</a>';
                echo '</button>';
            }
            echo "</ul>";
            }
            ?>

    </article>
    <article class="col-12 text-right padding-top-30 padding-bottom-15">
        <a class="btn btn-danger" href="javascript:history.back();"><?php echo VOLTAR ?></a>
    </article>
</section>

