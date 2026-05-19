<?php

	$user_id = get_current_user_id();
    print_r($user_id);
	$retorno_candidato = obter_dados_candidato($user_id);
	$origem_dados = $retorno_candidato['origem'];
	$curriculo = $retorno_candidato['dados'];

	$cargos = [];

	if (!empty($curriculo->cargo_efetivo)) {
		$cargos = json_decode($curriculo->cargo_efetivo, true);
	}

	$dados = obter_dados_candidato($user_id);
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
								Conteúdo etapa 3
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
								Conteúdo etapa 4
							</div>
						</div>
					</div>

					<div class="etapa-formulario text-right mb-4">
						<button
							type="submit"
							name="acao_curriculo"
							value="rascunho"
							class="btn btn-rascunho save-btn">

							Salvar rascunho

						</button>

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

	});
	
</script>

<script>

	jQuery(document).ready(function($){

		/*
		|--------------------------------------------------------------------------
		| BOTÃO FINALIZAR
		|--------------------------------------------------------------------------
		*/

		$('button[value="finalizar"]').on('click', function(e){

			let formularioValido = true;

			/*
			|--------------------------------------------------------------------------
			| LIMPA ERROS
			|--------------------------------------------------------------------------
			*/

			$('.erro-validacao').remove();
			$('.is-invalid').removeClass('is-invalid');

			/*
			|--------------------------------------------------------------------------
			| INPUTS E SELECTS
			|--------------------------------------------------------------------------
			*/

			$('.campo-obrigatorio:visible').each(function(){

				const campo = $(this);

				if($.trim(campo.val()) === '') {

					formularioValido = false;

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

			$('.campo-obrigatorio-radio:visible').each(function(){

				const grupo = $(this);
				const radios = grupo.find('input[type="radio"]');

				if(!radios.is(':checked')) {

					formularioValido = false;

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

			$('.campo-obrigatorio-checkbox:visible').each(function(){

				const grupo = $(this);
				const checkboxes = grupo.find('input[type="checkbox"]');

				if(!checkboxes.is(':checked')) {

					formularioValido = false;

					grupo.append(
						'<small class="erro-validacao text-danger d-block mt-1">' +
						'Selecione ao menos uma opção.' +
						'</small>'
					);

				}

			});

			/*
			|--------------------------------------------------------------------------
			| BLOQUEIA ENVIO
			|--------------------------------------------------------------------------
			*/

			if(!formularioValido) {

				e.preventDefault();

				$('html, body').animate({

					scrollTop: $('.erro-validacao:first').offset().top - 120

				}, 500);

			}

		});

	});

</script>