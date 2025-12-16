<?php
/**
 * @package WordPress
 * @subpackage Theme_Compat
 * @deprecated 3.0.0
 *
 * This file is here for backward compatibility with old themes and will be removed in a future version
 *
 */
_deprecated_file(
/* translators: %s: template name */
	sprintf(__('Theme without %s'), basename(__FILE__)),
	'3.0.0',
	null,
	/* translators: %s: template name */
	sprintf(__('Please include a %s template in your theme.'), basename(__FILE__))
);

// Do not delete these lines
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');

if (post_password_required()) { ?>
	<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.'); ?></p>
	<?php
	return;
}
?>

<!-- You can start editing here. -->
<?php if (have_comments()) : ?>
	<h3 id="comments">
		<?php
		if (1 == get_comments_number()) {
			/* translators: %s: post title */
			printf(__('One response to %s'), '&#8220;' . get_the_title() . '&#8221;');
		} else {
			/* translators: 1: number of comments, 2: post title */
			printf(_n('%1$s response to %2$s', '%1$s responses to %2$s', get_comments_number()),
				number_format_i18n(get_comments_number()), '&#8220;' . get_the_title() . '&#8221;');
		}
		?>
	</h3>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>

	<ol class="commentlist">
		<?php 
			wp_list_comments(array(
				'callback' => 'custom_comment_callback',
			));
		?>
	</ol>

	<div class="navigation">
		<div class="alignleft"><?php previous_comments_link() ?></div>
		<div class="alignright"><?php next_comments_link() ?></div>
	</div>
	
<?php else : // this is displayed if there are no comments so far ?>

	<?php if (comments_open()) : ?>
		<!-- If comments are open, but there are no comments. -->

	<?php else : // comments are closed ?>
		<!-- If comments are closed. -->		

	<?php endif; ?>
<?php endif; ?>

<?php
// Customizando os campos do comment_form()
$comment_args = array('title_reply' => 'Deixe seu comentário:',
	'fields' => apply_filters('comment_form_default_fields', array(
			'author' => '<div class="form-group"><label for="author">' . __('Name') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
				'<input class="form-control" id="author" name="author" type="text" value="' . esc_attr($commenter['comment_author']) . '" ' . $aria_req . $html_req . ' /></div>',
			'email' => '<div class="form-group"><label for="email">' . __('Email') . ($req ? ' <span class="required">*</span>' : '') . '</label> ' .
				'<input class="form-control" id="email" name="email" ' . ($html5 ? 'type="email"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_email']) . '" aria-describedby="email-notes"' . $aria_req . $html_req . ' /></div>',
			'url' => '<div class="form-group"><label for="url">' . __('Website') . '</label> ' .
				'<input class="form-control" id="url" name="url" ' . ($html5 ? 'type="url"' : 'type="text"') . ' value="' . esc_attr($commenter['comment_author_url']) . '" /></div>')
	),
	'comment_field' => '<div class="form-group"><label for="comment">' . _x('Comment', 'noun') . '*</label> <textarea class="form-control" rows="3" id="comment" name="comment" maxlength="65525" aria-required="true" required="required"></textarea></div>',
	wp_nonce_field( 'user_check', 'hdn_hash' ),
    'class_submit' => 'btn btn-primary',
);

comment_form($comment_args);