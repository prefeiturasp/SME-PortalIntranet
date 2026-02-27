<?php

namespace Classes\ModelosDePaginas\Login;


use Classes\Lib\Util;

class LoginRecuperar extends Util
{
	
	public function __construct()
	{

		$this->montaHtmlLogin();
		//contabiliza visualiza��es de noticias
		//setPostViews(get_the_ID()); /*echo getPostViews(get_the_ID());*/

	}

	
	public function montaHtmlLogin(){
		$imagem = get_field('imagem');
		?>

		<div class="container-fluid container-forms" style="background-image: url('<?= $imagem; ?>');">
			<div class="container">
				<div class="row">
					<div class="col-12 col-md-6 offset-md-6">

						<div class='core_login_form'>
							<h2>Esqueceu sua senha</h2>
							<hr>
							<p>Fique atento! A mudança de senha aqui, também acarretará automaticamente na mudança de senha do SGP, Plateia e outros Sistemas. Caso a senha do SGP esteja salva em seus dispositivos, lembre-se de usar a nova senha em seus próximos acessos.</p>
							<p>As orientações para redefinição da sua senha serão enviadas para o seu  e-mail.</p>
							<p>Para usuários das Unidades Parceiras, a senha foi enviada para o e-mail da Unidade Educacional.</p>
							<form id="lost-pass" action="<?= get_the_permalink(); ?>" method="post">
								<p class="login-username">
									<label for="user">Usuário</label>
									<input type="text" name="log" id="user" class="input" value="" size="20" placeholder="Informe o RF/Usuário.">
									<div class="buttons-form text-right">
										<a href="<?= get_home_url(); ?>" class="btn btn-outline-primary" id="cancel">Cancelar</a>
										<input type="submit" value="Continuar" id="continue" class="btn btn-primary">
									</div>
									
								</p>
							</form>
						</div>

					</div>
				</div>
			</div>			
		</div>
		<?php

		function filterEmail($email) {
			$emailSplit = explode('@', $email);
			$email = $emailSplit[0];
			$len = strlen($email);
			for($i = 3; $i < $len; $i++) {
				$email[$i] = '*';
			}
			return $email . '@' . $emailSplit[1];
		}

		echo '<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>';

		if($_POST['log'] && $_POST['log'] != ''){
			
			$usuario = $rf;
			$api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/v1/autenticacao/RecuperarSenha/usuario?sistema=2';
			$response = wp_remote_post( $api_url, array(
				'method'      => 'POST',                    
				'headers' => array( 
					'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99',
					'Content-Type' => 'application/json-patch+json'
				),
				'body' => '"' . $_POST['log'] . '"',
				)
			);

			if ( is_wp_error( $response ) ) {
				//$error_message = $response->get_error_message();
				//echo "Something went wrong: $error_message";
			} else {
				$response = $response;
			}		

			//echo "<pre>";
			//print_r($response);
			//echo "</pre>";
			
			if($response['response']['code'] == 200){		

				$userEmail = filterEmail($response['body']);
				echo "<script>Swal.fire({
					title: 'Email enviado com sucesso!',
  					icon: 'success',
					html:
					'Seu link de troca de senha dos sistemas da SME (Intranet, SGP e outros) foi enviado para " . $userEmail . ", verifique sua caixa de entrada. Se você não reconhece ou não tem acesso a esse e-mail, solicite o reset da senha da Intranet via Whatsapp (+55 61 3247-3192) e, após seu primeiro acesso na Intranet, atualize o e-mail na tela de perfil.',
				})</script>";

				
				
			} elseif($response['response']['code'] == 601){				
				
				if($response['body'] == '"Usuário ou RF não encontrado"'){
					echo "<script>Swal.fire('RF ou Usuário não encontrado!', 'Verifique os dados digitados e tente novamente!', 'info');</script>";
				} else {
					echo "<script>Swal.fire('Email não encontrado!', 'Você não tem um e-mail cadastrado para recuperar sua senha. Solicite o reset da senha da Intranet via Whatsapp (+55 61 3247-3192) e, após seu primeiro acesso na Intranet, atualize o e-mail na tela de perfil.', 'warning');	</script>";
				}
				
			} else {
				echo "<script>Swal.fire('Ocorreu um erro!', 'Por favor tente novamente. Caso o problema persista, procure o responsável pela Intranet na sua unidade.', 'error');	</script>";
			}
			?>
			
			<?php
		} elseif($_POST['log'] && $_POST['log'] == '') {
			echo "<script>Swal.fire('Campo vazio!', 'Insira o seu usuário ou RF', 'error');	</script>";
		}
	}
}