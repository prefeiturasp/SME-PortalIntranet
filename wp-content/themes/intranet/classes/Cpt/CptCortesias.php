<?php

namespace Classes\Cpt;


class CptCortesias extends Cpt
{
	private $post_type;

	public function __construct()
	{
		$this->post_type = self::getCptSlugExtend();

		add_action( 'init', [$this, 'vincular_taxonomias_compartilhadas'] );
		add_action( 'admin_menu', [$this, 'remover_meta_boxes'] );

		add_filter( 'manage_posts_columns', [$this, 'exibe_cols'], 10, 2 );
		add_filter( "post_type_labels_{$this->post_type}", [$this, 'personalizar_labels'] );
		add_filter( 'register_post_type_args', function( $args, $post_type ) {
			if ( $post_type === $this->post_type ) {
				$args['menu_position'] = 8;
			}
			return $args;
		}, 10, 2 );
	}

	public function personalizar_labels( $labels ) {
		$labels->add_new_item = 'Add Gratuidade e Cortesias';

		return $labels;
	}

	public function vincular_taxonomias_compartilhadas() {
		$taxonomias = ['post_tag'];

		foreach ( $taxonomias as $tax ) {
			register_taxonomy_for_object_type( $tax, $this->post_type );
		}
	}

	public function remover_meta_boxes() {
		remove_meta_box( 'tagsdiv-post_tag', $this->post_type, 'normal' );
	}

	public function exibe_cols ($cols, $post_type ) {
		if ( $post_type === $this->post_type ) {
			$columns = array(
				'cb' => '<input type="checkbox" />',
				'title' => 'Titulo',
				'author' => 'Autor',
				'categories' => 'Categorias',
				'tags' => 'Local',
				'featured_thumb' => 'Thumbnail',				
				'date' => 'Data',
			);

			return $columns;
		}else{
			return $cols;
		}
	}
}