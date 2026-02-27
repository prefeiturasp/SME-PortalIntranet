<?php

namespace Classes\ModelosDePaginas\Login;


use Classes\Lib\Util;

class Login extends Util
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
						<?php							
							new LoginForm();				
						?>
					</div>
				</div>
			</div>
			<?php
			/*
			<div class="modal" tabindex="-1" id="meuModal">
				<div class="modal-dialog modal-dialog-centered">
					<div class="modal-content">
					<div class="modal-header bg-warning">
						<h2 class="modal-title">Atenção!</h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<p>No momento, o acesso à Intranet pode apresentar instabilidades. Nossa equipe técnica já está trabalhando na correção e na normalização do serviço.</p>
						<p>Agradecemos a compreensão de todos.</p>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-primary" data-dismiss="modal">Fechar</button>
					</div>
					</div>
				</div>
				</div>
			</div>
			<style>
				.modal-header {
					display: -ms-flexbox;
					display: flex;
					-ms-flex-align: start;
					align-items: center;
					-ms-flex-pack: justify;
					justify-content: center;
					padding: 1rem 1rem;
					border-bottom: 1px solid #dee2e6;
					border-top-left-radius: calc(.3rem - 1px);
					border-top-right-radius: calc(.3rem - 1px);
					text-align: center;
				}
			</style>
			<script>
				jQuery(document).ready(function () {
					jQuery('#meuModal').modal('show');
				});
			</script>
			*/
		?>
		<?php
	}
}