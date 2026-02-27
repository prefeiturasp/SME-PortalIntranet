<?php
/*
Plugin Name: Definir Campo Alt da Imagem
Plugin URI: https://ottobonidesign.com.br
Description: Define o campo alt das imagens enviadas para o título. Copia o título da imagem para alt quando inserido no editor. Defina alt da miniatura da postagem para o título da postagem. Código copiado de várias fontes.
Author: Ollyver Ottoboni.
Version: 0.1
*/
function dcwd_title_to_words( $title ) {
	// Sanitize the title:  remove hyphens, underscores & extra spaces:
	$title = preg_replace( '%\s*[-_\s]+\s*%', ' ',  $title );
	// Sanitize the title:  capitalize first letter of every word (other letters lower case):
	$title = ucwords( strtolower( $title ) );
	return $title;
}
// Copied from: https://brutalbusiness.com/automatically-set-the-wordpress-image-title-alt-text-other-meta/
add_action( 'add_attachment', 'dcwd_set_image_meta_upon_image_upload' );
function dcwd_set_image_meta_upon_image_upload( $post_ID ) {
	// Check if uploaded file is an image, else do nothing
	if ( wp_attachment_is_image( $post_ID ) ) {
		$my_image_title = get_post( $post_ID )->post_title;
		$my_image_title = dcwd_title_to_words( $my_image_title );
		// Create an array with the image meta (Title, Caption, Description) to be updated
		// Note:  comment out the Excerpt/Caption or Content/Description lines if not needed
		$my_image_meta = array(
			'ID' => $post_ID,			// Specify the image (ID) to be updated
			'post_title' => $my_image_title,		// Set image Title to sanitized title
			// Damien: Omit setting the caption as I rarely use captions when I insert images.
			//'post_excerpt' => $my_image_title,		// Set image Caption (Excerpt) to sanitized title
			'post_content' => $my_image_title,		// Set image Description (Content) to sanitized title
		);
		// Set the image Alt-Text
		update_post_meta( $post_ID, '_wp_attachment_image_alt', $my_image_title );
		// Set the image meta (e.g. Title, Excerpt, Content)
		wp_update_post( $my_image_meta );
	}
}
// Enhanced version of: https://wordpress.org/plugins/automatic-image-alt-attributes/
add_filter('image_send_to_editor', 'dcwd_auto_alt_fix_1', 10, 2);
function dcwd_auto_alt_fix_1($html, $id) {
	$image_title = get_the_title( $id );
	$image_title = dcwd_title_to_words( $image_title );
	return str_replace('alt=""','alt="' . $image_title . '"',$html);
}
add_filter('wp_get_attachment_image_attributes', 'dcwd_auto_alt_fix_2', 10, 2);
function dcwd_auto_alt_fix_2($attributes, $attachment){
	if ( !isset( $attributes['alt'] ) || '' === $attributes['alt'] ) {
		$attributes['alt'] = dcwd_title_to_words( get_the_title( $attachment->ID ) );
	}
	return $attributes;
}
// From: https://mekshq.com/change-image-alt-tag-in-wordpress/
/* Replace alt attribute of post thumbnail with post title */
add_filter( 'post_thumbnail_html', 'meks_post_thumbnail_alt_change', 10, 5 );
function meks_post_thumbnail_alt_change( $html, $post_id, $post_thumbnail_id, $size, $attr ) {
	$post_title = get_the_title();
	$html = preg_replace( '/(alt=")(.*?)(")/i', '$1'.esc_attr( $post_title ).'$3', $html );

	return $html;

}