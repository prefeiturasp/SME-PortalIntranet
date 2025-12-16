<?php

//contatos selecionados									
if(get_sub_field('contatos'))://repeater
										
    // gerar numero aleatorio
    $count = mt_rand(10,99);
    $count2 = $count;
    
    echo '<div class="container">';
        echo '<div class="row">';
            echo "<div class='col-sm-12 mb-4' id='lista-contatos'>";
                echo "<ul>";										

                    while(has_sub_field('contatos')):
                        // menu de ancora
                        $principal = get_sub_field('contato_principal');
                    
                        if($principal && $principal != ''){
                            
                            foreach($principal as $contato){
                                echo "<li class='mb-3'><a href='#" . $count ."'>" . get_the_title($contato) . "</a></li>";
                            }
                                    
                        }

                        $count++;													
                    endwhile;

                echo "</ul>";
            echo "</div>"; // end col-sm-12
        echo "</div>"; // end row
    echo "</div>"; // end container
    
    //loop contatos
    echo '<div class="container">';
        echo '<div class="row">';
            
            
            while(has_sub_field('contatos'))://verifica conteudo no repeater
                
                
                echo "<div class='col-sm-12 contacts-list' id='" . $count2 . "'>";

                    // Contato principal	
                    $principal = get_sub_field('contato_principal');

                    foreach($principal as $contato){

                        echo '<h2>' . get_the_title($contato) . '</h2>';

                        echo "<div class='col-sm-12 mb-3'>";

                            // pega os campos de cada contato
                            $rows = get_field('campos_contato', $contato);
                            
                            if( $rows ) {
                                
                                foreach( $rows as $row ) {
                                    // verifica se os campos estao vazios
                                    if( ($row['nome_campo'] && $row['nome_campo'] != '')  || ($row['informacao_campo'] && $row['informacao_campo'] != '') ){
                                        
                                        // verifica o tipo do campos
                                        if($row['tipo_de_campo'] == 'telefone'){
                                            
                                            $telefone = $row['informacao_campo']; // pega o campo telefone
                                            $telefone = preg_replace('/[^A-Za-z0-9\-]/', '', $telefone); // remove os caracteres especiais
                                            $telefone = str_replace('-', '', $telefone); // troca o - por vazio

                                            echo "<p class='mb-0'><strong>" . $row['nome_campo'] . "</strong>: <a href='tel:" . $telefone ."'>" . $row['informacao_campo'] . "</a></p>";
                                        } elseif($row['tipo_de_campo'] == 'email'){

                                            echo "<p class='mb-0'><strong>" . $row['nome_campo'] . "</strong>: <a href='mailto:" . $row['informacao_campo'] ."'>" . $row['informacao_campo'] . "</a></p>";
                                        
                                        } elseif($row['tipo_de_campo'] == 'url'){

                                            echo "<p class='mb-0'><strong>" . $row['nome_campo'] . "</strong>: <a href='" . $row['informacao_campo'] ."'>" . $row['informacao_campo'] . "</a></p>";
                                        
                                        } elseif($row['tipo_de_campo'] == 'sub'){
                                                
                                            echo "<h3 class='mb-0 mt-2'><strong>" . $row['nome_campo'] . "</strong></h3>";
                                        } else {
                                            $nome = $row['nome_campo'];
                                            $info = $row['informacao_campo'];

                                            echo "<p class='mb-0'>";
                                            if($nome){
                                                echo "<strong>" . $row['nome_campo'] . "</strong>";
                                            }

                                            if($nome && $info){
                                                echo ": ";
                                            }

                                            if($info){
                                                echo $row['informacao_campo'];
                                            }
                                                
                                            echo "</p>";
                                        }
                                    }
                                    
                                }
                                
                            }

                        echo "</div>";

                    }

                

                    
                    // Contato secundario	
                    $secundario = get_sub_field('contato_secundario');
                    if($secundario && $secundario != ''):
                        echo "<div class='col-sm-12'>";

                            echo "<div class='row d-flex align-items-stretch'>";

                                foreach($secundario as $contato){

                                    echo "<div class='col-12 col-sm-6 col-md-4 mb-3 d-flex second-contact'>";
                                        echo '<div class="border p-3 rounded w-100">';

                                        echo '<h3>' . get_the_title($contato) . '</h3>';
                                    
                                        // pega os campos de cada contato
                                        $rows = get_field('campos_contato', $contato);
                                        
                                        if( $rows ) {
                                            
                                            foreach( $rows as $row ) {
                                                // verifica se os campos estao vazios
                                                if( ($row['nome_campo'] && $row['nome_campo'] != '')  || ($row['informacao_campo'] && $row['informacao_campo'] != '') ){
                                                    
                                                    // verifica o tipo do campos
                                                    if($row['tipo_de_campo'] == 'telefone'){
                                                        
                                                        $telefone = $row['informacao_campo']; // pega o campo telefone
                                                        $telefone = preg_replace('/[^A-Za-z0-9\-]/', '', $telefone); // remove os caracteres especiais
                                                        $telefone = str_replace('-', '', $telefone); // troca o - por vazio

                                                        echo "<p class='mb-0'><strong>" . $row['nome_campo'] . "</strong>: <a href='tel:" . $telefone ."'>" . $row['informacao_campo'] . "</a></p>";
                                                    } elseif($row['tipo_de_campo'] == 'email'){

                                                        echo "<p class='mb-0'><strong>" . $row['nome_campo'] . "</strong>: <a href='mailto:" . $row['informacao_campo'] ."'>" . $row['informacao_campo'] . "</a></p>";
                                                    
                                                    } elseif($row['tipo_de_campo'] == 'url'){

                                                        echo "<p class='mb-0'><strong>" . $row['nome_campo'] . "</strong>: <a href='" . $row['informacao_campo'] ."'>" . $row['informacao_campo'] . "</a></p>";
                                                    
                                                    } elseif($row['tipo_de_campo'] == 'sub'){
                                                
                                                        echo "<h3 class='mb-0 mt-2'><strong>" . $row['nome_campo'] . "</strong></h3>";
                                                    } else {
                                                        $nome = $row['nome_campo'];
                                                        $info = $row['informacao_campo'];
        
                                                        echo "<p class='mb-0'>";
                                                        if($nome){
                                                            echo "<strong>" . $row['nome_campo'] . "</strong>";
                                                        }
        
                                                        if($nome && $info){
                                                            echo ": ";
                                                        }
        
                                                        if($info){
                                                            echo $row['informacao_campo'];
                                                        }
                                                            
                                                        echo "</p>";
                                                    }
                                                }
                                                
                                            }
                                            
                                        }

                                        echo "</div>"; // end border

                                    echo "</div>";

                                } // end foreach

                            echo "</div>"; // end row													 

                        echo "</div>"; // end col-sm-12
                    endif;

                    echo "<p class='mb-0 mt-3 text-right'><a href='#lista-contatos'>voltar para o topo</a></p>";
                    echo "<hr>";

                echo "</div>";

                //echo get_sub_field('contato_principal') . "<br>";
                //echo get_sub_field('contato_secundario') . "<br>";

                $count2++;
            endwhile;

        echo '</div>';	
    echo '</div>';		
endif;