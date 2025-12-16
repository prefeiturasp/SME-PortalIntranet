<?php
if (have_posts()) :
	$conta_posts = 0;
	?>
    <section class="row container-post-categorias">
	<?php
	while (have_posts()) : the_post();

		$post_thumbnail_id = get_post_thumbnail_id( $post_id );
		$image_alt = get_post_meta( $post_thumbnail_id, '_wp_attachment_image_alt', true);
        ?>
        <article class='col-12 col-md-4'>
			<?php if (has_post_thumbnail()) { ?>
                <figure>
                    <img alt="<?= $image_alt ?>" class="img-fluid aligncenter img-thumbnail" src="<?= get_the_post_thumbnail_url() ?>"/>
                </figure>
			<?php } ?>
            <h3 class="titulo-post-categorias"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            <?php the_excerpt(); ?>

        </article>
		<?php
		$conta_posts++;
	endwhile;
	?>
    </section>

<?php else:
	?>
    <p>
		<?php _e('Não existem posts cadastrados.', 'sme-portal-institucional'); ?>
    </p>
<?php endif; ?>
<br/>
<?php //wp_pagenavi();    ?>
<section class="row mb-3">
    <article class="col-12 text-right">
        <a class="btn btn-primary" href="javascript:history.back();"><?php echo VOLTAR ?></a>
    </article>
</section>
