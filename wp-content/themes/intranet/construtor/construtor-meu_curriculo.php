<div class="container">
	<div class="row">
		<div class="col-sm-12">

			<form action="<?= get_the_permalink(); ?>" method="post">

				<div class="accordion" id="meuCurriculoAccordion">

					<!-- ETAPA 1 -->
					<div class="card">

						<div class="card-header" id="headingOne">
							<h2 class="mb-0">
								<button class="btn btn-block text-left"
									type="button"
									data-toggle="collapse"
									data-target="#collapseOne">

									<span class="numeral">1</span> Banco de talentos
								</button>
							</h2>
						</div>

						<div id="collapseOne" class="collapse show">
							<div class="card-body">

								<p>A Secretaria Municipal de Educação disponibiliza o presente formulário com o objetivo de promover a transparência pública, ampliar o acesso à informação e fortalecer as práticas de governança e integridade institucional.</p>

								<p>O formulário destina-se ao cadastro de currículos e perfis profissionais de servidores da Rede Municipal de Ensino, sendo de uso exclusivo desses profissionais. As informações cadastradas serão utilizadas para processos relacionados a cargos de designação.</p>

								<blockquote>
									* Tempo estimado de preenchimento: 30 minutos <br>
									* Procure um local tranquilo e evite interrupções
								</blockquote>

								<div class="aviso">
									<p class="aviso-title"><i class="fa fa-shield" aria-hidden="true"></i> Aviso de Privacidade e Uso de Dados</p>

									<p>As informações fornecidas serão utilizadas exclusivamente para fins de análise de perfil e composição do banco de talentos da Secretaria Municipal de Educação, respeitando os princípios de confidencialidade e proteção de dados.</p>

									<p>Seus dados serão tratados de forma confidencial e em conformidade com a Lei Geral de Proteção de Dados (LGPD).</p>

									<p>Ao prosseguir com o preenchimento deste formulário, você declara estar ciente e de acordo com a coleta e o uso dos seus dados para as finalidades descritas acima.</p>

									<div class="form-group">

										<p class="m-0 label-text">Deseja continuar? <span class="required-icon">*</span></p>

										<div class="form-check">
											<input class="form-check-input"
												type="radio"
												name="avisoPrivacidade"
												id="radioSim"
												value="sim">

											<label class="form-check-label" for="radioSim">
												Sim
											</label>
										</div>

										<div class="form-check">
											<input class="form-check-input"
												type="radio"
												name="avisoPrivacidade"
												id="radioNao"
												value="nao">

											<label class="form-check-label" for="radioNao">
												Não
											</label>
										</div>

									</div>

								</div>

								<!-- BLOCO DE MENSAGEM -->
								<div class="mensagem-bloqueio alert alert-warning mt-3" style="display:none;">
									Para participar do Banco de Talentos, é necessário aceitar os termos de privacidade e uso de dados.
								</div>

							</div>
						</div>

					</div>

					<!-- ETAPA 2 -->
					<div class="card etapa-formulario">

						<div class="card-header" id="headingTwo">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed"
									type="button"
									data-toggle="collapse"
									data-target="#collapseTwo">

									<span class="numeral">2</span> Identificação do Candidato
								</button>
							</h2>
						</div>

						<div id="collapseTwo" class="collapse">
							<div class="card-body">
								Conteúdo etapa 2
							</div>
						</div>

					</div>

					<!-- ETAPA 3 -->
					<div class="card etapa-formulario">

						<div class="card-header" id="headingThree">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed"
									type="button"
									data-toggle="collapse"
									data-target="#collapseThree">

									<span class="numeral">3</span> Formação Acadêmica
								</button>
							</h2>
						</div>

						<div id="collapseThree" class="collapse">
							<div class="card-body">
								Conteúdo etapa 3
							</div>
						</div>

					</div>

					<!-- ETAPA 4 -->
					<div class="card etapa-formulario">
						<div class="card-header" id="headingFour">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
									<span class="numeral">4</span> Vivências Profissionais
								</button>
							</h2>
						</div>
						<div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#accordion">
							<div class="card-body">
								Conteúdo etapa 4
							</div>
						</div>
					</div>

			</form>

		</div>
	</div>
</div>

<script>
jQuery(document).ready(function($){

	// Esconde tudo inicialmente
	$('.etapa-formulario').hide();
	$('.mensagem-bloqueio').hide();

	function controlarEtapas() {

		const valor = $('input[name="avisoPrivacidade"]:checked').val();

		// Nenhuma opção selecionada
		if(!valor) {

			$('.etapa-formulario').slideUp();
			$('.mensagem-bloqueio').hide();

			return;
		}

		// SIM
		if(valor === 'sim') {

			$('.etapa-formulario').slideDown();
			$('.mensagem-bloqueio').hide();

		}

		// NÃO
		if(valor === 'nao') {

			$('.etapa-formulario').slideUp();
			$('.mensagem-bloqueio').fadeIn();

			// Fecha accordions abertos
			$('.etapa-formulario .collapse').collapse('hide');

		}
	}

	// Executa ao trocar radio
	$('input[name="avisoPrivacidade"]').on('change', function(){
		controlarEtapas();
	});

});
</script>