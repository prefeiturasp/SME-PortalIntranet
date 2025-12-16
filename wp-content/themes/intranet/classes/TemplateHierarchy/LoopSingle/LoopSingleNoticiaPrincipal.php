<?php
namespace Classes\TemplateHierarchy\LoopSingle;
use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;
class LoopSingleNoticiaPrincipal extends LoopSingle
{
	private $tipo_evento;

	public function __construct()
	{
		// Tipo do evento (datas, periodo, premiacao)
		$this->tipo_evento = get_field( 'tipo_evento', get_the_ID() );
		
		$this->init();
	}
	public function init()
	{
		$this->montaHtmlNoticiaPrincipal();
	}

	public function montaHtmlNoticiaPrincipal(){
		if (have_posts()):
			while (have_posts()): the_post();
			echo "<article class='col-12 col-lg-8 content-article content-explica news-content' data-tipo-evento='{$this->tipo_evento}'>";
			$this->getDataPublicacaoAlteracao();

			$current_date = date('Ymd');

			// Obtendo o valor da data de encerramento
			$enc_inscri = get_field('enc_inscri');

			// Verificando se a data de encerramento é menor que a data atual
			$status_prefix = ($enc_inscri < $current_date) ? 'ENCERRADO - ' : '';
			$texto_subtitulo = ($enc_inscri < $current_date) ? 'Sorteio' : 'Sorteio será realizado';

			echo '<h2 class="titulo-noticia-principal mb-3" id="'.get_post_field( 'post_name', get_post() ).'">'. $status_prefix . get_the_title().'</h2>';
			//echo $this->getSubtitulo(get_the_ID(), 'h3');
			$dataSorteio = ($enc_inscri < $current_date) ? obter_ultima_data_sorteio( get_the_ID() ) : obter_proxima_data_sorteio( get_the_ID() );
			if($dataSorteio){
				echo '<h3>' . $texto_subtitulo . ' ' . $dataSorteio . '</h3>';	
			}				
			//echo "<hr><br>";
			
			$image = get_the_post_thumbnail( $post_id, 'default-image', array( 'class' => 'img-fluid mb-4 d-block mx-auto my-0' ) );
			if($image) :
				echo $image;			
			endif;

			$content = get_the_content();
			if (trim(wp_strip_all_tags($content)) !== '') {
				echo "<hr><br>";
				the_content();	
			}
				
			echo "<hr>";

			$this->getInfoVisita();

			$this->getFormInscri();			

			$this->getPostLikes();

			echo '</article>';
			endwhile;
		endif;
		wp_reset_query();
	}
	public function getDataPublicacaoAlteracao(){
		//padrão de horario G\hi
		echo '<p class="data"><span class="display-autor">Publicado em: '.get_the_date('d/m/Y G\hi').' | Atualizado em: '.get_the_modified_date('d/m/Y');
		 
			$term_obj_list = get_the_terms( get_the_ID(), 'category' );
			$i = 0;
			if($term_obj_list){
				echo " - em ";
				foreach($term_obj_list as $categoria){
					if($i == 0){
						echo "<span>" . $categoria->name . "</span>";
						//echo "<a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
					} else {
						echo ", <span>" . $categoria->name . "</span>";
						//echo ", <a href='" . $urlPage . "?categoria=" . $categoria->slug . "'>" . $categoria->name . "</a>";
					}
					$i++;
				}                                        
			}
		
		echo '</span></p>';
	}

