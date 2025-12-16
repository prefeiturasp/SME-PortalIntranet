<!DOCTYPE html>
<?php 

	use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;

	get_header('conf-cancel-sorteio'); 
	//###### É NECESSÁRIO ADICIONAR UMA PÁGINA NO PAINEL DO WORDPRESS COM O NOME: CANCELA INSCRICAO SORTEIOS (Ascendente - > Sorteio)
	$url = get_site_url();

	if ($_POST['idInscrito']){
	
		$result = Envia_Emails_Sorteio_SME::remove_inscrito_by_id($_POST['idInscrito'], $_POST['modelo'], $_POST['datas'] ?? [], $_POST['chave'] ?? '');
		$datas = $_POST['datas'] ?? [];
		$modelo = $_POST['modelo'] ?? '';
		$inscrito = Envia_Emails_Sorteio_SME::get_inscrito_by_id( $_POST['idInscrito'] );
		$tipo_evento = get_field( 'tipo_evento', $inscrito[0]->post_id );
		
		if ($modelo == 'multi' && is_array($datas) && count($datas) > 1){
			if ( $tipo_evento === 'premio' ) {
				$mensagem = 'Inscrição para os prêmios selecionados cancelada com sucesso. Você não participará mais desses sorteios.';
			} else {
				$mensagem = 'Inscrição nas datas selecionadas cancelada com sucesso. Você não participará mais desses sorteios.';
			}
		} else {
			$mensagem = 'Você não está mais participando deste sorteio.';
		}

		if($result['res']){
			echo '<script>
					Swal.fire({
						position: "center",
						title: "Inscrição cancelada com sucesso!",
						text: "'.$mensagem.'",
						icon: "success",
						confirmButtonText: "Fechar"
					});
					setTimeout(() => {
					location.href = "'.$url.'?cancelamento=1";
					}, 2000); 
				</script>';
		}

		exit;
	}

	$dados = json_decode(base64_decode($_REQUEST['ni']), true);
	$idInscrito = $dados['id']; //ID do inscrito do sorteio
	$inscrito = Envia_Emails_Sorteio_SME::get_inscrito_by_id($idInscrito);
	$encerraInscri = get_field('enc_inscri', $inscrito[0]->post_id);
	$current_date = date('Ymd');
	$array_premios = obter_array_premios_sorteio( $inscrito[0]->post_id, true );
	$tipo_evento = get_field( 'tipo_evento', $inscrito[0]->post_id );
	
	if ($dados && isset($dados['id'], $dados['chave'])) {
        $inscricao_id = intval($dados['id']);
        $chave = sanitize_text_field($dados['chave']);

        global $wpdb;
        $link = $wpdb->get_row($wpdb->prepare(
            "SELECT * FROM int_inscricao_cancelamento WHERE inscricao_id = %d AND chave = %s",
            $inscricao_id,
            $chave
        ));

        if ($link) {
            $agora = current_time('mysql');

            if ($link->status == 2) {
                // Cenário 5: Link já utilizado
                header("Location: " . $url . '?expirado=3');
				exit;
            } elseif ($agora > $link->expira_em) {
                // Cenário 4: Link expirado
                header("Location: " . $url . '?expirado=2');
				exit;
            } elseif($encerraInscri < $current_date && $link->status == 1) {
				// Cenário 6: inscrições encerradas
				header("Location: " . $url . '?expirado=1&evento=' . $inscrito[0]->post_id);
				exit;
			} elseif($link->status == 0){
				// Cenário 7: novo link gerado
				header("Location: " . $url . '?expirado=4');
				exit;
			}
        } else {
            echo "<p>Link inválido ou não encontrado.</p>";
        }
    } else {
        echo "<p>Link malformado ou inválido.</p>";
    }


	/*
	if (count($inscrito) < 1 && !$_POST['idInscrito']) {
		header("Location: " . $url . '?expirado=1');
		exit;
	}

	if ($encerraInscri < $current_date && !$_POST['idInscrito']) {
		header("Location: " . $url . '?expirado=2');
		exit;
	}
	
	// Redireciona se o inscrito já foi sorteado
	if (isset($inscrito[0]->sorteado) && $inscrito[0]->sorteado == 1 && !$_POST['idInscrito']) {
		header("Location: " . $url . '?expirado=2');
		exit;
	}
	*/


	$fuso = new DateTimeZone('America/Sao_Paulo');
	$agora = new DateTime('now', $fuso);

	$temFuturo = false;
	$datas = $inscrito[0]->datas;
	if ( $datas ) {
		foreach ($datas as $data) {
			$dt = new DateTime($data, $fuso);
			if ($dt > $agora) {
				$temFuturo = true;
				break;
			}
		}
	}

	// Redireciona se as datas de inscrição já estão expiradas
	/*
	if (!$temFuturo && !$_POST['idInscrito']) {
		header("Location: " . $url . '?expirado=2');
		exit;
	}*/

	$nomeInscrito = $inscrito[0]->nome_completo; //Nome de inscrito do evento
	$cpfInscrito = $inscrito[0]->cpf; //CPF do Inscrito
	$EmailInscrito = $inscrito[0]->email_institucional; //Email do Inscrito
	$tituloEvento = $inscrito[0]->post_title;
	$datas = $inscrito[0]->datas;
	$modelo = $inscrito[0]->modelo;

	$cpfInscrito = substr($cpfInscrito, 0, 3) . '.***.***-' . substr($cpfInscrito, 9, 2);

	function converter_data($data) {
		$dataObj = DateTime::createFromFormat('Y-m-d H:i:s', $data);
		if (!$dataObj) return '';

		$hora = $dataObj->format('H');
		$minuto = (int) $dataObj->format('i');

		if ($minuto === 0) {
			$horaFormatada = "{$hora}h";
		} else {
			$horaFormatada = sprintf("%sh%02d", $hora, $minuto);
		}

		return $dataObj->format('d/m/Y') . ' ' . $horaFormatada;
	}

