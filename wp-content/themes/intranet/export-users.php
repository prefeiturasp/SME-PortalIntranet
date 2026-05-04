<?php
require_once("./././wp-load.php");

global $wpdb;

$date = date('d_m_y_h_i_s');
$fileName = $date . '_usuarios_portal.csv';

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename={$fileName}");

$fh = fopen('php://output', 'w');

// Cabeçalho
fputcsv($fh, ['id', 'login', 'email', 'funcao', 'grupo', 'setor']);

$limit = 1000;
$offset = 0;

function convertFunc($funcao){
    switch ($funcao):
        case 'administrator': return 'Administrador';
        case 'contributor': return 'Colaborador';
        case 'editor': return 'Editor';
        case 'assessor': return 'Assessor';
        default: return $funcao;
    endswitch;
}

do {

    $users = $wpdb->get_results($wpdb->prepare("
        SELECT ID, user_login, user_email
        FROM {$wpdb->users}
        LIMIT %d OFFSET %d
    ", $limit, $offset));

    if (empty($users)) break;

    $user_ids = array_column($users, 'ID');
    $ids_string = implode(',', array_map('intval', $user_ids));

    // Buscar meta em lote
    $meta = $wpdb->get_results("
        SELECT user_id, meta_key, meta_value
        FROM {$wpdb->usermeta}
        WHERE user_id IN ($ids_string)
    ");

    $meta_map = [];
    foreach ($meta as $m) {
        $meta_map[$m->user_id][$m->meta_key] = maybe_unserialize($m->meta_value);
    }

    foreach ($users as $user) {

        $roles = $meta_map[$user->ID]['wp_capabilities'] ?? [];
        $role = is_array($roles) ? array_key_first($roles) : '';

        $setor = $meta_map[$user->ID]['setor'] ?? '';
        $grupos = $meta_map[$user->ID]['grupo'] ?? [];

        // Se grupo for array de IDs
        $grupoTitle = '';
        if (is_array($grupos) && !empty($grupos)) {
            $grupo_ids = implode(',', array_map('intval', $grupos));

            $titles = $wpdb->get_col("
                SELECT post_title
                FROM {$wpdb->posts}
                WHERE ID IN ($grupo_ids)
            ");

            $grupoTitle = implode(', ', $titles);
        }

        fputcsv($fh, [
            $user->ID,
            $user->user_login,
            $user->user_email,
            convertFunc($role),
            $grupoTitle,
            $setor
        ]);
    }

    $offset += $limit;

} while (true);

fclose($fh);
exit;