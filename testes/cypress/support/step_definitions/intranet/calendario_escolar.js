import { Given, When, Then, And } from 'cypress-cucumber-preprocessor/steps'
import {
	Adicionar_Calendario_Escolar_Localizadores,
	Lista_Calendario_Escolar_Localizadores,
} from '../../locators/calendario_escolar_locators'
import gerarDadosEvento from '../../utils/intranet/gerar_evento'

const Dado = Given
const Quando = When
const Entao = Then
const E = And

const adicionar = new Adicionar_Calendario_Escolar_Localizadores()
const lista = new Lista_Calendario_Escolar_Localizadores()
const tituloTeste = `Evento de teste ${Date.now()}`

const preencherEventoCompleto = (tipoData, visibilidade) => {
	const dados = gerarDadosEvento()

	cy.preencher_titulo_calendario_escolar(dados.titulo)
	if (tipoData === 'periodo') {
		cy.selecionar_tipo_data_periodo()
		cy.preencher_periodo_calendario_escolar(
			dados.dataEvento,
			dados.dataEventoFinal,
		)
	} else {
		cy.selecionar_tipo_data_unica()
		cy.preencher_data_unica_calendario_escolar(dados.dataEvento)
	}
	cy.adicionar_evento_do_dia_calendario_escolar()
	cy.preencher_todos_os_campos_evento_do_dia_calendario_escolar({
		compromisso: 'outros',
		nome: dados.nome,
		horaInicio: '09:00',
		horaFim: '10:00',
		descricao: dados.descricao,
		endereco: 'outros',
		enderecoManual: dados.enderecoManual,
		participantes: dados.participantes,
	})
	if (visibilidade === 'servidores-sim') {
		cy.definir_servidores_publicos_calendario_escolar(true)
	}
	if (visibilidade === 'servidores-nao') {
		cy.definir_servidores_publicos_calendario_escolar(false)
	}
	if (visibilidade === 'rede-sim') {
		cy.definir_rede_parceira_calendario_escolar(true)
	}
	if (visibilidade === 'rede-nao') {
		cy.definir_rede_parceira_calendario_escolar(false)
	}
}

Dado('eu acesso a listagem do calendário escolar no wp-admin', () => {
	cy.realizar_login_intranet()
	cy.visitar_listagem_calendario_escolar()
})

Dado('eu acesso ao formulário de novo item do calendário escolar', () => {
	cy.realizar_login_intranet()
	cy.ir_para_formulario_adicionar_calendario_escolar()
})

Dado(
	'eu acesso ao formulário de edição de um evento do calendário escolar',
	() => {
		cy.realizar_login_intranet()
		cy.visitar_listagem_calendario_escolar()
		cy.editar_primeiro_evento_do_calendario_escolar()
	},
)

Quando('eu pesquiso um evento pelo título', () => {
	cy.pesquisar_evento_no_calendario_escolar('Recreio nas Férias')
})

Quando('eu edito o primeiro evento do calendário escolar', () => {
	cy.editar_primeiro_evento_do_calendario_escolar()
})

Quando('eu visualizo o primeiro evento publicado do calendário escolar', () => {
	cy.visualizar_primeiro_evento_do_calendario_escolar()
})

Quando('eu crio um evento com data única e todos os campos preenchidos', () => {
	preencherEventoCompleto('unica')
})

Quando('eu crio um evento por período e todos os campos preenchidos', () => {
	preencherEventoCompleto('periodo')
})

Quando('eu crio um evento com servidores públicos definido como sim', () => {
	preencherEventoCompleto('unica', 'servidores-sim')
})

Quando('eu crio um evento com servidores públicos definido como não', () => {
	preencherEventoCompleto('unica', 'servidores-nao')
})

Quando(
	'eu crio um evento com profissionais da rede parceira definido como sim',
	() => {
		preencherEventoCompleto('unica', 'rede-sim')
	},
)

Quando(
	'eu crio um evento com profissionais da rede parceira definido como não',
	() => {
		preencherEventoCompleto('unica', 'rede-nao')
	},
)

Quando('eu publico o item do calendário escolar', () => {
	cy.publicar_calendario_escolar()
})

Quando('eu seleciono o tipo de data única', () => {
	cy.selecionar_tipo_data_unica()
})

Quando('eu altero o título do calendário escolar', () => {
	cy.preencher_titulo_calendario_escolar(`${tituloTeste} editado`)
})

Quando('eu salvo a alteração do calendário escolar', () => {
	cy.publicar_calendario_escolar()
})

Entao('devo visualizar o resultado da pesquisa do calendário escolar', () => {
	cy.get(lista.tabela_eventos()).should('be.visible')
})

Entao('devo visualizar o formulário de edição do calendário escolar', () => {
	cy.get(adicionar.campo_titulo()).should('be.visible')
})

Entao('devo visualizar o evento do calendário escolar no portal', () => {
	cy.url().should('not.include', '/wp-admin/')
})

Entao('devo visualizar a mensagem de sucesso do calendário escolar', () => {
	cy.validar_mensagem_sucesso_calendario_escolar()
})

Entao('devo visualizar os campos do calendário escolar', () => {
	cy.validar_campo_titulo_calendario_escolar()
	cy.get(adicionar.checkbox_tipo_periodo()).should('exist')
	cy.get(adicionar.campo_data_inicio()).should('exist')
	cy.get(adicionar.link_adicionar_evento()).should('exist')
	cy.get(adicionar.toggle_servidores_publicos()).should('exist')
	cy.get(adicionar.toggle_rede_parceira()).should('exist')
})
