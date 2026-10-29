import {
	Compromissos_Agenda_SME_Localizadores,
	Enderecos_Agenda_SME_Localizadores,
	Adicionar_Agenda_Eventos_SME_Localizadores,
	Lista_Agenda_Eventos_SME_Localizadores,
} from '../locators/agenda_eventos_sme_locators'

const agendaEventosSme = new Adicionar_Agenda_Eventos_SME_Localizadores()
const listaAgendaEventosSme = new Lista_Agenda_Eventos_SME_Localizadores()
const categoriasCompromissosSme = new Compromissos_Agenda_SME_Localizadores()

const obterLinhaCategoriaCompromisso = (nome) =>
	cy.get(categoriasCompromissosSme.linhas()).filter((_, linha) =>
		linha.querySelector(categoriasCompromissosSme.link_nome())?.textContent.trim() === nome,
	).should('have.length', 1)

Cypress.Commands.add('visitar_compromissos_agenda_sme', () => {
	cy.visit('/wp-admin/edit-tags.php?taxonomy=compromisso_sec&post_type=agenda')
	cy.get(categoriasCompromissosSme.titulo_pagina()).should('have.text', 'Compromissos')
})

Cypress.Commands.add('preencher_categoria_compromisso_agenda_sme', (dados, edicao = false) => {
	const campos = edicao
		? [categoriasCompromissosSme.campo_nome_edicao(), categoriasCompromissosSme.campo_slug_edicao(), categoriasCompromissosSme.campo_descricao_edicao()]
		: [categoriasCompromissosSme.campo_nome(), categoriasCompromissosSme.campo_slug(), categoriasCompromissosSme.campo_descricao()]
	campos.forEach((seletor, indice) => {
		const valor = [dados.nome, dados.slug, dados.descricao][indice]
		cy.get(seletor).should('be.visible').clear()
		if (valor) cy.get(seletor).type(valor, { parseSpecialCharSequences: false })
	})
	cy.get(edicao ? categoriasCompromissosSme.select_ascendente_edicao() : categoriasCompromissosSme.select_ascendente()).select(dados.ascendente || 'Nenhum')
})

Cypress.Commands.add('salvar_categoria_compromisso_agenda_sme', () => {
	cy.intercept('POST', '**/wp-admin/admin-ajax.php', (requisicao) => {
		const parametros = new URLSearchParams(requisicao.body)
		if (parametros.get('action') === 'add-tag') {
			requisicao.alias = 'adicionarCategoriaCompromisso'
		}
	})
	cy.get(categoriasCompromissosSme.botao_adicionar()).click()
	cy.wait('@adicionarCategoriaCompromisso').its('response.statusCode').should('eq', 200)
})

Cypress.Commands.add('criar_categoria_compromisso_agenda_sme', (dados) => {
	cy.visitar_compromissos_agenda_sme()
	cy.preencher_categoria_compromisso_agenda_sme(dados)
	cy.salvar_categoria_compromisso_agenda_sme()
	obterLinhaCategoriaCompromisso(dados.nome).should('be.visible')
})

Cypress.Commands.add('pesquisar_categoria_compromisso_agenda_sme', (nome, deveExistir = true) => {
	cy.get(categoriasCompromissosSme.campo_busca()).clear().type(nome)
	cy.get(categoriasCompromissosSme.botao_buscar()).click()
	cy.get(categoriasCompromissosSme.campo_busca()).should('have.value', nome)
	if (deveExistir) obterLinhaCategoriaCompromisso(nome).should('be.visible')
	else cy.get(categoriasCompromissosSme.linhas()).find(categoriasCompromissosSme.link_nome()).should('not.exist')
})

Cypress.Commands.add('validar_pesquisa_categoria_compromisso_agenda_sme', (dados) => {
	cy.get(categoriasCompromissosSme.linhas()).find(categoriasCompromissosSme.link_nome()).should('have.length', 1).and('have.text', dados.nome)
	cy.validar_categoria_compromisso_agenda_sme(dados)
})

