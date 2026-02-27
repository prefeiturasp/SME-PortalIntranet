<div class="recados-destaques">
    <div class="recados-title d-flex justify-content-between">
        <h3>Mural de Recados</h3>
        <?php
            $ver_mais = get_sub_field('link_ver_mais');
            if($ver_mais)
                echo '<p><a href="' . $ver_mais . '">Ver mais</a></p>';
        ?>
    </div>

    <?php
                
    // the query
    $args = array(
        'post_type' => 'destaque',
        'posts_per_page' => get_sub_field('quantidade'),
        'paged' => $paged,
        'meta_query' => array(
            array(
                'key'   => 'pagina_principal',
                'value' => '1',
            )
        )            
    );

    $the_query = new WP_Query( $args ); ?>
    
    <?php if ( $the_query->have_posts() ) : ?>
    
        <!-- pagination here -->
    
        <!-- the loop -->
        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>

            <?php 
                $categorias = get_the_terms(get_the_ID(), 'categorias-destaque');
                $tags = get_the_terms(get_the_ID(), 'tags-destaque');
            ?>
            <div class="recado">
                <div class="row">
                    <div class="col-2 pr-0">
                        <?php 
                            if($categorias)
                                $image = get_field('imagem_principal', 'categorias-destaque_' . $categorias[0]->term_id);
                                $i = 0;
                                
                        ?>
                        <?php if($image): ?>
                            <img src="<?= $image['sizes']['thumbnail']; ?>" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                        <?php else: ?>
                            <img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                        <?php endif; ?>
                    </div>
                    <div class="col-10">
                        <?php
                            //echo "<pre>";
                            //print_r($image);
                            //echo "</pre>";
                        ?>                        

                        <p class="data"><?= getDay(get_the_date('w')); ?>, <?= get_the_date('M d') ?> às <?= get_the_date('H\hi\m\i\n') ?></p>
                        
                        <?php if($tags): ?>
                            <div class="tags-recados">
                                <?php 
                                    foreach($tags as $tag){
                                        $cor = get_field('cor_principal', 'tags-destaque_' . $tag->term_id);
                                        echo '<a href="' . get_home_url() . '/index.php/mural-de-recados/?tag=' . $tag->term_id . '" style="background: ' . $cor . '">' . firstLetter($tag->name) . '</a> ';
                                    }
                                ?>
                            </div>
                        <?php endif; ?>

                        <a href="#" class="link-modal" data-toggle="modal" data-target="#modal-<?= get_the_ID(); ?>"><h2><?= get_the_title(); ?></h2></a>

                        <?php if($categorias): ?>
                            <p class="categs">
                                <?php 
                                    foreach($categorias as $term){
                                        if($i == 0){
                                            echo '<a href="' . get_home_url() . '/index.php/mural-de-recados/?categoria=' . $term->term_id . '">' . $term->name . '</a>';
                                        } else {
                                            echo ', <a href="' . get_home_url() . '/index.php/mural-de-recados/?categoria=' . $term->term_id . '">' . $term->name . '</a>';
                                        }
                                        $i++;
                                    }                                        
                                ?>
                            </p>
                        <?php endif; ?>
                                              
                    </div>                    
                </div>

                <hr>

                <!-- Modal -->
                <div class="modal fade modal-recados" id="modal-<?= get_the_ID(); ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel"><?= get_the_title(); ?></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">

                            <?php
                                $subtitulo = get_field('insira_o_subtitulo');
                                if($subtitulo && $subtitulo != '')
                                    echo '<p class="subtitulo">' . $subtitulo . '</p>';
                            ?> 

                            <div class="recado-content">
                                <?php the_content(); ?>
                                <?php if( get_field('insira_o_link') ): ?>
                                    <p class="link-externo"><a href="<?= get_field('insira_o_link'); ?>">Ver link externo</a></p>
                                <?php endif; ?>
                            </div>
                            <?php if(get_field('url_do_video')): ?>
                                <div class="recado-video">
                                    <div class="embed-container">
                                        <?php the_field('url_do_video'); ?>
                                    </div>                                    
                                </div>
                            <?php endif; ?>

                            <?php if(get_field('selecione_imagem')): ?>
                                <div class="recado-video">                                    
                                    <?php $imagem = get_field('selecione_imagem'); ?>
                                    <img src="<?= $imagem['url']; ?>" alt="<?= $imagem['alt']; ?>">
                                </div>
                            <?php endif; ?>

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>                            
                        </div>
                        </div>
                    </div>
                </div>                
            </div>
        <?php endwhile; ?>
        <!-- end of the loop -->
    
        <?php wp_reset_postdata(); ?>
    
    <?php else : ?>
        <p><?php _e( 'Não há nenhuma publicação encontrada.' ); ?></p>
    <?php endif; ?>
</div>