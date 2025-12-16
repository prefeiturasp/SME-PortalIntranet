<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since 1.0.0
 */

?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( ! twentynineteen_can_show_post_thumbnail() ) : ?>
	<header class="entry-header">
		<?php get_template_part( 'template-parts/header/entry', 'header' ); ?>
	</header>
	<?php endif; ?>

	<div class="entry-content">

		

		<?php
			/*
			if($_POST['user'] != '' && $_POST['senha'] != ''){
				$login = $_POST['user'];
				$password = $_POST['senha'];
				$url = 'http://localhost/teste/validalogin.php?user=' . $login .'&pass=' . $password;
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL,$url);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
				curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
				curl_setopt($ch, CURLOPT_USERPWD, "$login:$password");
				// Set request method to POST
				curl_setopt($ch, CURLOPT_POST, 1);
				$result = curl_exec($ch);
				curl_close($ch);
				$result = json_decode($result);
				echo "<pre>";
				print_r($result);
				echo "</pre>";
				//if($result->auth == ''){

				//}
			}*/

			$api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/v1/autenticacao';

			$body = wp_json_encode( array(
				"login" => "7924488",
				"senha" => "Sgp@1234",
			) );

			$response = wp_remote_post( $api_url ,
					array( 
						'headers' => array( 
							'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99',
							'Content-Type'=> 'application/json-patch+json'
						),
						'body' => $body,
					));

			$user = json_decode($response['body']);

			//echo "<pre>";
			//print_r($response);
			//echo "</pre>";

			if($user->codigoRf){
				$api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/AutenticacaoSgp/' . $user->codigoRf . '/dados';
				$response = wp_remote_get( $api_url ,
					array( 
						'headers' => array( 
							'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99',							
						)
					)
				);

				$user = json_decode($response['body']);

				//echo "<pre>";
				//print_r($user);
				//echo "</pre>";
			}

		?>

		<?php
		the_content();

		wp_link_pages(
			array(
				'before' => '<div class="page-links">' . __( 'Pages:', 'twentynineteen' ),
				'after'  => '</div>',
			)
		);
		?>
	</div><!-- .entry-content -->

	<?php if ( get_edit_post_link() ) : ?>
		<footer class="entry-footer">
			<?php
			edit_post_link(
				sprintf(
					wp_kses(
						/* translators: %s: Name of current post. Only visible to screen readers */
						__( 'Edit <span class="screen-reader-text">%s</span>', 'twentynineteen' ),
						array(
							'span' => array(
								'class' => array(),
							),
						)
					),
					get_the_title()
				),
				'<span class="edit-link">',
				'</span>'
			);
			?>
		</footer><!-- .entry-footer -->
	<?php endif; ?>
</article><!-- #post-<?php the_ID(); ?> -->
