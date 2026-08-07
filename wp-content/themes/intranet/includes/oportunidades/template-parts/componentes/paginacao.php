<?php

$pagina_atual = max(1, (int) ($args['pagina_atual'] ?? 1));
$total_paginas = (int) ($args['total_paginas'] ?? 1);
$parametro = $args['parametro'] ?? 'paged';

if ( $total_paginas <= 1 ) {
    return;
}

// Define a quantidade de páginas exibidas antes e depois da página atual
$inicio = max( 1, $pagina_atual - 2 );
$fim = min( $total_paginas, $pagina_atual + 2 );

?>

<nav class="mt-4" aria-label="Paginação">
    <ul class="pagination justify-content-center">

        <?php if ( $pagina_atual > 1 ) : ?>

            <li class="page-item">
                <a class="page-link" href="<?= esc_url(add_query_arg($parametro, 1)); ?>" aria-label="Primeira página">
                    <i class="fa fa-angle-double-left" aria-hidden="true"></i>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link" href="<?= esc_url(add_query_arg($parametro, $pagina_atual - 1)); ?>" aria-label="Página anterior">
                    <i class="fa fa-angle-left" aria-hidden="true"></i>
                </a>
            </li>

        <?php endif; ?>

        <?php if ( $inicio > 1 ) : ?>

            <li class="page-item">
                <a class="page-link" href="<?= esc_url( add_query_arg( $parametro, 1 ) ); ?>">
                    1
                </a>
            </li>

            <?php if  ($inicio > 2 ) : ?>

                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>

            <?php endif; ?>

        <?php endif; ?>

        <?php for ( $i = $inicio; $i <= $fim; $i++ ) : ?>

            <li class="page-item <?= $i === $pagina_atual ? 'active' : ''; ?>">
                <a class="page-link" href="<?= esc_url(add_query_arg($parametro, $i)); ?>">
                    <?= esc_html( $i ); ?>
                </a>
            </li>

        <?php endfor; ?>

        <?php if ( $fim < $total_paginas ) : ?>

            <?php if ( $fim < $total_paginas - 1 ) : ?>

                <li class="page-item disabled">
                    <span class="page-link">…</span>
                </li>

            <?php endif; ?>

            <li class="page-item">
                <a class="page-link" href="<?= esc_url(add_query_arg($parametro, $total_paginas)); ?>">
                    <?= esc_html( $total_paginas ); ?>
                </a>
            </li>

        <?php endif; ?>

        <?php if ($pagina_atual < $total_paginas) : ?>

            <li class="page-item">
                <a class="page-link" href="<?= esc_url(add_query_arg($parametro, $pagina_atual + 1)); ?>" aria-label="Próxima página">
                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                </a>
            </li>

            <li class="page-item">
                <a class="page-link" href="<?= esc_url(add_query_arg($parametro, $total_paginas)); ?>" aria-label="Última página">
                    <i class="fa fa-angle-double-right" aria-hidden="true"></i>
                </a>
            </li>

        <?php endif; ?>

    </ul>
</nav>