Cypress.Commands.add('validar_categoria_compromisso_agenda_sme', (dados) => {
	cy.get(categoriasCompromissosSme.cabecalhos()).within(() => {
		const colunas = ['Nome', 'Descrição', 'Slug', 'Contagem']
		colunas.forEach((coluna) => cy.contains('th', coluna).should('be.visible'))
	})
	obterLinhaCategoriaCompromisso(dados.nome).within(() => {
		cy.get(categoriasCompromissosSme.coluna_nome()).find(categoriasCompromissosSme.link_nome()).should('have.text', dados.nome)
		cy.get(categoriasCompromissosSme.coluna_descricao()).should(($celula) => {
			const conteudoVisivel = $celula.clone()
			conteudoVisivel.find(categoriasCompromissosSme.texto_acessivel_descricao()).remove()
			expect(conteudoVisivel.text().trim(), 'descrição visível').to.eq(dados.descricao || '—')
			if (!dados.descricao) {
				expect(
					$celula.find(categoriasCompromissosSme.texto_acessivel_descricao()).text().trim(),
					'descrição para leitores de tela',
				).to.eq('Sem descrição')
			}
		})
		cy.get(categoriasCompromissosSme.coluna_slug()).should('have.text', dados.slug)
		cy.get(categoriasCompromissosSme.coluna_contagem()).should(($celula) => {
			expect($celula.text().trim()).to.eq(dados.contagem)
		})
	})
})

Cypress.Commands.add('abrir_edicao_categoria_compromisso_agenda_sme', (dados) => {
	cy.pesquisar_categoria_compromisso_agenda_sme(dados.nome)
	obterLinhaCategoriaCompromisso(dados.nome).find(categoriasCompromissosSme.link_nome()).click()
	cy.get(categoriasCompromissosSme.campo_nome_edicao()).should('have.value', dados.nome)
	cy.get(categoriasCompromissosSme.campo_slug_edicao()).should('have.value', dados.slug)
	cy.get(categoriasCompromissosSme.campo_descricao_edicao()).should('have.value', dados.descricao)
	cy.get(categoriasCompromissosSme.select_ascendente_edicao()).find('option:selected').should('have.text', dados.ascendente)
})

Cypress.Commands.add('atualizar_categoria_compromisso_agenda_sme', (dados) => {
	cy.preencher_categoria_compromisso_agenda_sme(dados, true)
	cy.intercept('POST', '**/wp-admin/edit-tags.php').as('atualizarCategoriaCompromisso')
	cy.get(categoriasCompromissosSme.botao_atualizar()).click()
	cy.wait('@atualizarCategoriaCompromisso').its('response.statusCode').should('eq', 302)
	cy.visitar_compromissos_agenda_sme()
	cy.reload()
	cy.abrir_edicao_categoria_compromisso_agenda_sme(dados)
})

Cypress.Commands.add('validar_nome_obrigatorio_compromisso_agenda_sme', (mensagem) => {
	cy.get(categoriasCompromissosSme.mensagem_erro()).should('be.visible').and('contain.text', mensagem)
	cy.get(categoriasCompromissosSme.campo_nome()).should('have.value', '')
})

const validarElementosVisiveis = (seletores) => {
	seletores.forEach((seletor) => {
		cy.get(seletor).should('be.visible')
	})
}

const preencherCampo = (seletor, valor, opcoes = {}) => {
	cy.get(seletor)
		.first()
		.should('exist')
		.clear(opcoes)
		.type(valor, opcoes)
}

