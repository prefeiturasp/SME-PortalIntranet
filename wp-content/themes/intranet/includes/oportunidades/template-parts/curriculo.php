<?php

if (!defined('ABSPATH')) {
	exit;
}

$curriculo = $args['curriculo'] ?? null;
if (!$curriculo) {
	return;
}

$vivencias = $args['vivencias'] ?? null;
if (!$vivencias) {
    return;
}

$informatica = $args['informatica'] ?? null;
if (!$informatica) {
    return;
}

$comportamental = $args['comportamental'] ?? null;
if (!$comportamental) {
    return;
}

// Incluir os arquivos de mapeamento
require_once get_template_directory() . '/includes/oportunidades/dados/helpers.php';
require get_template_directory() . '/includes/oportunidades/dados/mapeamentos.php';
$ESTRUTURA_CURRICULO = require get_template_directory() . '/includes/oportunidades/dados/estrutura-curriculo.php';

// Fazer o decode do cargo efetivo, que é armazenado como JSON no banco de dados
$cargos = [];

if (!empty($curriculo->cargo_efetivo)) {

	$cargos = json_decode($curriculo->cargo_efetivo, true);

	if (!is_array($cargos)) {
		$cargos = [];
	}

}

// Transformar o array de competências em um array associativo para facilitar a exibição
$competencias = [];
foreach ($informatica as $item) {
	$competencias[$item->competencia] = $item->nivel;
}

// Transformar o array de comportamentais em um array associativo para facilitar a exibição
$afirmacoes = [];
foreach ($comportamental as $item) {
	$afirmacoes[$item->pergunta] = $item->nivel;
}

