<?php
/**
 * Template Name: Nova senha
 *
 * Allow users to update their profiles from Frontend.
 *
 
*/
$imagem = get_field('imagem');

get_header('forms'); // Loads the header.php template. ?>

<div class="container-fluid container-forms" style="background-image: url('<?= $imagem; ?>');">
			<div class="container">
				<div class="row">
					<div class="col-12 col-md-6 offset-md-6">

						<div class='core_login_form'>
							<h2>Definir nova senha</h2>
							<hr>							
							<form id="newPassForm" action="<?= get_the_permalink(); ?>?token=<?= $_GET['token']; ?>" method="post">
								<p class="login-username">
                                    <div class="requisitos">
                                        <p>Identificamos que você ainda não definiu uma senha pessoal para acesso a Intranet. Este passo é obrigatório para que você tenha acesso ao sistema.</p>
                                        <div class="form-check">
                                            <input class="form-check-input w-auto" type="checkbox" value="" id="ciencia-senha">
                                            <label class="form-check-label" for="ciencia-senha">
                                                <span>*</span> Estou ciente que a nova senha será aplicada para os outros acessos (Portais e Sistemas) da SME, incluindo o SGP.
                                            </label>
                                        </div>                                        
                                        <div class="form-group senha-nova">
                                            <label for="senha-atual"><span>*</span> Nova senha</label>
                                            <input type="password" class="form-control" id="senha-nova" name="senha-nova" placeholder="Nova senha">
                                        </div>
                                        <div class="form-group senha-repita">
                                            <label for="senha-atual"><span>*</span> Confirmação da nova senha</label>
                                            <input type="password" class="form-control" id="senha-repita" name="senha-repita" placeholder="Repita a nova senha">
                                        </div>
                                        
                                        <div class="requisitos-texto">
                                            <strong>Requisitos de segurança da senha:</strong>
                                            <br><br>
                                            Uma letra maiúscula<br>
                                            Uma letra minúscula<br>
                                            As senhas devem ser iguais<br>
                                            Não pode conter espaços em branco<br>
                                            Não pode conter caracteres acentuados<br>
                                            Um número ou símbolo (caractere especial)<br>
                                            Deve ter no mínimo 8 e no máximo 12 caracteres
                                        </div>
                                        
                                    </div>
									<div class="buttons-form text-right">
										<a href="<?= get_home_url(); ?>" class="btn btn-outline-primary" id="cancel">Cancelar</a>
										<input type="submit" value="Continuar" id="newPass" class="btn btn-primary">
									</div>
									
								</p>
							</form>
						</div>

					</div>
				</div>
			</div>			
		</div>

<?php get_footer('forms'); // Loads the footer.php template. ?>

<?php

    function valida_senha($senha){
        if(!preg_match('/^(?=.*\d)(?=.*[@#\-_$%^&+=§!\?])(?=.*[a-z])(?=.*[A-Z])[0-9A-Za-z@#\-_$%^&+=§!\?]{8,12}$/',$senha)) {
           return false;
        } else {
            return true;
        }
    }

    if($_GET['token'] && $_GET['token'] != ''){
        
        $api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/v1/autenticacao/RecuperarSenha/token/validar';
        $response = wp_remote_post( $api_url, array(
            'method'      => 'POST',                    
            'headers' => array( 
                'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99',
                'Content-Type' => 'application/json-patch+json'
            ),
            'body' => '"' . $_GET['token'] . '"',
            )
        );

        if ( is_wp_error( $response ) ) {
            //$error_message = $response->get_error_message();
            //echo "Something went wrong: $error_message";
        } else {
            $response = $response;
        }

        if($response['response']['code'] == 200 && $response['body'] == 'true'){
            $token = true;
        } else {
            $token = false;
            echo "<script>                    
                    window.location.replace('" . get_home_url() . "/index.php/recupere-sua-senha/?pass=new');
                </script>";
        }

        //echo "<pre>";
        //print_r($token);
        //echo "<pre>";

		if(isset($_POST['senha-nova']) && $_POST['senha-nova'] == '' && $_POST['senha-repita'] == '' && $token){
            echo 
                "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Senhas obrigatórias',
                        text: 'Preencha todos os campos de senha.',
                    });
                </script>";
        }

        if($_POST['senha-nova'] != $_POST['senha-repita'] && $token){
            echo 
                "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Senhas diferentes',
                        text: 'As novas senhas não conferem, por gentileza revise e tente novamente.',
                    });
                </script>";
        } else {

            if(isset($_POST['senha-nova'])){
                $senha = $_POST['senha-nova'];
                $validar = valida_senha($senha);
                if($token && $validar){
                    
                    // URL da API
                    $api_url = 'https://hom-smeintegracaoapi.sme.prefeitura.sp.gov.br/api/v1/autenticacao/AlterarSenha';

                    // Conversao do body para JSON
                    $tokenKey = $_GET['token'];

                    $response = wp_remote_post( $api_url ,
                            array(
                                'headers' => array( 
                                    'x-api-eol-key' => 'fe8c65abfac596a39c40b8d88302cb7341c8ec99', // Chave da API
                                ),
                                'body' => array("Token" => "$tokenKey","Senha" => "$senha"),
                            ));

                    //echo "<pre>";
                    //print_r($response);
                    //echo "</pre>";

                    if($response['response']['code'] == 200){
                        
                        echo "<script>                    
                            window.location.replace('" . get_home_url() . "/?login=new');
                        </script>";

                    } elseif($response['body'] == 'A nova senha não pode ser uma das ultimas 5 anteriores'){
                        echo 
                        "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Atenção',
                                text: 'A nova senha não pode ser uma das ultimas 5 anteriores.',
                            });
                        </script>";
                    } else {
                        echo 
                        "<script>
                            Swal.fire({
                                icon: 'error',
                                title: 'Senha não alterada',
                                text: 'A sua senha não foi alterada, tente novamente. Caso o problema persista solicite o reset da senha da Intranet via Whatsapp (+55 61 3247-3192).',
                            });
                        </script>";
                    }
                    
                    /*echo 
                    "<script>
                        Swal.fire({
                            icon: 'success',
                            title: 'Tudo certo',
                            text: 'Ocorreu tudo certo',
                        });
                    </script>";*/
                } else {
                    echo 
                    "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Senhas inválida',
                            text: 'Sua nova senha deve conter letras Maiúsculas, Minúsculas, números e símbolos. Por favor digite outra senha.',
                        });
                    </script>";
                }
            }
            
        }
    }