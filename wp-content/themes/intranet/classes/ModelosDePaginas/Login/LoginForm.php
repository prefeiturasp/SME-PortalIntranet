<?php

namespace Classes\ModelosDePaginas\Login;

class LoginForm
{

	public function __construct()
	{
		$this->montaHtmlForm();
	}

	public function montaHtmlForm(){
		
		// Se ja estiver conectado
		//if (is_user_logged_in() && !is_admin()):
			//echo "<h2>Você já está conectado!</h2>";
		//else:
		// Inclui o formulario de login
		?>
			<div class='core_login_form'>
				<h2>Bem-vindo a Intranet</h2>
				<hr>
				<?php
					// Mensagem de erro exibida na tela
					$page_showing = basename($_SERVER['REQUEST_URI']);

					if (strpos($page_showing, 'failed') !== false) {
						echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
  							<strong>ERRO:</strong> Usuário e/ou senha inválidos.
  							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    						<span aria-hidden="true">&times;</span>
  							</button>
						</div>';
					} elseif (strpos($page_showing, 'blank') !== false ) {						
						echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
							<strong>ERRO:</strong> Usuário e/ou senha estão vazios.
  							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    						<span aria-hidden="true">&times;</span>
  							</button>
						</div>';
					} elseif (strpos($page_showing, 'noresponse') !== false ) {						
						echo '<div class="alert alert-warning alert-dismissible fade show" role="alert">
							<strong>ERRO:</strong> Não foi possível fazer login. Tente novamente agora ou volte mais tarde.
  							<button type="button" class="close" data-dismiss="alert" aria-label="Close">
    						<span aria-hidden="true">&times;</span>
  							</button>
						</div>';
					}
				
					$args = array(
					'redirect' => home_url(), // Apos login redireciona para a home
					'id_username' => 'user', // ID no input de usuario
					'id_password' => 'pass', // ID no input da senha
					'label_username' => __( 'Usuário' ),
					'remember'       => false,
					);
					
					wp_login_form( $args ); // Inclui o formulario de login
					
				?>
				<p class="text-center text-duvidas">Em caso de dúvidas, entre em contato com: <br><a href="mailto:intranet.beneficios@sme.prefeitura.sp.gov.br">intranet.beneficios@sme.prefeitura.sp.gov.br</a></p>
			</div>
		<?php
			//endif;		
	}

}