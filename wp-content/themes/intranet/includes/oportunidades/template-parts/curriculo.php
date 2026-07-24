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
require_once get_template_directory() . '/includes/oportunidades/curriculo/helpers.php';
require_once get_template_directory() . '/includes/oportunidades/curriculo/mapeamentos.php';

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

                <p class="titulo-secao">Identificação do Candidato</p>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Dados Pessoais e Funcionais</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Nome Completo</th>
                                <td><?= valor($curriculo->nome_completo); ?></td>
                            </tr>

                            <tr>
                                <th>Nome Social</th>
                                <td><?= valor($curriculo->nome_social); ?></td>
                            </tr>

                            <tr>
                                <th>RF</th>
                                <td><?= valor($curriculo->rf); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Identificação Pessoal</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Data de Nascimento</th>
                                <td><?= date('d/m/Y', strtotime($curriculo->data_nascimento)); ?></td>
                            </tr>

                            <tr>
                                <th>Como você se identifica?</th>
                                <td><?= traduzir($curriculo->identificacao_racial, $MAPEAMENTO_IDENTIFICACAO_RACIAL); ?></td>
                            </tr>

                                <tr>
                                <th>Qual sua identidade de gênero?</th>
                                <td><?= traduzir($curriculo->identidade_genero, $MAPEAMENTO_IDENTIFICACAO_GENERO); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Informações Complementares</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Você é uma pessoa com deficiência?</th>
                                <td><?= sim_nao($curriculo->possui_deficiencia); ?></td>
                            </tr>

                            <?php if ((int) $curriculo->possui_deficiencia === 1) : ?>

                                <tr>
                                    <th>Você precisa de algum tipo de adaptação para executar trabalhos de escritório?</th>
                                    <td><?= sim_nao($curriculo->necessita_adaptacao); ?></td>
                                </tr>

                                <?php if ((int) $curriculo->necessita_adaptacao === 1) : ?>

                                    <tr>
                                        <th>Se sim, por gentileza, descreva as adaptações que seriam necessárias ao ambiente ou tecnologias assistivas.</th>
                                        <td><?= valor($curriculo->descreva_adaptacao); ?></td>
                                    </tr>

                                <?php endif; ?>

                            <?php endif; ?>

                            <tr>
                                <th>Você é servidor em readaptação funcional (readaptado)?</th>
                                <td><?= sim_nao($curriculo->servidor_readaptado); ?></td>
                            </tr>

                            <?php if ((int) $curriculo->servidor_readaptado === 1) : ?>

                                <tr>
                                    <th>Você precisa de algum tipo de adaptação para executar trabalhos de escritório?</th>
                                    <td><?= sim_nao($curriculo->readaptado_necessita); ?></td>
                                </tr>

                                <?php if ((int) $curriculo->readaptado_necessita === 1) : ?>

                                    <tr>
                                        <th>Se sim, por gentileza, descreva as adaptações que seriam necessárias ao ambiente ou tecnologias assistivas.</th>
                                        <td><?= valor($curriculo->readaptado_descricao); ?></td>
                                    </tr>

                                <?php endif; ?>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Contato</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Telefone de contato (WhatsApp)</th>
                                <td><?= valor($curriculo->telefone_whatsapp); ?></td>
                            </tr>

                            <?php if (!empty($curriculo->telefone_opcional)) : ?>

                                <tr>
                                    <th>Telefone de contato (Opcional)</th>
                                    <td><?= valor($curriculo->telefone_opcional); ?></td>
                                </tr>

                            <?php endif; ?>

                            <tr>
                                <th>E-mail Institucional ou de Uso Principal</th>
                                <td><?= valor($curriculo->email_principal); ?></td>
                            </tr>

                            <?php if (!empty($curriculo->email_secundario)) : ?>

                                <tr>
                                    <th>E-mail secundário</th>
                                    <td><?= valor($curriculo->email_secundario); ?></td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Lotação e Exercício</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Concluiu o estágio probatório?</th>
                                <td><?= sim_nao($curriculo->concluiu_estagio); ?></td>
                            </tr>

                            <tr>
                                <th>Qual é o seu cargo efetivo?</th>
                                <td>

                                    <?php

                                    if (!empty($cargos)) {

                                        echo esc_html(implode(', ', $cargos));

                                    } else {

                                        echo '<em>Não informado</em>';

                                    }

                                    ?>

                                </td>
                            </tr>

                            <?php if (in_array('Outro', $cargos, true)) : ?>

                                <tr>
                                    <th>Informe o cargo</th>
                                    <td><?= valor($curriculo->cargo_outro); ?></td>
                                </tr>

                            <?php endif; ?>

                            <tr>
                                <th>DRE de lotação</th>
                                <td><?= traduzir($curriculo->dre_lotacao, $MAPEAMENTO_OPCOES_DRES); ?></td>
                            </tr>

                            <tr>
                                <th>Unidade de Lotação</th>
                                <td><?= valor($curriculo->unidade_lotacao); ?></td>
                            </tr>

                            <tr>
                                <th>DRE de Exercício</th>
                                <td><?= traduzir($curriculo->dre_exercicio, $MAPEAMENTO_OPCOES_DRES); ?></td>
                            </tr>

                            <tr>
                                <th>Unidade de Exercício</th>
                                <td><?= valor($curriculo->unidade_exercicio); ?></td>
                            </tr>

                            <tr>
                                <th>Você acumula cargo na SME ou em outro órgão?</th>
                                <td><?= sim_nao($curriculo->acumula_cargo); ?></td>
                            </tr>

                            <?php if ((int) $curriculo->acumula_cargo === 1) : ?>

                                <tr>
                                    <th>Informe o órgão e o cargo onde acumula</th>
                                    <td><?= valor($curriculo->acumula_descricao); ?></td>
                                </tr>

                            <?php endif; ?>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="curriculo-secao">

                <p class="titulo-secao">Formação Acadêmica</p>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Escolaridade</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Qual seu nível de escolaridade?</th>
                                <td><?= traduzir($curriculo->escolaridade, $CURRICULO_MAPA_ESCOLARIDADE); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

                <?php if ($curriculo->escolaridade !== 'medio') : ?>

                    <div class="curriculo-subsecao">

                        <p class="subtitulo-secao">Bacharelado, Tecnólogo, Licenciatura</p>

                        <table class="table table-striped">

                            <tbody>

                                <tr>
                                    <th>Curso de graduação (nome do curso e da instituição de ensino)</th>
                                    <td><?= valor($curriculo->curso_graduacao); ?></td>
                                </tr>

                                <tr>
                                    <th>Ano de conclusão ou previsão de concluir</th>
                                    <td><?= valor($curriculo->ano_conclusao); ?></td>
                                </tr>

                                <tr>
                                    <th>Possui outra graduação e gostaria de informar?</th>
                                    <td><?= sim_nao($curriculo->outra_graduacao); ?></td>
                                </tr>

                                <?php if ((int) $curriculo->outra_graduacao === 1) : ?>

                                    <tr>
                                        <th>Segunda graduação (nome do curso e da instituição de ensino)</th>
                                        <td><?= valor($curriculo->segunda_graduacao); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Ano de conclusão ou previsão de concluir</th>
                                        <td><?= valor($curriculo->ano_conclusao_seg); ?></td>
                                    </tr>

                                <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                <?php endif; ?>

                <div class="curriculo-subsecao">

                    <p class="subtitulo-secao">Outros Cursos e/ou Projetos Relevantes</p>

                    <table class="table table-striped">

                        <tbody>

                            <tr>
                                <th>Por favor, descreva outros cursos e/ou projetos relevantes</th>
                                <td><?= valor($curriculo->outros_cursos); ?></td>
                            </tr>

                        </tbody>

                    </table>

                </div>

            </section>

            <section class="curriculo-secao">

                <p class="titulo-secao">Vivências Profissionais</p>    

                <?php if (!empty($vivencias)) : ?>

                    <?php foreach ($vivencias as $indice => $vivencia) : ?>

                        <div class="curriculo-subsecao">

                            <p class="subtitulo-secao">Vivência <?php echo $indice + 1; ?></p>

                            <table class="table table-striped">

                                <tbody>

                                    <tr>
                                        <th>Organização / Empresa</th>
                                        <td><?= valor($vivencia->organizacao_empresa); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Cargo / Função</th>
                                        <td><?= valor($vivencia->cargo_funcao); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Duração</th>
                                        <td><?= traduzir($vivencia->duracao, $MAPEAMENTO_DURACAO); ?></td>
                                    </tr>

                                    <tr>
                                        <th>Atividades e Competências Desenvolvidas</th>
                                        <td><?= valor($vivencia->atividades_competencias); ?></td>
                                    </tr>

                                </tbody>

                            </table>

                        </div>

                    <?php endforeach; ?>

                <?php else : ?>

                    <p><em>Nenhuma vivência profissional cadastrada.</em></p>

                <?php endif; ?>

            </section>

            <section class="curriculo-secao">

                <p class="titulo-secao">Conhecimentos em Informática e Tecnologia</p>
                <p>Quais dos sistemas a seguir você já trabalhou e/ou tem facilidade de navegação?</p>

                <div class="curriculo-subsecao">

                    <table class="table table-striped">

                        <thead>

                            <tr>
                                <th>Sistema</th>
                                <th>Nível</th>
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

            <section class="current-section">

                <p class="titulo-secao">Preferências e Perfil Comportamental</p>
                <p>Por gentileza, indique a ação que mais reflete sua atitude nas situações a seguir.</p>

                <div class="curriculo-subsecao">

                    <table class="table table-striped">

                        <thead>

                            <tr>
                                <th>Afirmação</th>
                                <th>Ação</th>
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