const selecionarDataNoDatepicker = (seletor, valor) => {
	const [dia, mes, ano] = valor.split('/')
	const indiceMes = String(Number(mes) - 1)
	const diaSemZero = String(Number(dia))

	cy.get(seletor).first().should('be.visible').click({ force: true })
	cy.get('#ui-datepicker-div').should('be.visible')
	cy.get('#ui-datepicker-div .ui-datepicker-month').select(indiceMes)
	cy.get('#ui-datepicker-div .ui-datepicker-year').select(ano)
	cy.get(
		`#ui-datepicker-div td[data-month="${indiceMes}"][data-year="${ano}"] a[data-date="${diaSemZero}"]`,
	).click({ force: true })
	cy.get(seletor).first().should('have.value', valor)
}

const selecionarHorarioNoTimepicker = (indiceLinha, seletor, valor) => {
	const [hora, minuto] = valor.split(':')

	cy.get(agendaEventosSme.linhas_eventos())
		.eq(indiceLinha)
		.find(seletor)
		.should('be.visible')
		.click({ force: true })
	cy.get('#ui-datepicker-div').should('be.visible')
	cy.get('#ui-datepicker-div .ui_tpicker_hour select').select(
		String(Number(hora)),
	)
	cy.get('#ui-datepicker-div .ui_tpicker_minute select').select(
		String(Number(minuto)),
	)
	cy.get('#ui-datepicker-div .ui-datepicker-close').click({ force: true })
	cy.get(agendaEventosSme.linhas_eventos())
		.eq(indiceLinha)
		.find(seletor)
		.should('have.value', `${hora.padStart(2, '0')}:${minuto.padStart(2, '0')}`)
}

const definirToggle = (seletor, habilitado) => {
	const toggle = cy.get(seletor).should('exist')

	if (habilitado) {
		toggle.check({ force: true }).should('be.checked')
		return
	}

	toggle.uncheck({ force: true }).should('not.be.checked')
}

const normalizarTitulo = (titulo) =>
	titulo.replace(/[–—]/g, '-').replace(/\s+/g, ' ').trim()

const obterLinhaEventoPorTitulo = (titulo) =>
	cy
		.get(`${listaAgendaEventosSme.linhas_eventos()} .row-title`)
		.then(($titulos) => {
			const tituloEsperado = normalizarTitulo(titulo)
			const elementoEncontrado = [...$titulos].find(
				(elemento) =>
					normalizarTitulo(elemento.textContent) === tituloEsperado,
			)

			expect(elementoEncontrado, `evento ${titulo}`).to.exist

			return cy.wrap(elementoEncontrado).closest('tr')
		})

Cypress.Commands.add('visitar_listagem_agenda_sme', () => {
	cy.visit('/wp-admin/edit.php?post_type=agenda')
})

Cypress.Commands.add('ir_para_formulario_adicionar_agenda_sme', () => {
	cy.visit('/wp-admin/post-new.php?post_type=agenda')
})

Cypress.Commands.add('adicionar_evento_agenda_sme', () => {
	cy.get(agendaEventosSme.campo_eventos_do_dia()).then(($campoEventos) => {
		const quantidadeAtual = $campoEventos.find(
			'tr.acf-row:not(.acf-clone)',
		).length

		cy.get(agendaEventosSme.botao_adicionar_evento())
			.should('be.visible')
			.click()
		cy.get(agendaEventosSme.linhas_eventos())
			.should('have.length', quantidadeAtual + 1)
			.and('be.visible')
	})
})

Cypress.Commands.add('selecionar_tipo_data_periodo_agenda_sme', () => {
	cy.get(agendaEventosSme.checkbox_tipo_de_data()).check({ force: true })
})

Cypress.Commands.add('selecionar_tipo_data_unica_agenda_sme', () => {
	cy.get(agendaEventosSme.checkbox_tipo_de_data()).uncheck({ force: true })
})

Cypress.Commands.add('preencher_periodo_agenda_sme', (dataInicial, dataFinal) => {
	cy.selecionar_tipo_data_periodo_agenda_sme()
	selecionarDataNoDatepicker(
		agendaEventosSme.campo_data_do_evento(),
		dataInicial,
	)
	selecionarDataNoDatepicker(agendaEventosSme.campo_data_final(), dataFinal)
})

