<?php
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE);
ini_set('display_errors', '1');

define('WP_DEVELOP_DIR', 'C:/Users/felipe.viana/Documents/SME/TesteUnitario/wordpress-develop');
define('WP_CONTENT_DIR', 'C:/Users/felipe.viana/Documents/SME/intranet/wp-content');
define('WP_DEFAULT_THEME', 'intranet');

require WP_DEVELOP_DIR . '/tests/phpunit/includes/bootstrap.php';

// ativa o tema (define no banco de dados)
switch_theme('intranet');

// Carrega diretamente o functions.php do seu tema para garantir que as funções estejam disponíveis
require_once 'C:/Users/felipe.viana/Documents/SME/intranet/wp-content/themes/intranet/functions.php';