<?php
/**
 * Plugin Name: Taxonomy Custom Order
 * Description: Ordenação drag & drop para taxonomias customizadas
 * Version:     2.0.9
 */

defined('ABSPATH') || exit;

/* =========================================================
 * CONFIG
 * =======================================================*/
define('TCO_TAXONOMIES', [
    'locais',
    'coordenadorias',
    'eixos_atuacao'
]);

define('TCO_META_KEY', 'term_order');


/* =========================================================
 * FORÇAR ORDENAÇÃO
 * =======================================================*/
add_action('parse_term_query', function ($query) {
    
    if (!is_admin()) {
        return;
    }
    
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'edit-tags') {
        return;
    }
    
    $query_vars = $query->query_vars;
    
    if (empty($query_vars['taxonomy'])) {
        return;
    }
    
    $taxonomy = is_array($query_vars['taxonomy']) ? reset($query_vars['taxonomy']) : $query_vars['taxonomy'];
    
    if (!in_array($taxonomy, TCO_TAXONOMIES, true)) {
        return;
    }
    
    // Se usuário escolheu outra ordenação
    if (!empty($_GET['orderby']) && !in_array($_GET['orderby'], ['tco_order', 'custom_term_order', ''])) {
        return;
    }
    
    // Aplica ordenação customizada
    $query->query_vars['meta_key'] = TCO_META_KEY;
    $query->query_vars['orderby'] = 'meta_value_num';
    $query->query_vars['order'] = 'ASC';
    
    // Garante que todos os termos apareçam
    $query->query_vars['meta_query'] = [
        'relation' => 'OR',
        [
            'key' => TCO_META_KEY,
            'compare' => 'EXISTS'
        ],
        [
            'key' => TCO_META_KEY,
            'compare' => 'NOT EXISTS'
        ]
    ];
});

// Backup com pre_get_terms
add_action('pre_get_terms', function ($query) {
    
    if (!is_admin()) return;
    
    $screen = get_current_screen();
    if (!$screen || $screen->base !== 'edit-tags') return;
    
    $query_vars = $query->query_vars;
    
    if (empty($query_vars['taxonomy'])) return;
    
    $taxonomy = is_array($query_vars['taxonomy']) ? reset($query_vars['taxonomy']) : $query_vars['taxonomy'];
    
    if (!in_array($taxonomy, TCO_TAXONOMIES, true)) return;
    
    if (!empty($_GET['orderby']) && !in_array($_GET['orderby'], ['tco_order', 'custom_term_order', ''])) return;
    
    $query->query_vars['meta_key'] = TCO_META_KEY;
    $query->query_vars['orderby'] = 'meta_value_num';
    $query->query_vars['order'] = 'ASC';
    
    $query->query_vars['meta_query'] = [
        'relation' => 'OR',
        [
            'key' => TCO_META_KEY,
            'compare' => 'EXISTS'
        ],
        [
            'key' => TCO_META_KEY,
            'compare' => 'NOT EXISTS'
        ]
    ];
});


/* =========================================================
 * ADMIN ASSETS
 * =======================================================*/
add_action('admin_enqueue_scripts', function () {

    $screen = get_current_screen();

    if (!$screen || $screen->base !== 'edit-tags') return;

    if (!in_array($screen->taxonomy, TCO_TAXONOMIES, true)) return;

    wp_enqueue_script('jquery-ui-sortable');

    wp_add_inline_script('jquery-ui-sortable', tco_js(), 'after');
});


