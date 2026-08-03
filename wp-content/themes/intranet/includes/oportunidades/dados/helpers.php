<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * Retorna um valor formatado para exibição.
 */
function valor($valor): string
{
	if ($valor === null || $valor === '') {
		return '<em>—</em>';
	}

	return nl2br(esc_html($valor));
}

/**
 * Converte valores booleanos em Sim/Não.
 */
function sim_nao($valor): string
{
	return (int) $valor === 1 ? 'Sim' : 'Não';
}

/**
 * Traduz um valor utilizando um mapa.
 */
function traduzir($valor, array $mapa): string
{
	if ($valor === null || $valor === '') {
		return '<em>—</em>';
	}

	return $mapa[$valor] ?? esc_html($valor);
}

/**
 * Traduz uma lista de valores utilizando um mapa.
 */
function traduzir_lista(array $valores, array $mapa): string
{
	if (empty($valores)) {
		return '<em>—</em>';
	}

	$traduzidos = [];

	foreach ($valores as $valor) {
		$traduzidos[] = $mapa[$valor] ?? $valor;
	}

	return esc_html(implode(', ', $traduzidos));
}