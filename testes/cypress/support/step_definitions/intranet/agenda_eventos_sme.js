import {
	Given,
	When,
	Then,
	And,
} from 'cypress-cucumber-preprocessor/steps'
import gerarEventoAgendaSme from '../../utils/intranet/gerar_evento_agenda_sme'

const Dado = Given
const Quando = When
const Entao = Then
const E = And

const gerarEventoCompletoAgendaSme = () => ({
	...gerarEventoAgendaSme(),
	tipoEvento: 'Eventos em Coordenadorias e SME',
	compromisso: 'Outros',
	horaInicio: '09:00',
	horaFim: '10:00',
	endereco: 'Outros',
})

const preencherEventoCompletoAgendaSme = (opcoes = {}) => {
	cy.preencher_evento_agenda_sme(gerarEventoCompletoAgendaSme(), opcoes)
}

let eventoPublicado
let tituloEventoEditado

const prepararEventoPublicadoAgendaSme = () => {
	eventoPublicado = gerarEventoCompletoAgendaSme()
	tituloEventoEditado = `${eventoPublicado.titulo} editado`

	cy.realizar_login_intranet()
	cy.criar_evento_publicado_agenda_sme(eventoPublicado)
}

Dado(
	'que acesso o formulário de novo item da Agenda de Eventos SME',
	() => {
		cy.realizar_login_intranet()
		cy.ir_para_formulario_adicionar_agenda_sme()
	},
)

Dado('que possuo um evento publicado na Agenda de Eventos SME', () => {
	prepararEventoPublicadoAgendaSme()
})

E('acesso a listagem de eventos da Agenda de Eventos SME', () => {
	cy.visitar_listagem_agenda_sme()
})

Quando('adiciono um evento à Agenda de Eventos SME', () => {
	cy.adicionar_evento_agenda_sme()
})

E('seleciono o tipo de data por período', () => {
	cy.selecionar_tipo_data_periodo_agenda_sme()
})

Quando('preencho todos os campos do evento com uma data única', () => {
	preencherEventoCompletoAgendaSme()
})

Quando('preencho todos os campos do evento por período', () => {
	preencherEventoCompletoAgendaSme({ tipoData: 'periodo' })
})

Quando('pesquiso o evento pelo título', () => {
	cy.pesquisar_evento_agenda_sme(eventoPublicado.titulo)
})

Quando('edito o item publicado da Agenda de Eventos SME', () => {
	cy.editar_evento_publicado_agenda_sme(eventoPublicado.titulo)
	cy.alterar_titulo_evento_agenda_sme(tituloEventoEditado)
})

E('salvo as alterações do evento da Agenda de Eventos SME', () => {
	cy.salvar_alteracoes_evento_agenda_sme()
})

Quando('preencho o evento com servidores públicos definido como sim', () => {
	preencherEventoCompletoAgendaSme({ servidoresPublicos: true })
})

Quando('preencho o evento com servidores públicos definido como não', () => {
	preencherEventoCompletoAgendaSme({ servidoresPublicos: false })
})

Quando(
	'preencho o evento com profissionais da rede parceira definido como sim',
	() => {
		preencherEventoCompletoAgendaSme({ profissionaisRedeParceira: true })
	},
)

Quando(
	'preencho o evento com profissionais da rede parceira definido como não',
	() => {
		preencherEventoCompletoAgendaSme({ profissionaisRedeParceira: false })
	},
)

E('publico o evento da Agenda de Eventos SME', () => {
	cy.publicar_evento_agenda_sme()
})

Entao(
	'devo visualizar os campos do novo item da Agenda de Eventos SME',
	() => {
		cy.validar_campos_novo_item_agenda_sme()
	},
)

Entao('devo visualizar a confirmação de publicação do evento', () => {
	cy.validar_publicacao_evento_agenda_sme()
})

Entao('devo visualizar somente o evento pesquisado', () => {
	cy.validar_resultado_pesquisa_agenda_sme(eventoPublicado.titulo)
})

Entao('devo visualizar o evento publicado na listagem', () => {
	cy.validar_evento_publicado_na_listagem_agenda_sme(eventoPublicado.titulo)
})

Entao('devo visualizar a confirmação de atualização do evento', () => {
	cy.validar_atualizacao_evento_agenda_sme()
})

E('devo visualizar o evento atualizado na listagem', () => {
	cy.visitar_listagem_agenda_sme()
	cy.pesquisar_evento_agenda_sme(tituloEventoEditado)
	cy.validar_evento_publicado_na_listagem_agenda_sme(tituloEventoEditado)
})
