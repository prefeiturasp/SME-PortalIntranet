<?php
namespace Classes\TemplateHierarchy\LoopCortesias;
use EnviaEmailSme\classes\Envia_Emails_Sorteio_SME;
use WP_Error;

class LoopCortesiasNoticiaPrincipal extends LoopCortesias
{
	private $tipo_evento;
	private $tipo_administracao;
	private $qtd_limite;

	public function __construct()
	{
		// Tipo do evento (datas, periodo, premiacao)
		$this->tipo_evento = get_field( 'tipo_evento', get_the_ID() );
		$this->tipo_administracao = get_field( 'administracao_ingressos', get_the_ID() );
		
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

			$current_date = obter_data_com_timezone( 'Ymd', 'America/Sao_Paulo' );

			// Obtendo o valor da data de encerramento
			$enc_inscri = get_field('enc_inscri');

			// Verificando se a data de encerramento é menor que a data atual
			$status_prefix = ($enc_inscri < $current_date) ? 'ENCERRADO - ' : '';

			echo '<h2 class="titulo-noticia-principal mb-3" id="'.get_post_field( 'post_name', get_post() ).'">'. $status_prefix . get_the_title().'</h2>';
			
			if ( $enc_inscri < $current_date ) {
				echo '<h3>Evento encerrado. Consulte mais detalhes na notícia</h3>';
			} else {
				echo '<h3>Ingressos gratuitos por ordem de inscrição, enquanto houver disponibilidade</h3>';
			}				
			
			$image = get_the_post_thumbnail( get_the_ID(), 'default-image', array( 'class' => 'img-fluid mb-4 d-block mx-auto my-0' ) );
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
		echo '<p class="data"><span class="display-autor">Publicado em: '.get_the_date('d/m/Y G\hi').' | Atualizado em: '.get_the_modified_date('d/m/Y') . ' - em Gratuidade e Cortesias';
		 
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
			echo '<p class="title-info">Informações:</p>';
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
		$genero = get_field('genero_taxo', get_the_ID()); // Tipo de evento
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
				echo '<strong>Data: </strong> <div class="datas-eventos">' . $dataEvento . '</div></br>';
			}

			//Aparece apenas em sorteios do tipo Período
			if( isset( $info_periodo_evento['descricao'] ) && !empty( $info_periodo_evento['descricao'] ) ){
				echo '<strong>Período: </strong> ' . esc_html( $info_periodo_evento['descricao'] ) . '</br>';
			}
			
