<?php
    wp_register_script('orgchart', STM_THEME_URL . 'js/orgchart.js', false, true);
    wp_enqueue_script('orgchart');

    wp_register_style('orgchart_css', STM_THEME_URL . 'css/orgchart.css', null, null, 'all');
    wp_enqueue_style('orgchart_css');

    $principal = get_sub_field('contato_principal');
    $segCamada = get_sub_field('contatos_secundarios');
    $terCamada = get_sub_field('terceira_camada');

    $texto = get_sub_field('texto_introducao');
    $baixar_organ = get_sub_field('baixar_organograma');
    $tel_email = get_sub_field('link_tel_email');
    $link_unidades = get_sub_field('link_unidades');

    $conselhos = get_sub_field('conselhos');

    $lastEdit = new WP_Query( array(
        'post_type'   => 'contato',
        'posts_per_page' => 1,
        'orderby'     => 'modified',
    ));

    $date = $lastEdit->post->post_modified;
    $lastEdit = new DateTime($date);

    $pageDate = get_the_modified_time('Y-m-d H:i:s');
    $pageDate = new DateTime($pageDate);

    //echo $pageDate;

    if($lastEdit > $pageDate){
        $dataShow = $lastEdit;
    } else {
        $dataShow = $pageDate;
    }

    $i = 1;

    //echo "<pre>";
    //print_r($terciario);
    //echo "</pre>";

    //print_r($principal);
?>
<script>
        $s(document).ready(function() {
            // create a tree
            $s("#tree-data").jOrgChart({
                chartElement: $s("#tree-view"),
                nodeClicked: nodeClicked
            });

            // create a tree
            $s("#tree-data3").jOrgChart({
                chartElement: $s("#tree-view3"),
                nodeClicked: nodeClicked
            });

            // create a tree
            $s("#tree-data4").jOrgChart({
                chartElement: $s("#tree-view4"),
                nodeClicked: nodeClicked
            });

            // lighting a node in the selection
            function nodeClicked(node, type) {
                node = node || $s(this);
                $s('.jOrgChart .selected').removeClass('selected');
                node.addClass('selected');
            }
        });
    </script>
    <style>
        
        
    </style>
<div class="container">
    <div class="row">

    <div class="col-sm-12 texto-org">
            <?php
                if($texto && $texto != ''){
                    echo $texto;
                }

                if($dataShow != ''){
                    echo "<p class='font-italic text-date'>Atualizado em:" . $dataShow->format('d/m/Y H:i:s') . "</p>";
                }
            ?>

        </div>

        <?php if($tel_email != '' || $baixar_organ != ''): ?>
            <div class="col-sm-12 text-right">
                <p>
                    <?php if($baixar_organ) : ?>
                        <a href="<?php echo $baixar_organ; ?>" class="font-weight-bold"><i class="fa fa-download" aria-hidden="true"></i> Baixar Organograma</a> &nbsp;
                    <?php endif; ?>
                    <?php if($tel_email) : ?>
                        <a href="<?php echo $tel_email; ?>" class="font-weight-bold"><i class="fa fa-comment" aria-hidden="true"></i>Ver telefones e emails de contato</a>
                    <?php endif; ?>
                </p>
            </div>
        <?php endif; ?>
        
        <div class="col-sm-12">

            <div class="btn-organograma">
                <button id="btn_ZoomIn"><i class="fa fa-plus" aria-hidden="true"></i></button>
                <button id="btn_ZoomOut"><i class="fa fa-minus" aria-hidden="true"></i></button>
            </div>

            <div id="organograma-site">            
       
                <ul id="tree-data" style="display:none">
                    <li id="root">
                        <?php 
                            $contPrincial = get_field('campos_contato', $principal);                            
                            foreach ($contPrincial as $contato){
                                echo "<p>" . $contato['nome_campo'] . ": " . $contato['informacao_campo'] . "</p>";
                            }
                        ?>
                        
                        <ul>
                            <?php
                                foreach($segCamada as $contato){
                                    echo '<li><a data-toggle="modal" data-target="#contsecundario' . $i . '">' . get_the_title($contato) . '</a></li>';
                                    $i++;
                                }
                            ?>
                        </ul>
                    </li>
                </ul>

                <ul id="tree-data3" style="display:none">

                    <li id="root"> &nbsp;
                        <ul>
                            <?php
                                foreach($terCamada as $contato){
                                    echo "<li><a data-toggle='modal' data-target='#contato" . $i . "'>" . get_the_title($contato['contato']) . "</a>";

                                    // Contatos Secundarios
                                    $secundarios = $contato['contatos_secundarios'];

                                    if($secundarios){
                                        echo "<ul type='vertical'>";
                                            foreach($secundarios as $contatoSeg){
                                                echo "<li><a data-toggle='modal' data-target='#secundario" . $i . "'>" . get_the_title($contatoSeg['contato']) . "</a>";

                                                // Contatos Terciarios
                                                $terciarios = $contatoSeg['contato_terciario'];

                                                
                                                $i++;

                                                if($terciarios){
                                                    echo "<div><img class='coversub img-fluid' src='/wp-content/themes/sme-portal-institucional/img/orgchart.plus.png'></div>";
                                                    echo "<ul type='vertical'>";
                                                        foreach($terciarios as $terciario){
                                                            echo "<li><a data-toggle='modal' data-target='#terciario" . $i . "'>" . get_the_title($terciario) . "</a></li>";
                                                            $i++;
                                                        }
                                                    echo "</ul>";
                                                }

                                                echo "</li>";
                                                
                                            }
                                        echo "</ul>";
                                    }

                                    $i++;
                                    
                                    echo  "</li>";
                                }
                            ?>
                        </ul>
                    </li>

                </ul>

                <ul id="tree-data4" style="display:none">
                    <li id="root">&nbsp;
                        <ul>
                            <?php
                                if($conselhos){
                                    
                                    foreach($conselhos as $conselho){
                                        echo "<li><a data-toggle='modal' data-target='#conselho" . $i . "'>" . get_the_title($conselho) . "</a></li>";
                                        $i++;
                                    }
                                    
                                }
                            ?>
                        </ul>
                    </li>
                    
                </ul>

                <div id="tree-view"></div>
                <div id="tree-view3"></div>
                <div id="tree-view4"><p class="title-cons">Conselhos</p></div>
    
            </div>
        </div>

        <?php if($link_unidades != ''): ?>
            <div class="col-sm-12 mt-3">
                <a href="<?php echo $link_unidades; ?>" class="font-weight-bold"><i class="fa fa-university" aria-hidden="true"></i> Consultar Unidades Escolares </a>
            </div>
        <?php endif; ?>

    </div>
