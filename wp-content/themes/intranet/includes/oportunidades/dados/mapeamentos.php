<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Mapeamento de valores para exibição no currículo.
 */
$MAPEAMENTO_IDENTIFICACAO_RACIAL = [
    'preto'    => 'Preto(a)',
    'pardo'    => 'Pardo(a)',
    'amarelo'  => 'Amarelo(a)',
    'indigena' => 'Indígena',
    'branco'   => 'Branco(a)',
];

/**
 * Mapeamento de valores para exibição no currículo.
 */
$MAPEAMENTO_IDENTIFICACAO_GENERO = [
    'homem_cis'    => 'Homem cis',
    'homem_trans'  => 'Homem trans',
    'mulher_cis'   => 'Mulher cis',
    'mulher_trans' => 'Mulher trans',
    'nao_binario'  => 'Não binário',
];

/**
 * Escolaridade
 */
$CURRICULO_MAPA_ESCOLARIDADE = [
	'medio'           => 'Ensino Médio',
	'superior'        => 'Ensino superior: Licenciatura, Bacharelado, Tecnólogo',
	'especializacao'  => 'Pós-graduação: Especialização/MBA (lato sensu)',
	'mestrado'        => 'Pós-graduação: Mestrado (stricto sensu)',
	'doutorado'       => 'Pós-graduação: Doutorado (stricto sensu)o',
];

$MAPEAMENTO_DURACAO = [
    'ate-1-ano'   => 'Até 1 ano',
    'entre-1-3'   => 'Entre 1 e 3 anos',
    'de-3-5'      => 'De 3 a 5 anos',
    'acima-5'     => 'Acima de 5 anos',
];

/**
 * Nível de conhecimento em informática
 */
$CURRICULO_MAPA_NIVEL_COMPETENCIA = [
	0 => 'Nenhum',
	1 => 'Básico',
	2 => 'Intermediário',
	3 => 'Avançado',
];

/**
 * Competências em informática
 */
$CURRICULO_MAPA_COMPETENCIAS = [
    'eol'                   => 'EOL - Escola On Line',
    'sei'                   => 'SEI - Sistema Eletrônico de Informações',
    'sof'                   => 'SOF - Sistema de Orçamento e Finanças',
    'sigpec'                => 'SIGPEC - Sistema Integrado de Gestão de Pessoas e Competências',
    'sig-escola'            => 'SIG - Escola - Sistema Integrado de Gestão da Escola',
    'sigep'                 => 'SIGEP - Sistema Integrado de Gestão de Parcerias',
    'sgp'                   => 'SGP - Sistema de Gestão Pedagógica',
    'doc'                   => 'DOC - Diário Oficial da Cidade de São Paulo',
    'tid'                   => 'TID - Trâmite Interno Digital',
    'simproc'               => 'SIMPROC - Sistema Municipal de Processos',
    'sigpae'                => 'SIGPAE - Sistema de Gestão do Programa de Alimentação Escolar',
    'cdep'                  => 'CDEP - Centro de Documentação da Educação Paulistana',
    'clic'                  => 'CLIC - Central de Informações e Apoio da COGEP',
    'apps-365'              => 'Apps Microsoft 365',
    'office-word'           => 'Office Word',
    'office-excel'          => 'Office Excel',
    'office-ppt'            => 'Office PowerPoint',
    'canva'                 => 'Canva',
    'power-bi'              => 'Power BI',
    'teams'                 => 'Teams/Meet/Zoom/Workplace',
    'sharepoint'            => 'Sharepoint/Workspace/Confluence',
    'forms'                 => 'Forms/Google Forms/SurveyMars',
    'planner'               => 'Planner/Monday/ClickUp',
];

/**
 * Nível de ações para perfil de comportamento
 */
$CURRICULO_MAPA_NIVEL_COMPORTAMENTAL = [
	0 => 'Concordo plenamente',
	1 => 'Concordo',
	2 => 'Discordo',
	3 => 'Discordo plenamente',
];

/**
 * Perfil de comportamento
 */
$CURRICULO_PERFIL_COMPORTAMENTAL = [
    'sociabilidade' => 'Sinto-me mais motivado e produtivo quando meu trabalho envolve interação constante com pessoas, networking e trocas sociais frequentes.',
    'analitico'     => 'Sinto que minha produtividade é consideravelmente maior quando trabalho sozinho em tarefas técnicas, preferindo focar em dados e processos do que em interações sociais constantes ou networking.',
    'inovacao'      => 'Sinto-me à vontade em ambientes de mudança constante e prefiro ter liberdade para criar novas soluções ou métodos, em vez de seguir estritamente regras, manuais ou padrões já estabelecidos.',
    'tecnico'       => 'Sinto-me mais confortável e produtivo realizando tarefas técnicas, lidando com números, dados e prazos de forma isolada, do que trabalhando diretamente com atendimento ao público.',
    'rotina'        => 'Sinto-me mais produtivo e seguro quando sigo uma rotina com processos bem definidos e previsíveis, preferindo manter métodos que já funcionam do que buscar constantemente formas inovadoras ou diferentes de realizar o trabalho.',
    'conservador'   => 'Sinto-me mais seguro e produtivo utilizando métodos de trabalho tradicionais e ferramentas já conhecidas, preferindo manter uma rotina estável em vez de ter que lidar com inovações constantes, novas tecnologias ou análise detalhada de dados técnicos.',
    'executor'      => 'Sinto-me mais produtivo e realizado resolvendo problemas práticos e imediatos na execução direta das tarefas do que dedicando tempo ao planejamento detalhado, organização de processos ou gestão administrativa.',
];

/**
 * Mapeamento DREs
 */

$MAPEAMENTO_OPCOES_DRES = [
    'dre-butanta'               => 'DRE Butantã',
    'dre-campo-limpo'           => 'DRE Campo Limpo',
    'dre-capela-socorro'        => 'DRE Capela do Socorro',
    'dre-freguesia-brasilandia' => 'DRE Freguesia/Brasilândia',
    'dre-guaianases'            => 'DRE Guaianases',
    'dre-ipiranga'              => 'DRE Ipiranga',
    'dre-itaquera'              => 'DRE Itaquera',
    'dre-jacana-tremembe'       => 'DRE Jaçanã/Tremembé',
    'dre-penha'                 => 'DRE Penha',
    'dre-pirituba-jaragua'      => 'DRE Pirituba/Jaraguá',
    'dre-santo-amaro'           => 'DRE Santo Amaro',
    'dre-sao-mateus'            => 'DRE São Mateus',
    'dre-sao-miguel'            => 'DRE São Miguel',
    'coordenadoria-sme'         => 'Coordenadoria/SME',
];

$MAPEAMENTO_OPCOES_VISUALIZACAO = [
    '0'   => 'Apenas os gestores das vagas às quais eu me candidatar',
    '1'   => 'Qualquer gestor que esteja consultando currículos',
];