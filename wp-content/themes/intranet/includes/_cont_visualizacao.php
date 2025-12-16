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

