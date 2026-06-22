<?php

	$user_id = get_current_user_id();
	$dados = obter_dados_candidato($user_id);
	$origem_dados = $dados['origem'];
	$curriculo = $dados['dados'];
    $vivencias = $dados['vivencias'];
	$informatica = $dados['informatica'];
	$comportamental = $dados['comportamental'];

	$cargos = [];

	if (!empty($curriculo->cargo_efetivo)) {
		$cargos = json_decode($curriculo->cargo_efetivo, true);
	}    
?>

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

									<div class="form-group campo-obrigatorio-radio">

										<p class="m-0 label-text">Deseja continuar? <span class="required-icon">*</span></p>

										<div class="form-check">
											<input class="form-check-input"
												type="radio"
												name="avisoPrivacidade"
												id="radioSim"
												value="sim"
												<?= isset($curriculo) && $curriculo ? 'checked' : '' ?>>

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

								<?php if($origem_dados !== 'banco') : ?>
									<div class="alert alert-warning mb-4">
										Alguns dados foram preenchidos automaticamente.
										Revise as informações antes de salvar.
									</div>
								<?php endif; ?>

								<h3>DADOS PESSOAIS E FUNCIONAIS</h3>

								<div class="form-group">
									<label for="nomeCompleto">Nome Completo</label>
									<input type="text" class="form-control campo-obrigatorio" id="nomeCompleto" name="nomeCompleto" placeholder="Nome completo" value="<?= esc_attr($curriculo->nome_completo ?? '') ?>" readonly>
								</div>

								<div class="form-group">
									<label for="nomeSocial">Nome Social (Opcional)</label>
									<input type="text" class="form-control" id="nomeSocial" name="nomeSocial" placeholder="Nome social (se houver)" value="<?= esc_attr($curriculo->nome_social ?? '') ?>">
								</div>

								<div class="form-group">
									<label for="rf">RF</label>
									<input type="text" class="form-control campo-obrigatorio" id="rf" name="rf" placeholder="RF" value="<?= esc_attr($curriculo->rf ?? '') ?>" readonly>
								</div>

								<h3>Identificação pessoal</h3>

								<div class="form-group">
									<label for="dataNascimento">Data de Nascimento <span class="required-icon">*</span></label>
									<input type="date" class="form-control campo-obrigatorio" id="dataNascimento" name="dataNascimento" value="<?= esc_attr($curriculo->data_nascimento ?? '') ?>">
								</div>

								<div class="form-group">
									<label for="seIdentifica">Como você se identifica? <span class="required-icon">*</span></label>
									<select class="form-control campo-obrigatorio" id="seIdentifica" name="seIdentifica">
										<option value="">-- Selecione uma opção --</option>
										<option value="preto" <?= selected($curriculo->identificacao_racial ?? '', 'preto', false) ?>>Preto(a)</option>
										<option value="pardo" <?= selected($curriculo->identificacao_racial ?? '', 'pardo', false) ?>>Pardo(a)</option>
										<option value="amarelo" <?= selected($curriculo->identificacao_racial ?? '', 'amarelo', false) ?>>Amarelo(a)</option>
										<option value="indigena" <?= selected($curriculo->identificacao_racial ?? '', 'indigena', false) ?>>Indígena</option>
										<option value="branco" <?= selected($curriculo->identificacao_racial ?? '', 'branco', false) ?>>Branco(a)</option>
									</select>
								</div>

								<div class="form-group">
									<label for="idGenero">Qual sua identidade de gênero? <span class="required-icon">*</span></label>
									<select class="form-control campo-obrigatorio" id="idGenero" name="idGenero">
										<option value="">-- Selecione uma opção --</option>
										<option value="homem_cis" <?= selected($curriculo->identidade_genero ?? '', 'homem_cis', false) ?>>Homem cis</option>
										<option value="homem_trans" <?= selected($curriculo->identidade_genero ?? '', 'homem_trans', false) ?>>Homem trans</option>
										<option value="mulher_cis" <?= selected($curriculo->identidade_genero ?? '', 'mulher_cis', false) ?>>Mulher cis</option>
										<option value="mulher_trans" <?= selected($curriculo->identidade_genero ?? '', 'mulher_trans', false) ?>>Mulher trans</option>
										<option value="nao_binario" <?= selected($curriculo->identidade_genero ?? '', 'nao_binario', false) ?>>Não binário</option>
									</select>
								</div>

								<h3>Informações complementares</h3>

								<div class="form-group campo-obrigatorio-radio">
									<label>Você é uma pessoa com deficiência? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="possuiDeficiencia" id="deficienciaSim" value="1" <?= checked($curriculo->possui_deficiencia ?? '', '1', false) ?>>
										<label class="form-check-label" for="deficienciaSim">Sim</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="possuiDeficiencia" id="deficienciaNao" value="0" <?= checked($curriculo->possui_deficiencia ?? '', '0', false) ?>>
										<label class="form-check-label" for="deficienciaNao">Não</label>
									</div>
								</div>

								<div class="form-group grupo-adaptacao campo-obrigatorio-radio">
									<label>Você precisa de algum tipo de adaptação para executar trabalhos de escritório? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="necessitaAdaptacao" id="adaptacaoSim" value="1" <?= checked($curriculo->necessita_adaptacao ?? '', '1', false) ?>>
										<label class="form-check-label" for="adaptacaoSim">Sim</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="necessitaAdaptacao" id="adaptacaoNao" value="0" <?= checked($curriculo->necessita_adaptacao ?? '', '0', false) ?>>
										<label class="form-check-label" for="adaptacaoNao">Não</label>
									</div>
								</div>

								<div class="form-group grupo-descricao-adaptacao">
									<label for="descrevaAdapta">
										Se sim, por gentileza, descreva as adaptações que seriam necessárias ao ambiente ou tecnologias assistivas. <span class="required-icon">*</span>
									</label>

									<textarea class="form-control campo-obrigatorio"
										id="descrevaAdapta"
										name="descrevaAdapta"
										rows="3"><?= esc_textarea($curriculo->descreva_adaptacao ?? '') ?></textarea>
								</div>

								<div class="form-group campo-obrigatorio-radio">
									<label>Você é servidor em readaptação funcional (readaptado)? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="servidorReadaptado"
											id="readaptadoSim"
											value="1"
											<?= checked($curriculo->servidor_readaptado ?? '', '1', false) ?>>

										<label class="form-check-label" for="readaptadoSim">
											Sim
										</label>
									</div>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="servidorReadaptado"
											id="readaptadoNao"
											value="0"
											<?= checked($curriculo->servidor_readaptado ?? '', '0', false) ?>>

										<label class="form-check-label" for="readaptadoNao">
											Não
										</label>
									</div>
								</div>

								<div class="form-group grupo-readaptacao campo-obrigatorio-radio">
									<label>
										Você precisa de algum tipo de adaptação para executar trabalhos de escritório? <span class="required-icon">*</span>
									</label>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="readaptadoNecessita"
											id="readaptadoAdaptacaoSim"
											value="1"
											<?= checked($curriculo->readaptado_necessita ?? '', '1', false) ?>>

										<label class="form-check-label" for="readaptadoAdaptacaoSim">
											Sim
										</label>
									</div>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="readaptadoNecessita"
											id="readaptadoAdaptacaoNao"
											value="0"
											<?= checked($curriculo->readaptado_necessita ?? '', '0', false) ?>>

										<label class="form-check-label" for="readaptadoAdaptacaoNao">
											Não
										</label>
									</div>
								</div>

								<div class="form-group grupo-readaptacao-descricao">
									<label for="readaptadoDescreva">
										Se sim, por gentileza, descreva as adaptações que seriam necessárias ao ambiente ou tecnologias assistivas. <span class="required-icon">*</span>
									</label>

									<textarea class="form-control campo-obrigatorio"
										id="readaptadoDescreva"
										name="readaptadoDescreva"
										rows="3"><?= esc_textarea($curriculo->readaptado_descricao ?? '') ?></textarea>
								</div>

								<h3>Contato</h3>

								<div class="form-group">
									<label for="telWhatsapp">Telefone de contato (WhatsApp) <span class="required-icon">*</span><br><small>(por gentileza, escreva o número com o DDD)</small></label>									
									<input type="text" class="form-control campo-obrigatorio" id="telWhatsapp" name="telWhatsapp" placeholder="(99) 99999-9999" value="<?= esc_attr($curriculo->telefone_whatsapp ?? '') ?>">
								</div>

								<div class="form-group">
									<label for="telOpcional">Telefone de contato (Opcional) <br><small>(por gentileza, escreva o número com o DDD)</small></label>
									<input type="text" class="form-control" id="telOpcional" name="telOpcional" placeholder="(99) 99999-9999" value="<?= esc_attr($curriculo->telefone_opcional ?? '') ?>">
								</div>

								<div class="form-group">
									<label for="email">E-mail Institucional ou de Uso Principal <span class="required-icon">*</span></label>
									<input type="email" class="form-control campo-obrigatorio" id="email" name="email" value="<?= esc_attr($curriculo->email_principal ?? '') ?>" placeholder="Insira o e-mail institucional. Ex: email.institucional@sme.prefeitura.sp.gov.br">
								</div>

								<div class="form-group">
									<label for="emailSec">E-mail secundário</label>
									<input type="email" class="form-control" id="emailSec" name="emailSec" value="<?= esc_attr($curriculo->email_secundario ?? '') ?>" placeholder="Insira um e-mail alternativo para contato. Ex: nome@dominio.com">
								</div>

								<h3>Lotação e Exercício</h3>

								<div class="form-group campo-obrigatorio-radio">
									<label>Concluiu o estágio probatório? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="estagio" id="estagioSim" value="1" <?= checked($curriculo->concluiu_estagio ?? '', '1', false) ?>>
										<label class="form-check-label" for="estagioSim">Sim</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="estagio" id="estagioNao" value="0" <?= checked($curriculo->concluiu_estagio ?? '', '0', false) ?>>
										<label class="form-check-label" for="estagioNao">Não</label>
									</div>
								</div>

								<div class="form-group campo-obrigatorio-checkbox">
									<label>Qual é o seu cargo efetivo? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Auxiliar Técnico de Educação (ATE)" 
											id="cargoAte" 
											name="cargoEfetivo[]" 
											<?= in_array('Auxiliar Técnico de Educação (ATE)', $cargos) ? 'checked' : '' ?>>
										<label for="cargoAte">Auxiliar Técnico de Educação (ATE)</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Agente Escolar" 
											id="cargoAgente" 
											name="cargoEfetivo[]" 
											<?= in_array('Agente Escolar', $cargos) ? 'checked' : '' ?>>
										<label for="cargoAgente">Agente Escolar</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Coordenador(a) Pedagógico" 
											id="cargoCoordenador" 
											name="cargoEfetivo[]" 
											<?= in_array('Coordenador(a) Pedagógico', $cargos) ? 'checked' : '' ?>>
										<label for="cargoCoordenador">Coordenador(a) Pedagógico</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Diretor(a) de Escola" 
											id="cargoDiretor" 
											name="cargoEfetivo[]" 
											<?= in_array('Diretor(a) de Escola', $cargos) ? 'checked' : '' ?>>
										<label for="cargoDiretor">Diretor(a) de Escola</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Professor(a) de Educação Infantil (PEI)" 
											id="cargoPei" 
											name="cargoEfetivo[]" 
											<?= in_array('Professor(a) de Educação Infantil (PEI)', $cargos) ? 'checked' : '' ?>>
										<label for="cargoPei">Professor(a) de Educação Infantil (PEI)</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Professor(a) de Educação Infantil e Ensino Fundamental I (PEIF)" 
											id="cargoPeif" 
											name="cargoEfetivo[]" 
											<?= in_array('Professor(a) de Educação Infantil e Ensino Fundamental I (PEIF)', $cargos) ? 'checked' : '' ?>>
										<label for="cargoPeif">Professor(a) de Educação Infantil e Ensino Fundamental I (PEIF)</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Professor(a) de Ensino Fundamental II e Médio" 
											id="cargoFundamental2" 
											name="cargoEfetivo[]" 
											<?= in_array('Professor(a) de Ensino Fundamental II e Médio', $cargos) ? 'checked' : '' ?>>
										<label for="cargoFundamental2">Professor(a) de Ensino Fundamental II e Médio</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Supervisor(a) Escolar" 
											id="cargoSupervisor" 
											name="cargoEfetivo[]" 
											<?= in_array('Supervisor(a) Escolar', $cargos) ? 'checked' : '' ?>>
										<label for="cargoSupervisor">Supervisor(a) Escolar</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" 
											type="checkbox" 
											value="Outro" 
											id="cargoOutroCheck" 
											name="cargoEfetivo[]" 
											<?= in_array('Outro', $cargos) ? 'checked' : '' ?>>
										<label for="cargoOutroCheck">Outro</label>
									</div>
								</div>

								<div class="form-group form-group-cargo-outro">
									<label for="cargoOutro">Informe o cargo <span class="required-icon">*</span></label>
									<input type="text" class="form-control campo-obrigatorio" id="cargoOutro" name="cargoOutro" value="<?= esc_attr($curriculo->cargo_outro ?? '') ?>">
								</div>

								<div class="form-group">
									<label for="dreLotacao">DRE de lotação? <span class="required-icon">*</span><br><small>A unidade de lotação é o órgão onde o cargo do servidor público está oficialmente vinculado para exercer suas funções.</small></label>
									<select class="form-control campo-obrigatorio" id="dreLotacao" name="dreLotacao">
										<option value=''>-- Selecione uma DRE --</option>
										<option value='dre-butanta' <?= selected($curriculo->dre_lotacao ?? '', 'dre-butanta', false) ?>>DRE Butantã</option>
										<option value='dre-campo-limpo' <?= selected($curriculo->dre_lotacao ?? '', 'dre-campo-limpo', false) ?>>DRE Campo Limpo</option>
										<option value='dre-capela-socorro' <?= selected($curriculo->dre_lotacao ?? '', 'dre-capela-socorro', false) ?>>DRE Capela do Socorro</option>
										<option value='dre-freguesia-brasilandia' <?= selected($curriculo->dre_lotacao ?? '', 'dre-freguesia-brasilandia', false) ?>>DRE Freguesia/Brasilândia</option>
										<option value='dre-guaianases' <?= selected($curriculo->dre_lotacao ?? '', 'dre-guaianases', false) ?>>DRE Guaianases</option>
										<option value='dre-ipiranga' <?= selected($curriculo->dre_lotacao ?? '', 'dre-ipiranga', false) ?>>DRE Ipiranga</option>
										<option value='dre-itaquera' <?= selected($curriculo->dre_lotacao ?? '', 'dre-itaquera', false) ?>>DRE Itaquera</option>
										<option value='dre-jacana-tremembe' <?= selected($curriculo->dre_lotacao ?? '', 'dre-jacana-tremembe', false) ?>>DRE Jaçanã/Tremembé</option>
										<option value='dre-penha' <?= selected($curriculo->dre_lotacao ?? '', 'dre-penha', false) ?>>DRE Penha</option>
										<option value='dre-pirituba-jaragua' <?= selected($curriculo->dre_lotacao ?? '', 'dre-pirituba-jaragua', false) ?>>DRE Pirituba/Jaraguá</option>
										<option value='dre-santo-amaro' <?= selected($curriculo->dre_lotacao ?? '', 'dre-santo-amaro', false) ?>>DRE Santo Amaro</option>
										<option value='dre-sao-mateus' <?= selected($curriculo->dre_lotacao ?? '', 'dre-sao-mateus', false) ?>>DRE São Mateus</option>
										<option value='dre-sao-miguel' <?= selected($curriculo->dre_lotacao ?? '', 'dre-sao-miguel', false) ?>>DRE São Miguel</option>
										<option value='coordenadoria-sme' <?= selected($curriculo->dre_lotacao ?? '', 'coordenadoria-sme', false) ?>>Coordenadoria/SME</option>
									</select>
								</div>

								<div class="form-group">
									<label for="unidadeLotacao">Unidade de lotação <span class="required-icon">*</span><br><small>A unidade de lotação é o órgão onde o cargo do servidor público está oficialmente vinculado para exercer suas funções.</small></label>
									<input type="text" class="form-control campo-obrigatorio" id="unidadeLotacao" name="unidadeLotacao" value="<?= esc_attr($curriculo->unidade_lotacao ?? '') ?>" placeholder="Digite a nomenclatura completa">
								</div>

								<div class="form-group">
									<label for="dreExercicio">DRE de exercício <span class="required-icon">*</span><br><small>A unidade de exercício é o local onde o servidor efetivamente desempenha suas atividades diárias, podendo ou não coincidir com a sua unidade de lotação.</small></label>
									<select class="form-control campo-obrigatorio" id="dreExercicio" name="dreExercicio">
										<option value=''>-- Selecione uma DRE --</option>
										<option value='dre-butanta' <?= selected($curriculo->dre_exercicio ?? '', 'dre-butanta', false) ?>>DRE Butantã</option>
										<option value='dre-campo-limpo' <?= selected($curriculo->dre_exercicio ?? '', 'dre-campo-limpo', false) ?>>DRE Campo Limpo</option>
										<option value='dre-capela-socorro' <?= selected($curriculo->dre_exercicio ?? '', 'dre-capela-socorro', false) ?>>DRE Capela do Socorro</option>
										<option value='dre-freguesia-brasilandia' <?= selected($curriculo->dre_exercicio ?? '', 'dre-freguesia-brasilandia', false) ?>>DRE Freguesia/Brasilândia</option>
										<option value='dre-guaianases' <?= selected($curriculo->dre_exercicio ?? '', 'dre-guaianases', false) ?>>DRE Guaianases</option>
										<option value='dre-ipiranga' <?= selected($curriculo->dre_exercicio ?? '', 'dre-ipiranga', false) ?>>DRE Ipiranga</option>
										<option value='dre-itaquera' <?= selected($curriculo->dre_exercicio ?? '', 'dre-itaquera', false) ?>>DRE Itaquera</option>
										<option value='dre-jacana-tremembe' <?= selected($curriculo->dre_exercicio ?? '', 'dre-jacana-tremembe', false) ?>>DRE Jaçanã/Tremembé</option>
										<option value='dre-penha' <?= selected($curriculo->dre_exercicio ?? '', 'dre-penha', false) ?>>DRE Penha</option>
										<option value='dre-pirituba-jaragua' <?= selected($curriculo->dre_exercicio ?? '', 'dre-pirituba-jaragua', false) ?>>DRE Pirituba/Jaraguá</option>
										<option value='dre-santo-amaro' <?= selected($curriculo->dre_exercicio ?? '', 'dre-santo-amaro', false) ?>>DRE Santo Amaro</option>
										<option value='dre-sao-mateus' <?= selected($curriculo->dre_exercicio ?? '', 'dre-sao-mateus', false) ?>>DRE São Mateus</option>
										<option value='dre-sao-miguel' <?= selected($curriculo->dre_exercicio ?? '', 'dre-sao-miguel', false) ?>>DRE São Miguel</option>
										<option value='coordenadoria-sme' <?= selected($curriculo->dre_exercicio ?? '', 'coordenadoria-sme', false) ?>>Coordenadoria/SME</option>
									</select>
								</div>

								<div class="form-group">
									<label for="unidadeExercicio">Unidade de exercício <span class="required-icon">*</span><br><small>A unidade de exercício é o local onde o servidor efetivamente desempenha suas atividades diárias, podendo ou não coincidir com a sua unidade de lotação.</small></label>
									<input type="text" class="form-control campo-obrigatorio" id="unidadeExercicio" name="unidadeExercicio" value="<?= esc_attr($curriculo->unidade_exercicio ?? '') ?>" placeholder="Digite a nomenclatura completa">
								</div>

								<div class="form-group campo-obrigatorio-radio">
									<label>Você acumula cargo na SME ou em outro órgão? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="acumulaCargo"
											id="acumulaSim"
											value="1"
											<?= checked($curriculo->acumula_cargo ?? '', '1', false) ?>>

										<label for="acumulaSim">Sim</label>
									</div>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="acumulaCargo"
											id="acumulaNao"
											value="0"
											<?= checked($curriculo->acumula_cargo ?? '', '0', false) ?>>

										<label for="acumulaNao">Não</label>
									</div>
								</div>

								<div class="form-group grupo-acumula-cargo">
									<label for="informaCargo">
										Informe o órgão e o cargo onde acumula <span class="required-icon">*</span>
									</label>

									<input type="text"
										class="form-control campo-obrigatorio"
										id="informaCargo"
										name="informaCargo"
										value="<?= esc_attr($curriculo->acumula_descricao ?? '') ?>"
										placeholder="Órgão - Cargo">
								</div>

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
								<h3>Escolaridade</h3>							

								<div class="form-group">
									<label for="escolaridade">Qual seu nível de escolaridade? <span class="required-icon">*</span></label>
									<select class="form-control campo-obrigatorio" id="escolaridade" name="escolaridade">
										<option value="">-- Selecione --</option>
										<option value="medio" <?= selected($curriculo->escolaridade ?? '', 'medio', false) ?>>Ensino Médio</option>
										<option value="superior" <?= selected($curriculo->escolaridade ?? '', 'superior', false) ?>>Ensino superior: Licenciatura, Bacharelado, Tecnólogo</option>
										<option value="especializacao" <?= selected($curriculo->escolaridade ?? '', 'especializacao', false) ?>>Pós-graduação: Especialização/MBA (lato sensu)</option>
										<option value="mestrado" <?= selected($curriculo->escolaridade ?? '', 'mestrado', false) ?>>Pós-graduação: Mestrado (stricto sensu)</option>
										<option value="doutorado" <?= selected($curriculo->escolaridade ?? '', 'doutorado', false) ?>>Pós-graduação: Doutorado (stricto sensu)</option>
									</select>
								</div>

                                <h3 class="escolaridade-hide">Bacharelado, Tecnólogo, Licenciatura</h3>
                                <small class="escolaridade-hide">Por favor, nos dê mais detalhes do(s) curso(s) que você cursa/cursou.</small>

                                <div class="form-group">
									<label for="cursoGraduacao">Curso de graduação (nome do curso e da instituição de ensino) <span class="required-icon">*</span></label>
									<input type="text" class="form-control campo-obrigatorio" id="cursoGraduacao" name="cursoGraduacao" placeholder="Ex.: Administração - Universidade de São Paulo (USP)" value="<?= esc_attr($curriculo->curso_graduacao ?? '') ?>">
								</div>

                                <div class="form-group">
									<label for="anoConclusao">Ano de conclusão ou previsão de concluir <span class="required-icon">*</span></label>
									<input type="text" class="form-control campo-obrigatorio" id="anoConclusao" name="anoConclusao" placeholder="Ex.: 2022" value="<?= esc_attr($curriculo->ano_conclusao ?? '') ?>">
								</div>

                                <div class="form-group campo-obrigatorio-radio">
									<label>Possui outra graduação e gostaria de informar? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="outraGraduacao" id="outraGraduacaoSim" value="1" <?= checked($curriculo->outra_graduacao ?? '', '1', false) ?>>
										<label class="form-check-label" for="outraGraduacaoSim">Sim</label>
									</div>

									<div class="form-check">
										<input class="form-check-input" type="radio" name="outraGraduacao" id="outraGraduacaoNao" value="0" <?= checked($curriculo->outra_graduacao ?? '', '0', false) ?>>
										<label class="form-check-label" for="outraGraduacaoNao">Não</label>
									</div>
								</div>

                                <div class="form-group">
									<label for="segundaGraduacao">Segunda graduação (nome do curso e da instituição de ensino) <span class="required-icon">*</span></label>
									<input type="text" class="form-control campo-obrigatorio" id="segundaGraduacao" name="segundaGraduacao" placeholder="Ex.: Administração - Universidade de São Paulo (USP)" value="<?= esc_attr($curriculo->segunda_graduacao ?? '') ?>">
								</div>

                                <div class="form-group">
									<label for="anoConclusaoSeg">Ano de conclusão ou previsão de concluir <span class="required-icon">*</span></label>
									<input type="text" class="form-control campo-obrigatorio" id="anoConclusaoSeg" name="anoConclusaoSeg" placeholder="Ex.: 2022" value="<?= esc_attr($curriculo->ano_conclusao_seg ?? '') ?>">
								</div>


                                <h3>Outros Cursos e/ou Projetos Relevantes</h3>

                                <div class="form-group">
									<label for="outrosCursos">
										Por favor, descreva outros cursos e/ou projetos relevantes. Edite o modelo abaixo para cada item:
									</label>

                                    <?php

                                        $outros_cursos = '';

                                        if (
                                            !empty($curriculo->outros_cursos) &&
                                            is_string($curriculo->outros_cursos)
                                        ) {

                                            $outros_cursos = $curriculo->outros_cursos;

                                        } else {

                                            $outros_cursos = "Instituição: \nNome do curso/formação: \nFoco principal / principais competências desenvolvidas: \nCarga horária total: \nAno de conclusão: ";

                                        }

                                    ?>

                                    <textarea
                                        class="form-control"
                                        id="outrosCursos"
                                        name="outrosCursos"
                                        rows="6"><?= esc_textarea($outros_cursos); ?></textarea>
								</div>
							</div>
						</div>

					</div>

					<!-- ETAPA 4 -->
					<div class="card etapa-formulario">
						<div class="card-header" id="headingFour">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed" 
								type="button"
								data-toggle="collapse"
								data-target="#collapseFour">

									<span class="numeral">4</span> Vivências Profissionais
								</button>
							</h2>
						</div>
						<div id="collapseFour" class="collapse">
							<div class="card-body">
								<small>Por favor nos dê mais detalhes da(s) sua(s) vivência(s) profissional(is) relevantes nos últimos anos, iniciando pela mais recente.</small>

								<div class="bloco-vivencia" data-vivencia="1">
									<h3 class="mt-3">Vivência 1</h3>

									<div class="form-group">
										<label for="organizacaoEmp1">Organização/Empresa <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="organizacaoEmp1" name="organizacaoEmp1" value="<?= esc_attr($vivencias[1]['organizacao_empresa'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="cargoFuncao1">Cargo/Função: <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="cargoFuncao1" name="cargoFuncao1" value="<?= esc_attr($vivencias[1]['cargo_funcao'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="duracao1">Duração: <span class="required-icon">*</span></label>
										<select class="form-control campo-obrigatorio" id="duracao1" name="duracao1">
											<option value="">-- Selecione --</option>
											<option value="ate-1-ano" <?= selected($vivencias[1]['duracao'] ?? '', 'ate-1-ano', false) ?>>Até 1 ano</option>
											<option value="entre-1-3" <?= selected($vivencias[1]['duracao'] ?? '', 'entre-1-3', false) ?>>Entre 1 e 3 anos</option>
											<option value="de-3-5" <?= selected($vivencias[1]['duracao'] ?? '', 'de-3-5', false) ?>>De 3 a 5 anos</option>
											<option value="acima-5" <?= selected($vivencias[1]['duracao'] ?? '', 'acima-5', false) ?>>Acima de 5 anos</option>										
										</select>
									</div>

									<div class="form-group">
										<label for="atividadesComp1">
											Atividades e Competências Desenvolvidas: <span class="required-icon">*</span><br><small>Por favor, descreva de forma breve, com o uso de palavras-chave, as atividades e competências adquiridas</small>
										</label>

										<textarea
											class="form-control campo-obrigatorio"
											id="atividadesComp1"
											name="atividadesComp1"
											rows="6"><?= esc_textarea($vivencias[1]['atividades_competencias'] ?? ''); ?></textarea>
									</div>

									<div class="form-group campo-obrigatorio-radio">
										<label>Gostaria de compartilhar outra vivência profissional? <span class="required-icon">*</span></label>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="outraVivencia1" id="outraVivencia1Sim" value="1" <?= (($vivencias[1]['outra_vivencia'] ?? '') === '1') ? 'checked' : '' ?>>
											<label class="form-check-label" for="outraVivencia1Sim">Sim</label>
										</div>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="outraVivencia1" id="outraVivencia1Nao" value="0" <?= (($vivencias[1]['outra_vivencia'] ?? '') === '0') ? 'checked' : '' ?>>
											<label class="form-check-label" for="outraVivencia1Nao">Não</label>
										</div>
									</div>
								</div>

								<div class="bloco-vivencia" data-vivencia="2">

									<h3 class="mt-3 titulo-vivencia">Vivência 2</h3>

									<div class="form-group">
										<label for="organizacaoEmp2">Organização/Empresa <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="organizacaoEmp2" name="organizacaoEmp2" value="<?= esc_attr($vivencias[2]['organizacao_empresa'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="cargoFuncao2">Cargo/Função: <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="cargoFuncao2" name="cargoFuncao2" value="<?= esc_attr($vivencias[2]['cargo_funcao'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="duracao2">Duração: <span class="required-icon">*</span></label>
										<select class="form-control campo-obrigatorio" id="duracao2" name="duracao2">
											<option value="">-- Selecione --</option>
											<option value="ate-1-ano" <?= selected($vivencias[2]['duracao'] ?? '', 'ate-1-ano', false) ?>>Até 1 ano</option>
											<option value="entre-1-3" <?= selected($vivencias[2]['duracao'] ?? '', 'entre-1-3', false) ?>>Entre 1 e 3 anos</option>
											<option value="de-3-5" <?= selected($vivencias[2]['duracao'] ?? '', 'de-3-5', false) ?>>De 3 a 5 anos</option>
											<option value="acima-5" <?= selected($vivencias[2]['duracao'] ?? '', 'acima-5', false) ?>>Acima de 5 anos</option>										
										</select>
									</div>

									<div class="form-group">
										<label for="atividadesComp2">
											Atividades e Competências Desenvolvidas: <span class="required-icon">*</span><br><small>Por favor, descreva de forma breve, com o uso de palavras-chave, as atividades e competências adquiridas</small>
										</label>

										<textarea
											class="form-control campo-obrigatorio"
											id="atividadesComp2"
											name="atividadesComp2"
											rows="6"><?= esc_textarea($vivencias[2]['atividades_competencias'] ?? ''); ?></textarea>
									</div>

									<div class="form-group campo-obrigatorio-radio">
										<label>Gostaria de compartilhar outra vivência profissional? <span class="required-icon">*</span></label>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="outraVivencia2" id="outraVivencia2Sim" value="1" <?= (($vivencias[2]['outra_vivencia'] ?? '') === '1') ? 'checked' : '' ?>>
											<label class="form-check-label" for="outraVivencia2Sim">Sim</label>
										</div>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="outraVivencia2" id="outraVivencia2Nao" value="0" <?= (($vivencias[2]['outra_vivencia'] ?? '') === '0') ? 'checked' : '' ?>>
											<label class="form-check-label" for="outraVivencia2Nao">Não</label>
										</div>
									</div>
								</div>

								<div class="bloco-vivencia" data-vivencia="3">

									<h3 class="mt-3 titulo-vivencia">Vivência 3</h3>

									<div class="form-group">
										<label for="organizacaoEmp3">Organização/Empresa <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="organizacaoEmp3" name="organizacaoEmp3" value="<?= esc_attr($vivencias[3]['organizacao_empresa'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="cargoFuncao3">Cargo/Função: <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="cargoFuncao3" name="cargoFuncao3" value="<?= esc_attr($vivencias[3]['cargo_funcao'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="duracao3">Duração: <span class="required-icon">*</span></label>
										<select class="form-control campo-obrigatorio" id="duracao3" name="duracao3">
											<option value="">-- Selecione --</option>
											<option value="ate-1-ano" <?= selected($vivencias[3]['duracao'] ?? '', 'ate-1-ano', false) ?>>Até 1 ano</option>
											<option value="entre-1-3" <?= selected($vivencias[3]['duracao'] ?? '', 'entre-1-3', false) ?>>Entre 1 e 3 anos</option>
											<option value="de-3-5" <?= selected($vivencias[3]['duracao'] ?? '', 'de-3-5', false) ?>>De 3 a 5 anos</option>
											<option value="acima-5" <?= selected($vivencias[3]['duracao'] ?? '', 'acima-5', false) ?>>Acima de 5 anos</option>										
										</select>
									</div>

									<div class="form-group">
										<label for="atividadesComp3">
											Atividades e Competências Desenvolvidas: <span class="required-icon">*</span><br><small>Por favor, descreva de forma breve, com o uso de palavras-chave, as atividades e competências adquiridas</small>
										</label>

										<textarea
											class="form-control campo-obrigatorio"
											id="atividadesComp3"
											name="atividadesComp3"
											rows="6"><?= esc_textarea($vivencias[3]['atividades_competencias'] ?? ''); ?></textarea>
									</div>

									<div class="form-group campo-obrigatorio-radio">
										<label>Gostaria de compartilhar outra vivência profissional? <span class="required-icon">*</span></label>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="outraVivencia3" id="outraVivencia3Sim" value="1" <?= (($vivencias[3]['outra_vivencia'] ?? '') === '1') ? 'checked' : '' ?>>
											<label class="form-check-label" for="outraVivencia3Sim">Sim</label>
										</div>

										<div class="form-check">
											<input class="form-check-input" type="radio" name="outraVivencia3" id="outraVivencia3Nao" value="0" <?= (($vivencias[3]['outra_vivencia'] ?? '') === '0') ? 'checked' : '' ?>>
											<label class="form-check-label" for="outraVivencia3Nao">Não</label>
										</div>
									</div>
								
								</div>

								<div class="bloco-vivencia" data-vivencia="4">

									<h3 class="mt-3 titulo-vivencia">Vivência 4</h3>

									<div class="form-group">
										<label for="organizacaoEmp4">Organização/Empresa <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="organizacaoEmp4" name="organizacaoEmp4" value="<?= esc_attr($vivencias[4]['organizacao_empresa'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="cargoFuncao4">Cargo/Função: <span class="required-icon">*</span></label>
										<input type="text" class="form-control campo-obrigatorio" id="cargoFuncao4" name="cargoFuncao4" value="<?= esc_attr($vivencias[4]['cargo_funcao'] ?? '') ?>">
									</div>

									<div class="form-group">
										<label for="duracao4">Duração: <span class="required-icon">*</span></label>
										<select class="form-control campo-obrigatorio" id="duracao4" name="duracao4">
											<option value="">-- Selecione --</option>
											<option value="ate-1-ano" <?= selected($vivencias[4]['duracao'] ?? '', 'ate-1-ano', false) ?>>Até 1 ano</option>
											<option value="entre-1-3" <?= selected($vivencias[4]['duracao'] ?? '', 'entre-1-3', false) ?>>Entre 1 e 3 anos</option>
											<option value="de-3-5" <?= selected($vivencias[4]['duracao'] ?? '', 'de-3-5', false) ?>>De 3 a 5 anos</option>
											<option value="acima-5" <?= selected($vivencias[4]['duracao'] ?? '', 'acima-5', false) ?>>Acima de 5 anos</option>										
										</select>
									</div>

									<div class="form-group">
										<label for="atividadesComp4">
											Atividades e Competências Desenvolvidas: <span class="required-icon">*</span><br><small>Por favor, descreva de forma breve, com o uso de palavras-chave, as atividades e competências adquiridas</small>
										</label>

										<textarea
											class="form-control campo-obrigatorio"
											id="atividadesComp4"
											name="atividadesComp4"
											rows="6"><?= esc_textarea($vivencias[4]['atividades_competencias'] ?? ''); ?></textarea>
									</div>

								</div>


							</div>
						</div>
					</div>

					<!-- ETAPA 5 -->
					 <div class="card etapa-formulario">
					 	<div class="card-header" id="headingFive">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed"
									type="button"
									data-toggle="collapse"
									data-target="#collapseFive">

									<span class="numeral">5</span> Conhecimentos em Informática e Tecnologia
								</button>
							</h2>
						</div>

						<div id="collapseFive" class="collapse">
							<div class="card-body">

								<p>
									Quais dos sistemas a seguir você já trabalhou e/ou tem facilidade de navegação? <span class="required-icon">*</span><br>
									<small class="font-italic">(Eventualmente, poderá haver teste de conhecimento, a critério do gestor da vaga)</small>
								</p>

								<table class="table table-striped">									
									<tbody>
										<tr class="align-middle">
											<th width="40%" class="align-middle">Sistema</th>
											<td width="15%" class="text-center align-middle fw-600">Nenhum<br><small>Nunca acessei</small></td>
											<td width="15%" class="text-center align-middle fw-600">Básico<br><small>Conheço e acessei algumas vezes</small></td>
											<td width="15%" class="text-center align-middle fw-600">Intermediário<br><small>Acesso com frequência, porém sem funcionalidades avançadas de gestão</small></td>
											<td width="15%" class="text-center align-middle fw-600">Avançado<br><small>Emissão e análise de relatórios, produção de estatística e painéis, mudanças e criação de telas</small></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>EOL - Escola On Line</th>
											<td class="text-center"><input type="radio" name="informatica[eol]" value="0" <?= checked($informatica['eol'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[eol]" value="1" <?= checked($informatica['eol'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[eol]" value="2" <?= checked($informatica['eol'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[eol]" value="3" <?= checked($informatica['eol'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SEI - Sistema Eletrônico de Informações</th>
											<td class="text-center"><input type="radio" name="informatica[sei]" value="0" <?= checked($informatica['sei'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sei]" value="1" <?= checked($informatica['sei'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sei]" value="2" <?= checked($informatica['sei'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sei]" value="3" <?= checked($informatica['sei'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SOF - Sistema de Orçamento e Finanças</th>
											<td class="text-center"><input type="radio" name="informatica[sof]" value="0" <?= checked($informatica['sof'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sof]" value="1" <?= checked($informatica['sof'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sof]" value="2" <?= checked($informatica['sof'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sof]" value="3" <?= checked($informatica['sof'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SIGPEC - Sistema Integrado de Gestão de Pessoas e Competências</th>
											<td class="text-center"><input type="radio" name="informatica[sigpec]" value="0" <?= checked($informatica['sigpec'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigpec]" value="1" <?= checked($informatica['sigpec'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigpec]" value="2" <?= checked($informatica['sigpec'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigpec]" value="3" <?= checked($informatica['sigpec'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SIG - Escola - Sistema Integrado de Gestão da Escola</th>
											<td class="text-center"><input type="radio" name="informatica[sig-escola]" value="0" <?= checked($informatica['sig-escola'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sig-escola]" value="1" <?= checked($informatica['sig-escola'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sig-escola]" value="2" <?= checked($informatica['sig-escola'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sig-escola]" value="3" <?= checked($informatica['sig-escola'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SIGEP - Sistema Integrado de Gestão de Parcerias</th>
											<td class="text-center"><input type="radio" name="informatica[sigep]" value="0" <?= checked($informatica['sigep'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigep]" value="1" <?= checked($informatica['sigep'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigep]" value="2" <?= checked($informatica['sigep'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigep]" value="3" <?= checked($informatica['sigep'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SGP - Sistema de Gestão Pedagógica</th>
											<td class="text-center"><input type="radio" name="informatica[sgp]" value="0" <?= checked($informatica['sgp'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sgp]" value="1" <?= checked($informatica['sgp'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sgp]" value="2" <?= checked($informatica['sgp'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sgp]" value="3" <?= checked($informatica['sgp'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>DOC - Diário Oficial da Cidade de São Paulo</th>
											<td class="text-center"><input type="radio" name="informatica[doc]" value="0" <?= checked($informatica['doc'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[doc]" value="1" <?= checked($informatica['doc'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[doc]" value="2" <?= checked($informatica['doc'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[doc]" value="3" <?= checked($informatica['doc'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>TID - Trâmite Interno Digital</th>
											<td class="text-center"><input type="radio" name="informatica[tid]" value="0" <?= checked($informatica['tid'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[tid]" value="1" <?= checked($informatica['tid'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[tid]" value="2" <?= checked($informatica['tid'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[tid]" value="3" <?= checked($informatica['tid'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SIMPROC - Sistema Municipal de Processos</th>
											<td class="text-center"><input type="radio" name="informatica[simproc]" value="0" <?= checked($informatica['simproc'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[simproc]" value="1" <?= checked($informatica['simproc'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[simproc]" value="2" <?= checked($informatica['simproc'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[simproc]" value="3" <?= checked($informatica['simproc'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>SIGPAE - Sistema de Gestão do Programa de Alimentação Escolar</th>
											<td class="text-center"><input type="radio" name="informatica[sigpae]" value="0" <?= checked($informatica['sigpae'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigpae]" value="1" <?= checked($informatica['sigpae'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigpae]" value="2" <?= checked($informatica['sigpae'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sigpae]" value="3" <?= checked($informatica['sigpae'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>CDEP - Centro de Documentação da Educação Paulistana</th>
											<td class="text-center"><input type="radio" name="informatica[cdep]" value="0" <?= checked($informatica['cdep'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[cdep]" value="1" <?= checked($informatica['cdep'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[cdep]" value="2" <?= checked($informatica['cdep'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[cdep]" value="3" <?= checked($informatica['cdep'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>CLIC - Central de Informações e Apoio da COGEP</th>
											<td class="text-center"><input type="radio" name="informatica[clic]" value="0" <?= checked($informatica['clic'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[clic]" value="1" <?= checked($informatica['clic'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[clic]" value="2" <?= checked($informatica['clic'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[clic]" value="3" <?= checked($informatica['clic'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Apps Microsoft 365</th>
											<td class="text-center"><input type="radio" name="informatica[apps-365]" value="0" <?= checked($informatica['apps-365'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[apps-365]" value="1" <?= checked($informatica['apps-365'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[apps-365]" value="2" <?= checked($informatica['apps-365'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[apps-365]" value="3" <?= checked($informatica['apps-365'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Office Word</th>
											<td class="text-center"><input type="radio" name="informatica[office-word]" value="0" <?= checked($informatica['office-word'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-word]" value="1" <?= checked($informatica['office-word'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-word]" value="2" <?= checked($informatica['office-word'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-word]" value="3" <?= checked($informatica['office-word'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Office Excel</th>
											<td class="text-center"><input type="radio" name="informatica[office-excel]" value="0" <?= checked($informatica['office-excel'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-excel]" value="1" <?= checked($informatica['office-excel'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-excel]" value="2" <?= checked($informatica['office-excel'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-excel]" value="3" <?= checked($informatica['office-excel'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Office PowerPoint </th>
											<td class="text-center"><input type="radio" name="informatica[office-ppt]" value="0" <?= checked($informatica['office-ppt'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-ppt]" value="1" <?= checked($informatica['office-ppt'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-ppt]" value="2" <?= checked($informatica['office-ppt'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[office-ppt]" value="3" <?= checked($informatica['office-ppt'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Canva</th>
											<td class="text-center"><input type="radio" name="informatica[canva]" value="0" <?= checked($informatica['canva'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[canva]" value="1" <?= checked($informatica['canva'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[canva]" value="2" <?= checked($informatica['canva'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[canva]" value="3" <?= checked($informatica['canva'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Power BI</th>
											<td class="text-center"><input type="radio" name="informatica[power-bi]" value="0" <?= checked($informatica['power-bi'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[power-bi]" value="1" <?= checked($informatica['power-bi'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[power-bi]" value="2" <?= checked($informatica['power-bi'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[power-bi]" value="3" <?= checked($informatica['power-bi'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Teams/Meet/Zoom/Workplace</th>
											<td class="text-center"><input type="radio" name="informatica[teams]" value="0" <?= checked($informatica['teams'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[teams]" value="1" <?= checked($informatica['teams'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[teams]" value="2" <?= checked($informatica['teams'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[teams]" value="3" <?= checked($informatica['teams'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Sharepoint/Workspace/Confluence</th>
											<td class="text-center"><input type="radio" name="informatica[sharepoint]" value="0" <?= checked($informatica['sharepoint'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sharepoint]" value="1" <?= checked($informatica['sharepoint'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sharepoint]" value="2" <?= checked($informatica['sharepoint'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[sharepoint]" value="3" <?= checked($informatica['sharepoint'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Forms/Google Forms/SuveyMars</th>
											<td class="text-center"><input type="radio" name="informatica[forms]" value="0" <?= checked($informatica['forms'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[forms]" value="1" <?= checked($informatica['forms'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[forms]" value="2" <?= checked($informatica['forms'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[forms]" value="3" <?= checked($informatica['forms'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-informatica-obrigatoria">
											<th>Planner/Monday/ClickUp</th>
											<td class="text-center"><input type="radio" name="informatica[planner]" value="0" <?= checked($informatica['planner'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[planner]" value="1" <?= checked($informatica['planner'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[planner]" value="2" <?= checked($informatica['planner'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="informatica[planner]" value="3" <?= checked($informatica['planner'] ?? '', '3', false) ?>></td>
										</tr>

									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- ETAPA 6 -->
					 <div class="card etapa-formulario">
					 	<div class="card-header" id="headingSix">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed"
									type="button"
									data-toggle="collapse"
									data-target="#collapseSix">

									<span class="numeral">6</span> Preferências e Perfil Comportamental
								</button>
							</h2>
						</div>

						<div id="collapseSix" class="collapse">
							<div class="card-body">
								<p>Por gentileza, indique a ação que mais reflete sua atitude nas situações a seguir.</p>

								<table class="table table-striped">									
									<tbody>
										<tr class="align-middle">
											<th width="40%" class="align-middle">Afirmação</th>
											<td width="15%" class="text-center align-middle fw-600">Concordo plenamente</td>
											<td width="15%" class="text-center align-middle fw-600">Concordo</td>
											<td width="15%" class="text-center align-middle fw-600">Discordo</td>
											<td width="15%" class="text-center align-middle fw-600">Discordo plenamente</td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto-me mais motivado e produtivo quando meu trabalho envolve interação constante com pessoas, networking e trocas sociais frequentes.</th>
											<td class="text-center"><input type="radio" name="comportamental[sociabilidade]" value="0" <?= checked($comportamental['sociabilidade'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[sociabilidade]" value="1" <?= checked($comportamental['sociabilidade'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[sociabilidade]" value="2" <?= checked($comportamental['sociabilidade'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[sociabilidade]" value="3" <?= checked($comportamental['sociabilidade'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto que minha produtividade é consideravelmente maior quando trabalho sozinho em tarefas técnicas, preferindo focar em dados e processos do que em interações sociais constantes ou networking.</th>
											<td class="text-center"><input type="radio" name="comportamental[analitico]" value="0" <?= checked($comportamental['analitico'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[analitico]" value="1" <?= checked($comportamental['analitico'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[analitico]" value="2" <?= checked($comportamental['analitico'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[analitico]" value="3" <?= checked($comportamental['analitico'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto-me à vontade em ambientes de mudança constante e prefiro ter liberdade para criar novas soluções ou métodos, em vez de seguir estritamente regras, manuais ou padrões já estabelecidos.</th>
											<td class="text-center"><input type="radio" name="comportamental[inovacao]" value="0" <?= checked($comportamental['inovacao'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[inovacao]" value="1" <?= checked($comportamental['inovacao'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[inovacao]" value="2" <?= checked($comportamental['inovacao'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[inovacao]" value="3" <?= checked($comportamental['inovacao'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto-me mais confortável e produtivo realizando tarefas técnicas, lidando com números, dados e prazos de forma isolada, do que trabalhando diretamente com atendimento ao público.</th>
											<td class="text-center"><input type="radio" name="comportamental[tecnico]" value="0" <?= checked($comportamental['tecnico'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[tecnico]" value="1" <?= checked($comportamental['tecnico'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[tecnico]" value="2" <?= checked($comportamental['tecnico'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[tecnico]" value="3" <?= checked($comportamental['tecnico'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto-me mais produtivo e seguro quando sigo uma rotina com processos bem definidos e previsíveis, preferindo manter métodos que já funcionam do que buscar constantemente formas inovadoras ou diferentes de realizar o trabalho.</th>
											<td class="text-center"><input type="radio" name="comportamental[rotina]" value="0" <?= checked($comportamental['rotina'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[rotina]" value="1" <?= checked($comportamental['rotina'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[rotina]" value="2" <?= checked($comportamental['rotina'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[rotina]" value="3" <?= checked($comportamental['rotina'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto-me mais seguro e produtivo utilizando métodos de trabalho tradicionais e ferramentas já conhecidas, preferindo manter uma rotina estável em vez de ter que lidar com inovações constantes, novas tecnologias ou análise detalhada de dados técnicos.</th>
											<td class="text-center"><input type="radio" name="comportamental[conservador]" value="0" <?= checked($comportamental['conservador'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[conservador]" value="1" <?= checked($comportamental['conservador'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[conservador]" value="2" <?= checked($comportamental['conservador'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[conservador]" value="3" <?= checked($comportamental['conservador'] ?? '', '3', false) ?>></td>
										</tr>

										<tr class="linha-comportamental-obrigatoria">
											<th>Sinto-me mais produtivo e realizado resolvendo problemas práticos e imediatos na execução direta das tarefas do que dedicando tempo ao planejamento detalhado, organização de processos ou gestão administrativa.</th>
											<td class="text-center"><input type="radio" name="comportamental[executor]" value="0" <?= checked($comportamental['executor'] ?? '', '0', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[executor]" value="1" <?= checked($comportamental['executor'] ?? '', '1', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[executor]" value="2" <?= checked($comportamental['executor'] ?? '', '2', false) ?>></td>
											<td class="text-center"><input type="radio" name="comportamental[executor]" value="3" <?= checked($comportamental['executor'] ?? '', '3', false) ?>></td>
										</tr>

									</tbody>
								</table>
							</div>
						</div>
					</div>

					<!-- ETAPA 7 -->
					 <div class="card etapa-formulario">
					 	<div class="card-header" id="headingSeven">
							<h2 class="mb-0">
								<button class="btn btn-block text-left collapsed"
									type="button"
									data-toggle="collapse"
									data-target="#collapseSeven">

									<span class="numeral">7</span> Finalização e Visualização
								</button>
							</h2>
						</div>

						<div id="collapseSeven" class="collapse">
							<div class="card-body">
								<div class="alert alert-warning mt-3">
									<strong>Importante:</strong> <br>Independentemente da resposta selecionada abaixo, o(a) candidato(a) deverá realizar a inscrição em todas as vagas de seu interesse.
								</div>

								<div class="form-group campo-obrigatorio-radio">

									<label for="visualizarCurriculo">Quem poderá visualizar as informações que você preencheu neste cadastro? <span class="required-icon">*</span></label>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="visualizarCurriculo"
											id="gestor"
											value="0"
											<?= checked($curriculo->visualizar_curriculo ?? '', '0', false) ?>>

										<label class="form-check-label" for="gestor">
											Apenas os gestores das vagas às quais eu me candidatar
										</label>
									</div>

									<div class="form-check">
										<input class="form-check-input"
											type="radio"
											name="visualizarCurriculo"
											id="todos"
											value="1" <?= checked($curriculo->visualizar_curriculo ?? '', '1', false) ?>>

										<label class="form-check-label" for="todos">
											Qualquer gestor que esteja consultando currículos
										</label>
									</div>

								</div>

								<p><strong>Sugestões</strong><br><small>Você teria alguma sugestão para melhorar este questionário?</small></p>
								
								<div class="form-group">
									<label for="sugestoes">
										Utilize esse espaço livremente para expressar suas críticas e/ou sugestões.
									</label>

									<textarea
										class="form-control"
										id="sugestoes"
										name="sugestoes"
										placeholder="Escreva aqui suas sugestões..."
										rows="6"><?= esc_textarea($curriculo->sugestoes ?? '') ?></textarea>
								</div>

							</div>
						</div>
					</div>

					<div class="etapa-formulario text-right mb-4">
						
					<?php if($curriculo->status_curriculo === 'rascunho' || !$curriculo->status_curriculo): ?>
						<button
							type="submit"
							name="acao_curriculo"
							value="rascunho"
							class="btn btn-rascunho save-btn">

							Salvar rascunho

						</button>
					<?php endif; ?>

						<button
							type="submit"
							name="acao_curriculo"
							value="finalizar"
							class="btn btn-primary save-btn">

							Salvar currículo

						</button>
					</div>

				</div>
				<input type="hidden" name="acao_formulario" value="salvar_banco_talentos">
				<?php wp_nonce_field('salvar_curriculo', 'curriculo_nonce'); ?>
			</form>

		</div>
	</div>
</div>

<script>
	jQuery(document).ready(function($){

		 // Máscaras dos inputs    	
    	$('#telWhatsapp').mask('(00) 00000-0000');
    	$('#telOpcional').mask('(00) 00000-0000');

		/*
		|--------------------------------------------------------------------------
		| CONTROLE DAS ETAPAS DO FORMULÁRIO
		|--------------------------------------------------------------------------
		*/

		function validarEmails() {

			let valido = true;

			/*
			|--------------------------------------------------------------------------
			| Regex email
			|--------------------------------------------------------------------------
			*/

			const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

			/*
			|--------------------------------------------------------------------------
			| Email principal
			|--------------------------------------------------------------------------
			*/

			const emailPrincipal = $('#email');
			const valorPrincipal = emailPrincipal.val().trim();

			removerErro(emailPrincipal);

			if (!regexEmail.test(valorPrincipal)) {

				mostrarErro(
					emailPrincipal,
					'Informe um e-mail válido.'
				);

				valido = false;

			} else if (
				!valorPrincipal.endsWith('@sme.prefeitura.sp.gov.br')
			) {

				mostrarErro(
					emailPrincipal,
					'O e-mail deve ser institucional @sme.prefeitura.sp.gov.br'
				);

				valido = false;
			}

			/*
			|--------------------------------------------------------------------------
			| Email secundário
			|--------------------------------------------------------------------------
			*/

			const emailSec = $('#emailSec');
			const valorSec = emailSec.val().trim();

			removerErro(emailSec);

			// só valida se preenchido
			if (
				valorSec !== '' &&
				!regexEmail.test(valorSec)
			) {

				mostrarErro(
					emailSec,
					'Informe um e-mail secundário válido.'
				);

				valido = false;
			}

			return valido;
		}

		function controlarEtapas(animar = true) {

			const valor = $('input[name="avisoPrivacidade"]:checked').val();

			if(!valor) {

				animar
					? $('.etapa-formulario').slideUp()
					: $('.etapa-formulario').hide();

				$('.mensagem-bloqueio').hide();

				return;
			}

			if(valor === 'sim') {

				animar
					? $('.etapa-formulario').slideDown()
					: $('.etapa-formulario').show();

				$('.mensagem-bloqueio').hide();

			}

			if(valor === 'nao') {

				animar
					? $('.etapa-formulario').slideUp()
					: $('.etapa-formulario').hide();

				$('.mensagem-bloqueio').fadeIn();

				$('.etapa-formulario .collapse').collapse('hide');

			}

		}

		/*
		|--------------------------------------------------------------------------
		| FUNÇÃO GENÉRICA
		|--------------------------------------------------------------------------
		|
		| parentField       => radio principal
		| childField        => radio secundário
		| childGroup        => bloco do radio secundário
		| textareaGroup     => bloco do textarea
		| textareaField     => textarea
		|--------------------------------------------------------------------------
		*/

		function controlarCamposDependentes(config, animar = true) {

			const parentValue = $('input[name="' + config.parentField + '"]:checked').val();

			/*
			|--------------------------------------------------------------------------
			| CAMPO PAI = NÃO
			|--------------------------------------------------------------------------
			*/

			if(parentValue === '0' || !parentValue) {

				if(config.childGroup) {

					animar
						? $(config.childGroup).slideUp()
						: $(config.childGroup).hide();

				}

				if(config.textareaGroup) {

					animar
						? $(config.textareaGroup).slideUp()
						: $(config.textareaGroup).hide();

				}

				return;
			}

			/*
			|--------------------------------------------------------------------------
			| CAMPO PAI = SIM
			|--------------------------------------------------------------------------
			*/

			if(parentValue === '1') {

				if(config.childGroup) {

					animar
						? $(config.childGroup).slideDown()
						: $(config.childGroup).show();

				}

			}

			/*
			|--------------------------------------------------------------------------
			| NÃO POSSUI CAMPO FILHO
			|--------------------------------------------------------------------------
			*/

			if(!config.childField) {
				return;
			}

			const childValue = $('input[name="' + config.childField + '"]:checked').val();

			/*
			|--------------------------------------------------------------------------
			| CAMPO FILHO = SIM
			|--------------------------------------------------------------------------
			*/

			if(childValue === '1') {

				animar
					? $(config.textareaGroup).slideDown()
					: $(config.textareaGroup).show();

			} else {

				animar
					? $(config.textareaGroup).slideUp()
					: $(config.textareaGroup).hide();

			}

		}

		/*
		|--------------------------------------------------------------------------
		| CONFIGURAÇÕES
		|--------------------------------------------------------------------------
		*/

		const gruposDependentes = [

			{
				parentField: 'possuiDeficiencia',
				childField: 'necessitaAdaptacao',
				childGroup: '.grupo-adaptacao',
				textareaGroup: '.grupo-descricao-adaptacao',
				textareaField: '#descrevaAdapta'
			},

			{
				parentField: 'servidorReadaptado',
				childField: 'readaptadoNecessita',
				childGroup: '.grupo-readaptacao',
				textareaGroup: '.grupo-readaptacao-descricao',
				textareaField: '#readaptadoDescreva'
			},

			{
				parentField: 'acumulaCargo',
				childGroup: '.grupo-acumula-cargo'
			}

		];

		/*
		|--------------------------------------------------------------------------
		| EVENTOS
		|--------------------------------------------------------------------------
		*/

		$('input[name="avisoPrivacidade"]').on('change', function(){

			controlarEtapas(true);

		});

		/*
		|--------------------------------------------------------------------------
		| EVENTOS DINÂMICOS
		|--------------------------------------------------------------------------
		*/

		gruposDependentes.forEach(function(config){

			/*
			|--------------------------------------------------------------------------
			| Campo pai
			|--------------------------------------------------------------------------
			*/

			$('input[name="' + config.parentField + '"]').on('change', function(){

				if($(this).val() === '0') {

					if(config.childField) {

						$('input[name="' + config.childField + '"]')
							.prop('checked', false);

					}

					if(config.textareaField) {

						$(config.textareaField).val('');

					}

				}

				controlarCamposDependentes(config);

			});

			/*
			|--------------------------------------------------------------------------
			| Campo filho
			|--------------------------------------------------------------------------
			*/

			if(config.childField) {

				$('input[name="' + config.childField + '"]').on('change', function(){

					if($(this).val() === '0' && config.textareaField) {

						$(config.textareaField).val('');

					}

					controlarCamposDependentes(config);

				});

			}

			/*
			|--------------------------------------------------------------------------
			| Inicialização
			|--------------------------------------------------------------------------
			*/

			controlarCamposDependentes(config, false);

		});

		/*
		|--------------------------------------------------------------------------
		| Inicialização geral
		|--------------------------------------------------------------------------
		*/

		controlarEtapas(false);

		
		// Controle do campo de cargo outro
		const campoCargoOutro = $('#cargoOutro').closest('.form-group');

		function controlarCargoOutro() {
			const checked = $('#cargoOutroCheck').is(':checked');

			if (checked) {
				campoCargoOutro.stop(true, true).slideDown();
			} else {
				campoCargoOutro.stop(true, true).slideUp();
				$('#cargoOutro').val('');
			}
		}		

		$('#cargoOutroCheck').on('change', controlarCargoOutro);

		controlarCargoOutro();

        /*
        |--------------------------------------------------------------------------
        | CONTROLE ESCOLARIDADE
        |--------------------------------------------------------------------------
        */

        function controlarEscolaridade() {

            const escolaridade = $('#escolaridade').val();

            console.log('Escolaridade selecionada:', escolaridade);

            const blocoGraduacao = [
                $('#cursoGraduacao').closest('.form-group'),
                $('#anoConclusao').closest('.form-group'),
                $('input[name="outraGraduacao"]').closest('.form-group'),
                $('#segundaGraduacao').closest('.form-group'),
                $('#anoConclusaoSeg').closest('.form-group'),
                $('.escolaridade-hide')
            ];

            /*
            |--------------------------------------------------------------------------
            | ESCONDE TUDO INICIALMENTE
            |--------------------------------------------------------------------------
            */

            $(blocoGraduacao).each(function(){
                $(this).hide();
            });

            /*
            |--------------------------------------------------------------------------
            | SEM ESCOLARIDADE / ENSINO MÉDIO
            |--------------------------------------------------------------------------
            */

            if(
                escolaridade === '' ||
                escolaridade === 'medio'
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | MOSTRA PRIMEIRA GRADUAÇÃO
            |--------------------------------------------------------------------------
            */

            $('#cursoGraduacao')
                .closest('.form-group')
                .show();

            $('#anoConclusao')
                .closest('.form-group')
                .show();

            $('input[name="outraGraduacao"]')
                .closest('.form-group')
                .show();

            $('.escolaridade-hide').show();

            /*
            |--------------------------------------------------------------------------
            | CONTROLA SEGUNDA GRADUAÇÃO
            |--------------------------------------------------------------------------
            */

            controlarSegundaGraduacao();
        }

        /*
        |--------------------------------------------------------------------------
        | CONTROLE SEGUNDA GRADUAÇÃO
        |--------------------------------------------------------------------------
        */

        function controlarSegundaGraduacao() {

            const valor = $('input[name="outraGraduacao"]:checked').val();

            const segundaGraduacao = $('#segundaGraduacao')
                .closest('.form-group');

            const anoConclusaoSeg = $('#anoConclusaoSeg')
                .closest('.form-group');

            segundaGraduacao.hide();
            anoConclusaoSeg.hide();

            if(valor === '1') {

                segundaGraduacao.show();
                anoConclusaoSeg.show();

            }
        }

        /*
        |--------------------------------------------------------------------------
        | EVENTOS
        |--------------------------------------------------------------------------
        */

        $('#escolaridade').on('change', function(){
            console.log('escolaridade');
            controlarEscolaridade();
        });

        $('input[name="outraGraduacao"]').on('change', function(){
            controlarSegundaGraduacao();
        });

        /*
        |--------------------------------------------------------------------------
        | EXECUÇÃO INICIAL
        |--------------------------------------------------------------------------
        */

        controlarEscolaridade();

		function controlarVivencias() {

			// Valores selecionados
			const vivencia1 = $('input[name="outraVivencia1"]:checked').val();
			const vivencia2 = $('input[name="outraVivencia2"]:checked').val();
			const vivencia3 = $('input[name="outraVivencia3"]:checked').val();

			// Estado inicial
			$('.bloco-vivencia[data-vivencia="2"]').hide();
			$('.bloco-vivencia[data-vivencia="3"]').hide();
			$('.bloco-vivencia[data-vivencia="4"]').hide();

			// Vivência 2
			if (vivencia1 === '1') {

				$('.bloco-vivencia[data-vivencia="2"]').show();

				// Vivência 3
				if (vivencia2 === '1') {

					$('.bloco-vivencia[data-vivencia="3"]').show();

					// Vivência 4
					if (vivencia3 === '1') {

						$('.bloco-vivencia[data-vivencia="4"]').show();

					}

				}

			}

		}

		// Executa no carregamento
		controlarVivencias();

		// Executa ao alterar radios
		$(document).on(
			'change',
			'input[name^="outraVivencia"]',
			function () {
				controlarVivencias();
			}
		);

		// Controla se o usuário está alterando o formulário
		// Se alterar, impede o fechamento da página e mostra um alerta de confirmação
		let formularioAlterado = false;

		$('form :input').on('change input', function() {
			formularioAlterado = true;
		});

		$('form').on('submit', function() {
			formularioAlterado = false;
		});

		window.addEventListener('beforeunload', function(e) {

			if (!formularioAlterado) {
				return;
			}

			e.preventDefault();
			e.returnValue = '';

		});

	});
	
</script>

<script>

	jQuery(document).ready(function($){

		/*
		|--------------------------------------------------------------------------
		| FUNÇÃO VALIDAÇÃO
		|--------------------------------------------------------------------------
		*/

		function deveValidarCampo(campo) {
			

			if(campo.css('display') === 'none') {
				return false;
			}			

			if(
				campo.parents().filter(function(){

					return $(this).css('display') === 'none' &&
						!$(this).hasClass('collapse') &&
						!$(this).hasClass('accordion-collapse');

				}).length
			) {
				return false;
			}

			return true;

		}

		/*
		|--------------------------------------------------------------------------
		| BOTÃO FINALIZAR
		|--------------------------------------------------------------------------
		*/

		$('button[value="finalizar"]').on('click', function(e){

			let formularioValido = true;

			/*
			|--------------------------------------------------------------------------
			| PRIMEIRO CAMPO COM ERRO
			|--------------------------------------------------------------------------
			*/

			let primeiroCampoComErro = null;

			/*
			|--------------------------------------------------------------------------
			| LIMPA ERROS
			|--------------------------------------------------------------------------
			*/

			$('.erro-validacao').remove();
			$('.linha-erro').removeClass('linha-erro');
			$('.is-invalid').removeClass('is-invalid');

			/*
			|--------------------------------------------------------------------------
			| INPUTS E SELECTS
			|--------------------------------------------------------------------------
			*/

			$('.campo-obrigatorio').each(function(){

				const campo = $(this);

				if(!deveValidarCampo(campo)) {
					return;
				}

				if($.trim(campo.val()) === '') {

					formularioValido = false;

					/*
					|--------------------------------------------------------------------------
					| PRIMEIRO ERRO
					|--------------------------------------------------------------------------
					*/

					if(!primeiroCampoComErro) {
						primeiroCampoComErro = campo;
					}

					campo.addClass('is-invalid');

					campo.after(
						'<small class="erro-validacao text-danger">' +
						'Este campo é de preenchimento obrigatório.' +
						'</small>'
					);

				}

			});

			/*
			|--------------------------------------------------------------------------
			| VALIDA E-MAIL PRINCIPAL
			|--------------------------------------------------------------------------
			*/

			const emailPrincipal = $('#email');
			const valorEmailPrincipal = $.trim(emailPrincipal.val());

			// Regex básica de email
			const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

			// Domínios permitidos
			const dominiosPermitidos = [
				'@sme.prefeitura.sp.gov.br',
				'@prefeitura.sp.gov.br'
			];

			if(valorEmailPrincipal !== '') {

				// Valida formato
				if(!regexEmail.test(valorEmailPrincipal)) {

					formularioValido = false;

					if(!primeiroCampoComErro) {
						primeiroCampoComErro = emailPrincipal;
					}

					emailPrincipal.addClass('is-invalid');

					emailPrincipal.after(
						'<small class="erro-validacao text-danger">' +
						'Informe um e-mail válido.' +
						'</small>'
					);

				} else {

					// Valida domínio
					const dominioValido = dominiosPermitidos.some(function(dominio){
						return valorEmailPrincipal.toLowerCase().endsWith(dominio);
					});

					if(!dominioValido) {

						formularioValido = false;

						if(!primeiroCampoComErro) {
							primeiroCampoComErro = emailPrincipal;
						}

						emailPrincipal.addClass('is-invalid');

						emailPrincipal.after(
							'<small class="erro-validacao text-danger">' +
							'É obrigatório a utilização do e-mail institucional' +
							'</small>'
						);

					}

				}

			}

			/*
			|--------------------------------------------------------------------------
			| VALIDA E-MAIL SECUNDÁRIO
			|--------------------------------------------------------------------------
			*/

			const emailSecundario = $('#emailSec');
			const valorEmailSecundario = $.trim(emailSecundario.val());

			if(valorEmailSecundario !== '') {

				if(!regexEmail.test(valorEmailSecundario)) {

					formularioValido = false;

					if(!primeiroCampoComErro) {
						primeiroCampoComErro = emailSecundario;
					}

					emailSecundario.addClass('is-invalid');

					emailSecundario.after(
						'<small class="erro-validacao text-danger">' +
						'Informe um e-mail secundário válido.' +
						'</small>'
					);

				}

			}

			/*
			|--------------------------------------------------------------------------
			| RADIOS
			|--------------------------------------------------------------------------
			*/

			$('.campo-obrigatorio-radio').each(function(){

				const grupo = $(this);

				if(!deveValidarCampo(grupo)) {
					return;
				}

				const radios = grupo.find('input[type="radio"]');

				if(!radios.is(':checked')) {

					formularioValido = false;

					if(!primeiroCampoComErro) {
						primeiroCampoComErro = grupo;
					}

					grupo.append(
						'<small class="erro-validacao text-danger d-block mt-1">' +
						'Selecione uma opção.' +
						'</small>'
					);

				}

			});

			/*
			|--------------------------------------------------------------------------
			| CHECKBOXES
			|--------------------------------------------------------------------------
			*/

			$('.campo-obrigatorio-checkbox').each(function(){

				const grupo = $(this);

				if(!deveValidarCampo(grupo)) {
					return;
				}

				const checkboxes = grupo.find('input[type="checkbox"]');

				if(!checkboxes.is(':checked')) {

					formularioValido = false;

					if(!primeiroCampoComErro) {
						primeiroCampoComErro = grupo;
					}

					grupo.append(
						'<small class="erro-validacao text-danger d-block mt-1">' +
						'Selecione ao menos uma opção.' +
						'</small>'
					);

				}

			});

			function validarTabelaRadios(seletor, mensagemErro) {

				$(seletor).each(function(){

					const linha = $(this);
					const radios = linha.find('input[type="radio"]');

					if(!radios.is(':checked')) {

						formularioValido = false;

						if(!primeiroCampoComErro) {
							primeiroCampoComErro = linha;
						}

						linha.addClass('linha-erro');

						linha.find('th').append(
							'<small class="erro-validacao text-danger d-block mt-1">' +
								mensagemErro +
							'</small>'
						);

					}

				});

			}

			/*
			|--------------------------------------------------------------------------
			| INFORMÁTICA
			|--------------------------------------------------------------------------
			*/

			validarTabelaRadios(
				'.linha-informatica-obrigatoria',
				'Selecione ao menos um nível de conhecimento para continuar.'
			);

			/*
			|--------------------------------------------------------------------------
			| PERFIL COMPORTAMENTAL
			|--------------------------------------------------------------------------
			*/

			validarTabelaRadios(
				'.linha-comportamental-obrigatoria',
				'Por gentileza, indique a ação que mais reflete sua atitude nas situações a seguir.'
			);

			/*
			|--------------------------------------------------------------------------
			| BLOQUEIA ENVIO
			|--------------------------------------------------------------------------
			*/

			if(!formularioValido) {

				e.preventDefault();

				/*
				|--------------------------------------------------------------------------
				| Abre accordion automaticamente
				|--------------------------------------------------------------------------
				*/

				const collapse = primeiroCampoComErro.closest('.collapse');

				if(collapse.length && !collapse.hasClass('show')) {

					collapse.collapse('show');

				}

				/*
				|--------------------------------------------------------------------------
				| Scroll após abrir accordion
				|--------------------------------------------------------------------------
				*/

				setTimeout(function(){

					$('html, body').animate({

						scrollTop: primeiroCampoComErro.offset().top - 120

					}, 500);

				}, 300);

			}

		});

	});

</script>