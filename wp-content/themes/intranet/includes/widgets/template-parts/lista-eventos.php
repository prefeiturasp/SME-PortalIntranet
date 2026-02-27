<style>
.js-toggle-icon {
    transition: transform 0.2s ease;
}
.js-toggle-icon.is-open {
    transform: rotate(180deg);
}
</style>

<?php

wp_enqueue_style( 'widgets-dashboard' );
wp_enqueue_script( 'widgets' );

extract( $args );

if ( !empty( $eventos ) || !empty( $cortesias ) ) :
    ?>

    <div class="community-events">
        <?php if ( !empty( $eventos ) ) : ?>
            <div class="js-bloco-toggle mb-4 border-light border-bottom pb-2">
                <div class="cabecalho-lista d-flex justify-content-between js-toggle-lista">
                    <strong>Sorteios</strong>
                    <i class="fa fa-angle-up js-toggle-icon"></i>
                </div>
                <ul class="community-events-results activity-block last mt-2 mb-2 js-lista-conteudo">
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
        <?php endif; ?>
        
        <?php if ( !empty( $cortesias ) ) : ?>
            <div class="js-bloco-toggle mb-4 border-light border-bottom pb-2">
                <div class="cabecalho-lista d-flex justify-content-between js-toggle-lista">
                    <strong>Cortesias</strong>
                    <i class="fa fa-angle-up js-toggle-icon"></i>
                </div>
                <ul class="community-events-results activity-block last mt-2 mb-2 js-lista-conteudo">
                    <?php

                    foreach ( $cortesias as $cortesia ) :
                        setup_postdata(get_post($cortesia['post_id']));

                        $datas_info = obter_informacoes_datas_cortesia( $cortesia['post_id'], $filtro );
                        $local = $cortesia['local'];
                        ?>
                        <li class="event event-wordcamp wp-clearfix">
                            <div class="container">
                                <div class="dashicons event-icon" aria-hidden="true"></div>
                                <div class="event-info-inner">
                                    <a class="event-title" href="<?= esc_url(get_edit_post_link($cortesia['post_id'])); ?>">
                                        <?= esc_html($cortesia['title']); ?>
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
                                                    <span class="ce-separator"></span> <?php echo esc_html( "{$info['data']} - {$info['estoque_atual']}" ); ?>
                                                    <?php if( $info['instrucoes'] != '' ) : ?>
                                                        <br>- <strong><?php echo esc_html( "{$info['instrucoes']}" ); ?></strong>
                                                    <?php endif; ?>
                                                </li>
                                            <?php endforeach ?>
                                        </ul>
                                    <?php endif; ?>

                                </div>
                            </div>
                        </li>
                        <?php
                    endforeach;
                    ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <?php
    wp_reset_postdata();
endif;
?>

<?php
if ( empty( $eventos ) && empty( $cortesias ) ) {
    echo "<p>{$mensagem}</p>";
};
?>

<script>
    jQuery(function($){
        $(document).on('click', '.js-toggle-lista', function(e){

            e.preventDefault();
            e.stopImmediatePropagation();

            var $bloco = $(this).closest('.js-bloco-toggle');
            var $lista = $bloco.find('.js-lista-conteudo');
            var $icon  = $bloco.find('.js-toggle-icon');

            $lista.stop(true,true).slideToggle(200);
            $icon.toggleClass('is-open');

        });
    });
</script>