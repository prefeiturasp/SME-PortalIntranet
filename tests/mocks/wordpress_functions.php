<?php
if (!function_exists('do_shortcode')) {
    function do_shortcode($shortcode) {
        // Implementação mockada para testes
        return $shortcode;
    }
}

function acf_render_field_setting($field, $args) {
    // Função mockada para capturar parâmetros
    return true;
}