	public function getInfoVisita(){
		if ($this->tipo_evento == 'premio') {
			echo '<p class="title-info">Informações do Sorteio:</p>';
		} else {
			echo '<p class="title-info">Informação da Visita/Evento:</p>';
		}

		$resumo = get_field('resumo');
		$link = get_field('link_infos');
		$tituloLink = get_field('texto_do_link');

		if($resumo){
			echo '<p>' . $resumo . '</p>';
		}		

		$dataEvento = obter_datas_evento_formatadas( get_the_ID() );
		$genero = get_field('genero_taxo', get_the_ID());
		$duracao = get_field('duracao');
		$class_indicativa = get_field('class_indicativa');
		$local = get_field('local');
		$local_outros = get_field('local_outros');
		$endereco = get_field('endereco');

		if ( $this->tipo_evento === 'periodo' ) {
			$info_periodo_evento = get_field( 'evento_periodo', get_the_ID() );
		}
		
		echo '<p>';

			echo '<strong>O que é: </strong> ' . get_the_title() . '</br>';
			
			if(!empty($dataEvento) && $this->tipo_evento === 'data'){
				echo '<strong>Data: </strong> ' . $dataEvento . '</br>';
			}

			//Aparece apenas em sorteios do tipo Período
			if( isset( $info_periodo_evento['descricao'] ) && !empty( $info_periodo_evento['descricao'] ) ){
				echo '<strong>Período: </strong> ' . esc_html( $info_periodo_evento['descricao'] ) . '</br>';
			}
			
			if($genero){
				echo '<strong>Gênero: </strong> ' . $genero->name . '</br>';
			}
			if($duracao){
				echo '<strong>Duração: </strong> ' . $duracao . '</br>';
			}
			if($class_indicativa){
				echo '<strong>Classificação Indicativa: </strong> ' . $class_indicativa . '</br>';
			}
			if($local && $local != 'outros'){
				$term = get_term($local);

				if ($term && !is_wp_error($term)) {
					echo '<strong>Local: </strong> ' . $term->name . '</br>';
				}
				
			}
			if($local && $local == 'outros'){
				echo '<strong>Local: </strong> ' . $local_outros . '</br>';
			}
			if($endereco){
				echo '<strong>Endereço: </strong> ' . $endereco . '</br>';
			} elseif($local && $local != 'outros' && $term->description){
				echo '<strong>Endereço: </strong> ' . $term->description . '</br>';
			}
		echo '</p>';

		if($link){
			if ($tituloLink) {
				echo '<p><strong>Link para mais informações:</strong> <a href="' . $link . '" target="_blank">' . $tituloLink . '</a></p>';
			} else {
				echo '<p><strong>Link para mais informações:</strong> <a href="' . $link . '" target="_blank">Saiba Mais</a></p>';
			}
		}

		$regras_info = get_field('regras_info');

		if($regras_info){
			echo '<hr>';
			echo '<p class="title-info">Informações importantes:</p>';
			echo $regras_info;
		}

		echo '<p class="title-info">Boa sorte a todos!</p>';
		echo '<hr>';

		//EXIBE LISTA DE CONTEMPLADOS DO SORTEIO
		echo do_shortcode('[exibe_tab_resultado_pagina]');
		
	}
	public function getFormInscri(){
		$user_id = get_current_user_id();
		$current_date = obter_data_com_timezone('Ymd', 'America/Sao_Paulo');
		$dataLimite = get_field('enc_inscri');
		$parceira = get_field('parceira', 'user_' . $user_id);
		$this->tipo_evento = get_field('tipo_evento');

		if($dataLimite >= $current_date){

			if ($_SERVER["REQUEST_METHOD"] === "POST") { //requisição POST
				global $wpdb;

				// Sanitização e captura dos dados do formulário
				$user_id = get_current_user_id(); // Pega o ID do usuário logado (se aplicável)
				$cpf = isset($_POST['cpf']) ? preg_replace('/\D/', '', $_POST['cpf']) : ''; // Remove caracteres não numéricos
				$post_id = get_the_ID(); // ID do post atual
				$nome_completo = sanitize_text_field($_POST['nomeComp']);
				$email_institucional = sanitize_email($_POST['emailInsti']);
				$email_secundario = sanitize_email($_POST['emailSec']);
				$celular = sanitize_text_field($_POST['celular']);
				$dre = sanitize_text_field($_POST['dre']);
				$telefone_comercial = sanitize_text_field($_POST['telCom']);
				$cargo_principal = sanitize_text_field($_POST['cargo_principal']);
				$unidade_setor = sanitize_text_field($_POST['uniSetor']);
				$disciplina = sanitize_text_field($_POST['disciplina']);
				$ciente = isset($_POST['ciente']) ? 1 : 0;
				$remanescentes = isset($_POST['remanescentes']) ? 1 : 0;
				$datas_escolhidas = ( isset( $_POST['datas'] ) && is_array( $_POST['datas'] ) ) ?  $_POST['datas'] : [];

				// Verifica se o CPF já está inscrito para este post/evento
				$tabela = $wpdb->prefix . "inscricoes";
				$existe = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*) FROM $tabela WHERE post_id = %d AND cpf = %s",
					$post_id, $cpf
				));

