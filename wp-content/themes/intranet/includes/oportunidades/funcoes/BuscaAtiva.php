<?php

namespace Classes\Curriculos;

use Inscricao;

class BuscaAtiva
{

	public function __construct()
	{
		add_action('admin_menu', array($this, 'admin_menu'));
	}

	public function admin_menu()
	{
		add_submenu_page(
            'edit.php?post_type=oportunidade', // menu pai (CPT)
            'Busca Ativa de Candidatos',              // título da página
            'Busca Ativa',                               // título do menu
            'edit_oportunidades',                     // capability necessária
            'busca_ativa',                 // slug
            array( $this, 'render_page' ),            // callback
            20
        );
	}

	/**
	 * Obtém os filtros enviados pelo formulário.
	 */
	private function obterFiltrosBusca(): array {
		return [
			'palavra' => sanitize_text_field($_GET['palavra'] ?? ''),
			'dre_exercicio' => sanitize_text_field($_GET['dre_exercicio'] ?? ''),
			'cargo' => sanitize_text_field($_GET['cargo'] ?? ''),
			'readaptado' => isset($_GET['readaptado'])
				? sanitize_text_field($_GET['readaptado'])
				: '',
			'escolaridade' => sanitize_text_field($_GET['escolaridade'] ?? ''),
			'informatica' => $_GET['informatica'] ?? [],
		];
	}

	/**
	 * Adiciona a busca por palavra-chave.
	 *
	 * Permite buscar por vários termos separados por:
	 * - vírgula
	 * - ponto e vírgula
	 * - quebra de linha
	 */
	private function adicionarBuscaPalavra(
		array &$where,
		array &$params,
		string $palavra
	): void {

		global $wpdb;

		$termos = array_filter(
			array_map(
				'trim',
				preg_split('/[,;\r\n]+/', $palavra)
			)
		);

		if (empty($termos)) {
			return;
		}

		$buscas = [];

		foreach ($termos as $termo) {

			$busca = '%' . $wpdb->esc_like($termo) . '%';

			$buscas[] = "
			(
				bt.nome_completo LIKE %s
				OR bt.nome_social LIKE %s
				OR bt.unidade_exercicio LIKE %s
				OR bt.curso_graduacao LIKE %s
				OR bt.segunda_graduacao LIKE %s
				OR bt.outros_cursos LIKE %s
				OR EXISTS (
					SELECT 1
					FROM {$wpdb->prefix}banco_talentos_vivencias viv
					WHERE viv.curriculo_id = bt.id
					AND (
						viv.cargo_funcao LIKE %s
						OR viv.atividades_competencias LIKE %s
						OR viv.organizacao_empresa LIKE %s
					)
				)
			)";

			// Campos da tabela principal (6 campos)
			for ($i = 0; $i < 6; $i++) {
				$params[] = $busca;
			}
			
			// Campos das vivências
			foreach ([
				'cargo_funcao',
				'atividades_competencias',
				'organizacao_empresa',
			] as $_) {
				$params[] = $busca;
			}

		}

		$where[] = '(' . implode(' OR ', $buscas) . ')';
	}

	/**
	 * Adiciona os filtros de informática.
	 */
	private function adicionarBuscaInformatica(
		array &$where,
		array &$params,
		array $informatica
	): void {

		global $wpdb;

		$exists = [];

		foreach ($informatica as $competencia => $nivel) {

			if ($nivel === '' || $nivel === null) {
				continue;
			}

			$exists[] = "
			EXISTS (

				SELECT 1

				FROM {$wpdb->prefix}banco_talentos_informatica inf

				WHERE inf.curriculo_id = bt.id
				AND inf.competencia = %s
				AND inf.nivel = %d

			)";

			$params[] = $competencia;
			$params[] = (int) $nivel;

		}

		if (!empty($exists)) {
			$where[] = '(' . implode(' AND ', $exists) . ')';
		}

	}

