<br>
<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	<section class="row container-taxonomias" id="conteudo">
        <article class='col-12'>
            <h2 class="titulo-taxonomias"><i class="fa fa-th-large"></i>
				<?php the_title(); ?>

            </h2>
			<?php the_content(); ?>
        </article>
    </section>

	<?php
	$attachments = get_posts( array(
		'post_type' => 'attachment',
		'posts_per_page' => -1,
		'post_parent' => $post->ID,
		'orderby'	=> 'ID',
		'order'	=> 'ASC',
		'exclude'     => get_post_thumbnail_id()
	) );

	if ( $attachments ) {

	    echo '<section id="arquivos-anexos">';
		echo '<h2>Arquivos Anexos</h2>';

		foreach ( $attachments as $attachment ) {
			echo '<p><a target="_blank" style="font-size:26px" href="'.$attachment->guid.'"><i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i> '. $attachment->post_title.'</a></p>';
		}
		echo '</section>';

	}

endwhile;
else: ?>
    <p>
		<?php _e('Não existem posts cadastrados.', 'site-profissional'); ?>
    </p>
<?php endif; ?>



<br/>
<div class="row container-taxonomias padding-bottom-15">
    <div class="col-12 text-right">
        <a class="btn btn-success" href="javascript:history.back();"><< Voltar</a>
    </div>
</div>
