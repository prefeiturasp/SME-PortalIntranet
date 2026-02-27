<?php

namespace Classes\Cpt;


class CptNoticias extends Cpt
{
	public function __construct()
	{
		add_filter('manage_posts_columns', array($this, 'exibe_cols'), 10, 2);
		add_action('manage_noticia_posts_custom_column', array($this, 'cols_content'));
	}

	// add featured thumbnail to admin post columns
	public function exibe_cols($cols, $post_type) {
		if ($post_type === 'noticia') {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Titulo',
				'author' => 'Autor',
				'categorias' => 'Categorias',
				//'tags' => 'Tags',
				'comments' => '<span class="vers"><div title="Comments" class="comment-grey-bubble"></div></span>',
				'featured_thumb' => 'Thumbnail',				
				'date' => 'Data',

			);

			return $columns;
		}else{
			return $cols;
		}

	}

	public function cols_content($column) {
		switch ( $column ) {

            case 'categorias':
                $terms = get_the_terms( $post_id, 'categorias-noticias' );
                if ( $terms && ! is_wp_error( $terms ) ) {
                    $terms_links = array();
                    foreach ( $terms as $term ) {
                        $terms_links[] = '<a href="' . esc_url( admin_url('edit.php/?post_type=noticia&categorias-noticias=' . $term->slug) ) . '">' . esc_html( $term->name ) . '</a>';
                    }
                    echo implode( ', ', $terms_links );
                } else {
                    echo '-';
                }
                
                break;

		}
	}

}