?>
<div class="container">
    <div class="row">
        <div class="col-12">
            <section class="curriculo-secao">

                <p class="titulo-secao"><?= $ESTRUTURA_CURRICULO['identificacao']['titulo']; ?></p>

                <div class="curriculo-subsecao">

                    <?php $dadosPessoais = $ESTRUTURA_CURRICULO['identificacao']['subsecoes']['dados_pessoais']; ?>

                    <p class="subtitulo-secao">
                        <?= $dadosPessoais['titulo']; ?>
                    </p>

                    <table class="table table-striped">
                        <tbody>
                            <?php foreach ($dadosPessoais['campos'] as $campo => $config) : ?>
                                <tr>
                                    <th>
                                        <?= $config['label']; ?>
                                    </th>                                    
                                    <td>
                                        <?php
                                        $valorCampo = $curriculo->$campo ?? '';

                                        if (isset($config['mapa'])) {

                                            $valorCampo = traduzir(
                                                $valorCampo,
                                                $config['mapa']
                                            );

                                        }

                                        echo valor($valorCampo);

                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div class="curriculo-subsecao">

                    <?php $identiPessoal = $ESTRUTURA_CURRICULO['identificacao']['subsecoes']['identificacao_pessoal']; ?>

                    <p class="subtitulo-secao"><?= $identiPessoal['titulo']; ?></p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th><?= $identiPessoal['campos']['data_nascimento']['label']; ?></th>
                                <td><?= date('d/m/Y', strtotime($curriculo->data_nascimento)); ?></td>
                            </tr>

                            <tr>
                                <th><?= $identiPessoal['campos']['identificacao_racial']['label']; ?></th>
                                <td><?= traduzir($curriculo->identificacao_racial, $MAPEAMENTO_IDENTIFICACAO_RACIAL); ?></td>
                            </tr>

                            <tr>
                                <th><?= $identiPessoal['campos']['identidade_genero']['label']; ?></th>
                                <td><?= traduzir($curriculo->identidade_genero, $MAPEAMENTO_IDENTIFICACAO_GENERO); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <?php $infoComplementares = $ESTRUTURA_CURRICULO['identificacao']['subsecoes']['informacoes_complementares']; ?>

                    <p class="subtitulo-secao"><?= $infoComplementares['titulo']; ?></p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th><?= $infoComplementares['campos']['possui_deficiencia']['label']; ?></th>
                                <td><?= sim_nao($curriculo->possui_deficiencia); ?></td>
                            </tr>

                            <?php if ((int) $curriculo->possui_deficiencia === 1) : ?>

                                <tr>
                                    <th><?= $infoComplementares['campos']['necessita_adaptacao']['label']; ?></th>
                                    <td><?= sim_nao($curriculo->necessita_adaptacao); ?></td>
                                </tr>

                                <?php if ((int) $curriculo->necessita_adaptacao === 1) : ?>

                                    <tr>
                                        <th><?= $infoComplementares['campos']['descreva_adaptacao']['label']; ?></th>
                                        <td><?= valor($curriculo->descreva_adaptacao); ?></td>
                                    </tr>

                                <?php endif; ?>

                            <?php endif; ?>

                            <tr>
                                <th><?= $infoComplementares['campos']['servidor_readaptado']['label']; ?></th>
                                <td><?= sim_nao($curriculo->servidor_readaptado); ?></td>
                            </tr>

                            <?php if ((int) $curriculo->servidor_readaptado === 1) : ?>

                                <tr>
                                    <th><?= $infoComplementares['campos']['readaptado_necessita']['label']; ?></th>
                                    <td><?= sim_nao($curriculo->readaptado_necessita); ?></td>
                                </tr>

                                <?php if ((int) $curriculo->readaptado_necessita === 1) : ?>

                                    <tr>
                                        <th><?= $infoComplementares['campos']['readaptado_descricao']['label']; ?></th>
                                        <td><?= valor($curriculo->readaptado_descricao); ?></td>
                                    </tr>

                                <?php endif; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <?php $contato = $ESTRUTURA_CURRICULO['identificacao']['subsecoes']['contato']; ?>

                    <p class="subtitulo-secao"><?= $contato['titulo']; ?></p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th><?= $contato['campos']['telefone_whatsapp']['label']; ?></th>
                                <td><?= valor($curriculo->telefone_whatsapp); ?></td>
                            </tr>

                            <?php if (!empty($curriculo->telefone_opcional)) : ?>

                                <tr>
                                    <th><?= $contato['campos']['telefone_opcional']['label']; ?></th>
                                    <td><?= valor($curriculo->telefone_opcional); ?></td>
                                </tr>

                            <?php endif; ?>

                            <tr>
                                <th><?= $contato['campos']['email_principal']['label']; ?></th>
                                <td><?= valor($curriculo->email_principal); ?></td>
                            </tr>

                            <?php if (!empty($curriculo->email_secundario)) : ?>

                                <tr>
                                    <th><?= $contato['campos']['email_secundario']['label']; ?></th>
                                    <td><?= valor($curriculo->email_secundario); ?></td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <?php $lotacaoExercicio = $ESTRUTURA_CURRICULO['identificacao']['subsecoes']['lotacao_exercicio']; ?>

                    <p class="subtitulo-secao"><?= $lotacaoExercicio['titulo']; ?></p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['concluiu_estagio']['label']; ?></th>
                                <td><?= sim_nao($curriculo->concluiu_estagio); ?></td>
                            </tr>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['cargo_efetivo']['label']; ?></th>
                                <td>

                                    <?php

                                    if (!empty($cargos)) {

                                        echo esc_html(implode(', ', $cargos));

                                    } else {

                                        echo '<em>—</em>';

                                    }

                                    ?>

                                </td>
                            </tr>

                            <?php if (in_array('Outro', $cargos, true)) : ?>

                                <tr>
                                    <th><?= $lotacaoExercicio['campos']['cargo_outro']['label']; ?></th>
                                    <td><?= valor($curriculo->cargo_outro); ?></td>
                                </tr>

                            <?php endif; ?>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['dre_lotacao']['label']; ?></th>
                                <td><?= traduzir($curriculo->dre_lotacao, $MAPEAMENTO_OPCOES_DRES); ?></td>
                            </tr>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['unidade_lotacao']['label']; ?></th>
                                <td><?= valor($curriculo->unidade_lotacao); ?></td>
                            </tr>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['dre_exercicio']['label']; ?></th>
                                <td><?= traduzir($curriculo->dre_exercicio, $MAPEAMENTO_OPCOES_DRES); ?></td>
                            </tr>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['unidade_exercicio']['label']; ?></th>
                                <td><?= valor($curriculo->unidade_exercicio); ?></td>
                            </tr>

                            <tr>
                                <th><?= $lotacaoExercicio['campos']['acumula_cargo']['label']; ?></th>
                                <td><?= sim_nao($curriculo->acumula_cargo); ?></td>
                            </tr>

                            <?php if ((int) $curriculo->acumula_cargo === 1) : ?>

                                <tr>
                                    <th><?= $lotacaoExercicio['campos']['acumula_descricao']['label']; ?></th>
                                    <td><?= valor($curriculo->acumula_descricao); ?></td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="curriculo-secao">

                <?php $formacao = $ESTRUTURA_CURRICULO['formacao']; ?>

                <p class="titulo-secao"><?= $formacao['titulo']; ?></p>

                <div class="curriculo-subsecao">

                    <?php $escolaridade = $formacao['subsecoes']['escolaridade']; ?>

                    <p class="subtitulo-secao"><?= $escolaridade['titulo']; ?></p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th><?= $escolaridade['campos']['escolaridade']['label']; ?></th>
                                <td><?= traduzir($curriculo->escolaridade, $CURRICULO_MAPA_ESCOLARIDADE); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <?php if ($curriculo->escolaridade !== 'medio') : ?>

                    <div class="curriculo-subsecao">

                        <?php $graduacao = $formacao['subsecoes']['graduacao']; ?>

                        <p class="subtitulo-secao"><?= $graduacao['titulo']; ?></p>

                        <table class="table table-striped">

                            <tbody>

                                <tr>
                                    <th><?= $graduacao['campos']['curso_graduacao']['label']; ?></th>
                                    <td><?= valor($curriculo->curso_graduacao); ?></td>
                                </tr>

                                <tr>
                                    <th><?= $graduacao['campos']['ano_conclusao']['label']; ?></th>
                                    <td><?= valor($curriculo->ano_conclusao); ?></td>
                                </tr>

                                <tr>
                                    <th><?= $graduacao['campos']['outra_graduacao']['label']; ?></th>
                                    <td><?= sim_nao($curriculo->outra_graduacao); ?></td>
                                </tr>

                                <?php if ((int) $curriculo->outra_graduacao === 1) : ?>

                                    <tr>
                                        <th><?= $graduacao['campos']['segunda_graduacao']['label']; ?></th>
                                        <td><?= valor($curriculo->segunda_graduacao); ?></td>
                                    </tr>

                                    <tr>
                                        <th><?= $graduacao['campos']['ano_conclusao_seg']['label']; ?></th>
                                        <td><?= valor($curriculo->ano_conclusao_seg); ?></td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

                <div class="curriculo-subsecao">

                    <?php $outrosCursos = $formacao['subsecoes']['outros_cursos']; ?>

                    <p class="subtitulo-secao"><?= $outrosCursos['titulo']; ?></p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th><?= $outrosCursos['campos']['outros_cursos']['label']; ?></th>
                                <td><?= valor($curriculo->outros_cursos); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="curriculo-secao">

                <?php $vivenciasSecao = $ESTRUTURA_CURRICULO['vivencias']; ?>

                <p class="titulo-secao"><?= $vivenciasSecao['titulo']; ?></p>    

                <?php if (!empty($vivencias)) : ?>

                    <?php foreach ($vivencias as $indice => $vivencia) : ?>

                        <div class="curriculo-subsecao">

                            <?php $vivenciaTemplate = $vivenciasSecao['subsecoes']['vivencia']; ?>

                            <p class="subtitulo-secao"><?= $vivenciaTemplate['titulo'] . ' ' . ($indice + 1); ?></p>

                            <table class="table table-striped">

                                <tbody>

                                    <tr>
                                        <th><?= $vivenciaTemplate['campos']['organizacao_empresa']['label']; ?></th>
                                        <td><?= valor($vivencia->organizacao_empresa); ?></td>
                                    </tr>

                                    <tr>
                                        <th><?= $vivenciaTemplate['campos']['cargo_funcao']['label']; ?></th>
                                        <td><?= valor($vivencia->cargo_funcao); ?></td>
                                    </tr>

                                    <tr>
                                        <th><?= $vivenciaTemplate['campos']['duracao']['label']; ?></th>
                                        <td><?= traduzir($vivencia->duracao, $MAPEAMENTO_DURACAO); ?></td>
                                    </tr>

                                    <tr>
                                        <th><?= $vivenciaTemplate['campos']['atividades_competencias']['label']; ?></th>
                                        <td><?= valor($vivencia->atividades_competencias); ?></td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    <?php endforeach; ?>

                <?php else : ?>

                    <p><em><?= $vivenciasSecao['mensagem_vazia']; ?></em></p>

                <?php endif; ?>

            </section>

            <section class="curriculo-secao">

                <?php $tecnologia = $ESTRUTURA_CURRICULO['tecnologia']; ?>

                <p class="titulo-secao"><?= $tecnologia['titulo']; ?></p>
                <p><?= $tecnologia['descricao']; ?></p>

                <div class="curriculo-subsecao">

                    <?php $competenciasSecao = $tecnologia['subsecoes']['competencias']; ?>

                    <table class="table table-striped">

                        <thead>

                            <tr>
                                <th><?= $competenciasSecao['campos']['sistema']['label']; ?></th>
                                <th><?= $competenciasSecao['campos']['nivel']['label']; ?></th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($CURRICULO_MAPA_COMPETENCIAS as $chave => $nome) : ?>

                                <tr>

                                    <td><?= esc_html($nome); ?></td>

                                    <td>
                                        <?= traduzir(
                                            $competencias[$chave] ?? null,
                                            $CURRICULO_MAPA_NIVEL_COMPETENCIA
                                        ); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="curriculo-secao">

                <?php $comportamental = $ESTRUTURA_CURRICULO['comportamental']; ?>

                <p class="titulo-secao"><?= $comportamental['titulo']; ?></p>
                <p><?= $comportamental['descricao']; ?></p>

                <div class="curriculo-subsecao">

                    <?php $perfilSecao = $comportamental['subsecoes']['perfil']; ?>

                    <table class="table table-striped">

                        <thead>

                            <tr>
                                <th><?= $perfilSecao['campos']['afirmacao']['label']; ?></th>
                                <th><?= $perfilSecao['campos']['acao']['label']; ?></th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php foreach ($CURRICULO_PERFIL_COMPORTAMENTAL as $chave => $nome) : ?>

                                <tr>

                                    <td><?= esc_html($nome); ?></td>

                                    <td>
                                        <?= traduzir(
                                            $afirmacoes[$chave] ?? null,
                                            $CURRICULO_MAPA_NIVEL_COMPORTAMENTAL
                                        ); ?>
                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            </section>
        </div>
    </div>
</div>