?>

  <div class="container">
    <div class="logo text-center">
      <img src="https://hom-intranet.sme.prefeitura.sp.gov.br/wp-content/uploads/2022/02/Logo_Educacao.png" alt="Logo Cidade de SP" height="70">
    </div>

    <h2>Cancelamento de Cadastro no Sorteio</h2>
    <p class="description">
		<?php if($inscrito[0]->modelo == 'multi' && count($datas) > 0): ?>
			<?php if ( $tipo_evento === 'premio' ) : ?>
				<span class="semibold">Para cancelar sua inscrição no sorteio do evento</span> <span class="bold"><?= $tituloEvento; ?></span><span class="semibold">, verifique seus dados, escolha o prêmio que deseja cancelar e clique em Cancelar Inscrição.</span>
			<?php else : ?>
				<span class="semibold">Para cancelar sua inscrição no sorteio do evento</span> <span class="bold"><?= $tituloEvento; ?></span><span class="semibold">, verifique seus dados, escolha a data que deseja cancelar e clique em Cancelar Inscrição.</span>
			<?php endif; ?>
		<?php else: ?>
        	<span class="semibold">Para cancelar sua inscrição no sorteio do evento</span> <span class="bold"><?= $tituloEvento; ?></span><span class="semibold">, verifique seus dados e clique em Cancelar Inscrição.</span>
		<?php endif; ?>
    </p>

    <form action="#" method="post">
		<label>Nome completo <span class="required">*</span></label>
		<input type="text" placeholder="Insira seu nome completo" value="<?= $nomeInscrito; ?>" disabled required>
		<input type="hidden" name="idInscrito" id="idInscrito" value="<?= $idInscrito; ?>">

		<div class="form-row">
			<div class="form-group">
				<label>E-mail Principal/Institucional <span class="required">*</span></label>
				<input type="email" placeholder="Insira seu e-mail institucional" value="<?= $EmailInscrito; ?>" disabled required>
			</div>

			<div class="form-group">
				<label>CPF <span class="required">*</span></label>
				<input type="text" placeholder="000.000.000-00" value="<?= $cpfInscrito; ?>" disabled required>
			</div>
		</div>

		<?php if($inscrito[0]->modelo == 'multi' && count($datas) > 1): ?>
			<?php if ( $tipo_evento === 'premio' ) : ?>
				<div class="datas-cancelamento">
					<p>Cancelar minha inscrição para o(s) prêmio(s):</p>
					<?php
					$fuso = new DateTimeZone('America/Sao_Paulo');
					$agora = new DateTime('now', $fuso);

					foreach( $datas as $data ):

						$data_sorteio = $array_premios[$data]['data'];
						$premio = $array_premios[$data]['premio'];
						$dt = new DateTime( $data_sorteio, $fuso );

						if ( $dt <= $agora ) continue; // Pula os prêmios em que a data do sorteio já passou
						?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="data-<?= esc_attr($data); ?>" value="<?= esc_attr($data); ?>" name="datas[]">
							<label class="custom-control-label d-inline-block" for="data-<?= esc_attr($data); ?>">
								<?php echo esc_html( $premio ); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="datas-cancelamento">
					<p>Cancelar minha inscrição para a(s) data(s):</p>
					<?php
						$fuso = new DateTimeZone('America/Sao_Paulo');
						$agora = new DateTime('now', $fuso);
					?>
					<?php foreach($datas as $data): ?>
						<?php 
							$dt = new DateTime($data, $fuso);
							if ($dt <= $agora) continue; // pula datas passadas
						?>
						<div class="custom-control custom-checkbox">
							<input type="checkbox" class="custom-control-input" id="data-<?= esc_attr($data); ?>" value="<?= esc_attr($data); ?>" name="datas[]">
							<label class="custom-control-label d-inline-block" for="data-<?= esc_attr($data); ?>">
								<?= converter_data($data); ?>
							</label>
						</div>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>
			
		<?php endif; ?>

		<?php
			$disabled = '';
			if($inscrito[0]->modelo == 'multi' && count($datas) > 1){
				$disabled = 'disabled';
			}
		?>
		<?php if($inscrito[0]->modelo == 'multi' && count($datas) == 1): ?>
			<input type="hidden" name="datas" value="<?= $datas[0]; ?>">
		<?php endif; ?>
		<input type="hidden" name="modelo" value="<?= $inscrito[0]->modelo; ?>">
		<input type="hidden" name="chave" value="<?= $dados['chave'] ?? ''; ?>">
		<center><button type="submit" id="btn-cancela-inscricao" <?= $disabled; ?>>Cancelar Inscrição</button></center>
    </form>
  </div>

<script>
	document.addEventListener('DOMContentLoaded', function () {
		const checkboxes = document.querySelectorAll('input[name="datas[]"]');

		// Só continua se existirem checkboxes com esse name
		if (checkboxes.length === 0) return;

		const botao = document.getElementById('btn-cancela-inscricao');

		function verificarCheckboxes() {
			const peloMenosUmMarcado = Array.from(checkboxes).some(checkbox => checkbox.checked);
			botao.disabled = !peloMenosUmMarcado;
		}

		// Verifica ao carregar a página
		verificarCheckboxes();

		// Adiciona evento a todos os checkboxes
		checkboxes.forEach(checkbox => {
			checkbox.addEventListener('change', verificarCheckboxes);
		});
	});
</script>