Cypress.Commands.add('preencher_titulo_agenda_sme', (titulo) => {
	preencherCampo(agendaEventosSme.campo_titulo(), titulo)
})

Cypress.Commands.add('selecionar_tipo_evento_agenda_sme', (tipoEvento) => {
	cy.get(agendaEventosSme.select_tipo_de_evento())
		.should('be.visible')
		.select(tipoEvento)
})

Cypress.Commands.add('preencher_data_evento_agenda_sme', (dataEvento) => {
	selecionarDataNoDatepicker(
		agendaEventosSme.campo_data_do_evento(),
		dataEvento,
	)
	cy.get(agendaEventosSme.campo_data_do_evento_interno()).should(
		'not.have.value',
		'',
	)
})

Cypress.Commands.add(
	'preencher_compromisso_agenda_sme',
	(indiceLinha, compromisso) => {
		const {
			tipo,
			nome,
			horaInicio,
			horaFim,
			descricao,
			endereco,
			enderecoManual,
			participantes,
		} = compromisso

		if (tipo) {
			cy.get(agendaEventosSme.linhas_eventos())
				.eq(indiceLinha)
				.find(agendaEventosSme.select_compromisso_na_linha())
				.select(tipo)
		}

		if (nome) {
			cy.get(agendaEventosSme.linhas_eventos())
				.eq(indiceLinha)
				.find(agendaEventosSme.campo_nome_compromisso_na_linha())
				.clear({ force: true })
				.type(nome, { force: true })
		}

		if (horaInicio) {
			selecionarHorarioNoTimepicker(
				indiceLinha,
				agendaEventosSme.campo_hora_evento_na_linha(),
				horaInicio,
			)
		}

		if (horaFim) {
			selecionarHorarioNoTimepicker(
				indiceLinha,
				agendaEventosSme.campo_fim_evento_na_linha(),
				horaFim,
			)
		}

		if (descricao) {
			cy.get(agendaEventosSme.linhas_eventos())
				.eq(indiceLinha)
				.find(agendaEventosSme.campo_descricao_evento_na_linha())
				.clear({ force: true })
				.type(descricao, { force: true })
		}

		if (endereco) {
			cy.get(agendaEventosSme.linhas_eventos())
				.eq(indiceLinha)
				.find(agendaEventosSme.select_endereco_evento_na_linha())
				.select(endereco)
		}

		if (enderecoManual) {
			cy.get(agendaEventosSme.linhas_eventos())
				.eq(indiceLinha)
				.find(agendaEventosSme.campo_endereco_manual_na_linha())
				.clear({ force: true })
				.type(enderecoManual, { force: true })
		}

		if (participantes) {
			cy.get(agendaEventosSme.linhas_eventos())
				.eq(indiceLinha)
				.find(agendaEventosSme.textarea_participantes_evento_na_linha())
				.should('exist')
				.type(participantes, { force: true })
		}
	},
)

Cypress.Commands.add('definir_servidores_publicos_agenda_sme', (habilitado) => {
	definirToggle(agendaEventosSme.toggle_servidores_publicos(), habilitado)
})

Cypress.Commands.add(
	'definir_profissionais_rede_parceira_agenda_sme',
	(habilitado) => {
		definirToggle(
			agendaEventosSme.toggle_profissionais_rede_parceira(),
			habilitado,
		)
	},
)

