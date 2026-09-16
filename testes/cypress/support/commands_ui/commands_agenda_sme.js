import {
	Adicionar_Agenda_Eventos_SME_Localizadores,
	Lista_Agenda_Eventos_SME_Localizadores,
} from '../locators/agenda_eventos_sme_locators'

const agendaEventosSme = new Adicionar_Agenda_Eventos_SME_Localizadores()
const listaAgendaEventosSme = new Lista_Agenda_Eventos_SME_Localizadores()

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

const definirToggle = (seletor, habilitado) => {
	const toggle = cy.get(seletor).should('exist')

	if (habilitado) {
		toggle.check({ force: true }).should('be.checked')
		return
	}

	toggle.uncheck({ force: true }).should('not.be.checked')
}

const obterLinhaEventoPorTitulo = (titulo) =>
	cy
		.contains(`${listaAgendaEventosSme.linhas_eventos()} .row-title`, titulo)
		.should('have.text', titulo)
		.closest('tr')

Cypress.Commands.add('visitar_listagem_agenda_sme', () => {
	cy.visit('/wp-admin/edit.php?post_type=agenda')
})

Cypress.Commands.add('ir_para_formulario_adicionar_agenda_sme', () => {
	cy.visit('/wp-admin/post-new.php?post_type=agenda')
})

Cypress.Commands.add('adicionar_evento_agenda_sme', () => {
	cy.get(agendaEventosSme.botao_adicionar_evento())
		.should('be.visible')
		.click()

	cy.get(agendaEventosSme.linhas_eventos())
		.should('have.length', 1)
		.and('be.visible')
})

Cypress.Commands.add('selecionar_tipo_data_periodo_agenda_sme', () => {
	cy.get(agendaEventosSme.checkbox_tipo_de_data()).check({ force: true })
})

Cypress.Commands.add('selecionar_tipo_data_unica_agenda_sme', () => {
	cy.get(agendaEventosSme.checkbox_tipo_de_data()).uncheck({ force: true })
})

Cypress.Commands.add('preencher_periodo_agenda_sme', (dataInicial, dataFinal) => {
	cy.selecionar_tipo_data_periodo_agenda_sme()
	preencherCampo(agendaEventosSme.campo_data_do_evento(), dataInicial, {
		force: true,
	})
	preencherCampo(agendaEventosSme.campo_data_final(), dataFinal, {
		force: true,
	})
})

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

	preencherCampo(agendaEventosSme.campo_titulo(), titulo)
	cy.get(agendaEventosSme.select_tipo_de_evento()).select(tipoEvento)

	if (tipoData === 'periodo') {
		cy.preencher_periodo_agenda_sme(dataEvento, dataEventoFinal)
	} else {
		cy.selecionar_tipo_data_unica_agenda_sme()
		preencherCampo(agendaEventosSme.campo_data_do_evento(), dataEvento, {
			force: true,
		})
	}

	cy.adicionar_evento_agenda_sme()
	cy.get(agendaEventosSme.select_compromisso()).first().select(compromisso)
	preencherCampo(agendaEventosSme.campo_nome_compromisso(), nome, {
		force: true,
	})
	preencherCampo(agendaEventosSme.campo_hora_evento(), horaInicio, {
		force: true,
	})
	preencherCampo(agendaEventosSme.campo_fim_evento(), horaFim, {
		force: true,
	})
	preencherCampo(agendaEventosSme.campo_descricao_evento(), descricao, {
		force: true,
	})
	cy.get(agendaEventosSme.select_endereco_evento()).first().select(endereco)
	preencherCampo(agendaEventosSme.campo_endereco_manual(), enderecoManual, {
		force: true,
	})
	preencherCampo(
		agendaEventosSme.textarea_participantes_evento(),
		participantes,
		{ force: true },
	)

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
	cy.get(agendaEventosSme.botao_publicar())
		.should('be.visible')
		.click({ force: true })
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
