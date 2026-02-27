<?php

if(get_sub_field('fx_fl1_selecione_rede_1_2') == 'insta'):
    ?>

        <div class="container">	
            <div class="row social-block">
                <div class="col-sm-5">
                    <?php if(get_sub_field('fx_fl1_titulo_1_2')): ?>
                        <p><?php echo get_sub_field('fx_fl1_titulo_1_2'); ?></p>
                    <?php endif; ?>

                    <?php if(get_sub_field('fx_fl1_descricao_1_2')): ?>
                        <p class='social-descri'><?php echo get_sub_field('fx_fl1_descricao_1_2'); ?></p>
                    <?php endif; ?>

                    <hr>

                    <?php if(get_sub_field('fx_fl1_pagina_1_2')): ?>
                        <a href="<?php echo get_sub_field('fx_fl1_pagina_1_2'); ?>"><button type="button" class="btn btn-primary btn-sm px-3"><i class="fa fa-instagram" aria-hidden="true"></i> | Instagram</button></a>
                    <?php endif; ?>
                    
                </div>
                <div class="col-sm-7">
                    <?php if(get_sub_field('fx_fl1_shortcode_1_2')):
                        $short = get_sub_field('fx_fl1_shortcode_1_2');
                        echo do_shortcode($short);
                    endif; ?>
                    
                </div>
            </div>
        </div>

    <?php
        elseif(get_sub_field('fx_fl1_selecione_rede_1_2') == 'face'):
    ?>
        <div class="row social-block">
            <div class="col-sm-5">

                <?php if(get_sub_field('fx_fl1_titulo_1_2')): ?>
                    <p><?php echo get_sub_field('fx_fl1_titulo_1_2'); ?></p>
                <?php endif; ?>

                <?php if(get_sub_field('fx_fl1_descricao_1_2')): ?>
                    <p class='social-descri'><?php echo get_sub_field('fx_fl1_descricao_1_2'); ?></p>
                <?php endif; ?>

                <hr>
                <?php if(get_sub_field('fx_fl1_pagina_1_2')): ?>
                    <a href="<?php echo get_sub_field('fx_fl1_pagina_1_2'); ?>"><button type="button" class="btn btn-primary btn-sm px-3"><i class="fa fa-facebook-square" aria-hidden="true"></i> | Facebook</button></a>
                <?php endif; ?>

            </div>
            <div class="col-sm-7">
                <div class="fb-page" data-href="<?php echo get_sub_field('fx_fl1_pagina_1_2'); ?>" data-tabs="timeline" data-width="" data-height="310" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="https://www.facebook.com/EducaPrefSP/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/EducaPrefSP/">Secretaria Municipal de Educação de São Paulo</a></blockquote></div>
            </div>
        </div>
    <?php
        elseif(get_sub_field('fx_fl1_selecione_rede_1_2') == 'ytube'):
    ?>
        <div class="container">
            <div class="row social-block">
                <div class="col-sm-12">
                    
                    <?php if(get_sub_field('fx_fl1_titulo_1_2')): ?>
                        <p><?php echo get_sub_field('fx_fl1_titulo_1_2'); ?></p>
                    <?php endif; ?>

                    <?php if(get_sub_field('fx_fl1_descricao_1_2')): ?>
                        <p class='social-descri'><?php echo get_sub_field('fx_fl1_descricao_1_2'); ?></p>
                    <?php endif; ?>		

                    <?php if(get_sub_field('fx_fl1_pagina_1_2')): ?>
                        <a href="<?php echo get_sub_field('fx_fl1_pagina_1_2'); ?>"><button type="button" class="btn btn-primary btn-sm px-3"><i class="fa fa-youtube-play" aria-hidden="true"></i> | Youtube</button></a>
                    <?php endif; ?>
                    
                </div>												
                <div class="col-sm-12 mt-4">
                    <?php
                        if(get_sub_field('fx_fl1_video_1_2')) :
                            $url = get_sub_field('fx_fl1_video_1_2');
                            parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
                    ?>
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $my_array_of_vars['v']; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    endif;