<?php
$chave = 'fx_fl1_selecione_rede_' . $args['key'];
$titulo = 'fx_fl1_titulo_' . $args['key'];
$descri = 'fx_fl1_descricao_' . $args['key'];
$pagina = 'fx_fl1_pagina_' . $args['key'];
$shortCode = 'fx_fl1_shortcode_' . $args['key'];
$video = 'fx_fl1_video_' . $args['key'];

if($args['key'] == '1_1' || $args['key'] == '1_2' || $args['key'] == '2_2'){
    $column1 = 'col-sm-5';
    $column2 = 'col-sm-7';
} else {
    $column1 = 'col-sm-12';
    $column2 = 'col-sm-12';
}

if(get_sub_field($chave) == 'insta'):
    ?>

        	
        <div class="row social-block">
            <div class="<?php echo $column1; ?>">
                <?php if(get_sub_field($titulo)): ?>
                    <p><?php echo get_sub_field($titulo); ?></p>
                <?php endif; ?>

                <?php if(get_sub_field($descri)): ?>
                    <p class='social-descri'><?php echo get_sub_field($descri); ?></p>
                <?php endif; ?>

                <hr>

                <?php if(get_sub_field($pagina)): ?>
                    <a href="<?php echo get_sub_field($pagina); ?>"><button type="button" class="btn btn-primary btn-sm px-3"><i class="fa fa-instagram" aria-hidden="true"></i> | Instagram</button><span class="esconde-item-acessibilidade">(Link para um novo sítio)</span></a>
                <?php endif; ?>
                
            </div>
            <div class="<?php echo $column2; ?>">
                <?php if(get_sub_field($shortCode)):
                    $short = get_sub_field($shortCode);
                    echo do_shortcode($short);
                endif; ?>
                
            </div>
        </div>
        

    <?php
        elseif(get_sub_field($chave) == 'face'):
    ?>
        <div class="row social-block">
            <div class="<?php echo $column1; ?> mb-2">

                <?php if(get_sub_field($titulo)): ?>
                    <p><?php echo get_sub_field($titulo); ?></p>
                <?php endif; ?>

                <?php if(get_sub_field($descri)): ?>
                    <p class='social-descri'><?php echo get_sub_field($descri); ?></p>
                <?php endif; ?>

                <hr>
                <?php if(get_sub_field($pagina)): ?>
                    <a href="<?php echo get_sub_field($pagina); ?>"><button type="button" class="btn btn-primary btn-sm px-3"><i class="fa fa-facebook-square" aria-hidden="true"></i> | Facebook</button><span class="esconde-item-acessibilidade">(Link para um novo sítio)</span></a>
                <?php endif; ?>

            </div>
            <div class="<?php echo $column2; ?>">
                <div class="fb-page" data-href="<?php echo get_sub_field($pagina); ?>" data-tabs="timeline" data-width="" data-height="310" data-small-header="true" data-adapt-container-width="true" data-hide-cover="false" data-show-facepile="false"><blockquote cite="https://www.facebook.com/EducaPrefSP/" class="fb-xfbml-parse-ignore"><a href="https://www.facebook.com/EducaPrefSP/">Secretaria Municipal de Educação de São Paulo</a></blockquote></div>
            </div>
        </div>
    <?php
        elseif(get_sub_field($chave) == 'ytube'):
    ?>
        <div class="container">
            <div class="row social-block">
                <div class="<?php echo $column1; ?>">
                    
                    <?php if(get_sub_field($titulo)): ?>
                        <p><?php echo get_sub_field($titulo); ?></p>
                    <?php endif; ?>

                    <?php if(get_sub_field($descri)): ?>
                        <p class='social-descri'><?php echo get_sub_field($descri); ?></p>
                    <?php endif; ?>		

                    <?php if(get_sub_field($pagina)): ?>
                        <a href="<?php echo get_sub_field($pagina); ?>"><button type="button" class="btn btn-primary btn-sm px-3"><i class="fa fa-youtube-play" aria-hidden="true"></i> | Youtube</button><span class="esconde-item-acessibilidade">(Link para um novo sítio)</span></a>
                    <?php endif; ?>
                    
                </div>												
                <div class="<?php echo $column2; ?> mt-4">
                    <?php
                        if(get_sub_field($video)) :
                            $url = get_sub_field($video);
                            parse_str( parse_url( $url, PHP_URL_QUERY ), $my_array_of_vars );
                    ?>
                        <iframe width="560" height="315" src="https://www.youtube.com/embed/<?php echo $my_array_of_vars['v']; ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <?php endif; ?>
                </div>
            </div>
        </div>
<?php
    endif;