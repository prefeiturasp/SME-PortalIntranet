<?php

function acf_reciprocal_relationship(
    $value,
    $post_id,
    $field,
    $current_field_key,
    $related_field_key
) {
    $current_field = acf_get_field($current_field_key);
    $related_field = acf_get_field($related_field_key);

    $current_key = $current_field['name'];
    $related_key = $related_field['name'];

    $old_values = get_post_meta($post_id, $current_key);

    $old_values = is_array($old_values) ? $old_values : [];
    $new_values = is_array($value) ? $value : [];

    // Remover relações antigas
    foreach (array_diff($old_values, $new_values) as $removed_id) {
        $related_values = get_post_meta($removed_id, $related_key);
        $related_values = array_diff((array) $related_values, [$post_id]);
        update_post_meta($removed_id, $related_key, array_values($related_values));
    }

    // Adicionar novas relações
    foreach (array_diff($new_values, $old_values) as $added_id) {
        $related_values = get_post_meta($added_id, $related_key);
        $related_values[] = $post_id;
        update_post_meta($added_id, $related_key, array_values(array_unique($related_values)));
    }

    update_post_meta($post_id, $current_key, $new_values);

    return $value;
}