				if ($existe) {
					echo "<script>
						jQuery(document).ready(function ($) {
							Swal.fire({
								icon: 'warning',
								title: 'Você já está inscrito neste sorteio!',
								text: 'Agora é só aguardar e torcer. Boa sorte!',
								confirmButtonText: 'Fechar',
							});
						});
					</script>";
					//return;
				} else {

					// Insere os dados na tabela
					$insert = $wpdb->insert(
						$tabela,
						[
							'user_id' => $user_id ?: null,
							'cpf' => $cpf,
							'post_id' => $post_id,
							'nome_completo' => $nome_completo,
							'email_institucional' => $email_institucional,
							'email_secundario' => $email_secundario,
							'celular' => $celular,
							'dre' => $dre,
							'telefone_comercial' => $telefone_comercial ?: null,
							'cargo_principal' => $cargo_principal,
							'unidade_setor' => $unidade_setor,
							'disciplina' => $disciplina ?: null,
							'ciente' => $ciente,
							'remanescentes' => $remanescentes,
							'data_inscricao' => current_time('mysql')
						],
						[
							'%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%d', '%s'
						]
					);

					if ($insert) {

						$inscricao_id = $wpdb->insert_id;

						if ( $datas_escolhidas ) {
							$datas = array_map( 'sanitize_text_field', $datas_escolhidas );							

							foreach ( $datas as $data ) {
								$wpdb->insert(
									$wpdb->prefix . 'inscricao_datas',
									[
										'inscricao_id' => $inscricao_id,
										'data_evento'  => $data,
									],
									['%d', '%s']
								);
							}
						}
						
						$tipo_usuario = $parceira ? 'parceira' : 'servidor';
						echo $this->exibe_mensagem_por_tipo_usuario( 'cadastro_realizado', $tipo_usuario );

					} else {
						echo "<script>
							jQuery(document).ready(function ($) {
								Swal.fire({
									icon: 'error',
									title: 'Erro ao salvar a inscrição',
									text: 'Houve um erro ao salvar a inscrição. Tente novamente, caso o problema persista entre em contato com o intranet.beneficios@sme.prefeitura.sp.gov.br.',
									confirmButtonText: 'Fechar',
								});
							});
						</script>";
					}

				}

				
			} // fim requisição POST

			
			//if(!$parceira){
				$post_id = get_the_ID();

				// Verifica se o usuário já está cadastrado no sorteio atual
				global $wpdb;
				$tabela_inscricoes = $wpdb->prefix . 'inscricoes'; // Substitua pelo nome da sua tabela
				$usuario_cadastrado = $wpdb->get_var($wpdb->prepare(
					"SELECT COUNT(*) FROM $tabela_inscricoes WHERE user_id = %s AND post_id = %d",
					$user_id, $post_id
				));

				$ativo = false;
				$tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';
				$cpf = get_field('cpf', 'user_' . $user_id);
				
				$sancao_usuario = $wpdb->get_var($wpdb->prepare(
					"SELECT data_validade FROM $tabela_sancoes WHERE cpf = %s",
					$cpf
				));

				if ($sancao_usuario) {
					$hoje = new \DateTime('today', new \DateTimeZone('America/Sao_Paulo')); 
					$validade = new \DateTime($sancao_usuario, new \DateTimeZone('America/Sao_Paulo'));

					if ($validade >= $hoje) {
						$ativo = true;
						$validadeMaisUm = clone $validade;
						$validadeMaisUm->modify('+1 day');
						$dataPermissao = $validadeMaisUm->format('d/m/Y');
					}
				}

				// Retorna a resposta
				if ($usuario_cadastrado && !$parceira && !$ativo) { // usuario ja cadastrado
					
					$dataSorteio = get_field('data_sorteio');			
					$dateTime = \DateTime::createFromFormat('Ymd', $dataSorteio);
					
					if ($dateTime) {
						
						$dataFormatada = $dateTime->format('l, d \d\e F \d\e Y');

						// Traduz os dias da semana e meses para português
						$diasSemana = [
							'Monday'    => 'segunda-feira',
							'Tuesday'   => 'terça-feira',
							'Wednesday' => 'quarta-feira',
							'Thursday'  => 'quinta-feira',
							'Friday'    => 'sexta-feira',
							'Saturday'  => 'sábado',
							'Sunday'    => 'domingo',
						];

						$meses = [
							'January'   => 'janeiro',
							'February'  => 'fevereiro',
							'March'     => 'março',
							'April'     => 'abril',
							'May'       => 'maio',
							'June'      => 'junho',
							'July'      => 'julho',
							'August'    => 'agosto',
							'September' => 'setembro',
							'October'   => 'outubro',
							'November'  => 'novembro',
							'December'  => 'dezembro',
						];

						$dataFormatada = str_replace(
							array_keys($diasSemana),
							array_values($diasSemana),
							$dataFormatada
						);

						$dataFormatada = str_replace(
							array_keys($meses),
							array_values($meses),
							$dataFormatada
						);

					}
					
					//if(!$this->verificaExibicaoListaSorteados()){ ?>
						<div class="msg-encerrado text-center">
							<h3>Sua inscrição foi realizada com sucesso!</h3>
							<p>Agora é só aguardar e torcer, o sorteio será realizado <strong><?= $dataFormatada ?>,</strong><br>
							e a lista de ganhadores será divulgada nesta página e por e-mail. Fique atento!</p>
							<button id="cancelarInscricao" class="btn btn-outline-primary mb-4">Cancelar Inscrição</button>
						</div>
					<?php //}
				} elseif($ativo && !$parceira) { // usuario com sação
					?>
						<div class="msg-encerrado text-center">
							<h3>Atenção!</h3>
							<p>Você está impedido de se inscrever em qualquer sorteio, devido à sua ausência em um evento anterior. Você poderá participar de novos sorteios a partir de <?= $dataPermissao; ?></p>
						</div>
					<?php					
				} else { ?>

					<div class="form-inscri">
						<div class="form-title">
							<h3>Preencha o formulário abaixo com seus dados:</h3>

							<div class="inscri-limite">
								<?php
									
									$dateTime = \DateTime::createFromFormat('Ymd', $dataLimite);

									if($dateTime){
										echo '<p>Inscrições até ' . $dateTime->format('d/m/Y') . '</p>';
									}
								?>
							</div>
						</div>

						<form action="<?= get_the_permalink(); ?>" method="post" id="form-inscri">				

							<div class="form-row">
								<?php
									if(!$parceira)
										$nome = esc_html(get_user_meta($user_id, 'first_name', true)) . ' ' . esc_html(get_user_meta($user_id, 'last_name', true));
								?>
								<div class="form-group col">
									<label for="nomeComp">Nome completo <span>*</span></label>
									<?php if(!$parceira): ?>
										<input type="text" class="form-control" id="nomeComp" value="<?= $nome; ?>" disabled>
										<input type="hidden" name="nomeComp" value="<?= $nome; ?>">
									<?php else: ?>
										<input type="text" class="form-control" name="nomeComp" id="nomeComp" placeholder="Insira seu nome completo">
									<?php endif; ?>
								</div>							
							</div>

							<div class="form-row">
								<?php
									if(!$parceira){
										$user_data = get_userdata($user_id);
										$user_email = $user_data->user_email;
									}
								?>

								<div class="form-group col">
									<?php if(!$parceira): ?>
										<label for="emailInsti">E-mail Institucional ou de Uso Principal <span>*</span></label>
										<?php if ($user_email && strpos($user_email, '@sme.prefeitura') !== false) : ?>								
											<input type="text" name="emailInstiDisa" class="form-control" id="emailInsti" value="<?= $user_email; ?>" disabled>
											<input type="hidden" name="emailInsti" value="<?= $user_email; ?>">
										<?php else: ?>
											<input type="text" name="emailInsti" class="form-control" id="emailInsti" placeholder="email@sme.prefeitura.sp.gov.br">
										<?php endif; ?>
									<?php else: ?>
										<label for="emailInsti">E-mail Principal <span>*</span></label>
										<input type="text" name="emailInsti" class="form-control" id="emailInsti" placeholder="Insira seu e-mail principal">
									<?php endif; ?>
									
								</div>

								<?php
									if(!$parceira){
										$cpf = get_field('cpf', 'user_' . $user_id);
										if($cpf){
											$cpf = preg_replace('/[^0-9]/', '', $cpf);
											$cpf = substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
										}
									}
								?>
								
								<div class="form-group col">
									<label for="cpf">CPF <span>*</span></label>
									<?php if ( ($cpf && !$parceira) && strlen($cpf) < 14 ): ?>
										<input type="text" name="cpf" class="form-control" id="cpf" value="<?= $cpf; ?>">
									<?php elseif($cpf && !$parceira): ?>
										<input type="text" name="cpfDisa" class="form-control" id="cpf" value="<?= $cpf; ?>" disabled>
										<input type="hidden" name="cpf" value="<?= $cpf; ?>">
									<?php else: ?>
										<input type="text" name="cpf" class="form-control" id="cpf" placeholder="000.000.000-00">
									<?php endif; ?>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col">
									<label for="emailSec">E-mail Secundário <span>*</span></label>
									<?php if ($user_email && strpos($user_email, '@sme.prefeitura') == false && !$parceira) : ?>								
										<input type="text"  name="emailSec" class="form-control" id="emailSec" value="<?= $user_email; ?>">
									<?php else: ?>
										<input type="text"  name="emailSec" class="form-control" id="emailSec" placeholder="email@provedor.com.br">
									<?php endif; ?>
								</div>

								<?php
									if(!$parceira)
										$celular = get_field('celular', 'user_' . $user_id); ?>
								<div class="form-group col">
									<label for="celular">Telefone Celular <span>*</span></label>
									<?php if($celular && !$parceira): ?>
										<input type="text" name="celular" class="form-control" id="celular" placeholder="(00) 0000-0000" value="<?= $celular; ?>">
									<?php else: ?>
										<input type="text" name="celular" class="form-control" id="celular" placeholder="(00) 0000-0000">
									<?php endif; ?>
								</div>
							</div>

							<div class="form-row">
								<?php
								
									$dre = '';
									
									if(!$parceira)
										$dre = get_field('dre', 'user_' . $user_id);
									
									$dres = array(
										"SME",
										"DRE Butantã",
										"DRE Campo Limpo",
										"DRE Capela do Socorro",
										"DRE Freguesia/Brasilândia",
										"DRE Guaianases",
										"DRE Ipiranga",
										"DRE Itaquera",
										"DRE Jaçanã/Tremembé",
										"DRE Penha",
										"DRE Pirituba",
										"DRE Santo Amaro",
										"DRE São Mateus",
										"DRE São Miguel",
										"Outros"
									);
								?>
								<div class="form-group col">
									<label for="dre">DRE/SME <span>*</span></label>

									<select class="form-control" name="dre" name="dre" id="dre">
										<option value="">-- Selecione --</option>
										<?php foreach ($dres as $opcao) : ?>
											<option value="<?php echo esc_attr($opcao); ?>" <?php selected($opcao, $dre); ?>>
												<?php echo esc_html($opcao); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>

								<div class="form-group col">
									<label for="telCom">Telefone Comercial</label>
									<input type="text" name="telCom" class="form-control" id="telCom" placeholder="(00) 0000-0000">
								</div>
							</div>

							<div class="form-row">
								<?php 
									if(!$parceira)
										$cargo = get_field('cargo_principal', 'user_' . $user_id);
								?>
								<div class="form-group col">
									<label for="cargo_principal">Cargo atual <span>*</span></label>
									<input type="text" name="cargo_principal" class="form-control" id="cargo_principal" placeholder="Cargo Atual" value="<?= $cargo; ?>">
								</div>

								<div class="form-group col">
									<?php 
										if(!$parceira)
											$local = get_field('local', 'user_' . $user_id);
									?>
									<label for="uniSetor">Unidade Escolar ou Setor <span>*</span></label>
									<input type="text" name="uniSetor" class="form-control" id="uniSetor" placeholder="Nome da Unidade Escolar ou Setor" value="<?= $local; ?>">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col">
									<label for="disciplina">Se professor indicar a disciplina que leciona</label>
									<input type="text" name="disciplina" class="form-control" id="disciplina" placeholder="Insira o nome da disciplina que leciona">
								</div>							
							</div>

							<?php if ( $this->tipo_evento === 'data' && $datas_disponivies = get_field('evento_datas') ) : ?>
								<div class="form-row">
									<div class="form-group col" id="grupo-datas">

										<?php if ($this->tipo_evento == 'premio') : ?>
											<label for="datas">Selecione os prêmios que deseja participar do sorteio: <span>*</span></label>
										<?php else : ?>
											<label for="datas">Selecione a(s) data(s) que deseja participar: <span>*</span></label>
										<?php endif; ?>
										
										<?php foreach ( $datas_disponivies as $data ) : ?>
											<?php
											$data_hora = $data['data']; // formato Y-m-d H:i:s
											$disponivel = verifica_disponibilidade_data_inscricao(get_the_id(), $data_hora);

											if ( $disponivel ) :
												$id_input = 'data-' . preg_replace('/[^0-9]/', '', $data_hora); // remove caracteres não numéricos
												$label_formatada = date('d/m/Y H\hi', strtotime($data_hora)); // dd/mm/aaaa hh(h)mm
											?>
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														name="datas[]"
														value="<?php echo esc_attr( $data_hora ); ?>"
														id="<?php echo esc_attr( $id_input ); ?>"
													>
													<?php
														$timestamp = strtotime($data_hora);
														$hora = date('H', $timestamp);
														$minutos = date('i', $timestamp);

														if ($minutos === '00') {
															$label_formatada = date('d/m/Y', $timestamp) . " {$hora}h";
														} else {
															$label_formatada = date('d/m/Y', $timestamp) . " {$hora}h{$minutos}";
														}
													?>
													<label class="form-check-label" for="<?php echo esc_attr( $id_input ); ?>">
														<?php echo esc_html( $label_formatada ); ?>
													</label>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>							
								</div>
							<?php endif; ?>

							<?php if ( $this->tipo_evento === 'premio' && $datas_disponivies = get_field('evento_premios') ) : ?>
								<div class="form-row">
									<div class="form-group col" id="grupo-datas">

										<?php if ($this->tipo_evento == 'premio') : ?>
											<label for="datas">Selecione os prêmios que deseja participar do sorteio: <span>*</span></label>
										<?php else : ?>
											<label for="datas">Selecione a(s) data(s) que deseja participar: <span>*</span></label>
										<?php endif; ?>

										<?php foreach ( $datas_disponivies as $data ) : ?>
											<?php
											$data_hora = $data['data']; // formato Y-m-d H:i:s
											$disponivel = verifica_disponibilidade_data_inscricao(get_the_id(), $data_hora, true);

											if ( $disponivel ) :
												$id_input = 'data-' . preg_replace('/[^0-9]/', '', $data_hora); // remove caracteres não numéricos												
											?>
												<div class="form-check">
													<input
														class="form-check-input"
														type="checkbox"
														name="datas[]"
														value="<?php echo esc_attr( $data_hora ); ?>"
														id="<?php echo esc_attr( $id_input ); ?>"
													>													
													<label class="form-check-label" for="<?php echo esc_attr( $id_input ); ?>">
														<?php echo $data['premio']; ?>
													</label>
												</div>
											<?php endif; ?>
										<?php endforeach; ?>
									</div>							
								</div>
							<?php endif; ?>

							<?php if ( $this->tipo_evento === 'periodo' ) : ?>
								<div class="form-row px-1 pt-2 pb-4">
									<em>Participe do sorteio informando os dados acima e, caso seja sorteado(a), poderá utilizar seu ingresso durante o período destacado na descrição do evento.</em>
								</div>
							<?php endif; ?>

							<div class="form-row">
								<div class="form-group col">

									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="ciente" value="1" id="ciente">
										<label class="form-check-label" for="ciente">
											Estou ciente das informações apresentadas e concordo em enviar meus dados para o sorteio.
										</label>
									</div>
									
									<?php /*
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="remanescentes" value="1" id="remanescentes">
										<label class="form-check-label" for="remanescentes">
											Tenho disponibilidade para concorrer a eventuais ingressos remanescentes de outros sorteios que ocorrem essa semana para os quais não fiz a inscrição.
										</label>
									</div>
									*/ ?>

								</div>							
							</div>

							<div class="buttons-group text-right">
								<a href="javascript:history.back()" class="btn btn-outline-primary">Voltar</a> 
								<input type="submit" value="Enviar" class="btn btn-primary" id="botaoEnviar">
							</div>
							

						</form>
						
					</div>
					
				<?php
					

				} // fim usuario ja cadastrado

			//}				

		} else {
			$dataSorteio = get_field('data_sorteio');
			
			$dateTime = \DateTime::createFromFormat('Ymd', $dataSorteio);

			
			if ($dateTime) {
				
				$dataFormatada = $dateTime->format('l, d \d\e F \d\e Y');

				// Traduz os dias da semana e meses para português
				$diasSemana = [
					'Monday'    => 'segunda-feira',
					'Tuesday'   => 'terça-feira',
					'Wednesday' => 'quarta-feira',
					'Thursday'  => 'quinta-feira',
					'Friday'    => 'sexta-feira',
					'Saturday'  => 'sábado',
					'Sunday'    => 'domingo',
				];

				$meses = [
					'January'   => 'janeiro',
					'February'  => 'fevereiro',
					'March'     => 'março',
					'April'     => 'abril',
					'May'       => 'maio',
					'June'      => 'junho',
					'July'      => 'julho',
					'August'    => 'agosto',
					'September' => 'setembro',
					'October'   => 'outubro',
					'November'  => 'novembro',
					'December'  => 'dezembro',
				];

				$dataFormatada = str_replace(
					array_keys($diasSemana),
					array_values($diasSemana),
					$dataFormatada
				);

				$dataFormatada = str_replace(
					array_keys($meses),
					array_values($meses),
					$dataFormatada
				);

			}
				
			if(!$this->verificaExibicaoListaSorteados()){

				$dataSorteio = obter_ultima_data_sorteio( get_the_ID() );
				echo '<div class="msg-encerrado text-center">';
					echo '<h3>Inscrições Encerradas!</h3>';
					echo '<p>O sorteio será realizado ' . $dataSorteio . ', <br>a lista de ganhadores será divulgada nesta página. Fique atento!</p>';
				echo '</div>';

			}
	
		};
	}

	public function verificaExibicaoListaSorteados(){
		$post_id = get_the_id();
		$exibicaoPagina = get_post_meta($post_id, 'exibe_resultado_pagina', true);
		if ($exibicaoPagina == '1') {
			return true;
		} else {
			return false;
		}
	}

	public function getPostLikes(){
		echo '<div class="d-flex justify-content-between">';
			echo '<div class="likes">';
			
				global $wpdb;
				$l = 0;
				$postid = get_the_id();
				$clientip  = get_client_ip();
				$row1 = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid' AND clientip = '$clientip'");
				if(!empty($row1)){
					$l = 1;
				}
				$totalrow1 = $wpdb->get_results( "SELECT id FROM $wpdb->post_like_table WHERE postid = '$postid'");
				$total_like1 = $wpdb->num_rows;
			
				$likes = '';
				
				if($l == 1){
					$likes = 'likes';
				}

				if($total_like1 == 1){
					$text_total = 'like';
				} else {
					$text_total = 'likes';
				}

				echo '<div class="post_like">';
					echo '<a class="pp_like ' . $likes . '" id="pp_like_' . get_the_id() . '" href="#" data-id="' . get_the_id() . '"><i class="fa fa-heart" aria-hidden="true"></i></i> <span>' . $total_like1 . ' ' . $text_total . '</span></a>';
				echo '</div>';
				
			echo '</div>';			
		echo '</div>';
	}
	public function getArquivosAnexos(){
		$unsupported_mimes  = array( 'image/jpeg', 'image/gif', 'image/png', 'image/bmp', 'image/tiff', 'image/x-icon' );
		$all_mimes          = get_allowed_mime_types();
		$accepted_mimes     = array_diff( $all_mimes, $unsupported_mimes );

		$attachments = get_posts( array(
			'post_type' => 'attachment',
			'post_mime_type'    => $accepted_mimes,
			'posts_per_page' => -1,
			'post_parent' => get_the_ID(),
			'orderby'	=> 'ID',
			'order'	=> 'ASC',
			'exclude'     => get_post_thumbnail_id()
		) );
		if ( $attachments ) {
			echo '<section id="arquivos-anexos">';
			echo '<h2>Arquivos Anexos</h2>';
			foreach ( $attachments as $attachment ) {
				echo '<article>';
				echo '<p><a target="_blank" style="font-size:26px" href="'.$attachment->guid.'"><i class="fa fa-file-text-o fa-3x" aria-hidden="true"></i> Ir para '. $attachment->post_title.'</a></p>';
				echo '<article>';
			}
			echo '</section>';
		}
	}
	public function getCategorias($id_post){
		$categorias = get_the_category($id_post);
		foreach ($categorias as $categoria){
			$category_link = get_category_link( $categoria->term_id );
			echo '<a href="'.$category_link.'"><span class="badge badge-pill badge-light border p-2 m-2 font-weight-normal">ir para '.$categoria->name.'</span></a>';
		}
	}

	private function exibe_mensagem_por_tipo_usuario( string $tipo_mensagem, string $tipo_usuario ) {
		$mensagem = '';
		switch ( $tipo_mensagem ) {
			case 'cadastro_realizado':

				if ( $tipo_usuario === 'parceira' ) {

					$mensagem = "<script>
						jQuery(document).ready(function ($) {
			
							if (window.history.replaceState) {
								window.history.replaceState(null, null, window.location.href);
							}
			
							Swal.fire({
								icon: 'success',
								title: 'Inscrição realizada com sucesso!',
								html: '<p>Agora é só aguardar e torcer. Boa sorte!</p><p>Caso deseje cancelar sua inscrição, acesse novamente a mesma notícia do sorteio, informe o <strong>CPF</strong> no formulário de inscrição e siga as instruções exibidas.</p>',
								confirmButtonText: 'Fechar',
							});
						});
					</script>";

				} else {
					$mensagem = "<script>
						jQuery(document).ready(function ($) {
			
							if (window.history.replaceState) {
								window.history.replaceState(null, null, window.location.href);
							}
			
							Swal.fire({
								icon: 'success',
								title: 'Inscrição realizada com sucesso!',
								html: '<p>Agora é só aguardar e torcer. Boa sorte!</p><p>Caso deseje cancelar sua inscrição, acesse novamente a mesma notícia do sorteio, siga as instruções exibidas e faça o cancelamento.</p>',
								confirmButtonText: 'Fechar',
							});
						});
					</script>";
				}
				
				break;
			
			default:
				# code...
				break;
		}

		return $mensagem;
		
	}
}
