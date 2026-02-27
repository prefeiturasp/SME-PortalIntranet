<?php

wp_enqueue_style( 'widgets-dashboard' );
wp_enqueue_script( 'widgets' );

extract( $args );

if ( !empty( $eventos ) ) :
    ?>

    <div class="community-events">
        <ul class="community-events-results activity-block last" aria-hidden="false">
            <?php
            foreach ($eventos as $evento) :
                setup_postdata(get_post($evento['post_id']));

                $datas_info = obter_informacoes_datas_sorteio( $evento['post_id'], $filtro );
                $local = $evento['local'];
                ?>
                <li class="event event-wordcamp wp-clearfix">
                    <div class="container">
                        <div class="dashicons event-icon" aria-hidden="true"></div>
                        <div class="event-info-inner">
                            <a class="event-title" href="<?= esc_url(get_edit_post_link($evento['post_id'])); ?>">
                                <?= esc_html($evento['title']); ?>
                            </a>

                            <?php
                            if ( $local && $local !== 'outros' ) :
                                $term = get_term($local);
                                ?>
                                <?php if ( $term && !is_wp_error( $term ) ) : ?>
                                    <small class="event-city"><b><?php echo esc_html( "- Local: {$term->name}" ); ?></b></small>
                                <?php endif; ?>
                                <?php
                            endif;
                            ?>

                            <?php if ( !empty( $datas_info ) ) : ?>
                                <ul class="js-limit-list">
                                    <?php foreach ( $datas_info as $info ) : ?>
                                        <li>
                                            <span class="ce-separator"></span> <?php echo esc_html( "{$info['data']} - {$info['status']}" ); ?>
                                            <?php if($info['status'] == 'Sorteio Realizado' && $info['instrucoes'] != '') : ?>
                                                <br>- <strong><?php echo esc_html("{$info['instrucoes']}"); ?></strong>
                                            <?php endif; ?>
                                        </li>
                                    <?php endforeach ?>
                                </ul>
                            <?php endif; ?>

                        </div>
                    </div>
                    <div class="event-date-time">
                        <span class="event-date">
                            <?= esc_html($data_formatada); ?>
                            <?php if (!empty($hora)) : ?>
                                <span class="ce-separator"></span>
                                <?= esc_html(formatar_hora($hora)); ?>
                            <?php endif; ?>
                        </span>
                    </div>
                </li>
                <?php
            endforeach;
            ?>
        </ul>
    </div>

    <?php
    wp_reset_postdata();
endif;
?>

<?php
if ( empty( $eventos ) ) {
    echo "<p>{$mensagem}</p>";
};
?>