<?php

if (!defined('ABSPATH')) {
	exit;
}

?>

<div class="wrap">

    <h1 class="wp-heading-inline mb-3">
        Busca Ativa de Candidatos
    </h1>

    <div class="alert alert-warning" role="alert">
        <i class="fa fa-info-circle" aria-hidden="true"></i> Exibindo apenas candidatos que autorizaram a visualização de seus currículos para qualquer gestor que esteja consultando o Banco de Talentos da SME.
    </div>

    <hr class="wp-header-end">

    <div class="card busca-ativa-card busca-ativa-form mt-3">

        <div class="card-header">

            <strong><i class="fa fa-search" aria-hidden="true"></i> Localizar candidatos</strong>

        </div>

        <div class="card-body">

            <form method="get" action="<?= esc_url(admin_url('edit.php')); ?>">

                <input type="hidden" name="post_type" value="oportunidade">
                <input type="hidden" name="page" value="busca_ativa">

                <!-- Palavra-chave -->
                <div class="form-group">

                    <label>Busca por Palavra-chave</label>

                    <input
                        type="text"
                        class="form-control"
                        name="palavra"
                        value="<?= esc_attr($_GET['palavra'] ?? ''); ?>"
                        placeholder="Ex.: Marketing, prestação de contas, EMEF Machado de Assis, DINUTRE...">

                    <small class="text-muted">Busque candidatos por experiências profissionais, formação, cursos, projetos ou local de exercício (DRE, UE, Divisão ou Coordenadoria). Informe uma ou mais palavras-chave separadas por vírgula.</small>

                </div>

                <!-- Linha 2 -->
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>DRE de Exercício</label>

                            <select
                                class="form-control"
                                name="dre_exercicio">

                                <option value="">Selecione</option>

                                <?php foreach ($filtros['dres'] as $valor => $label) : ?>

                                    <option value="<?= esc_attr($valor); ?>" <?= selected($valor, $_GET['dre_exercicio']) ?>>
                                        <?= esc_html($label); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Cargo</label>

                            <select
                                class="form-control"
                                name="cargo">

                                <option value="">Selecione</option>

                                <?php foreach ($filtros['cargos'] as $valor): ?>

                                    <option value="<?= esc_attr($valor); ?>" <?= selected($valor, $_GET['cargo']) ?>>
                                        <?= esc_html($valor); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                </div>

                <!-- Linha 3 -->
                <div class="row">

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Readaptado</label>

                            <select
                                class="form-control"
                                name="readaptado">

                                <option value="">Selecione</option>
                                <option value="1" <?= selected($_GET['readaptado'], '1') ?>>Sim</option>
                                <option value="0" <?= selected($_GET['readaptado'], '0') ?>>Não</option>

                            </select>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="form-group">

                            <label>Nível de Formação</label>

                            <select
                                class="form-control"
                                name="escolaridade">

                                <option value="">Selecione</option>

                                <?php foreach ($filtros['escolaridade'] as $valor => $label) : ?>

                                    <option value="<?= esc_attr($valor); ?>" <?= selected($valor, $_GET['escolaridade']) ?>>
                                        <?= esc_html($label); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>

                    </div>

                </div>

                <?php

                    $collapseTecnologia = false;

                    if (!empty($_GET['informatica']) && is_array($_GET['informatica'])) {
                        foreach ($_GET['informatica'] as $nivel) {
                            if ($nivel !== '') {
                                $collapseTecnologia = true;
                                break;
                            }
                        }
                    }
                ?>

                <!-- Tecnologia -->
                <div class="mt-4 mb-3">
                    <a
                        class="d-flex justify-content-between align-items-center busca-collapse-titulo"
                        data-toggle="collapse"
                        href="#collapseTecnologia"
                        role="button"
                        aria-expanded="<?= $collapseTecnologia ? 'true' : 'false'; ?>"
                        aria-controls="collapseTecnologia">

                        <span>
                            <strong>Informática e Tecnologia</strong>
                            <small class="text-muted">(opcional)</small>
                        </span>

                        <i class="fa fa-chevron-down toggle-icon"></i>

                    </a>
                </div>

                <div id="collapseTecnologia" class="collapse <?= $collapseTecnologia ? 'show' : ''; ?>">

                    <div class="row tecnologia-row">

                        <?php foreach ($filtros['competencias'] as $valor => $label) : ?>

                            <div class="col tecnologia-col">

                                <div class="form-group">

                                    <label><?= esc_html($label); ?></label>

                                    <select
                                        class="form-control"
                                        name="informatica[<?= esc_attr($valor); ?>]">

                                        <option value="">Selecione</option>

                                        <?php foreach ($filtros['niveis_competencia'] as $nivel => $descricao) : ?>
                                            <?php if ($nivel == '0') continue; ?>
                                            <option value="<?= esc_attr($nivel); ?>" <?= selected($nivel, $_GET['informatica'][$valor]) ?>>
                                                <?= esc_html($descricao); ?>
                                            </option>

                                        <?php endforeach; ?>

                                    </select>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

                <div class="mt-4">                    

                    <button type="submit" class="btn btn-primary mr-2">
                        <i class="fa fa-search" aria-hidden="true"></i> Buscar candidatos
                    </button>

                    <a href="<?= admin_url('edit.php?post_type=oportunidade&page=busca_ativa') ?>" class="btn btn-outline-secondary">Limpar filtros</a>

                </div>

            </form>

        </div>

    </div>

    <div class="card busca-ativa-card mt-4">

        <div class="card-header">

            <h2>
                Currículos cadastrados
                (<?= number_format_i18n($resultado['total']); ?>)
            </h2>

        </div>

        <div class="card-body">

                    

            <div class="busca-ativa">

                <div class="row">

                    <?php foreach ($resultado['dados'] as $curriculo) : ?>

                        <div class="col-xl-4 col-lg-6 mb-4">

                            <div class="card card-curriculo h-100">

                                <div class="card-body d-flex flex-column">

                                    <h5 class="card-title">

                                        <?= esc_html($curriculo['nome_completo']); ?>

                                    </h5>

                                    <div class="curriculo-texto">

                                        <?php 
                                            $cargos = json_decode($curriculo['cargo_efetivo'], true);
                                            
                                            if (!empty($cargos)) {
                                                echo esc_html(implode(', ', $cargos));
                                            } else {
                                                echo '<em>—</em>';
                                            }
                                        ?>

                                    </div>

                                    <div class="curriculo-texto mb-3">

                                        <?= esc_html(
                                            $filtros['dres'][$curriculo['dre_exercicio']]
                                            ?? '-'
                                        ); ?>

                                    </div>

                                    <div class="curriculo-data mb-4">

                                        Currículo atualizado em
                                        <strong>

                                            <?= esc_html(
                                                date_i18n(
                                                    'd/m/Y',
                                                    strtotime($curriculo['updated_at'])
                                                )
                                            ); ?>

                                        </strong>

                                    </div>

                                    <div class="mt-auto">
                                        <?php $url = home_url('/index.php/visualizar-curriculo/?user_id=' . $curriculo['user_id']); ?>
                                        <a
                                            href="<?= esc_url($url); ?>"
                                            class="curriculo-link btn-ver-curriculo"
                                            data-curriculo="<?= $curriculo['id']; ?>">

                                            <i class="fa fa-user-circle-o"></i>
                                            Ver currículo
                                        </a>

                                    </div>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                </div>

            </div>

            <?php if ($resultado['total_paginas'] > 1) : ?>

                <nav>
                    <ul class="pagination">

                        <?php for ($i = 1; $i <= $resultado['total_paginas']; $i++) : ?>

                            <li class="page-item <?= $i === $resultado['pagina'] ? 'active' : ''; ?>">

                                <a
                                    class="page-link"
                                    href="<?= esc_url(
                                        add_query_arg(
                                            'paged',
                                            $i
                                        )
                                    ); ?>">
                                    <?= $i; ?>
                                </a>

                            </li>

                        <?php endfor; ?>

                    </ul>
                </nav>

            <?php endif; ?>

        </div>

    </div>

</div>

<script>

    jQuery(document).ready(function($) {

        $(document).on('click', '.btn-ver-curriculo', function (e) {

            e.preventDefault();

            const url = $(this).attr('href');

            const largura = 1200;
            const altura = 900;

            const esquerda = (screen.width - largura) / 2;
            const topo = (screen.height - altura) / 2;

            window.open(
                url,
                'curriculo',
                `
                width=${largura},
                height=${altura},
                left=${esquerda},
                top=${topo},
                scrollbars=yes,
                resizable=yes
                `
            );

        });

        $('#btn-imprimir').on('click', function () {
            window.print();
        });

    });
</script>