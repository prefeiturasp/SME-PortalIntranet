<div class="container">
    <div class="row">
        <div class="col-12">

            <?php
            global $has_posts;
            $urlPage = get_the_permalink();
            $paged = 1;
            if ( get_query_var('paged') ) $paged = get_query_var('paged');
            if ( get_query_var('page') ) $paged = get_query_var('page');
            
            $sticky = get_option( 'sticky_posts' );
            $categorias = get_sub_field('fx_noticias_1_1');
            $exibir_data = get_sub_field('exibir_data');
            $exibicao = get_sub_field('sel_exib');
            $current_date = date('Ymd');
                                   
            $args_for_query1 = array(
                'post_type' => ['post', 'cortesias'],
                'fields' => 'ids',
                'posts_per_page' => -1,
                'paged' => $paged,                
                'post__in'  => $sticky,     
            );

            if (!isset($args_for_query1['tax_query'])) {
                $args_for_query1['tax_query'] = array();
            }

            // Filtro por CATEGORIA (se existir)
            if ($categorias) {
                $args_for_query1['tax_query'][] = array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $categorias,
                );
            }

            // Filtro por TAG (se passado via $_GET)
            if (isset($_GET['local']) && !empty($_GET['local'])) {
                $tag_id = intval($_GET['local']); 
                
                $args_for_query1['tax_query'][] = array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $tag_id,
                );
            }
            
            if (count($args_for_query1['tax_query']) > 1) {
                $args_for_query1['tax_query']['relation'] = 'AND';
            }

            if($exibir_data && $exibicao == 'proximo'){
                $args_for_query1['meta_query'] = array(
                    array(
                        'key' => 'enc_inscri', // Nome do campo personalizado
                        'value' => $current_date,
                        'compare' => '>=', // Maior que a data atual
                        'type' => 'DATE', 
                    ),
                );
            }

            if($exibir_data && $exibicao == 'encerrados'){
                $args_for_query1['meta_query'] = array(
                    array(
                        'key' => 'enc_inscri', // Nome do campo personalizado
                        'value' => $current_date,
                        'compare' => '<', // Menor que a data atual
                        'type' => 'DATE', 
                    ),
                );
            }

            $args_for_query2 = array(
                'post_type' => ['post', 'cortesias'],
                'fields' => 'ids',
                'posts_per_page' => -1,
                'paged' => $paged,
                'post__not_in' => $sticky,   
            );

            if (!isset($args_for_query2['tax_query'])) {
                $args_for_query2['tax_query'] = array();
            }

            // Filtro por CATEGORIA (se existir)
            if ($categorias) {
                $args_for_query2['tax_query'][] = array(
                    'taxonomy' => 'category',
                    'field'    => 'term_id',
                    'terms'    => $categorias,
                );
            }

            // Filtro por TAG (se passado via $_GET)
            if (isset($_GET['local']) && !empty($_GET['local'])) {
                $tag_id = intval($_GET['local']); 
                
                $args_for_query2['tax_query'][] = array(
                    'taxonomy' => 'post_tag',
                    'field'    => 'term_id',
                    'terms'    => $tag_id,
                );
            }
            
            if (count($args_for_query2['tax_query']) > 1) {
                $args_for_query2['tax_query']['relation'] = 'AND';
            }

            if($exibir_data && $exibicao == 'proximo'){
                // Inicializa o meta_query
				$args_for_query2['meta_query'] = array();

                $data_inicio = isset($_GET['dataInicio']) ? sanitize_text_field($_GET['dataInicio']) : '';
                $data_fim = isset($_GET['dataFim']) ? sanitize_text_field($_GET['dataFim']) : '';
                
                $data_inicio = $data_inicio ? date('Y-m-d', strtotime($data_inicio)) : '';
                $data_fim = $data_fim ? date('Y-m-d', strtotime($data_fim)) : '';

                // Converte para timestamp e aplica validação
                $timestamp_inicio = $data_inicio ? strtotime($data_inicio) : false;
                $timestamp_fim = $data_fim ? strtotime($data_fim) : false;
                $timestamp_atual = strtotime($current_date);

                // Ajusta as datas para não serem menores que a atual
                if ($timestamp_inicio && $timestamp_inicio < $timestamp_atual) {
                    $data_inicio = $current_date;
                }
                
                if ($timestamp_fim && $timestamp_fim < $timestamp_atual) {
                    $data_fim = $current_date;
                }
                
                // Se NÃO houver filtro por data
                if (empty($data_inicio) && empty($data_fim)) {
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $current_date,
                        'compare' => '>=',
                        'type' => 'DATE',
                    );
                }
                
                // Se houver APENAS dataInicio
                elseif (!empty($data_inicio) && empty($data_fim)) {
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_inicio,
                        'compare' => '>=',
                        'type' => 'DATE',
                    );
                }

                // Se houver APENAS dataFim
                elseif (empty($data_inicio) && !empty($data_fim)) {
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $current_date,
                        'compare' => '>=',
                        'type' => 'DATE',
                    );
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_fim,
                        'compare' => '<=',
                        'type' => 'DATE',
                    );
                }

                // Se houver AMBOS dataInicio e dataFim
                elseif (!empty($data_inicio) && !empty($data_fim)) {
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_inicio,
                        'compare' => '>=',
                        'type' => 'DATE',
                    );
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_fim,
                        'compare' => '<=',
                        'type' => 'DATE',
                    );
                }
                
                if (count($args_for_query2['meta_query']) > 1) {
                    $args_for_query2['meta_query']['relation'] = 'AND';
                }
            }

            if($exibir_data && $exibicao == 'encerrados'){
                // Inicializa o meta_query
                $args_for_query2['meta_query'] = array();

                // Verifica se recebeu dataInicio e dataFim via GET
                $data_inicio = isset($_GET['dataInicio']) ? sanitize_text_field($_GET['dataInicio']) : '';
                $data_fim = isset($_GET['dataFim']) ? sanitize_text_field($_GET['dataFim']) : '';

                // Se NÃO houver filtro por data (nenhum parâmetro enviado)
                if (empty($data_inicio) && empty($data_fim)) {
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $current_date,
                        'compare' => '<',
                        'type' => 'DATE',
                    );
                }

                // Se houver APENAS dataInicio
                elseif (!empty($data_inicio) && empty($data_fim)) {
                    $args_for_query2['meta_query']['relation'] = 'AND';
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_inicio,
                        'compare' => '>=',
                        'type' => 'DATE',
                    );
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $current_date,
                        'compare' => '<',
                        'type' => 'DATE',
                    );
                }

                // Se houver APENAS dataFim
                elseif (empty($data_inicio) && !empty($data_fim)) {
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_fim,
                        'compare' => '<', // enc_inscri < dataFim (não precisa comparar com current_date)
                        'type' => 'DATE',
                    );
                }

                // Se houver AMBOS dataInicio e dataFim
                elseif (!empty($data_inicio) && !empty($data_fim)) {
                    
                    // Converter para timestamps antes de comparar
                    $timestamp_inicio = strtotime($data_inicio);
                    $timestamp_fim = strtotime($data_fim);
                    $timestamp_atual = strtotime($current_date);

                    // Ajustar as datas
                    $data_inicio_ajustada = ($timestamp_inicio > $timestamp_atual) ? date('Y-m-d', $timestamp_atual) : $data_inicio;
                    $data_fim_ajustada = ($timestamp_fim > $timestamp_atual) ? date('Y-m-d', $timestamp_atual) : $data_fim;
                                        
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_inicio_ajustada,
                        'compare' => '>=',
                        'type' => 'DATE',
                    );
                    $args_for_query2['meta_query'][] = array(
                        'key' => 'enc_inscri',
                        'value' => $data_fim_ajustada,
                        'compare' => '<',
                        'type' => 'DATE',
                    );
                }

                // Se houver mais de uma condição no meta_query, defina a relação
                if (count($args_for_query2['meta_query']) > 1) {
                    $args_for_query2['meta_query']['relation'] = 'AND';
                }
            }

            //setup your queries as you already do
            $query1 = new WP_Query($args_for_query1);
            $query2 = new WP_Query($args_for_query2);

            $allTheIDs = array_merge($query1->posts,$query2->posts);

             if($exibicao == 'encerrados'){
                $args = array(
                    'post_type' => ['post', 'cortesias'],
                    'post__in' => $allTheIDs,
                    'posts_per_page' => get_sub_field('qtd'),
                    'paged' => $paged,
                    'ignore_sticky_posts' => 1,
                    'meta_key' => 'enc_inscri',
                    'orderby' => 'meta_value_num', 
                    'order' => 'DESC'
                );
            } else {
                $args = array(
                    'post_type' => ['post', 'cortesias'],
                    'post__in' => $allTheIDs,
                    'posts_per_page' => get_sub_field('qtd'),
                    'paged' => $paged,
                    'ignore_sticky_posts' => 1,
                    'orderby' => 'post__in' 
                );
            }

            //create new empty query and populate it with the other two
            $the_query = new WP_Query($args);
            
            ?>
            <?php if($exibir_data && $exibicao == 'proximo' && $timestamp_fim < $timestamp_atual && !empty($data_fim) ): ?>
                <div class="no-results">
                    <h2 class="search-title">
                        <span class="azul-claro-acervo"><strong>0</strong></span><strong> 
                            resultados</strong>
                    </h2>
                    <img src="https://educacao.sme.prefeitura.sp.gov.br/wp-content/themes/sme-portal-institucional/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado" class="img-fluid">
                    <p>Nenhum evento encontrado para os filtros informados</p>
                </div>
            <?php else : ?>

                <?php if ( $the_query->have_posts() ) : ?>
                    <?php $has_posts = true; ?>
                    <!-- pagination here -->
                    <div class="row">
                        <!-- the loop -->
                        <?php while ( $the_query->have_posts() ) : $the_query->the_post(); ?>
                        
                            <div class="col-12 col-md-4 mb-4">
                                <div class="mural sme-informe p-0 d-flex">
                                    <div class="row m-0">
                                        <div class="col-12 img-column mb-3 p-0">
                                            <?php 
                                                $image = get_the_post_thumbnail( $post_id, 'default-image', array( 'class' => 'img-fluid' ) );
                                                $post_type = get_post_type_label( get_the_ID() );
                                            ?>
                                            <?php if($image): ?>
                                                <?= $image; ?>
                                            <?php else: ?>
                                                <img src="<?= get_template_directory_uri(); ?>/img/categ-destaques.jpg" class="img-fluid rounded" alt="Imagem de ilustração categoria">
                                            <?php endif; ?>
                                            <?php if ( $post_type ) : ?>
                                                <span class="post-type-tag position-absolute">
                                                    <?php echo esc_html( mb_strtoupper( $post_type ) ); ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>

                                        <div class="col-12">
                                        <?php if ( $post_type === 'sorteio' ) : ?>
                                            <p class="data">
                                                <?php
                                                    $dataSorteio = get_field('data_sorteio', get_the_ID());
                                                    $dataSorteio =  $exibicao === 'encerrados' ? obter_ultima_data_sorteio( get_the_ID() ) : obter_proxima_data_sorteio( get_the_ID() );
                                                    if($dataSorteio){
                                                        $texto_subtitulo = $exibicao === 'encerrados' ? 'Sorteio' : 'O sorteio será realizado';
                                                        echo $texto_subtitulo . ' ' . $dataSorteio;	
                                                    }
                                                ?>
                                            </p>
                                        <?php endif; ?>
                                        <?php if ( $post_type === 'cortesias' ) : ?>
                                            <?php if ( $exibicao === 'encerrados' ) : ?>
                                                <p class="data">
                                                    Evento encerrado. Consulte mais detalhes na notícia
                                                </p>
                                            <?php else : ?>
                                                <p class="data">
                                                    Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade
                                                </p>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                        </div>

                                        <div class="col-12 col-md-9 mb-2">                                        
                                            <h2><a href="<?= get_the_permalink(); ?>"><?= $status_prefix . get_the_title(); ?></a></h2>                                                            
                                        </div>

                                        <div class="col-12 col-md-3 mb-2">
                                            <div class="likes">
                                                <?php 
                                                    global $wpdb;
                                                    $l = 0;
                                                    $postid = get_the_id();
                                                    $clientip  = get_client_ip();
                                                    $row1 = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid' AND clientip = '$clientip'");
                                                    if(!empty($row1)){
                                                        $l = 1;
                                                    }
                                                    $totalrow1 = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid'");
                                                    $total_like1 = $wpdb->num_rows;
                                                ?>

                                                <div class="post_like">
                                                    <a class="pp_like <?php if($l==1) {echo "likes"; } ?>" id="pp_like_<?php echo get_the_id(); ?>" href="#" data-id="<?php echo get_the_id(); ?>"><span><?php echo $total_like1; ?> <?php echo $total_like1 == 1 ? 'like' : 'likes'; ?></span><br><i class="fa fa-heart" aria-hidden="true"></i></a>	
                                                </div>

                                            </div>
                                        </div>

                                    </div>
                                    
                                </div>
                            </div>
                        <?php endwhile; ?>
                        <!-- end of the loop -->
                    </div>
                
                    <div class="container mt-4">
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="pagination-prog text-center">
                                    <?php wp_pagenavi( array( 'query' => $the_query ) ); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                
                    <?php wp_reset_postdata(); ?>
                
                <?php else : ?>
                    <div class="no-results">
                        <h2 class="search-title">
                            <span class="azul-claro-acervo"><strong>0</strong></span><strong> 
                                resultados</strong>
                        </h2>
                        <img src="https://educacao.sme.prefeitura.sp.gov.br/wp-content/themes/sme-portal-institucional/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado" class="img-fluid">
                        <p>Nenhum evento encontrado para os filtros informados</p>
                    </div>
                <?php endif; ?>
            
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if($has_posts): ?>
	<style>
		.no-results{
			display: none;
		}
	</style>	
<?php endif; ?>