function tco_js()
{
    $nonce = wp_create_nonce('tco_save_order');
    $ajax  = admin_url('admin-ajax.php');

    return <<<JS
jQuery(function($){

    var \$tbody = $('table.wp-list-table tbody');

    if (!\$tbody.length) return;

    function applyHandles(){
        \$tbody.find('tr').each(function(){
            if ($(this).find('.tco-handle').length) return;

            $(this).find('td.name .row-title').before(
                '<span class="tco-handle dashicons dashicons-move"></span>'
            );
        });
    }

    applyHandles();

    \$tbody.sortable({
        handle: '.tco-handle',
        axis: 'y',
        opacity: 0.8,
        cursor: 'grabbing',
        placeholder: 'tco-placeholder',

        start: function(e, ui){
            ui.placeholder.height(ui.item.height());
        },

        update: function(event, ui) {
            // Atualiza os números da coluna de ordem em tempo real
            \$tbody.find('tr').each(function(index) {
                var \$orderCell = $(this).find('td.tco_order');
                if (\$orderCell.length) {
                    \$orderCell.find('small').text(index + 1);
                }
            });
        },

        stop: function(){

            var order = [];

            \$tbody.find('tr').each(function(index){

                var id = $(this).attr('id');

                if (!id || id.indexOf('tag-') !== 0) return;

                order.push({
                    id: id.replace('tag-', ''),
                    pos: index
                });
            });

            // Remove mensagens anteriores
            $('.tco-saved').remove();

            $.post('$ajax', {
                action: 'tco_save_order',
                nonce: '$nonce',
                taxonomy: new URLSearchParams(window.location.search).get('taxonomy'),
                order: order
            }).done(function(res){

                if(res.success){
                    
                    // Mostra mensagem de sucesso no item que foi movido
                    var \$movedItem = ui.item;
                    if (\$movedItem && \$movedItem.length) {
                        \$movedItem.find('td.name').append(
                            '<span class="tco-saved">✓ salvo</span>'
                        );
                    }

                    setTimeout(function(){
                        $('.tco-saved').fadeOut(300, function(){ $(this).remove(); });
                    }, 1500);
                }
            });
        }

    }).disableSelection();
    
    
    // ========================================
    // NOVO: Detecta quando um termo é adicionado via AJAX
    // ========================================
    
    // Flag para controlar o reload
    var isReloading = false;
    
    // Função que verifica se deve recarregar
    function checkForNewTerm() {
        if (isReloading) return;
        
        // Verifica se #ajax-response tem uma mensagem de sucesso
        var \$ajaxResponse = $('#ajax-response');
        
        if (\$ajaxResponse.length) {
            
            // Verifica se existe uma mensagem de sucesso (não erro)
            var \$successMessage = \$ajaxResponse.find('.notice-success, .updated');
            
            if (\$successMessage.length && \$successMessage.text().trim() !== '') {
                
                isReloading = true;
                
                // Aguarda 1 segundo e recarrega
                setTimeout(function() {
                    window.location.reload();
                }, 1000);
            }
        }
    }
    
    // Opção 1: Usar MutationObserver para detectar mudanças no #ajax-response
    var observerTarget = document.getElementById('ajax-response');
    
    if (observerTarget) {
        
        var observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                // Verifica se novos nós foram adicionados
                if (mutation.type === 'childList' && mutation.addedNodes.length > 0) {
                    
                    // Procura por mensagens de sucesso nos nós adicionados
                    mutation.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) { // Element node
                            var \$node = $(node);
                            
                            if (\$node.hasClass('notice-success') || 
                                \$node.find('.notice-success').length ||
                                \$node.hasClass('updated')) {
                                
                                checkForNewTerm();
                            }
                        }
                    });
                }
            });
        });
        
        // Configura o observer para monitorar adições de elementos filhos
        observer.observe(observerTarget, {
            childList: true,
            subtree: true
        });
    }
    
    // Opção 2: Backup - Monitora o submit do formulário
    $('#addtag').on('submit', function() {
        
        // Verifica várias vezes após o submit
        setTimeout(checkForNewTerm, 1500);
        setTimeout(checkForNewTerm, 3000);
        setTimeout(checkForNewTerm, 5000);
    });
    
    // Opção 3: Backup - Monitora cliques no botão
    $(document).on('click', '#submit', function() {
        setTimeout(checkForNewTerm, 1500);
        setTimeout(checkForNewTerm, 3000);
        setTimeout(checkForNewTerm, 5000);
    });
    
});
JS;
}

/* =========================================================
 * AJAX SAVE
 * =======================================================*/
add_action('wp_ajax_tco_save_order', function () {

    check_ajax_referer('tco_save_order', 'nonce');

    $taxonomy = sanitize_key($_POST['taxonomy'] ?? '');

    if (!in_array($taxonomy, TCO_TAXONOMIES, true)) {
        wp_send_json_error();
    }

    if (!current_user_can(get_taxonomy($taxonomy)->cap->manage_terms)) {
        wp_send_json_error();
    }

    $order = $_POST['order'] ?? [];

    foreach ($order as $item) {

        $term_id = absint($item['id']);
        $pos     = absint($item['pos']);

        if (!$term_id) continue;

        update_term_meta($term_id, TCO_META_KEY, $pos);
    }

    wp_send_json_success();
});


/* =========================================================
 * DEFAULT ORDER (NOVOS TERMOS)
 * =======================================================*/
add_action('created_term', function ($term_id, $tt_id, $taxonomy) {

    if (!in_array($taxonomy, TCO_TAXONOMIES, true)) return;
    
    $term = get_term($term_id, $taxonomy);
    
    if (is_wp_error($term) || !$term) return;
    
    // Reordena tudo primeiro para garantir sequência sem falhas
    tco_reorder_all_terms($taxonomy);
    
    // Se o termo tem pai (é subcategoria)
    if ($term->parent > 0) {
        
        // Busca a posição do termo pai
        $parent_order = (int) get_term_meta($term->parent, TCO_META_KEY, true);
        
        // Busca todos os filhos do mesmo pai para calcular a posição
        $siblings = get_terms([
            'taxonomy'   => $taxonomy,
            'hide_empty' => false,
            'parent'     => $term->parent,
            'exclude'    => [$term_id],
            'fields'     => 'ids',
            'meta_key'   => TCO_META_KEY,
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
        ]);
        
        // Se já existem outros filhos, coloca depois do último
        if (!empty($siblings)) {
            $last_sibling_id = end($siblings);
            $last_sibling_order = (int) get_term_meta($last_sibling_id, TCO_META_KEY, true);
            $new_order = $last_sibling_order + 1;
        } else {
            // É o primeiro filho, coloca logo após o pai
            $new_order = $parent_order + 1;
        }
        
        // Insere o termo na posição correta e empurra os demais
        tco_insert_term_at_position($taxonomy, $term_id, $new_order);
        
    } else {
        // Termo raiz: simplesmente coloca no final
        $max_order = tco_get_max_order($taxonomy);
        update_term_meta($term_id, TCO_META_KEY, $max_order + 1);
    }

}, 10, 3);


