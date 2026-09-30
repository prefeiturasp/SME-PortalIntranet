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
let eventoValidacao

const criarTituloUnico = (descricao) => {
	const agora = new Date()
	const dataHora = agora
		.toISOString()
		.replace(/[-:]/g, '')
		.replace('T', '-')
		.slice(0, 13)

	return `${descricao} ${dataHora}-${agora.getMilliseconds()}`
}

const criarEventoValidacao = (descricao) => ({
	...gerarEventoCompletoAgendaSme(),
	titulo: criarTituloUnico(descricao),
})

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

Quando('preencho os dados obrigatórios sem o Tipo do Evento', () => {
	eventoValidacao = criarEventoValidacao('Teste sem Tipo do Evento')
	cy.preencher_titulo_agenda_sme(eventoValidacao.titulo)
	cy.preencher_data_evento_agenda_sme(eventoValidacao.dataEvento)
})

Quando('preencho os dados obrigatórios sem a Data do Evento', () => {
	eventoValidacao = criarEventoValidacao('Teste sem Data do Evento')
	cy.preencher_titulo_agenda_sme(eventoValidacao.titulo)
	cy.selecionar_tipo_evento_agenda_sme(eventoValidacao.tipoEvento)
})

Quando('preencho um período sem a Data Final', () => {
	eventoValidacao = criarEventoValidacao('Teste sem Data Final')
	cy.preencher_titulo_agenda_sme(eventoValidacao.titulo)
	cy.selecionar_tipo_evento_agenda_sme(eventoValidacao.tipoEvento)
	cy.selecionar_tipo_data_periodo_agenda_sme()
	cy.preencher_data_evento_agenda_sme(eventoValidacao.dataEvento)
})

E('tento publicar o evento da Agenda de Eventos SME', () => {
	cy.publicar_evento_agenda_sme()
})

Quando('preencho uma publicação com dois compromissos no mesmo dia', () => {
	eventoValidacao = criarEventoValidacao(
		'Teste automatizado — dois eventos —',
	)
	eventoValidacao.compromissos = [
		{ nome: 'Compromisso automatizado 1', hora: '09:00' },
		{ nome: 'Compromisso automatizado 2', hora: '14:00' },
	]

	cy.preencher_titulo_agenda_sme(eventoValidacao.titulo)
	cy.selecionar_tipo_evento_agenda_sme(eventoValidacao.tipoEvento)
	cy.selecionar_tipo_data_unica_agenda_sme()
	cy.preencher_data_evento_agenda_sme(eventoValidacao.dataEvento)

	eventoValidacao.compromissos.forEach((compromisso, indice) => {
		cy.adicionar_evento_agenda_sme()
		cy.preencher_compromisso_agenda_sme(indice, {
			tipo: 'Outros',
			nome: compromisso.nome,
			horaInicio: compromisso.hora,
		})
	})
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

Entao('a publicação deve ser bloqueada pelo Tipo do Evento', () => {
	cy.validar_publicacao_bloqueada_agenda_sme({
		titulo: eventoValidacao.titulo,
		campo: 'tipoEvento',
		mensagem: 'É necessário preencher o campo Tipo de Evento',
	})
	cy.get('#acf-field_67d9b9ce73c67').should('not.have.value', '')
})

Entao('a publicação deve ser bloqueada pela Data do Evento', () => {
	cy.validar_publicacao_bloqueada_agenda_sme({
		titulo: eventoValidacao.titulo,
		campo: 'dataEvento',
		mensagem: 'É necessário preencher o campo Data do Evento',
	})
	cy.get('#acf-field_67e546bb44fbe').should('have.value', 'sme')
})

Entao('a publicação deve ser bloqueada pela Data Final', () => {
	cy.validar_publicacao_bloqueada_agenda_sme({
		titulo: eventoValidacao.titulo,
		campo: 'dataFinal',
		mensagem: 'É necessário preencher o campo Data Final',
	})
	cy.get('#acf-field_67d9b9ce73be3').should('be.checked')
	cy.get('#acf-field_67d9b9ce73c67').should('not.have.value', '')
})

E('o evento não deve constar como publicado na Agenda de Eventos SME', () => {
	cy.validar_evento_nao_publicado_agenda_sme(eventoValidacao.titulo)
})

E('os dois compromissos devem permanecer após recarregar a página', () => {
	cy.validar_compromissos_agenda_sme(eventoValidacao.compromissos)
	cy.reload()
	cy.get('#post_status').should('have.value', 'publish')
	cy.validar_compromissos_agenda_sme(eventoValidacao.compromissos)
})

E('devo localizar a publicação com dois eventos na Agenda de Eventos SME', () => {
	cy.validar_evento_com_dois_compromissos_na_listagem_agenda_sme(
		eventoValidacao.titulo,
		eventoValidacao.dataEvento,
	)
})

E('ao abrir os detalhes devo visualizar os dois compromissos', () => {
	cy.validar_compromissos_agenda_sme(eventoValidacao.compromissos)
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
