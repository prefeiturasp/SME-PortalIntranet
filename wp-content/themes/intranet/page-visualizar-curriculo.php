<?php
/*
Template Name: Visualizar Currículo
*/

if (!is_user_logged_in()) {
    auth_redirect();
}

$current_user = wp_get_current_user();
$user_id = absint($_GET['user_id'] ?? 0);

if (!Curriculo::pode_visualizar_curriculo($current_user, $user_id)) {
    wp_die('Você não tem permissão para visualizar este currículo.');
}

if (!$user_id) {
    wp_die('Usuário inválido.');
}
show_admin_bar(false);
get_header('curriculo');


?>

<div class="curriculo-container">

    <div class="curriculo-toolbar">

        <button
            class="btn btn-primary btn-print"
            onclick="window.print();">
            Imprimir
        </button>

        <a
            class="btn btn-info"
            href="<?= admin_url(
                'admin-post.php?action=download_curriculo_pdf&user_id=' . $user_id
            ); ?>">
            Baixar PDF
        </a>

        <button
            class="btn btn-danger btn-close-window"
            onclick="window.close();">
            Fechar
        </button>

    </div>

            

    <div class="curriculo-wrapper">

        <?= Curriculo::visualizar($user_id); ?>

    </div>

</div>

<?php

get_footer('forms');