</div>



<?php
    // Segunda Camada
    $j = 1;
    foreach($segCamada as $secundario):
    ?>
        <div class="modal fade" id="contsecundario<?php echo $j; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                <div class="modal-header modal-org-title b-0">
                    <h5 class="modal-title" id="exampleModalLabel"><?php echo get_the_title($secundario); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body modal-org">
                    <div class="container-fluid">
                        <div class="row">
                            <?php 
                                
                                $campos = get_field('campos_contato', $secundario);
                                $saiba = get_field('link_saiba_mais', $secundario);

                                if($campos && $campos != ''){
                                    foreach ($campos as $campo){
                                        
                                        if($campo['tipo_de_campo'] == 'telefone'){
                                            echo '<div class="col-sm-6 org-info">
                                                    <span>' . $campo['nome_campo'] . '</span>
                                                    <a href="tel:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                    <div class="org-line"></div>
                                                </div>';
                                        } elseif($campo['tipo_de_campo'] == 'email'){
                                            echo '<div class="col-sm-12 org-info">
                                                    <span>' . $campo['nome_campo'] . '</span>
                                                    <a href="mailto:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                    <div class="org-line"></div>
                                                </div>';
                                        } else {
                                            echo '<div class="col-md-12 org-info">
                                                    <span>' . $campo['nome_campo'] . '</span>
                                                    ' . $campo['informacao_campo'] . '
                                                    <div class="org-line"></div>
                                                </div>';
                                        }
                                    }
                                }
                            ?>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer modal-org-footer">
                    <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fechar</button>
                    <?php if($saiba && $saiba != ''): ?>
                        <a href="<?php echo $saiba; ?>" class="btn btn-primary">Saiba mais <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </div>
<?php
    $j++;
    endforeach;

    // Terceira Camada
    foreach($terCamada as $contato):