Cypress.Commands.add('preencher_evento_agenda_sme', (evento, opcoes = {}) => {
	const {
		titulo,
		tipoEvento,
		dataEvento,
		dataEventoFinal,
		compromisso,
		nome,
		horaInicio,
		horaFim,
		descricao,
		endereco,
		enderecoManual,
		participantes,
	} = evento
	const {
		tipoData = 'unica',
		servidoresPublicos,
		profissionaisRedeParceira,
	} = opcoes

	cy.preencher_titulo_agenda_sme(titulo)
	cy.selecionar_tipo_evento_agenda_sme(tipoEvento)

	if (tipoData === 'periodo') {
		cy.preencher_periodo_agenda_sme(dataEvento, dataEventoFinal)
	} else {
		cy.selecionar_tipo_data_unica_agenda_sme()
		cy.preencher_data_evento_agenda_sme(dataEvento)
	}

	cy.adicionar_evento_agenda_sme()
	cy.preencher_compromisso_agenda_sme(0, {
		tipo: compromisso,
		nome,
		horaInicio,
		horaFim,
		descricao,
		endereco,
		enderecoManual,
		participantes,
	})

	if (typeof servidoresPublicos === 'boolean') {
		cy.definir_servidores_publicos_agenda_sme(servidoresPublicos)
	}

	if (typeof profissionaisRedeParceira === 'boolean') {
		cy.definir_profissionais_rede_parceira_agenda_sme(
			profissionaisRedeParceira,
		)
	}
})

Cypress.Commands.add('publicar_evento_agenda_sme', () => {
	cy.get(agendaEventosSme.link_permanente(), { timeout: 30000 })
		.should('have.attr', 'href')
		.and('not.be.empty')
	cy.get('#publishing-action .spinner').should('not.have.class', 'is-active')
	cy.get(agendaEventosSme.botao_publicar())
		.should('be.visible')
		.and('not.be.disabled')
		.scrollIntoView()
		.click()
	cy.wait(1500)
	cy.get('body').then(($body) => {
		const possuiResultado =
			$body.find(agendaEventosSme.mensagem_sucesso()).length > 0 ||
			$body.find('.acf-notice.-error.acf-error-message').length > 0 ||
			$body.find('#post_status').val() === 'publish'

		if (!possuiResultado) {
			cy.get('#publishing-action .spinner').should(
				'not.have.class',
				'is-active',
			)
			cy.get(agendaEventosSme.botao_publicar())
				.should('be.visible')
				.and('not.be.disabled')
				.click()
		}
	})
})

Cypress.Commands.add('criar_evento_publicado_agenda_sme', (evento) => {
	cy.ir_para_formulario_adicionar_agenda_sme()
	cy.preencher_evento_agenda_sme(evento)
	cy.publicar_evento_agenda_sme()
	cy.validar_publicacao_evento_agenda_sme()
})

Cypress.Commands.add('pesquisar_evento_agenda_sme', (titulo) => {
	preencherCampo(listaAgendaEventosSme.campo_busca(), titulo)
	cy.get(listaAgendaEventosSme.botao_buscar()).should('be.visible').click()
})

Cypress.Commands.add('validar_resultado_pesquisa_agenda_sme', (titulo) => {
	cy.get(listaAgendaEventosSme.linhas_eventos()).should('have.length', 1)
	obterLinhaEventoPorTitulo(titulo).should('have.class', 'status-publish')
})

Cypress.Commands.add('validar_evento_publicado_na_listagem_agenda_sme', (titulo) => {
	obterLinhaEventoPorTitulo(titulo)
		.should('be.visible')
		.and('have.class', 'status-publish')
})

Cypress.Commands.add('editar_evento_publicado_agenda_sme', (titulo) => {
	cy.pesquisar_evento_agenda_sme(titulo)
	obterLinhaEventoPorTitulo(titulo)
		.find('.row-actions .edit a')
		.should('exist')
		.click({ force: true })
})

Cypress.Commands.add('alterar_titulo_evento_agenda_sme', (titulo) => {
	preencherCampo(agendaEventosSme.campo_titulo(), titulo)
})

Cypress.Commands.add('salvar_alteracoes_evento_agenda_sme', () => {
	cy.get(agendaEventosSme.botao_publicar())
		.should('be.visible')
		.click({ force: true })
})