			if($genero){
				echo '<strong>Tipo de Evento: </strong> ' . $genero->name . '</br>';
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

		$adm = get_field('administracao_ingressos');
		$links = get_field('links_adicionais');
		$validarLinks = $this->array_valido($links);		

		if($adm == 'parceiro' && $validarLinks && $link){
			echo '<p><strong>Links para mais informações:</strong>';
				echo '<ul>';
					if ($tituloLink) {
						echo '<li><a href="' . $link . '" target="_blank">' . $tituloLink . '</a></li>';
					} else {
						echo '<li><a href="' . $link . '" target="_blank">Saiba Mais</a></li>';
					}
					foreach ($links as $link) {
						if (empty($link['link_infos']) && empty($link['texto_do_link'])) {
							continue;
						} elseif(empty($link['texto_do_link']) && !empty($link['link_infos'])) {
							$link['texto_do_link'] = $link['link_infos'];
						}
						echo '<li><a href="' . $link['link_infos'] . '" target="_blank">' . $link['texto_do_link'] . '</a></li>';
					}
				echo '</ul>';
			echo '</p>';
		} else {
			if($link){
				if ($tituloLink) {
					echo '<p><strong>Link para mais informações:</strong> <a href="' . $link . '" target="_blank">' . $tituloLink . '</a></p>';
				} else {
					echo '<p><strong>Link para mais informações:</strong> <a href="' . $link . '" target="_blank">Saiba Mais</a></p>';
				}
			}
		}

		$regras_info = get_field( 'regras_informacoes_evento', );

		if( $regras_info ){
			echo '<hr>';
			echo '<p class="title-info">Informações importantes:</p>';
			echo $regras_info;
		}

		echo '<hr>';

		//EXIBE LISTA DE CONTEMPLADOS DO SORTEIO
		echo do_shortcode('[exibe_tab_resultado_cortesias]');
		
	}
	public function getFormInscri(){
		$user_id = get_current_user_id();
		$current_date = obter_data_com_timezone('Ymd', 'America/Sao_Paulo');
		$dataLimite = get_field('enc_inscri');
		$parceira = get_field('parceira', 'user_' . $user_id);
		$this->tipo_evento = get_field('tipo_evento');
		$qtd_ingressos_inscrito = get_field( 'quantidade_ingressos_inscrito' );
		$datas_disponivies = get_datas_diponiveis( get_the_ID() );

		if( $dataLimite >= $current_date && !empty( $datas_disponivies ) ){

			if ($_SERVER["REQUEST_METHOD"] === "POST") { //requisição POST
				global $wpdb;

				$cpf = isset( $_POST['cpf'] ) ? preg_replace('/\D/', '', $_POST['cpf']) : ''; // Remove caracteres não numéricos
				$post_id = get_the_ID();

				// Verifica se o CPF já está inscrito para este post/evento
				$tabela = $wpdb->prefix . "cortesias_inscricoes";
				$existe = $wpdb->get_var($wpdb->prepare(
					"SELECT 1 FROM $tabela WHERE post_id = %s AND cpf = %d LIMIT 1",
					$post_id, $cpf
				));

				if ($existe) {
					echo "<script>
						jQuery(document).ready(function ($) {
							Swal.fire({
								icon: 'warning',
								title: 'Você já está inscrito neste evento!',
								text: 'Em breve enviaremos por e-mail as instruções para utilização do(s) ingresso(s).',
								confirmButtonText: 'Fechar',
							});
						});
					</script>";
					//return;
				} else {

					// Insere os dados na tabela
					$params = [
						'user_id' => get_current_user_id(),
						'cpf' => $cpf,
						'post_id' => $post_id,
						'nome_completo' => $_POST['nomeComp'],
						'email_institucional' => $_POST['emailInsti'],
						'email_secundario' => $_POST['emailSec'],
						'celular' =>$_POST['celular'],
						'dre' => $_POST['dre'],
						'telefone_comercial' => $_POST['telCom'],
						'cargo_principal' => $_POST['cargo_principal'],
						'unidade_setor' => $_POST['uniSetor'],
						'disciplina' => $_POST['disciplina'],
						'ciente' => $_POST['ciente'],
						'data_inscricao' => current_time('mysql'),
						'acf_id' =>  $_POST['data'],
                        'qtd' =>  $_POST['qtd']
					];
					
					$resultado = resgatar_cortesia( $params );

					if ( !is_wp_error( $resultado ) ) {
						$tipo_usuario = $parceira ? 'parceira' : 'servidor';
						echo $this->exibe_mensagem_por_tipo_usuario( 'cadastro_realizado', $tipo_usuario );
					} else {
						$this->exibe_mensagem_erro( $resultado );
					}

				}

				
			} // fim requisição POST

			
			//if(!$parceira){
				$post_id = get_the_ID();
				$cpf = get_field('cpf', 'user_' . $user_id);
				$cpf = preg_replace('/\D/', '', $cpf);

				// Verifica se o usuário já resgatou cortesias para o evento atual
				global $wpdb;
				$tabela_inscricoes = $wpdb->prefix . 'cortesias_inscricoes';
				$usuario_cadastrado = $wpdb->get_var($wpdb->prepare(
					"SELECT 1 FROM $tabela_inscricoes WHERE cpf = %s AND post_id = %d LIMIT 1",
					$cpf, $post_id
				));

				$ativo = false;
				$tabela_sancoes = $wpdb->prefix . 'inscricao_sancoes';				
				
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
					?>
					<?php if($this->tipo_evento === 'premio') : ?>
						<div class="msg-encerrado text-center">
							<h3>Sua inscrição foi realizada com sucesso!</h3>
							<p>
								Você garantiu seu(s) prêmio(s)! Fique atento ao e-mail informado,</br> 
								em breve enviaremos as instruções de retirada e utilização.
							</p>
							<button id="cancelarInscricaoCortesia" class="btn btn-outline-primary mb-4">Cancelar Inscrição</button>
						</div>
						<?php
					else : ?>
						<div class="msg-encerrado text-center">
							<h3>Sua inscrição foi realizada com sucesso!</h3>
							<p>
								Você garantiu seu(s) ingresso(s)! Fique atento ao e-mail informado,</br> 
								pois enviaremos as instruções de utilização e todas as informações sobre o evento.
							</p>
							<button id="cancelarInscricaoCortesia" class="btn btn-outline-primary mb-4">Cancelar Inscrição</button>
						</div>
						<?php	
					endif;
				} elseif($ativo && !$parceira) { // usuario com sação
					?>
						<div class="msg-encerrado text-center">
							<h3>Atenção!</h3>
							<p>Você está temporariamente impedido de se inscrever em novas oportunidades, devido à ausência em uma participação anterior. Você poderá realizar novas inscrições a partir de <?= $dataPermissao; ?></p>
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
										<input
											type="text"
											class="form-control"
											name="nomeComp"
											id="nomeComp"
											placeholder="Insira seu nome completo"
											value="<?php echo esc_html( old( 'nomeComp' ) ); ?>"
											>
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
											<input
												type="text"
												name="emailInsti"
												class="form-control"
												id="emailInsti"
												placeholder="email@sme.prefeitura.sp.gov.br"
												value="<?php echo esc_html( old( 'emailInsti' ) ); ?>"
											>
										<?php endif; ?>
									<?php else: ?>
										<label for="emailInsti">E-mail Institucional ou de Uso Principal <span>*</span></label>
										<input
											type="text"
											name="emailInsti"
											class="form-control"
											id="emailInsti"
											placeholder="Insira seu e-mail principal"
											value="<?php echo esc_html( old( 'emailInsti' ) ); ?>"
										>
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
										<input
											type="text"
											name="cpf"
											class="form-control"
											id="cpf"
											placeholder="000.000.000-00"
											value="<?php echo esc_html( old( 'cpf' ) ); ?>"
										>
									<?php endif; ?>
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col">
									<label for="emailSec">E-mail Secundário <span>*</span></label>
									<?php if ($user_email && strpos($user_email, '@sme.prefeitura') == false && !$parceira) : ?>								
										<input type="text"  name="emailSec" class="form-control" id="emailSec" value="<?= $user_email; ?>">
									<?php else: ?>
										<input
											type="text" 
											name="emailSec"
											class="form-control"
											id="emailSec"
											placeholder="email@provedor.com.br"
											value="<?php echo esc_html( old( 'emailSec' ) ); ?>"
										>
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
										<input
											type="text"
											name="celular"
											class="form-control"
											id="celular"
											placeholder="(00) 0000-0000"
											value="<?php echo esc_html( old( 'celular' ) ); ?>"
										>
									<?php endif; ?>
								</div>
							</div>

							<div class="form-row">
								<?php
								
									$dre = old( 'dre' );
									
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
									<input
										type="text"
										name="telCom"
										class="form-control"
										id="telCom"
										placeholder="(00) 0000-0000"
										value="<?php echo esc_html( old( 'telCom' ) ); ?>"
										>
								</div>
							</div>

							<div class="form-row">
								<?php 
									$cargo = old( 'cargo_principal' );
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

										if ( !isset( $local ) || empty( $local ) ) {
											$local = old( 'uniSetor' );
										}
									?>
									<label for="uniSetor">Unidade Escolar ou Setor <span>*</span></label>
									<input type="text" name="uniSetor" class="form-control" id="uniSetor" placeholder="Nome da Unidade Escolar ou Setor" value="<?= $local; ?>">
								</div>
							</div>

							<div class="form-row">
								<div class="form-group col">
									<label for="disciplina">Se professor, indicar a disciplina que leciona</label>
									<input
										type="text"
										name="disciplina"
										class="form-control"
										id="disciplina"
										placeholder="Insira o nome da disciplina que leciona"
										value="<?php echo esc_html( old( 'disciplina' ) ); ?>"
										>
								</div>							
							</div>

							<?php
							if ( $this->tipo_evento === 'data' ) :
								
								$datas_disponivies = get_datas_diponiveis( get_the_ID() );
								?>
								<div class="form-row">
									<div class="form-group col" id="grupo-data-selecionada">
										<div class="form-row px-1 pt-2 pb-4">
											<?php if ( intval( $qtd_ingressos_inscrito ) === 1 ) : ?>
												<em>Preencha os dados acima para realizar sua solicitação, conforme as regras e disponibilidade.</em>
											<?php else : ?>
												<em>
													Escolha a data em que deseja participar do evento e informe quantos ingressos você precisa.
													O limite é de <?php echo esc_html( $qtd_ingressos_inscrito . ' ' . _n( 'ingresso', 'ingressos', (int) $qtd_ingressos_inscrito ) ); ?> por inscrito.
												</em>
											<?php endif; ?>
										</div>
										<?php if ($this->tipo_evento == 'premio') : ?>
											<label for="data-selecionada">Selecione o prêmio que deseja participar: <span>*</span></label>
										<?php else : ?>
											<label for="data-selecionada">Selecione a data que deseja participar: <span>*</span></label>
										<?php endif; ?>
										<div class="datas-select">
											<?php
											foreach ( $datas_disponivies as $data ) :
												$data_hora = $data->data_evento;
												$id_input = 'data-' . $data->id; // remove caracteres não numéricos
												$label_formatada = date('d/m/Y H\hi', strtotime($data_hora)); // dd/mm/aaaa hh(h)mm
												?>
												<div class="form-check">
													<input
														class="form-check-input"
														type="radio"
														name="data"
														value="<?php echo esc_attr( $data->id ); ?>"
														id="<?php echo esc_attr( $id_input ); ?>"
														<?php checked( $data->id, old( 'data'  ) ); ?>
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
												<?php
											endforeach;
											?>
										</div>
									</div>							
								</div>
								<?php
							endif;
							?>

							<?php if ( $this->tipo_evento === 'premio' && $datas_disponivies = get_datas_diponiveis( get_the_ID() ) ) : ?>
								<div class="form-row">
									<div class="form-group col" id="grupo-datas">
										<div class="form-row px-1 pt-2 pb-4">
											<?php if ( intval( $qtd_ingressos_inscrito ) === 1 ) : ?>
												<em>Preencha os dados acima para realizar sua solicitação, conforme as regras e disponibilidade.</em>
											<?php else : ?>
												<em>
													Selecione o prêmio e informe a quantidade que deseja. O limite é de <?php echo esc_html( $qtd_ingressos_inscrito . ' ' . _n( 'unidade', 'unidades', (int) $qtd_ingressos_inscrito ) ); ?> por inscrito.
												</em>
											<?php endif; ?>
											
										</div>

										<?php if ($this->tipo_evento == 'premio') : ?>
											<label for="data">Selecione o prêmio que deseja participar: <span>*</span></label>
										<?php else : ?>
											<label for="data">Selecione a(s) data(s) que deseja participar: <span>*</span></label>
										<?php endif; ?>

										<?php
											foreach ( $datas_disponivies as $data ) :
												$data_hora = $data->data_evento;
												$id_input = 'data-' . $data->id; // remove caracteres não numéricos
												$label_formatada = date('d/m/Y H\hi', strtotime($data_hora)); // dd/mm/aaaa hh(h)mm
												?>
												<div class="form-check">
													<input
														class="form-check-input"
														type="radio"
														name="data"
														value="<?php echo esc_attr( $data->id ); ?>"
														id="<?php echo esc_attr( $id_input ); ?>"
														<?php checked( $data->id, old( 'data'  ) ); ?>
													>
													<label class="form-check-label" for="<?php echo esc_attr( $id_input ); ?>">
														<?php echo $data->premio; ?>
													</label>
												</div>
												<?php
											endforeach;
										?>
									</div>							
								</div>
							<?php endif; ?>

							<?php if ( $this->tipo_evento === 'periodo' ) : ?>
								<div class="form-row px-1 pt-2 pb-4">
									<?php if ( intval( $qtd_ingressos_inscrito ) === 1 ) : ?>
										<em>Preencha os dados acima para realizar sua solicitação, conforme as regras e disponibilidade.</em>
									<?php else : ?>
										<em>Solicite seus ingressos informando os dados acima e a quantidade desejada. A utilização deverá ocorrer dentro do período indicado na descrição do evento. O limite é de <?php echo esc_html( $qtd_ingressos_inscrito . ' ' . _n( 'ingresso', 'ingressos', (int) $qtd_ingressos_inscrito ) ); ?> por inscrito, conforme disponibilidade.</em>
									<?php endif; ?>
								</div>
								
								<?php if ( isset( $datas_disponivies[0] ) ) : ?>
									<input type="hidden" name="data" value="<?php echo esc_html( $datas_disponivies[0]->id ); ?>">
								<?php endif; ?>
								
							<?php endif; ?>
							
							<?php if ( intval( $qtd_ingressos_inscrito ) > 1 ) : ?>
								<div class="form-row">
									<div class="form-group col">
										<label for="qtd"><?php echo $this->tipo_evento == 'premio' ? 'Quantidade' : 'Quantidade de ingressos'; ?> <span>*</span></label>
										<input
											type="number"
											name="qtd"
											id="qtd"
											class="form-control"
											placeholder="<?php echo $this->tipo_evento == 'premio' ? 'Informe a quantidade que deseja' : 'Informe quantos ingressos você precisa'; ?>"
											min="1"
											max="<?php echo esc_html( $qtd_ingressos_inscrito ); ?>"
											value="<?php echo esc_html(  old( 'qtd' ) ); ?>"
										>
									</div>							
								</div>
							<?php endif; ?>

							<?php if ( intval( $qtd_ingressos_inscrito ) === 1 ) : ?>
								<input type="hidden" name="qtd" id="qtd" value="1">
							<?php endif; ?>

							<div class="form-row">
								<div class="form-group col">
									<div class="form-check">
										<input class="form-check-input" type="checkbox" name="ciente" value="1" id="ciente">
										<label class="form-check-label" for="ciente">
											Declaro estar ciente de que, após a solicitação da cortesia, poderei ser contatado(a) por e-mail para confirmar minha participação ou a retirada do benefício, dentro do prazo estabelecido pelo parceiro. A não confirmação dentro do prazo poderá resultar na perda do direito ao benefício.
										</label>
									</div>
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

			if($_SERVER["REQUEST_METHOD"] === "POST" && empty( $datas_disponivies )){
				
				if( $this->tipo_evento == 'premio' ){
					$mensagem = 'Enquanto você preenchia o formulário, os prêmios dessa opção se esgotaram. Para continuar, selecione outra premiação disponível.';
				} elseif($this->tipo_evento == 'periodo'){
					$mensagem = 'Enquanto você preenchia o formulário, os ingressos para este período se esgotaram. No momento, não há mais disponibilidade.';
				} else {
					$mensagem = 'Enquanto você preenchia o formulário, os ingressos para essa data/hora se esgotaram. Para continuar, selecione um novo dia.';
				}

				echo "<script>
					jQuery(document).ready(function ($) {
						Swal.fire({
							icon: 'info',
							title: 'Ops!',
							text: '{$mensagem}',
							confirmButtonText: 'Fechar',
						});
					});
				</script>";
			}


			if(!$this->verificaExibicaoListaContemplados()){
				if ( $this->tipo_evento != 'premio' && empty( $datas_disponivies ) && $this->tipo_administracao != 'parceiro' ) {
					if ( $this->tipo_evento == 'periodo' && $dataLimite < $current_date ) {
						echo '<div class="msg-encerrado text-center">
							<h3>Inscrições encerradas!</h3>
							<p>O período de inscrições foi encerrado. Fique de olho em nosso site para futuras oportunidades!</p>
						</div>';
						return;
					} else {
						echo '<div class="msg-encerrado text-center">
							<h3>Ingressos esgotados!</h3>
							<p>No momento, não há mais ingressos disponíveis para este evento.</p>
						</div>';
						return;
					}
				}
		
				if ( $this->tipo_evento == 'premio' && empty( $datas_disponivies ) ) {
					echo '<div class="msg-encerrado text-center">
						<h3>Prêmios esgotados!</h3>
						<p>No momento, não há opções disponíveis para solicitação.</p>
					</div>';
					return;
				}
					
				if( $this->tipo_administracao != 'parceiro' ) : ?>
					<div class="msg-encerrado text-center">
						<h3>Inscrições Encerradas!</h3>
						<p>O período de inscrições foi encerrado. Fique de olho em nosso site para futuras oportunidades!</p>
					</div>
				<?php else : ?>
					<?php if($dataLimite < $current_date) : ?>
						<div class="msg-encerrado text-center">
							<h3>Inscrições Encerradas!</h3>
							<p>
								A administração desta gratuidade/cortesia foi realizada pelo site do parceiro.<br>
								Para informações sobre o resultado, consulte o site do parceiro.
							</p>
						</div>
					<?php endif; ?>
				<?php endif;
			}

		}
	}

