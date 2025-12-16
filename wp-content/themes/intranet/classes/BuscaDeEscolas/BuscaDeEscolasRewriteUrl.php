<?php

namespace Classes\BuscaDeEscolas;


class BuscaDeEscolasRewriteUrl
{
	private $my_rewrite_rules_array;
	public function __construct()
	{
		$this->my_rewrite_rules_array = array(
			"busca-de-escolas$" => 'index.php?custom_page=busca-de-escolas',
		);

		add_action('wp_loaded', array($this, 'load_my_rewrite_rules'));

		add_action('rewrite_rules_array', array($this, 'my_rules_array'));

		add_filter('query_vars', array($this, 'my_query_vars'));

		add_action('template_redirect', array($this, 'my_template_redirect'));
	}

	public function load_my_rewrite_rules() {

		// Carrega as regras do WP
		$wp_rules = get_option('rewrite_rules');
		/**
		 * Verifica se todas as regras estão carregadas,
		 * se não, executa a função flush_rules()
		 */
		foreach ($this->my_rewrite_rules_array as $rule => $redirect) {
			if (!isset($wp_rules[$rule])) {
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
				break;
			}
			if ($wp_rules[$rule] != $redirect) {
				global $wp_rewrite;
				$wp_rewrite->flush_rules();
				break;
			}
		}
	}


	public function my_rules_array($rules_array) {
		return $this->my_rewrite_rules_array + $rules_array;
	}


	public function my_query_vars($query_vars) {
		$query_vars[] = "custom_page";
		return $query_vars;
	}


	public function my_template_redirect() {
		global $wpdb;
		global $wp_query;
		global $current_user;

		if ($wp_query->get('custom_page') == 'busca-de-escolas') {

			// Definimos o título da página
			add_filter('wp_title', function ($a){return "Busca de Escolas";});

			if (file_exists(TEMPLATEPATH . '/busca-de-escolas.php')) {
				include(TEMPLATEPATH . '/busca-de-escolas.php');
			} else {
				echo "Erro: Arquivo 'busca-de-escolas.php' não foi encontrado.";
			}

			exit();
		}
	}


}

new BuscaDeEscolasRewriteUrl();