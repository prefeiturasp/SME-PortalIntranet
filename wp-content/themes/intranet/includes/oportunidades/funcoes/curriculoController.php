<?php

class Curriculo
{

	/**
	 * Busca todos os dados necessários para renderizar o currículo.
	 *
	 * @param int $user_id
	 * @return array
	 */
	public static function obter_dados(int $user_id, array $opcoes = []): array
	{
		$curriculo = Inscricao::obter_curriculo_usuario($user_id);

		if (!$curriculo) {
			return [];
		}

		return [
			'curriculo'       => $curriculo,
			'vivencias'       => Inscricao::obter_vivencias_curriculo($curriculo->id),
			'informatica'     => Inscricao::obter_informatica_curriculo($curriculo->id),
			'comportamental'  => Inscricao::obter_comportamental_curriculo($curriculo->id),
			'opcoes'          => $opcoes,
		];
	}

	/**
	 * Gera o HTML do currículo.
	 *
	 * @param array $dados
	 * @return string
	 */
	public static function gerar_html(array $dados): string
	{
		if (empty($dados['curriculo'])) {
			return '
				<div class="notice notice-warning inline">
					<p><strong>Currículo não encontrado.</strong></p>
				</div>
			';
		}

		ob_start();

		get_template_part(
			'includes/oportunidades/template-parts/curriculo',
			null,
			$dados
		);

		return ob_get_clean();
	}

	/**
	 * Renderiza o currículo.
	 *
	 * @param int $user_id
	 * @param array $opcoes
	 * @return string
	 */
	public static function visualizar(int $user_id, array $opcoes = []): string
	{
		$dados = self::obter_dados($user_id, $opcoes);

		return self::gerar_html($dados);
	}

	/**
	 * Inicializa os hooks.
	 */
	public static function init(): void
	{
		add_action(
			'wp_ajax_visualizar_curriculo',
			[self::class, 'ajax_visualizar']
		);
	}

	/**
	 * AJAX responsável por retornar o HTML do currículo.
	 */
	public static function ajax_visualizar(): void
	{
		if (
			!isset($_POST['nonce']) ||
			!wp_verify_nonce($_POST['nonce'], 'visualizar_curriculo')
		) {
			wp_send_json_error([
				'mensagem' => 'Token inválido.'
			]);
		}

		$user_id = isset($_POST['user_id'])
			? absint($_POST['user_id'])
			: 0;

		if (!$user_id) {
			wp_send_json_error([
				'mensagem' => 'Currículo inválido.'
			]);
		}

		wp_send_json_success([
			'html' => self::visualizar($user_id)
		]);
	}

    public static function pode_visualizar_curriculo($current_user, $user_id): bool {
        // Lista de roles que podem visualizar qualquer currículo
        $roles_permitidas = array(
            'administrator',
            'gestor_unidade',
            'admin_portal',
        );
        
        // Verifica se o usuário tem alguma das roles permitidas
        foreach ($roles_permitidas as $role) {
            if (in_array($role, (array) $current_user->roles)) {
                return true;
            }
        }
        
        // Para os demais, apenas o próprio currículo
        return $current_user->ID === $user_id;
    }

}

Curriculo::init();