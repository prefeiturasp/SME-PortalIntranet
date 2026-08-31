import {
	Adicionar_Calendario_Escolar_Localizadores,
	Lista_Calendario_Escolar_Localizadores,
} from '../locators/calendario_escolar_locators'

const adicionar_Calendario_Escolar_Localizadores =
	new Adicionar_Calendario_Escolar_Localizadores()
const lista_Calendario_Escolar_Localizadores =
	new Lista_Calendario_Escolar_Localizadores()

Cypress.Commands.add('visitar_listagem_calendario_escolar', () => {
	cy.visit('/wp-admin/edit.php?post_type=agendanew')
})

Cypress.Commands.add('ir_para_formulario_adicionar_calendario_escolar', () => {
	cy.visit('/wp-admin/post-new.php?post_type=agendanew')
})

Cypress.Commands.add('pesquisar_evento_no_calendario_escolar', (titulo) => {
	cy.get(lista_Calendario_Escolar_Localizadores.campo_busca())
		.should('be.visible')
		.clear()
		.type(titulo)
	cy.get(lista_Calendario_Escolar_Localizadores.botao_buscar()).click()
})

Cypress.Commands.add('preencher_titulo_calendario_escolar', (titulo) => {
	cy.get(adicionar_Calendario_Escolar_Localizadores.campo_titulo())
		.clear()
		.type(titulo, { force: true })
})

Cypress.Commands.add('selecionar_tipo_data_unica', () => {
	cy.get(
		adicionar_Calendario_Escolar_Localizadores.checkbox_tipo_periodo(),
	).uncheck({ force: true })
})

Cypress.Commands.add('selecionar_tipo_data_periodo', () => {
	cy.get(
		adicionar_Calendario_Escolar_Localizadores.checkbox_tipo_periodo(),
	).check({ force: true })
})

Cypress.Commands.add('preencher_data_unica_calendario_escolar', (data) => {
	cy.get(adicionar_Calendario_Escolar_Localizadores.campo_data_inicio())
		.clear()
		.type(data, { force: true })
})

Cypress.Commands.add('preencher_periodo_calendario_escolar', (inicio, fim) => {
	cy.get(adicionar_Calendario_Escolar_Localizadores.campo_data_inicio())
		.clear()
		.type(inicio, { force: true })
	cy.get(adicionar_Calendario_Escolar_Localizadores.campo_data_fim())
		.should('be.visible')
		.clear()
		.type(fim, { force: true })
})

Cypress.Commands.add('adicionar_evento_do_dia_calendario_escolar', () => {
	cy.get(
		adicionar_Calendario_Escolar_Localizadores.link_adicionar_evento(),
	).click({ force: true })
})

Cypress.Commands.add(
	'preencher_todos_os_campos_evento_do_dia_calendario_escolar',
	({
		compromisso,
		nome,
		horaInicio,
		horaFim,
		descricao,
		endereco,
		enderecoManual,
		participantes,
	}) => {
		cy.get(adicionar_Calendario_Escolar_Localizadores.select_tipo_evento())
			.first()
			.select(compromisso)
		cy.get(adicionar_Calendario_Escolar_Localizadores.campo_nome_evento())
			.first()
			.clear()
			.type(nome, { force: true })
		cy.get(adicionar_Calendario_Escolar_Localizadores.campo_hora_evento())
			.first()
			.clear()
			.type(horaInicio, { force: true })
		cy.get(adicionar_Calendario_Escolar_Localizadores.campo_fim_evento())
			.first()
			.clear()
			.type(horaFim, { force: true })
		cy.get(adicionar_Calendario_Escolar_Localizadores.campo_descricao_evento())
			.first()
			.clear()
			.type(descricao, { force: true })
		cy.get(adicionar_Calendario_Escolar_Localizadores.select_endereco_evento())
			.first()
			.select(endereco)
		cy.get(adicionar_Calendario_Escolar_Localizadores.campo_endereco_manual())
			.first()
			.clear()
			.type(enderecoManual, { force: true })
		cy.get(
			adicionar_Calendario_Escolar_Localizadores.textarea_participantes_evento(),
		)
			.first()
			.should('exist')
			.type(participantes, { force: true })
	},
)

Cypress.Commands.add('publicar_calendario_escolar', () => {
	cy.get(adicionar_Calendario_Escolar_Localizadores.botao_publicar()).click({
		force: true,
	})
})

Cypress.Commands.add(
	'definir_servidores_publicos_calendario_escolar',
	(habilitado) => {
		const toggle = cy.get(
			adicionar_Calendario_Escolar_Localizadores.toggle_servidores_publicos(),
		)
		habilitado ? toggle.check({ force: true }) : toggle.uncheck({ force: true })
	},
)

Cypress.Commands.add(
	'definir_rede_parceira_calendario_escolar',
	(habilitado) => {
		const toggle = cy.get(
			adicionar_Calendario_Escolar_Localizadores.toggle_rede_parceira(),
		)
		habilitado ? toggle.check({ force: true }) : toggle.uncheck({ force: true })
	},
)

Cypress.Commands.add('editar_primeiro_evento_do_calendario_escolar', () => {
	cy.get(lista_Calendario_Escolar_Localizadores.link_editar_primeiro())
		.should('be.visible')
		.click({ force: true })
})

Cypress.Commands.add('visualizar_primeiro_evento_do_calendario_escolar', () => {
	cy.get(lista_Calendario_Escolar_Localizadores.link_visualizar_primeiro())
		.should('be.visible')
		.invoke('removeAttr', 'target')
		.click({ force: true })
})

Cypress.Commands.add('validar_campo_titulo_calendario_escolar', () => {
	cy.get(adicionar_Calendario_Escolar_Localizadores.campo_titulo()).should(
		'be.visible',
	)
})

Cypress.Commands.add('validar_mensagem_sucesso_calendario_escolar', () => {
	cy.get(adicionar_Calendario_Escolar_Localizadores.mensagem_sucesso()).should(
		'be.visible',
	)
})
