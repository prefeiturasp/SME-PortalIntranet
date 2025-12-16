<?php

$chave = 'fx_acessos_' . $args['key'];

if(get_sub_field($chave)) :
									

    echo '<session class="container-fluid container-fluid-botoes-persona">';

        echo '<div class="container">';

            echo '<div class="row">';

                echo '<div class="col-sm-12 p-0">';

                    echo '<ul class="card-group nav m-0 acesso-rapido" role="tablist">';

                        $acessosRapido = get_sub_field($chave);
                        $key = generateRandomString(5);

                        //echo "<pre>";
                        //print_r($acessosRapido);
                        //echo "</pre>";

                        foreach($acessosRapido as $acessos):
                        ?>
                            <li id="tab_<?php echo $acessos['menu'] . $key; ?>" class="container-a-icones-home card rounded-0 border-0">

                                <a id="tab_<?php echo $acessos['menu'] . $key; ?>" data-toggle="tab" href="#menu_<?php echo $acessos['menu'] . $key; ?>" role="tab" aria-selected="false" class="a-icones-home ">

                                    <div class="row m-0 w-100">

                                        <?php if($acessos['icone']['sizes']['thumbnail']) : ?>

                                            <div class="col-sm-4 pl-0">
                                                <div class="icon-card">
                                                    <img src="<?php echo $acessos['icone']['sizes']['thumbnail']; ?>" class="img-fluid" alt="<?php echo $acessos['icone']['alt']; ?>">
                                                </div>
                                            </div>

                                            <div class="col-sm-8">
                                                <div class="card-body text-center px-0">
                                                    <p class="card-text"><?php echo $acessos['titulo']; ?></p>
                                                </div>
                                            </div>

                                        <?php else: ?>

                                            <div class="col-sm-12">
                                                <div class="card-body text-center px-0">
                                                    <p class="card-text"><?php echo $acessos['titulo']; ?></p>
                                                </div>
                                            </div>

                                        <?php endif; ?>

                                    </div>
                                    
                                </a>
                                
                                <div class="acesso-mobile">
                                    <?php
                                        wp_nav_menu(array(
                                            'menu' => $acessos['menu'],
                                            'depth' => 2,
                                            'menu_class' => 'navbar-nav m-auto nav nav-tabs',
                                            'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                                            'walker'            => new \WP_Bootstrap_Navwalker(),
                                        ));
                                    ?>
                                </div>

                            </li>
                        <?php
                        endforeach;

                        
                                        
                    echo '</ul>';
                echo '</div>';                  

            echo '</div>';
        echo '</div>';
    echo '</session>';
    
    
    echo '<section class="tab-content container acesso-rapido-menu">';
                            
        foreach($acessosRapido as $acessos):

            echo '<section class="tab-pane fade container p-0 my-3" id="menu_' . $acessos['menu'] . $key . '" role="tabpanel" aria-labelledby="' . $acessos['menu'] . '">';

                echo '<nav class="navbar navbar-expand-lg nav-icones-menu">';


                    echo '<article class="collapse navbar-collapse">';
                    
                        wp_nav_menu(array(
                            'menu' => $acessos['menu'],
                            'depth' => 2,
                            'menu_class' => 'navbar-nav m-auto nav nav-tabs',
                            'fallback_cb'       => 'WP_Bootstrap_Navwalker::fallback',
                            'walker'            => new \WP_Bootstrap_Navwalker(),
                        ));
                        
                    echo '</article>';

                echo '</nav>';																						

            echo '</section>';

        endforeach;
    echo '</section>';
    

endif; // end fx_acessos_1_1