?> 
        <div class="modal fade" id="contato<?php echo $j; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header modal-org-title b-0">
                        <h5 class="modal-title" id="exampleModalLabel"><?php echo get_the_title($contato['contato']); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modal-org">
                        <div class="container-fluid">
                            <div class="row">
                                <?php 
                                    
                                    $campos = get_field('campos_contato', $contato['contato']);
                                    $saiba = get_field('link_saiba_mais', $contato['contato']);

                                    if($campos && $campos != ''){
                                        foreach ($campos as $campo){
                                            
                                            if($campo['tipo_de_campo'] == 'telefone'){
                                                echo '<div class="col-sm-6 org-info">
                                                        <span>' . $campo['nome_campo'] . '</span>
                                                        <a href="tel:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                        <div class="org-line"></div>
                                                    </div>';
                                            } elseif($campo['tipo_de_campo'] == 'email'){
                                                echo '<div class="col-sm-12 org-info">
                                                        <span>' . $campo['nome_campo'] . '</span>
                                                        <a href="mailto:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                        <div class="org-line"></div>
                                                    </div>';
                                            } else {
                                                echo '<div class="col-md-12 org-info">
                                                        <span>' . $campo['nome_campo'] . '</span>
                                                        ' . $campo['informacao_campo'] . '
                                                        <div class="org-line"></div>
                                                    </div>';
                                            }
                                        }
                                    }
                                ?>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-org-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fechar</button>
                        <?php if($saiba && $saiba != ''): ?>
                            <a href="<?php echo $saiba; ?>" class="btn btn-primary">Saiba mais <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

<?php
        // Contatos Secundarios
        $secundarios = $contato['contatos_secundarios'];

        if($secundarios):
            foreach($secundarios as $contatoSeg):
            ?>

                <div class="modal fade" id="secundario<?php echo $j; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header modal-org-title b-0">
                                <h5 class="modal-title" id="exampleModalLabel"><?php echo get_the_title($contatoSeg['contato']); ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body modal-org">
                                <div class="container-fluid">
                                    <div class="row">
                                        <?php 
                                            
                                            $campos = get_field('campos_contato', $contatoSeg['contato']);
                                            $saiba = get_field('link_saiba_mais', $contatoSeg['contato']);

                                            if($campos && $campos != ''){
                                                foreach ($campos as $campo){
                                                    
                                                    if($campo['tipo_de_campo'] == 'telefone'){
                                                        echo '<div class="col-sm-6 org-info">
                                                                <span>' . $campo['nome_campo'] . '</span>
                                                                <a href="tel:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                                <div class="org-line"></div>
                                                            </div>';
                                                    } elseif($campo['tipo_de_campo'] == 'email'){
                                                        echo '<div class="col-sm-12 org-info">
                                                                <span>' . $campo['nome_campo'] . '</span>
                                                                <a href="mailto:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                                <div class="org-line"></div>
                                                            </div>';
                                                    } else {
                                                        echo '<div class="col-md-12 org-info">
                                                                <span>' . $campo['nome_campo'] . '</span>
                                                                ' . $campo['informacao_campo'] . '
                                                                <div class="org-line"></div>
                                                            </div>';
                                                    }
                                                }
                                            }
                                        ?>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer modal-org-footer">
                                <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fechar</button>
                                <?php if($saiba && $saiba != ''): ?>
                                    <a href="<?php echo $saiba; ?>" class="btn btn-primary">Saiba mais <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            
            <?php
                // Contatos Terciarios
                $terciarios = $contatoSeg['contato_terciario'];

                //print_r($contatoSeg);
                $j++;
                if($terciarios && $terciarios != ''):
                    
                    
                    foreach($terciarios as $terciario):
                        
                    ?>    
                        <div class="modal fade" id="terciario<?php echo $j; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header modal-org-title b-0">
                                        <h5 class="modal-title" id="exampleModalLabel"><?php echo get_the_title($terciario); ?></h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body modal-org">
                                        <div class="container-fluid">
                                            <div class="row">
                                                <?php 
                                                    
                                                    $campos = get_field('campos_contato', $terciario);
                                                    $saiba = get_field('link_saiba_mais', $terciario);

                                                    if($campos && $campos != ''){
                                                        foreach ($campos as $campo){
                                                            
                                                            if($campo['tipo_de_campo'] == 'telefone'){
                                                                echo '<div class="col-sm-6 org-info">
                                                                        <span>' . $campo['nome_campo'] . '</span>
                                                                        <a href="tel:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                                        <div class="org-line"></div>
                                                                    </div>';
                                                            } elseif($campo['tipo_de_campo'] == 'email'){
                                                                echo '<div class="col-sm-12 org-info">
                                                                        <span>' . $campo['nome_campo'] . '</span>
                                                                        <a href="mailto:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                                        <div class="org-line"></div>
                                                                    </div>';
                                                            } else {
                                                                echo '<div class="col-md-12 org-info">
                                                                        <span>' . $campo['nome_campo'] . '</span>
                                                                        ' . $campo['informacao_campo'] . '
                                                                        <div class="org-line"></div>
                                                                    </div>';
                                                            }
                                                        }
                                                    }
                                                ?>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer modal-org-footer">
                                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fechar</button>
                                        <?php if($saiba && $saiba != ''): ?>
                                            <a href="<?php echo $saiba; ?>" class="btn btn-primary">Saiba mais <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                    <?php
                    $j++;
                    endforeach;
                    
                endif;

                //$j++;
            endforeach;
        endif;
        
    $j++;
    endforeach;

    foreach ($conselhos as $conselho):
    ?>
        <div class="modal fade" id="conselho<?php echo $j; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header modal-org-title b-0">
                        <h5 class="modal-title" id="exampleModalLabel"><?php echo get_the_title($conselho); ?></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body modal-org">
                        <div class="container-fluid">
                            <div class="row">
                                <?php 
                                    
                                    $campos = get_field('campos_contato', $conselho);
                                    $saiba = get_field('link_saiba_mais', $conselho);

                                    if($campos && $campos != ''){
                                        foreach ($campos as $campo){
                                            
                                            if($campo['tipo_de_campo'] == 'telefone'){
                                                echo '<div class="col-sm-6 org-info">
                                                        <span>' . $campo['nome_campo'] . '</span>
                                                        <a href="tel:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                        <div class="org-line"></div>
                                                    </div>';
                                            } elseif($campo['tipo_de_campo'] == 'email'){
                                                echo '<div class="col-sm-12 org-info">
                                                        <span>' . $campo['nome_campo'] . '</span>
                                                        <a href="mailto:' . $campo['informacao_campo'] . '">' . $campo['informacao_campo'] . '</a>
                                                        <div class="org-line"></div>
                                                    </div>';
                                            } else {
                                                echo '<div class="col-md-12 org-info">
                                                        <span>' . $campo['nome_campo'] . '</span>
                                                        ' . $campo['informacao_campo'] . '
                                                        <div class="org-line"></div>
                                                    </div>';
                                            }
                                        }
                                    }
                                ?>
                                
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer modal-org-footer">
                        <button type="button" class="btn btn-outline-primary" data-dismiss="modal">Fechar</button>
                        <?php if($saiba && $saiba != ''): ?>
                            <a href="<?php echo $saiba; ?>" class="btn btn-primary">Saiba mais <i class="fa fa-arrow-right" aria-hidden="true"></i></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php
    $j++;
    endforeach;
