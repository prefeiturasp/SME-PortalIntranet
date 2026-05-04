<?php
require_once("./././wp-load.php");

global $wpdb;

$limit  = 500;
$offset = 0;

$date = date('d_m_y_H_i_s');
$fileName = $date . '_usuarios_portal.csv';

header("Content-Type: text/csv; charset=utf-8");
header("Content-Disposition: attachment; filename={$fileName}");
header("Cache-Control: no-store, no-cache");

$fh = fopen('php://output', 'w');

// Cabeçalho CSV
fputcsv($fh, ['id', 'login', 'email', 'funcao', 'grupo', 'setor']);

function convertFunc($funcao){
    switch ($funcao):
        case 'administrator': return 'Administrador';
        case 'contributor': return 'Colaborador';
        case 'editor': return 'Editor';
        case 'assessor': return 'Assessor';
        default: return $funcao;
    endswitch;
}

$role_filter = isset($_GET['funcao']) && $_GET['funcao'] !== 'all'
    ? sanitize_text_field($_GET['funcao'])
    : null;

while (true) {
    
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

    $all_group_ids = [];

    foreach ($users as $user) {
        $grupos = $meta_map[$user->ID]['grupo'] ?? [];

        if (is_array($grupos)) {
            foreach ($grupos as $gid) {
                $all_group_ids[] = (int)$gid;
            }
        }
    }

    $all_group_ids = array_unique($all_group_ids);
    
    $group_titles_map = [];

    if (!empty($all_group_ids)) {
        $ids = implode(',', $all_group_ids);

        $results = $wpdb->get_results("
            SELECT ID, post_title
            FROM {$wpdb->posts}
            WHERE ID IN ($ids)
        ");

        foreach ($results as $r) {
            $group_titles_map[$r->ID] = $r->post_title;
        }
    }
    
    foreach ($users as $user) {

        $meta_user = $meta_map[$user->ID] ?? [];

        // ROLE
        $roles = $meta_user[$wpdb->prefix . 'capabilities'] ?? [];
        $role  = is_array($roles) ? array_key_first($roles) : '';

        // FILTRO POR ROLE
        if ($role_filter && $role !== $role_filter) {
            continue;
        }

        // SETOR
        $setor = $meta_user['setor'] ?? '';

        // GRUPOS
        $grupos = $meta_user['grupo'] ?? [];
        $grupoTitle = '';

        if (is_array($grupos)) {
            $titles = [];

            foreach ($grupos as $gid) {
                if (isset($group_titles_map[$gid])) {
                    $titles[] = $group_titles_map[$gid];
                }
            }

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
    
    unset($meta_map, $group_titles_map, $all_group_ids);
    
    $offset += $limit;

    // Evita buffer travado
    if (ob_get_length()) {
        ob_flush();
    }
    flush();
}

fclose($fh);
exit;