Cypress.Commands.add('validar_atualizacao_evento_agenda_sme', () => {
	cy.get(agendaEventosSme.mensagem_sucesso())
		.should('be.visible')
		.invoke('text')
		.should('match', /atualizado/i)
})

Cypress.Commands.add('validar_publicacao_evento_agenda_sme', () => {
	cy.get(agendaEventosSme.mensagem_sucesso())
		.should('be.visible')
		.invoke('text')
		.should('match', /publicado/i)
})

Cypress.Commands.add(
	'validar_publicacao_bloqueada_agenda_sme',
	({ titulo, campo, mensagem }) => {
		const containers = {
			tipoEvento: agendaEventosSme.container_tipo_de_evento(),
			dataEvento: agendaEventosSme.container_data_do_evento(),
			dataFinal: agendaEventosSme.container_data_final(),
		}

		cy.url().should('include', '/wp-admin/post-new.php?post_type=agenda')
		cy.get(agendaEventosSme.mensagem_sucesso()).should('not.exist')
		cy.get('#post_status').should('have.value', 'draft')
		cy.get(agendaEventosSme.campo_titulo()).should('have.value', titulo)
		cy.contains(
			'.acf-notice.-error.acf-error-message',
			'Falha na validação',
		).should('be.visible')
		cy.get(containers[campo])
			.find(agendaEventosSme.mensagem_obrigatoriedade())
			.should('be.visible')
			.and('contain', mensagem)
	},
)

Cypress.Commands.add('validar_evento_nao_publicado_agenda_sme', (titulo) => {
	cy.visitar_listagem_agenda_sme()
	cy.pesquisar_evento_agenda_sme(titulo)
	cy.get(listaAgendaEventosSme.linhas_eventos()).should(($linhas) => {
		const eventoPublicado = [...$linhas].some((linha) => {
			const tituloLinha = linha.querySelector('.row-title')?.textContent

			return (
				linha.classList.contains('status-publish') &&
				tituloLinha &&
				normalizarTitulo(tituloLinha) === normalizarTitulo(titulo)
			)
		})

		expect(eventoPublicado, 'evento publicado na listagem').to.be.false
	})
})

Cypress.Commands.add(
	'validar_compromissos_agenda_sme',
	(compromissosEsperados) => {
		cy.get(agendaEventosSme.linhas_eventos()).should(($linhas) => {
			expect($linhas, 'quantidade de compromissos').to.have.length(
				compromissosEsperados.length,
			)

			const compromissosEncontrados = [...$linhas].map((linha) => ({
				nome: linha.querySelector(
					agendaEventosSme.campo_nome_compromisso_na_linha(),
				)?.value,
				hora: linha.querySelector(
					agendaEventosSme.campo_hora_evento_na_linha(),
				)?.value,
			}))

			compromissosEsperados.forEach((compromisso) => {
				expect(compromissosEncontrados).to.deep.include(compromisso)
			})
		})
	},
)

Cypress.Commands.add(
	'validar_evento_com_dois_compromissos_na_listagem_agenda_sme',
	(titulo, dataEvento) => {
		cy.visitar_listagem_agenda_sme()
		cy.pesquisar_evento_agenda_sme(titulo)
		obterLinhaEventoPorTitulo(titulo).within(() => {
			cy.get('.column-data_evento').should('contain', dataEvento)
			cy.get('.column-qtd_evento').should('contain', '2 eventos')
		})
		obterLinhaEventoPorTitulo(titulo)
			.should('have.class', 'status-publish')
			.find('.row-title')
			.click({ force: true })
	},
)