	public function verificaExibicaoListaContemplados(){
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

					if($this->tipo_evento === 'premio'){

						$mensagem = "<script>
							jQuery(document).ready(function ($) {
				
								if (window.history.replaceState) {
									window.history.replaceState(null, null, window.location.href);
								}
				
								Swal.fire({
									icon: 'success',
									title: 'Sua inscrição foi realizada com sucesso!',
									html: '<p>Em breve enviaremos por e-mail as instruções para retirada e utilização do prêmio selecionado.</p><p>Caso deseje cancelar sua inscrição, acesse novamente a notícia, informe o <strong>CPF</strong> no formulário de inscrição e siga as instruções exibidas.</p>',
									confirmButtonText: 'Fechar',
								});

								$('#form-inscri')
									.find('input:not([type=button]):not([type=submit]):not([type=reset]):not([type=hidden]):not([type=radio]), textarea, select')
									.each(function () {
										this.value = '';
										this.defaultValue = '';
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
									title: 'Sua inscrição foi realizada com sucesso!',
									html: '<p>Em breve enviaremos por e-mail as instruções para utilização do(s) ingresso(s).</p><p>Caso deseje cancelar sua inscrição, acesse novamente a notícia, informe o <strong>CPF</strong> no formulário de inscrição e siga as instruções exibidas.</p>',
									confirmButtonText: 'Fechar',
								});

								$('#form-inscri')
									.find('input:not([type=button]):not([type=submit]):not([type=reset]):not([type=hidden]):not([type=radio]), textarea, select')
									.each(function () {
										this.value = '';
										this.defaultValue = '';
									});
							});
						</script>";
					}

				} else {
					if($this->tipo_evento === 'premio'){
						$mensagem = "<script>
							jQuery(document).ready(function ($) {
				
								if (window.history.replaceState) {
									window.history.replaceState(null, null, window.location.href);
								}
				
								Swal.fire({
									icon: 'success',
									title: 'Sua inscrição foi realizada com sucesso!',
									html: '<p>Em breve enviaremos por e-mail as instruções para retirada e utilização do prêmio selecionado.</p>',
									confirmButtonText: 'Fechar',
								});

								$('#form-inscri')
									.find('input:not([type=button]):not([type=submit]):not([type=reset]):not([type=hidden]):not([type=radio]), textarea, select')
									.each(function () {
										this.value = '';
										this.defaultValue = '';
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
									title: 'Sua inscrição foi realizada com sucesso!',
									html: '<p>Em breve enviaremos por e-mail as instruções para utilização do(s) ingresso(s).</p>',
									confirmButtonText: 'Fechar',
								});

								$('#form-inscri')
									.find('input:not([type=button]):not([type=submit]):not([type=reset]):not([type=hidden]):not([type=radio]), textarea, select')
									.each(function () {
										this.value = '';
										this.defaultValue = '';
									});
							});
						</script>";
					}
				}
				
