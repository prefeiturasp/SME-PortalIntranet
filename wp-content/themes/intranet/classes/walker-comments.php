<?php
if (!class_exists('Custom_Walker_Comment')) {
    
    class Custom_Walker_Comment extends Walker_Comment {

        public function html5_comment($comment, $depth, $args) {
            
            $GLOBALS['comment'] = $comment;

            $tag = ('div' === $args['style']) ? 'div' : 'li';
           
            $total_respostas = 0;
            $total = 1;

            if ($depth === 1) {
                $total_respostas = contar_respostas($comment->comment_ID);
                $total = 1 + $total_respostas;
            }
            
            ?>

            <<?php echo $tag; ?> <?php comment_class(!empty($args['has_children']) ? 'parent' : '', $comment); ?> id="comment-<?php comment_ID(); ?>">

                <!-- Contador + Botão -->
                <?php if ($depth === 1): ?>
                    <div class="comment-top d-flex justify-content-between">
                        <span class="comment-counter">
                            <?php echo $total; ?> comentário<?php echo ($total > 1 ? 's' : ''); ?>
                        </span>

                        <?php if ($total_respostas > 0): ?>
                            <button class="toggle-replies">
                                Ver menos
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
                    
                    <div class="comment-author vcard">
                        <?php
                            if (0 != $args['avatar_size']) {
                                echo get_avatar($comment, $args['avatar_size'], '', '', [
                                    'class' => 'avatar avatar-32 img-fluid'
                                ]);
                            }
                        ?>                        
                    </div>

                    <div class="comment-content-body">
                
                        <div class="d-flex flex-column flex-md-row justify-content-between">

                            <div class="comment-author">
                                <?php printf('<cite class="fn">%s</cite>', get_comment_author_link($comment)); ?>                            
                            </div>

                            <div class="comment-metadata">
                                <a href="<?php echo esc_url(get_comment_link($comment, $args)); ?>">
                                    <time datetime="<?php comment_time('c'); ?>">
                                        <?php printf('%1$s às %2$s', get_comment_date('', $comment), get_comment_time()); ?>
                                    </time>
                                </a>
                            </div>

                        </div>

                        <div class="comment-content">
                            <?php comment_text(); ?>
                        </div>

                        <div class="reply">
                            <?php
                            comment_reply_link(array_merge($args, [
                                'depth'     => $depth,
                                'max_depth' => $args['max_depth'],
                            ]));
                            ?>
                        </div>

                    </div>

                </article>
            <?php
        }

        public function end_el(&$output, $data_object, $depth = 0, $args = array()) {
            $tag = ('div' === $args['style']) ? 'div' : 'li';
            $output .= "</{$tag}>";
        }
    }
}