Cypress.Commands.add('validar_campos_novo_item_agenda_sme', () => {
	cy.get(agendaEventosSme.painel_agenda_eventos_sme()).should('be.visible')
	cy.get(agendaEventosSme.checkbox_tipo_de_data())
		.should('exist')
		.and('be.checked')

	validarElementosVisiveis([
		agendaEventosSme.campo_titulo(),
		agendaEventosSme.select_tipo_de_evento(),
		agendaEventosSme.campo_data_do_evento(),
		agendaEventosSme.campo_data_final(),
		agendaEventosSme.botao_adicionar_evento(),
		agendaEventosSme.select_compromisso(),
		agendaEventosSme.campo_nome_compromisso(),
		agendaEventosSme.campo_hora_evento(),
		agendaEventosSme.campo_fim_evento(),
		agendaEventosSme.campo_descricao_evento(),
		agendaEventosSme.select_endereco_evento(),
		agendaEventosSme.campo_endereco_manual(),
		agendaEventosSme.botao_adicionar_midia_participantes(),
		agendaEventosSme.iframe_participantes_evento(),
		agendaEventosSme.botao_definir_imagem_destacada(),
		agendaEventosSme.botao_salvar_rascunho(),
		agendaEventosSme.botao_visualizar(),
		agendaEventosSme.botao_publicar(),
	])

	cy.get(agendaEventosSme.textarea_participantes_evento()).should('exist')
})

const categoriasEnderecosSme = new Enderecos_Agenda_SME_Localizadores()

const obterLinhaCategoriaEndereco = (nome) =>
	cy.get(categoriasEnderecosSme.linhas()).filter((_, linha) =>
		linha.querySelector(categoriasEnderecosSme.link_nome())?.textContent.trim() === nome,
	).should('have.length', 1)

Cypress.Commands.add('visitar_enderecos_agenda_sme', () => {
	cy.visit('/wp-admin/edit-tags.php?taxonomy=endereco_sec&post_type=agenda')
	cy.get(categoriasEnderecosSme.titulo_pagina()).should('have.text', 'Endereços')
})

Cypress.Commands.add('preencher_categoria_endereco_agenda_sme', (dados, edicao = false) => {
	const campos = edicao
		? [categoriasEnderecosSme.campo_nome_edicao(), categoriasEnderecosSme.campo_slug_edicao(), categoriasEnderecosSme.campo_descricao_edicao()]
		: [categoriasEnderecosSme.campo_nome(), categoriasEnderecosSme.campo_slug(), categoriasEnderecosSme.campo_descricao()]
	campos.forEach((seletor, indice) => {
		const valor = [dados.nome, dados.slug, dados.descricao][indice]
		cy.get(seletor).should('be.visible').clear()
		if (valor) cy.get(seletor).type(valor, { parseSpecialCharSequences: false })
	})
	cy.get(edicao ? categoriasEnderecosSme.select_ascendente_edicao() : categoriasEnderecosSme.select_ascendente()).select(dados.ascendente || 'Nenhum')
})

Cypress.Commands.add('salvar_categoria_endereco_agenda_sme', () => {
	cy.intercept('POST', '**/wp-admin/admin-ajax.php', (requisicao) => {
		const parametros = new URLSearchParams(requisicao.body)
		if (parametros.get('action') === 'add-tag') {
			requisicao.alias = 'adicionarCategoriaEndereco'
		}
	})
	cy.get(categoriasEnderecosSme.botao_adicionar()).click()
	cy.wait('@adicionarCategoriaEndereco').its('response.statusCode').should('eq', 200)
})

Cypress.Commands.add('criar_categoria_endereco_agenda_sme', (dados) => {
	cy.visitar_enderecos_agenda_sme()
	cy.preencher_categoria_endereco_agenda_sme(dados)
	cy.salvar_categoria_endereco_agenda_sme()
	obterLinhaCategoriaEndereco(dados.nome).should('be.visible')
})