				break;
			
			default:
				# code...
				break;
		}

		return $mensagem;
		
	}

	private function exibe_mensagem_erro( WP_Error $resultado ) {
		switch ( $resultado->get_error_code() ) {
			case 'no_stock':
				if( $this->tipo_evento == 'premio' ){
					$mensagem = 'Enquanto você preenchia o formulário, os prêmios dessa opção se esgotaram. Para continuar, selecione outra premiação disponível.';
				} elseif($this->tipo_evento == 'periodo'){
					$mensagem = 'Enquanto você preenchia o formulário, os ingressos para este período se esgotaram. No momento, não há mais disponibilidade.';
				} else {
					$mensagem = 'Enquanto você preenchia o formulário, os ingressos para essa data/hora se esgotaram. Para continuar, selecione um novo dia.';
				}

				echo "<script>
					jQuery(document).ready(function ($) {
						Swal.fire({
							icon: 'info',
							title: 'Ops!',
							text: '{$mensagem}',
							confirmButtonText: 'Fechar',
						});
					});
				</script>";
				break;
			case 'insufficient_stock':
				$qtd = $resultado->get_error_data();
				$this->qtd_limite = $qtd['estoque_atual'];
				$texto_quantidade =  _n( 'ingresso', 'ingressos', (int) $this->qtd_limite );

				$mensagem = ( $this->tipo_evento == 'periodo' )
					? "A quantidade solicitada não está mais disponível no momento! Você pode solicitar até {$this->qtd_limite} {$texto_quantidade}."
					: "A quantidade solicitada não está mais disponível para a data/hora selecionada. Você pode solicitar até {$this->qtd_limite} {$texto_quantidade} ou selecionar outra data/hora.";

				echo "<script>
					jQuery(document).ready(function ($) {
						Swal.fire({
							icon: 'error',
							title: 'Quantidade indisponível',
							text: '{$mensagem}',
							confirmButtonText: 'Fechar',
						});
					});
				</script>";
				break;
			case 'invalid_quantity':
				$qtd = $resultado->get_error_data();
				$this->qtd_limite = $qtd['limite_por_usuario'];
				$texto_quantidade =  _n( 'ingresso', 'ingressos', (int) $this->qtd_limite );
				echo "<script>
					jQuery(document).ready(function ($) {
						Swal.fire({
							icon: 'error',
							title: 'Quantidade inválida',
							text: 'Quantidade máxima excedida.  O limite é de {$this->qtd_limite} {$texto_quantidade} por inscrito.',
							confirmButtonText: 'Fechar',
						});
					});
				</script>";
				break;
			case 'zero_quantity':
				echo "<script>
					jQuery(document).ready(function ($) {
						Swal.fire({
							icon: 'error',
							title: 'Quantidade inválida',
							text: 'Informe uma quantidade válida.',
							confirmButtonText: 'Fechar',
						});
					});
				</script>";
				break;
			
			default:
				echo "<script>
					jQuery(document).ready(function ($) {
						Swal.fire({
							icon: 'error',
							title: 'Erro ao realizar o resgate.',
							text: 'Houve um erro ao realizar o resgate da cortesia. Tente novamente, caso o problema persista entre em contato com o intranet.beneficios@sme.prefeitura.sp.gov.br.',
							confirmButtonText: 'Fechar',
						});
					});
				</script>";
				break;
		}
	}

	public function array_valido($data) {
		if (!is_array($data) || empty($data)) {
			return false;
		}

		foreach ($data as $item) {
			if (!is_array($item)) {
				continue;
			}

			if (
				!empty($item['link_infos']) ||
				!empty($item['texto_do_link'])
			) {
				return true;
			}
		}

		return false;
	}
		
}