?>




<script type="text/javascript">

        var currentZoom = 1.0;

        $s(document).ready(function() {
            $s('#btn_ZoomIn').click(
                function() {
                    $s('#organograma-site').animate({
                        'zoom': currentZoom += .2
                    }, 'fast');

                    console.log(currentZoom);

                    if (currentZoom > 1) {
                        var tamanho1 = $s('#tree-view3 table').width();
                        console.log(tamanho1);
                        
                        if(currentZoom == 1.2){
                            tamanho1 = 925;
                        } else if(currentZoom == 1.4){
                            tamanho1 = 867;
                        } 
                        console.log(tamanho1);
                        
                        //var tamanho2 = $s('#tree-view3 .jOrgChart').width();
                        
                        $s("#tree-view").css("width", tamanho1);
                        $s('#tree-view').addClass('more');

                    } else {
                        $s("#tree-view").css("width", 'auto');
                        $s('#tree-view').removeClass('more');
                    }

                })
            $s('#btn_ZoomOut').click(
                function() {
                    if(currentZoom == 1){
                        currentZoom = 1.0
                        $s('#organograma-site').animate({
                            'zoom': 1
                        }, 'fast');
                    } else {
                        $s('#organograma-site').animate({
                            'zoom': currentZoom -= .2
                        }, 'fast');
                    }
                    

                    if(currentZoom == 1.2){
                        tamanho1 = 925;
                    } else if(currentZoom == 1.4){
                        tamanho1 = 867;
                    }

                    $s("#tree-view").css("width", tamanho1);

                    if (currentZoom == 1) {

                        $s("#tree-view").css("width", 'auto');
                        $s('#tree-view').removeClass('more');
                    }

                });

            var qtd = $s('#tree-view .node-container').length;

            if (qtd % 2 == 0) {
                $s('#tree-view').addClass('par');
            } else {
                $s('#tree-view').addClass('impar');
            }

        });
    </script>