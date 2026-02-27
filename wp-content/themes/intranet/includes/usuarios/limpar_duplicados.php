<?php
$log = [];
$rf_encontrados = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && check_admin_referer('limpar_duplicados_rf')) {

    $modo = $_POST['modo'] ?? 'simulate';
    $simulate = $modo === 'simulate';
    $batch_size = max(1, min(intval($_POST['batch'] ?? 50), 500));

    global $wpdb;

    // RFs vindos da simulação anterior (caso seja exclusão real)
    if ($modo === 'real' && !empty($_POST['rfs_confirmados'])) {
        $duplicados = explode(',', sanitize_text_field($_POST['rfs_confirmados']));
    } else {
        // Buscando RFs duplicados para simular
        $duplicados = $wpdb->get_col($wpdb->prepare("
            SELECT meta_value
            FROM {$wpdb->usermeta}
            WHERE meta_key = 'rf' AND meta_value != ''
            GROUP BY meta_value
            HAVING COUNT(user_id) > 1
            LIMIT %d
        ", $batch_size));
    }

    if (!empty($duplicados)) {
        foreach ($duplicados as $rf_value) {
            $rf_encontrados[] = $rf_value; // salva para exibição futura

            $user_ids = $wpdb->get_col($wpdb->prepare("
                SELECT user_id FROM {$wpdb->usermeta}
                WHERE meta_key = 'rf' AND meta_value = %s
            ", $rf_value));

            $users = [];

            foreach ($user_ids as $user_id) {
                $user = get_userdata($user_id);
                if (!$user) continue;

                $last_login = get_user_meta($user_id, 'wp_last_login', true);
                $registered = strtotime($user->user_registered);
                $score = $last_login ? intval($last_login) : $registered;

                $users[] = [
                    'ID' => $user_id,
                    'email' => $user->user_email,
                    'score' => $score,
                ];
            }

            usort($users, fn($a, $b) => $b['score'] <=> $a['score']);
            $user_to_keep = array_shift($users);
            $log[] = "<strong>Mantido:</strong> {$user_to_keep['ID']} ({$user_to_keep['email']}) | RF: <code>$rf_value</code>";

            foreach ($users as $u) {
                $delete_url = wp_nonce_url(
                    admin_url('users.php?action=delete&user=' . $u['ID']),
                    'bulk-users'
                );
                
                $log[] = "→ <span style='color:red;'>Duplicado:</span> {$u['ID']} ({$u['email']}) | 
                <a href='user-edit.php?user_id={$u['ID']}' target='_blank'>Editar</a> | 
                <a href='{$delete_url}' style='color:red;' target='_blank'>Excluir</a>";
                
                if (!$simulate) {
                    wp_delete_user($u['ID']);
                }
            }

            $log[] = "<hr>";
        }

        echo '<div class="notice notice-success"><p><strong>' . ($simulate ? 'Simulação finalizada.' : 'Exclusão realizada.') . '</strong></p></div>';
    } else {
        echo '<div class="notice notice-info"><p>Nenhum grupo duplicado encontrado.</p></div>';
    }
}
?>

<div class="wrap">
    <h1>Limpar Duplicados por RF</h1>
    <p>Este processo identifica e remove usuários duplicados com base no campo <code>rf</code>.</p>

    <form id="limpar-duplicados-form" method="post">
        <?php wp_nonce_field('limpar_duplicados_rf'); ?>

        <label for="batch">Lote:</label>
        <input type="number" name="batch" id="batch" value="50" min="1" max="500">

        <input type="hidden" name="modo" value="simulate">

        <button type="submit" class="button button-primary">Simular</button>
    </form>

    <?php if (!empty($log)): ?>
        <h2>Resultado:</h2>
        <div style="background: #f9f9f9; padding: 10px; border: 1px solid #ccc; margin-top: 20px;">
            <?php echo implode('<br>', $log); ?>
        </div>

        <?php if (!empty($rf_encontrados)): ?>
            <form method="post" style="margin-top: 20px;">
                <?php wp_nonce_field('limpar_duplicados_rf'); ?>
                <input type="hidden" name="modo" value="real">
                <input type="hidden" name="rfs_confirmados" value="<?php echo esc_attr(implode(',', $rf_encontrados)); ?>">
                <input type="hidden" name="batch" value="<?php echo esc_attr($_POST['batch'] ?? 50); ?>">
                <button type="submit" class="button button-danger" style="background: #dc3232; color: white;">Confirmar Exclusão Real</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>