Cypress.Commands.add('pesquisar_categoria_endereco_agenda_sme', (nome, deveExistir = true) => {
	cy.get(categoriasEnderecosSme.campo_busca()).clear().type(nome)
	cy.get(categoriasEnderecosSme.botao_buscar()).click()
	cy.get(categoriasEnderecosSme.campo_busca()).should('have.value', nome)
	if (deveExistir) obterLinhaCategoriaEndereco(nome).should('be.visible')
	else cy.get(`${categoriasEnderecosSme.linhas()} ${categoriasEnderecosSme.link_nome()}`).should('not.exist')
})

Cypress.Commands.add('validar_pesquisa_categoria_endereco_agenda_sme', (dados) => {
	cy.get(categoriasEnderecosSme.linhas()).find(categoriasEnderecosSme.link_nome()).should('have.length', 1).and('have.text', dados.nome)
	cy.validar_categoria_endereco_agenda_sme(dados)
})

Cypress.Commands.add('validar_categoria_endereco_agenda_sme', (dados) => {
	cy.get(categoriasEnderecosSme.cabecalhos()).within(() => {
		const colunas = ['Nome', 'Descrição', 'Slug', 'Contagem']
		colunas.forEach((coluna) => cy.contains('th', coluna).should('be.visible'))
	})
	obterLinhaCategoriaEndereco(dados.nome).within(() => {
		cy.get(categoriasEnderecosSme.coluna_nome()).find(categoriasEnderecosSme.link_nome()).should('have.text', dados.nome)
		cy.get(categoriasEnderecosSme.coluna_descricao()).should(($celula) => {
			const conteudoVisivel = $celula.clone()
			conteudoVisivel.find(categoriasEnderecosSme.texto_acessivel_descricao()).remove()
			expect(conteudoVisivel.text().trim(), 'descrição visível').to.eq(dados.descricao || '—')
			if (!dados.descricao) {
				expect(
					$celula.find(categoriasEnderecosSme.texto_acessivel_descricao()).text().trim(),
					'descrição para leitores de tela',
				).to.eq('Sem descrição')
			}
		})
		cy.get(categoriasEnderecosSme.coluna_slug()).should('have.text', dados.slug)
		cy.get(categoriasEnderecosSme.coluna_contagem()).should(($celula) => {
			expect($celula.text().trim()).to.eq(dados.contagem)
		})
	})
})

Cypress.Commands.add('abrir_edicao_categoria_endereco_agenda_sme', (dados) => {
	cy.pesquisar_categoria_endereco_agenda_sme(dados.nome)
	obterLinhaCategoriaEndereco(dados.nome).find(categoriasEnderecosSme.link_nome()).click()
	cy.get(categoriasEnderecosSme.campo_nome_edicao()).should('have.value', dados.nome)
	cy.get(categoriasEnderecosSme.campo_slug_edicao()).should('have.value', dados.slug)
	cy.get(categoriasEnderecosSme.campo_descricao_edicao()).should('have.value', dados.descricao)
	cy.get(categoriasEnderecosSme.select_ascendente_edicao()).find('option:selected').should('have.text', dados.ascendente)
})

Cypress.Commands.add('atualizar_categoria_endereco_agenda_sme', (dados) => {
	cy.preencher_categoria_endereco_agenda_sme(dados, true)
	cy.intercept('POST', '**/wp-admin/edit-tags.php').as('atualizarCategoriaEndereco')
	cy.get(categoriasEnderecosSme.botao_atualizar()).click()
	cy.wait('@atualizarCategoriaEndereco').its('response.statusCode').should('eq', 302)
	cy.visitar_enderecos_agenda_sme()
	cy.reload()
	cy.abrir_edicao_categoria_endereco_agenda_sme(dados)
})

Cypress.Commands.add('validar_nome_obrigatorio_endereco_agenda_sme', (mensagem) => {
	cy.get(categoriasEnderecosSme.mensagem_erro()).should('be.visible').and('contain.text', mensagem)
	cy.get(categoriasEnderecosSme.campo_nome()).should('have.value', '')
})
