<?php
// Contador de visualizações de noticias
function getPostViews($postID){
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
        return "0";
    }
    return $count.'';
}
// conta as visitas.
function setPostViews($postID) {
    $count_key = 'post_views_count';
    $count = get_post_meta($postID, $count_key, true);
    if($count==''){
        $count = 0;
        delete_post_meta($postID, $count_key);
        add_post_meta($postID, $count_key, '0');
    }else{
        $count++;
        update_post_meta($postID, $count_key, $count);
    }
}

// Adiciona uma coluna no Admin
add_filter('manage_pages_columns', 'posts_column_views');
add_action('manage_pages_custom_column', 'posts_custom_column_views',10,2);
add_filter('manage_posts_columns', 'posts_column_views');
add_action('manage_posts_custom_column', 'posts_custom_column_views',10,2);

function posts_column_views($defaults){
    $defaults['post_views'] = __('<span class="dashicons dashicons-visibility"></span>');
    return $defaults;
}
function posts_custom_column_views($column_name, $id){
    if($column_name === 'post_views'){
        echo '<h3><strong>'.getPostViews(get_the_ID()).'</strong></h3>';
    }
}

add_filter( 'manage_agendanew_posts_columns', 'remove_post_views_column' );
add_filter( 'manage_agenda_posts_columns', 'remove_post_views_column' );
add_filter( 'manage_destaque_posts_columns', 'remove_post_views_column' );
add_filter( 'manage_portais_posts_columns', 'remove_post_views_column' );
add_filter( 'manage_intranet-faq_posts_columns', 'remove_post_views_column' );
add_filter( 'manage_parceiros_posts_columns', 'remove_post_views_column' );
function remove_post_views_column($columns) {
    unset( $columns['post_views'] );
    return $columns;
}

// Funcao para ativar ordenacao
function ws_sortable_manufacturer_column( $columns )    {
    $columns['post_views'] =  'post_views';
    return $columns;
}
add_filter( 'manage_edit-post_sortable_columns', 'ws_sortable_manufacturer_column' ); // Noticias
add_filter( 'manage_edit-info-sme-explica_sortable_columns', 'ws_sortable_manufacturer_column' ); // SME Explica
add_filter( 'manage_edit-mural-professores_sortable_columns', 'ws_sortable_manufacturer_column' ); // Mural dos professores
add_filter( 'manage_edit-page_sortable_columns', 'ws_sortable_manufacturer_column' ); // Paginas

// Funcao para ordernar
function ws_orderby_custom_column( $query ) {
    global $pagenow;

    if ( ! is_admin() || 'edit.php' != $pagenow || ! $query->is_main_query()  )  {
        return;
    }

    $orderby = $query->get( 'orderby' );

    print_r($orderby);

    switch ( $orderby ) {
        case 'post_views':
            $query->set( 'meta_key', 'post_views_count' );
            $query->set( 'orderby', 'meta_value_num' );
            break;

        default:
            break;
    }

}
add_action( 'pre_get_posts', 'ws_orderby_custom_column' );