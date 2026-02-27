<?php

namespace Classes\Cpt;


class CptPosts extends Cpt
{
	public function __construct()
	{
		add_filter('manage_posts_columns', array($this, 'exibe_cols'), 10, 2);
		add_action( 'manage_posts_custom_column' , array($this, 'cols_content'), 10, 2 );
	}

	// add featured thumbnail to admin post columns
	public function exibe_cols($cols, $post_type) {
		if ($post_type === 'post') {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Titulo',
				'author' => 'Autor',
				'categories' => 'Categorias',
				'tags' => 'Local',
				//'comments' => '<span class="vers"><div title="Comments" class="comment-grey-bubble"></div></span>',
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
			case 'featured_thumb':
				echo '<a href="' . get_edit_post_link() . '">';
				$thumb = get_thumb($post_id);
				echo "<img src='" . $thumb[0] . "' style='max-width: 100%;'>";
				echo '</a>';
				break;

		}
	}

}