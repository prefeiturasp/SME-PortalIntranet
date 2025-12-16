<?php

    global $has_posts;
    $titulo = get_sub_field('titulo');
    $pagSorteio = get_sub_field('pag_sorteios');
    $pagBusca = get_sub_field('pag_busca');

    wp_enqueue_style('select2-css');        
    wp_enqueue_script('select2-js');
    $tags = get_tags(array(
        'hide_empty' => false, // Inclui tags mesmo que não tenham posts associados
    ));

    function formatar_data($data_original) {
        if (empty($data_original)) return '';
        
        try {
            $data = new DateTime($data_original);
            return $data->format('d/m/Y');
        } catch (Exception $e) {
            return $data_original;
        }
    }

    $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'aberto'; // padrão "aberto"
?>

<div class="container">
    <div class="row">
        <div class="col-sm-12">

            <nav>
                <div class="nav nav-tabs" id="nav-tab" role="tablist">
                    <button 
                        class="nav-link <?= $filtro === 'aberto' ? 'active' : '' ?>" 
                        id="sort-ativos-tab" 
                        data-toggle="tab" 
                        data-target="#sort-ativos" 
                        type="button" 
                        role="tab" 
                        aria-controls="sort-ativos" 
                        aria-selected="<?= $filtro === 'aberto' ? 'true' : 'false' ?>"
                    >
                        Inscrições Abertas
                    </button>
                    <button 
                        class="nav-link <?= $filtro === 'encerrado' ? 'active' : '' ?>" 
                        id="sort-encerrados-tab" 
                        data-toggle="tab" 
                        data-target="#sort-encerrados" 
                        type="button" 
                        role="tab" 
                        aria-controls="sort-encerrados" 
                        aria-selected="<?= $filtro === 'encerrado' ? 'true' : 'false' ?>"
                    >
                        Inscrições Encerradas
                    </button>
                </div>
            </nav>

            <div class="tab-content" id="nav-tabContent">

                <div class="tab-pane fade <?= $filtro === 'aberto' ? 'show active' : '' ?>" id="sort-ativos" role="tabpanel" aria-labelledby="sort-ativos-tab">
                    <form action="<?= $pagBusca; ?>" method="get" class="filtro-sorteios">
                
                        <?php if($titulo): ?>
                            <div class="title-form">
                                <h2><?= $titulo; ?></h2>
                            </div>
                        <?php endif; ?>

                        <div class="form-row mb-2">
                            <div class="col">
                                <label for="nome-evento-ativo" class="form-label">Busque pelo Nome do Evento</label>
                                <input type="text" class="form-control" name="nome-evento" id="nome-evento-ativo" placeholder="Digite o nome ou parte do nome do evento" value="<?php echo isset($_GET['nome-evento']) ? esc_attr($_GET['nome-evento']) : ''; ?>">
                            </div>
                            <div class="invalid-feedback fieldError" style="display: none;">
                                Preencha ao menos um dos campos do formulário.
                            </div>
                        </div>

                        <div class="form-row"> 
                            
                            <div class="col-md-6 date-group mb-2">
                                <label class="form-label" for="dataInicio">Busque por intervalo de datas</label>
                                <div class="form-row align-items-center">                            
                                    <div class="col-12 col-sm">                                
                                        <input 
                                            type="date" 
                                            class="form-control" 
                                            name="dataInicio" 
                                            id="dataInicio"
                                            value="<?php echo isset($_GET['dataInicio']) ? esc_attr($_GET['dataInicio']) : ''; ?>"
                                        >
                                    </div>
                                    <div class="col-12 col-sm-auto align-self-end text-center">
                                        <span class="text-muted">até</span>
                                    </div>
                                    <div class="col-12 col-sm">                                
                                        <input 
                                            type="date" 
                                            class="form-control" 
                                            name="dataFim" 
                                            id="dataFim"
                                            value="<?php echo isset($_GET['dataFim']) ? esc_attr($_GET['dataFim']) : ''; ?>"
                                        >
                                    </div>
                                </div>

                                <div class="invalid-feedback dataError" style="display: none;">
                                    A data de início não pode ser maior que a data final!
                                </div>
                            </div>

                            
                            
                            <div class="col-md-6 mb-2">
                                <label for="local-ativo" class="form-label">Busque pelo Local do Evento</label>

                                <select class="form-control select-local" id="local-ativo" name="local">
                                    <option value=''>Digite ou selecione um local</option>
                                    <?php if ($tags) : ?>
                                        <?php foreach ($tags as $tag) : ?>
                                            <option 
                                                value="<?php echo esc_attr($tag->term_id); ?>" 
                                                <?php echo (isset($_GET['local']) && $_GET['local'] == $tag->term_id) ? 'selected' : ''; ?>
                                            >
                                                <?php echo esc_html($tag->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                        </div>
                        
                        <!-- Botão de submit (opcional) -->
                        <div class="form-row mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <input type="hidden" name="filtro" value="aberto">
                                <a href="<?= $pagSorteio; ?>" class="btn btn-outline-primary mr-2">Limpar filtros</a>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Buscar Eventos</button>
                            </div>
                        </div>

                    </form>
                </div>

                <div class="tab-pane fade <?= $filtro === 'encerrado' ? 'show active' : '' ?>" id="sort-encerrados" role="tabpanel" aria-labelledby="sort-encerrados-tab">
                    <form action="<?= $pagBusca; ?>" method="get" class="filtro-sorteios">
                
                        <?php if($titulo): ?>
                            <div class="title-form">
                                <h2><?= $titulo; ?></h2>
                            </div>
                        <?php endif; ?>

                        <div class="form-row mb-2">
                            <div class="col">
                                <label for="nome-evento" class="form-label">Busque pelo Nome do Evento</label>
                                <input type="text" class="form-control" name="nome-evento" id="nome-evento" placeholder="Digite o nome ou parte do nome do evento" value="<?php echo isset($_GET['nome-evento']) ? esc_attr($_GET['nome-evento']) : ''; ?>">
                            </div>
                            <div class="invalid-feedback fieldError" style="display: none;">
                                Preencha ao menos um dos campos do formulário.
                            </div>
                        </div>

                        <div class="form-row"> 
                            
                            <div class="col-md-6 date-group mb-2">
                                <label class="form-label" for="dataInicio">Busque por intervalo de datas</label>
                                <div class="form-row align-items-center">                            
                                    <div class="col-12 col-sm">                                
                                        <input 
                                            type="date" 
                                            class="form-control" 
                                            name="dataInicio" 
                                            id="dataInicio"
                                            value="<?php echo isset($_GET['dataInicio']) ? esc_attr($_GET['dataInicio']) : ''; ?>"
                                        >
                                    </div>
                                    <div class="col-12 col-sm-auto align-self-end text-center">
                                        <span class="text-muted">até</span>
                                    </div>
                                    <div class="col-12 col-sm">                                
                                        <input 
                                            type="date" 
                                            class="form-control" 
                                            name="dataFim" 
                                            id="dataFim"
                                            value="<?php echo isset($_GET['dataFim']) ? esc_attr($_GET['dataFim']) : ''; ?>"
                                        >
                                    </div>
                                </div>

                                <div class="invalid-feedback dataError" style="display: none;">
                                    A data de início não pode ser maior que a data final!
                                </div>
                            </div>

                            
                            
                            <div class="col-md-6 mb-2">
                                <label for="opcoes" class="form-label">Busque pelo Local do Evento</label>

                                <select class="form-control select2-search" id="opcoes" name="local">
                                    <option value=''>Digite ou selecione um local</option>
                                    <?php if ($tags) : ?>
                                        <?php foreach ($tags as $tag) : ?>
                                            <option 
                                                value="<?php echo esc_attr($tag->term_id); ?>" 
                                                <?php echo (isset($_GET['local']) && $_GET['local'] == $tag->term_id) ? 'selected' : ''; ?>
                                            >
                                                <?php echo esc_html($tag->name); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                        </div>
                        
                        <!-- Botão de submit (opcional) -->
                        <div class="form-row mt-3">
                            <div class="col-12 d-flex justify-content-end">
                                <input type="hidden" name="filtro" value="encerrado">
                                <a href="<?= $pagSorteio; ?>" class="btn btn-outline-primary mr-2">Limpar filtros</a>
                                <button type="submit" class="btn btn-primary"><i class="fa fa-search" aria-hidden="true"></i> Buscar Eventos</button>
                            </div>
                        </div>

                    </form>
                </div>

            </div>
            
        </div>
        
    </div>
</div>

<?php
// Array para armazenar as partes do texto
$partes_texto = [];

if(isset($_GET['nome-evento']) && $_GET['nome-evento'] != ''){
    $partes_texto[] = sanitize_text_field($_GET['nome-evento']);
}

// Verifica e processa as datas
$data_inicio = isset($_GET['dataInicio']) ? sanitize_text_field($_GET['dataInicio']) : '';
$data_fim = isset($_GET['dataFim']) ? sanitize_text_field($_GET['dataFim']) : '';

if (!empty($data_inicio) && !empty($data_fim)) {
    if (!empty($partes_texto)) {
            $partes_texto[] = "| " . formatar_data($data_inicio) . " até " . formatar_data($data_fim);
    } else {
        $partes_texto[] = formatar_data($data_inicio) . " até " . formatar_data($data_fim);
    }    
}

// Verifica e processa o local (tag)
if (isset($_GET['local']) && !empty($_GET['local'])) {
    $tag_id = intval($_GET['local']);
    $tag = get_tag($tag_id);
    
    if ($tag && !is_wp_error($tag)) {
        if (!empty($partes_texto)) {
            $partes_texto[] = "| " . $tag->name;
        } else {
            $partes_texto[] = $tag->name;
        }
    }
}

// Monta o texto final se houver filtros
if (!empty($partes_texto)) {
    if($_GET['filtro'] == 'aberto'){
        $textoTitulo = "EVENTOS - Inscrições Abertas";
        $status_prefix = '';
    } else if($_GET['filtro'] == 'encerrado'){
        $textoTitulo = "EVENTOS - Inscrições Encerradas";
        $status_prefix = 'ENCERRADO - ';
    }
    echo '<div class="container">';
        echo '<div class="row">';
            echo '<div class="col-sm-12">';
                echo '<div class="resultados-filtro mb-4">';
                    echo '<a href="' . $pagSorteio . '"><strong>' . $textoTitulo . '</strong></a> / Resultados para: ' . implode(' ', $partes_texto);
                echo '</div>';
            echo '</div>';
        echo '</div>';
    echo '</div>';
}
?>

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
                $current_date = date('Ymd');
                
                // Busca para sorteios fixos (sticky)
                $args_for_query1 = array(
                    'post_type' => ['post', 'cortesias'],
                    'fields' => 'ids',
                    'posts_per_page' => -1,
                    'paged' => $paged,                
                    'post__in'  => $sticky,     
                );

                // Busca para sorteios não fixos (sticky)
                $args_for_query2 = array(
                    'post_type' => ['post', 'cortesias'],
                    'fields' => 'ids',
                    'posts_per_page' => -1,
                    'paged' => $paged,
                    'post__not_in' => $sticky,   
                );

                if( isset($_GET['nome-evento']) && $_GET['nome-evento'] != ''){
                    $args_for_query1['s'] = $_GET['nome-evento'];
                    $args_for_query2['s'] = $_GET['nome-evento'];
                }

                // Filtro por TAG (se passado via $_GET)
                if (isset($_GET['local']) && !empty($_GET['local'])) {
                    $tag_id = intval($_GET['local']); 
                    
                    $args_for_query1['tax_query'][] = array(
                        'taxonomy' => 'post_tag',
                        'field'    => 'term_id',
                        'terms'    => $tag_id,
                    );

                    $args_for_query2['tax_query'][] = array(
                        'taxonomy' => 'post_tag',
                        'field'    => 'term_id',
                        'terms'    => $tag_id,
                    );
                }
                
                if( isset($_GET['dataInicio']) && $_GET['dataInicio'] != '' && isset($_GET['dataFim']) && $_GET['dataFim'] != ''){
                    $data_inicial = $_GET['dataInicio'] . ' 00:00:00';
                    $data_final   = $_GET['dataFim'] . ' 23:59:59';

                    $args_for_query1['meta_query'][] = array(
                        'relation' => 'OR',
                        [
                            'key'     => 'evento_datas_$_data', // % pega qualquer índice do repeater
                            'value'   => [ $data_inicial, $data_final ],
                            'compare' => 'BETWEEN',
                            'type'    => 'DATETIME',
                        ],
                        [
                            'key'     => 'evento_datas_$_data',
                            'compare' => 'NOT EXISTS',
                        ],
                    );

                    $args_for_query2['meta_query'][] = array(
                        'relation' => 'OR',
                        [
                            'key'     => 'evento_datas_$_data', // % pega qualquer índice do repeater
                            'value'   => [ $data_inicial, $data_final ],
                            'compare' => 'BETWEEN',
                            'type'    => 'DATETIME',
                        ],
                        [
                            'key'     => 'evento_datas_$_data',
                            'compare' => 'NOT EXISTS',
                        ],
                    );
                }

                // Data de hoje no mesmo formato que o ACF salva (ex: Y-m-d ou Y-m-d H:i:s)
                $hoje = date('Y-m-d'); // se o campo salva só data
                //$hoje = date('Y-m-d H:i:s'); // se salva data + hora

                if ( isset($_GET['filtro']) && !empty($_GET['filtro']) ) {
                    $filtro = $_GET['filtro'];

                    if ( $filtro === 'aberto' ) {
                        $args_for_query1['meta_query'][] = [
                            'key'     => 'enc_inscri',
                            'value'   => $hoje,
                            'compare' => '>=',
                            'type'    => 'DATE', // ou 'DATETIME' se o ACF salva com hora
                        ];
                        $args_for_query2['meta_query'][] = [
                            'key'     => 'enc_inscri',
                            'value'   => $hoje,
                            'compare' => '>=',
                            'type'    => 'DATE',
                        ];
                    }

                    if ( $filtro === 'encerrado' ) {
                        $args_for_query1['meta_query'][] = [
                            'key'     => 'enc_inscri',
                            'value'   => $hoje,
                            'compare' => '<',
                            'type'    => 'DATE',
                        ];
                        $args_for_query2['meta_query'][] = [
                            'key'     => 'enc_inscri',
                            'value'   => $hoje,
                            'compare' => '<',
                            'type'    => 'DATE',
                        ];
                    }
                }
                
                // Antes de criar a query, adiciona o filtro
                add_filter('posts_where', 'wpza_replace_repeater_field');

                if(isset($_GET['filtro'])){
                    // Cria a query normalmente
                    $query1 = new WP_Query($args_for_query1);
                    $query2 = new WP_Query($args_for_query2);
                }

                // Depois de criar a query, remove o filtro para não afetar outras queries
                remove_filter('posts_where', 'wpza_replace_repeater_field');                

                if($query1 && $query2){
                    $allTheIDs = array_merge($query1->posts,$query2->posts);
                }

                if($exibicao == 'encerrados'){
                    $args = array(
                        'post_type' => ['post', 'cortesias'],
                        'post__in' => $allTheIDs,
                        'posts_per_page' => 15,
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
                        'posts_per_page' => 15,
                        'paged' => $paged,
                        'ignore_sticky_posts' => 1,
                        'orderby' => 'post__in' 
                    );
                }

                if( !empty($allTheIDs)){
                    $the_query = new WP_Query($args);
                }
               
            ?>

            <?php if ( $the_query && $the_query->have_posts() ) : ?>
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
                                                $dataSorteio =  ($filtro && $filtro === 'encerrado') ? obter_ultima_data_sorteio( get_the_ID() ) : obter_proxima_data_sorteio( get_the_ID() );
                                                if($dataSorteio){
                                                    $texto_subtitulo = ($filtro && $filtro === 'encerrado') ? 'Sorteio' : 'O sorteio será realizado';
                                                    echo $texto_subtitulo . ' ' . $dataSorteio;	
                                                }
                                            ?>
                                        </p>
                                    <?php endif; ?>

                                    <?php if ( $post_type === 'cortesias' ) : ?>
                                        <p class="data">
                                            <?php
                                            echo ($filtro && $filtro === 'encerrado')
                                                ? 'Evento encerrado. Consulte mais detalhes na notícia'
                                                : 'Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade';  
                                            ?>
                                        </p>
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
            
            <?php elseif(!$the_query && isset($_GET['filtro'])): ?>
                <div class="no-results">
                    <h2 class="search-title">
                        <span class="azul-claro-acervo"><strong>0</strong></span><strong> 
                            resultados</strong>
                    </h2>
                    <img src="https://educacao.sme.prefeitura.sp.gov.br/wp-content/themes/sme-portal-institucional/img/search-empty.png" alt="Imagem ilustrativa para nenhum resultado de busca encontrado" class="img-fluid">
                    <p>Nenhum evento encontrado para os filtros informados</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script>

    jQuery(document).ready(function() {

        // Inicializa o Select2
        jQuery('.select-local, .select2-search').select2({
            placeholder: "Digite ou selecione um local",
            allowClear: true,
            width: '100%',
            language: {
                noResults: function() {
                    return "Nenhum local encontrado";
                },
                searching: function() {
                    return "Buscando…";
                },
                inputTooShort: function() {
                    return "Digite pelo menos um caractere";
                },
            }
        });

        

        jQuery('.tab-pane.active .select2-search').trigger('change.select2');

        // Validação de datas
        jQuery('.filtro-sorteios').on('submit', function(e) {
            const form = jQuery(this);

            const nomeEvento = form.find('input[name="nome-evento"]').val().trim();
            const dataInicio = form.find('input[name="dataInicio"]').val();
            const dataFim = form.find('input[name="dataFim"]').val();
            const local = form.find('select[name="local"]').val();

            const errorMsgData = form.find('.dataError');   // mensagens de data
            const errorMsgField = form.find('.fieldError'); // mensagens gerais
            const inputInicio = form.find('input[name="dataInicio"]');
            const inputFim = form.find('input[name="dataFim"]');

            // Reset de erros visuais
            errorMsgData.hide();
            errorMsgField.hide();
            inputInicio.removeClass('is-invalid');
            inputFim.removeClass('is-invalid');

            let hasError = false;

            // 1. Pelo menos um campo deve ser preenchido
            if (!nomeEvento && !dataInicio && !dataFim && !local) {
                hasError = true;
                errorMsgField.text("Preencha ao menos um dos campos do formulário.").show();
            }

            // 2. Se preencher apenas uma das datas → erro
            if ((dataInicio && !dataFim) || (!dataInicio && dataFim)) {
                hasError = true;
                errorMsgData.text("Preencha as duas datas.").show();
                if (!dataInicio) inputInicio.addClass('is-invalid');
                if (!dataFim) inputFim.addClass('is-invalid');
            }

            // 3. Se ambas preenchidas, validar intervalo
            if (dataInicio && dataFim) {
                const inicio = new Date(dataInicio);
                const fim = new Date(dataFim);

                if (inicio > fim) {
                    hasError = true;
                    errorMsgData.text("A data de início não pode ser maior que a final.").show();
                    inputInicio.addClass('is-invalid');
                    inputFim.addClass('is-invalid');
                }
            }

            if (hasError) {
                e.preventDefault(); // Impede envio
            }
        });


        // ---- Sincronização entre os dois forms ----
        function syncFields(selector) {
            jQuery(document).on('input change', selector, function(e, triggeredBySync) {
                if (triggeredBySync) return; // evita loop infinito

                const name = jQuery(this).attr('name');
                const value = jQuery(this).val();

                // Atualiza os outros campos de mesmo name
                jQuery(`input[name="${name}"], select[name="${name}"]`).not(this).each(function() {
                    if (jQuery(this).val() !== value) {
                        jQuery(this).val(value).trigger('change', [true]); 
                    }
                });
            });
        }

        // Sincronizar estes campos:
        syncFields('input[name="nome-evento"]');
        syncFields('input[name="dataInicio"]');
        syncFields('input[name="dataFim"]');
        syncFields('select[name="local"]');

    });

</script>