/**
 * Função auxiliar: Reordena todos os termos de forma sequencial
 */
function tco_reorder_all_terms($taxonomy) {
    
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'meta_key'   => TCO_META_KEY,
        'orderby'    => 'meta_value_num',
        'order'      => 'ASC',
        'meta_query' => [
            'relation' => 'OR',
            [
                'key'     => TCO_META_KEY,
                'compare' => 'EXISTS',
            ],
            [
                'key'     => TCO_META_KEY,
                'compare' => 'NOT EXISTS',
            ],
        ],
    ]);
    
    if (is_wp_error($terms)) return;
    
    $order = 0;
    foreach ($terms as $term) {
        update_term_meta($term->term_id, TCO_META_KEY, $order);
        $order++;
    }
}


/**
 * Função auxiliar: Insere um termo em uma posição específica
 * e empurra todos os termos seguintes para baixo
 */
function tco_insert_term_at_position($taxonomy, $term_id, $position) {
    
    // Busca todos os termos com ordem >= $position (exceto o termo atual)
    $terms_to_shift = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'exclude'    => [$term_id],
        'meta_key'   => TCO_META_KEY,
        'orderby'    => 'meta_value_num',
        'order'      => 'DESC', // Começa do maior para não sobrescrever
        'meta_query' => [
            [
                'key'     => TCO_META_KEY,
                'value'   => $position,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ],
    ]);
    
    if (is_wp_error($terms_to_shift)) return;
    
    // Empurra cada termo uma posição para baixo (+1)
    foreach ($terms_to_shift as $term) {
        $current_order = (int) get_term_meta($term->term_id, TCO_META_KEY, true);
        update_term_meta($term->term_id, TCO_META_KEY, $current_order + 1);
    }
    
    // Define a posição do novo termo
    update_term_meta($term_id, TCO_META_KEY, $position);
}


/**
 * Função auxiliar: Retorna o maior valor de ordem
 */
function tco_get_max_order($taxonomy) {
    
    $terms = get_terms([
        'taxonomy'   => $taxonomy,
        'hide_empty' => false,
        'meta_key'   => TCO_META_KEY,
        'orderby'    => 'meta_value_num',
        'order'      => 'DESC',
        'number'     => 1,
    ]);
    
    if (!empty($terms) && !is_wp_error($terms)) {
        return (int) get_term_meta($terms[0]->term_id, TCO_META_KEY, true);
    }
    
    return -1; // Se não houver termos, começa do 0
}


/* =========================================================
 * QUANDO UM TERMO É DELETADO, REORDENA
 * =======================================================*/
add_action('delete_term', function ($term_id, $tt_id, $taxonomy, $deleted_term) {
    
    if (!in_array($taxonomy, TCO_TAXONOMIES, true)) return;
    
    tco_reorder_all_terms($taxonomy);
    
}, 10, 4);


/* =========================================================
 * COLUNA ADMIN
 * =======================================================*/
foreach (TCO_TAXONOMIES as $tax) {

    add_filter("manage_edit-{$tax}_columns", function ($cols) {

        $new = [];

        foreach ($cols as $k => $v) {
            $new[$k] = $v;

            if ($k === 'cb') {
                $new['tco_order'] = '#';
            }
        }

        return $new;
    });

    add_filter("manage_edit-{$tax}_sortable_columns", function ($cols) {
        $cols['tco_order'] = 'tco_order';
        return $cols;
    });

    add_filter("manage_{$tax}_custom_column", function ($out, $col, $term_id) {

        if ($col !== 'tco_order') return $out;

        $pos = (int)get_term_meta($term_id, TCO_META_KEY, true);

        return '<small style="color:#666">' . ($pos + 1) . '</small>';

    }, 10, 3);
}


/* =========================================================
 * FALLBACK INICIAL - EXECUTA APENAS UMA VEZ
 * =======================================================*/
function tco_fix_missing_orders($taxonomy)
{
    $fixed = get_option("tco_fixed_{$taxonomy}");
    
    if ($fixed) return;
    
    tco_reorder_all_terms($taxonomy);
    
    update_option("tco_fixed_{$taxonomy}", true);
}

add_action('admin_init', function () {

    foreach (TCO_TAXONOMIES as $tax) {
        tco_fix_missing_orders($tax);
    }

});