	/**
	 * Monta dinamicamente o WHERE da consulta.
	 */
	private function montarWhere(array $filtros): array {
		$where = [
			'bt.visualizar_curriculo = 1',
			"bt.status_curriculo = 'finalizado'"
		];

		$params = [];

		/*
		* Palavra-chave
		*/
		if (!empty($filtros['palavra'])) {

			$this->adicionarBuscaPalavra(
				$where,
				$params,
				$filtros['palavra']
			);

		}

		/*
		* DRE
		*/
		if (!empty($filtros['dre_exercicio'])) {
			$where[] = 'bt.dre_exercicio = %s';
			$params[] = $filtros['dre_exercicio'];
		}

		/*
		* Escolaridade
		*/
		if (!empty($filtros['escolaridade'])) {
			$where[] = 'bt.escolaridade = %s';
			$params[] = $filtros['escolaridade'];
		}

		/*
		* Readaptado
		*/
		if ($filtros['readaptado'] !== '') {
			$where[] = 'bt.servidor_readaptado = %d';
			$params[] = (int) $filtros['readaptado'];
		}

		/*
		* Cargo
		*/
		if (!empty($filtros['cargo'])) {

			$where[] = 'JSON_CONTAINS(bt.cargo_efetivo, %s)';

			$params[] = json_encode(
				$filtros['cargo']
			);

		}

		/*
		* Informática
		*/
		$this->adicionarBuscaInformatica(
			$where,
			$params,
			$filtros['informatica']
		);

		

		return [
			'sql' => implode(' AND ', $where),
			'params' => $params,
		];
	}

	/**
     * Tecnologias exibidas na Busca Ativa.
     */

	private const COMPETENCIAS_BUSCA = [
		'office-word',
		'office-excel',
		'office-ppt',
		'power-bi',
		'sei',
		'eol',
		'sgp',
		'canva',
		'teams',
		'forms',
	];

	private function obterFiltros(): array{
		require get_template_directory()
			. '/includes/oportunidades/dados/mapeamentos.php';

		$competencias = [];

		foreach (self::COMPETENCIAS_BUSCA as $competencia) {

			if (isset($CURRICULO_MAPA_COMPETENCIAS[$competencia])) {
				$competencias[$competencia] = $CURRICULO_MAPA_COMPETENCIAS[$competencia];
			}

		}

		return [
			'dres' => $MAPEAMENTO_OPCOES_DRES,
			'escolaridade' => $CURRICULO_MAPA_ESCOLARIDADE,
			'competencias' => $competencias,
			//'competencias' => $CURRICULO_MAPA_COMPETENCIAS, // Para exibir todas as competências, caso necessário
			'cargos' => $MAPEAMENTO_CARGOS_EFETIVOS,
			'niveis_competencia' => $CURRICULO_MAPA_NIVEL_COMPETENCIA,
		];
	}

	/**
	 * Obtém os currículos cadastrados.
	 *
	 * @param array $filtros
	 * @param int $pagina
	 * @param int $porPagina
	 *
	 * @return array
	 */
	private function obterCurriculos(
		array $filtros = [],
		int $pagina = 1,
		int $porPagina = 21
	): array {

		$filtros = $this->obterFiltrosBusca();
		$where = $this->montarWhere($filtros);

		global $wpdb;

		$offset = ($pagina - 1) * $porPagina;

		$sql = "
			SELECT SQL_CALC_FOUND_ROWS

				bt.id,
				bt.user_id,
				bt.nome_completo,
				bt.nome_social,
				bt.cargo_efetivo,
				bt.cargo_outro,
				bt.dre_exercicio,
				bt.atualizado_em

			FROM {$wpdb->prefix}banco_talentos bt

			WHERE {$where['sql']}

			ORDER BY bt.nome_completo ASC

			LIMIT %d OFFSET %d
		";

		$params = $where['params'];
		$params[] = $porPagina;
		$params[] = $offset;

		$sqlPreparado = $wpdb->prepare(
			$sql,
			$params
		);		

		$resultados = $wpdb->get_results(
			$wpdb->prepare(
				$sql,
				$params
			),
			ARRAY_A
		);
		

		$total = (int) $wpdb->get_var(
			"SELECT FOUND_ROWS()"
		);

		// Adiciona as informações sobre o processo em etapa mais avançanda que o candidato está participando.
		$resultados = Inscricao::adicionar_processo_ativo( $resultados );

		return [
			'dados' => $resultados,
			'total' => $total,
			'pagina' => $pagina,
			'por_pagina' => $porPagina,
			'total_paginas' => (int) ceil(
				$total / $porPagina
			),
			'filtros_ativos' => empty( array_filter( $filtros ) ) ? false : true
		];
	}

	public function render_page(){
		$filtros = $this->obterFiltros();

		$pagina = max(
			1,
			(int) ($_GET['paged'] ?? 1)
		);

		$resultado = $this->obterCurriculos(
			[],
			$pagina
		);

		require get_template_directory()
			. '/includes/oportunidades/template-parts/busca-ativa.php';
	}

}

new BuscaAtiva;