import {
	Given,
	When,
	Then,
	And,
} from 'cypress-cucumber-preprocessor/steps'
import gerarEventoAgendaSme, {
	gerarCompromissoAgendaSme,
	gerarEnderecoAgendaSme,
} from '../../utils/intranet/gerar_evento_agenda_sme'

const Dado = Given
const Quando = When
const Entao = Then
const E = And

let categoriaCompromisso
let categoriaCompromissoEditada

Dado('que acesso Compromissos da Agenda de Eventos SME', () => {
	cy.realizar_login_intranet()
	cy.visitar_compromissos_agenda_sme()
})

Dado('que possuo um compromisso cadastrado com descrição na Agenda de Eventos SME', () => {
	categoriaCompromisso = gerarCompromissoAgendaSme()
	cy.realizar_login_intranet()
	cy.criar_categoria_compromisso_agenda_sme(categoriaCompromisso)
})

Dado('que possuo um compromisso cadastrado sem descrição na Agenda de Eventos SME', () => {
	categoriaCompromisso = gerarCompromissoAgendaSme({ descricao: '' })
	cy.realizar_login_intranet()
	cy.criar_categoria_compromisso_agenda_sme(categoriaCompromisso)
})

Quando('cadastro um compromisso preenchendo apenas o nome', () => {
	categoriaCompromisso = gerarCompromissoAgendaSme({ descricao: '' })
	cy.preencher_categoria_compromisso_agenda_sme({ nome: categoriaCompromisso.nome })
	cy.salvar_categoria_compromisso_agenda_sme()
})

Quando('cadastro um compromisso preenchendo todos os campos', () => {
	const ascendente = gerarCompromissoAgendaSme()
	cy.criar_categoria_compromisso_agenda_sme(ascendente)
	categoriaCompromisso = gerarCompromissoAgendaSme({ ascendente: ascendente.nome })
	cy.preencher_categoria_compromisso_agenda_sme(categoriaCompromisso)
	cy.salvar_categoria_compromisso_agenda_sme()
})

Quando('edito nome, slug e descrição do compromisso cadastrado', () => {
	cy.abrir_edicao_categoria_compromisso_agenda_sme(categoriaCompromisso)
	categoriaCompromissoEditada = gerarCompromissoAgendaSme()
	cy.atualizar_categoria_compromisso_agenda_sme(categoriaCompromissoEditada)
	categoriaCompromisso = categoriaCompromissoEditada
})

Quando('tento cadastrar um compromisso sem preencher o nome', () => {
	categoriaCompromisso = gerarCompromissoAgendaSme({ nome: '' })
	cy.preencher_categoria_compromisso_agenda_sme(categoriaCompromisso)
	cy.salvar_categoria_compromisso_agenda_sme()
})

Quando('pesquiso pelo nome do compromisso cadastrado', () => {
	cy.pesquisar_categoria_compromisso_agenda_sme(categoriaCompromisso.nome)
})

Entao('devo visualizar nome, descrição, slug e contagem do compromisso', () => {
	cy.visitar_compromissos_agenda_sme()
	cy.pesquisar_categoria_compromisso_agenda_sme(categoriaCompromisso.nome)
	cy.validar_categoria_compromisso_agenda_sme(categoriaCompromisso)
})

E('a descrição vazia do compromisso deve ser representada por um traço', () => {
	expect(categoriaCompromisso.descricao).to.eq('')
	cy.validar_categoria_compromisso_agenda_sme(categoriaCompromisso)
})

E('os campos cadastrados do compromisso devem permanecer na edição', () => {
	cy.abrir_edicao_categoria_compromisso_agenda_sme(categoriaCompromisso)
	cy.visitar_compromissos_agenda_sme()
	cy.abrir_edicao_categoria_compromisso_agenda_sme(categoriaCompromisso)
})

Entao('devo visualizar a mensagem de nome obrigatório do compromisso {string}', (mensagem) => {
	cy.validar_nome_obrigatorio_compromisso_agenda_sme(mensagem)
})

E('o compromisso sem nome não deve ser cadastrado', () => {
	cy.reload()
	cy.pesquisar_categoria_compromisso_agenda_sme(categoriaCompromisso.slug, false)
})

Entao('devo visualizar somente o compromisso pesquisado', () => {
	cy.validar_pesquisa_categoria_compromisso_agenda_sme(categoriaCompromisso)
})

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

let categoriaEndereco
let categoriaEnderecoEditada

Dado('que acesso Endereços da Agenda de Eventos SME', () => {
	cy.realizar_login_intranet()
	cy.visitar_enderecos_agenda_sme()
})

Dado('que possuo um endereço cadastrado com descrição na Agenda de Eventos SME', () => {
	categoriaEndereco = gerarEnderecoAgendaSme()
	cy.realizar_login_intranet()
	cy.criar_categoria_endereco_agenda_sme(categoriaEndereco)
})

Dado('que possuo um endereço cadastrado sem descrição na Agenda de Eventos SME', () => {
	categoriaEndereco = gerarEnderecoAgendaSme({ descricao: '' })
	cy.realizar_login_intranet()
	cy.criar_categoria_endereco_agenda_sme(categoriaEndereco)
})

Quando('cadastro um endereço preenchendo apenas o nome', () => {
	categoriaEndereco = gerarEnderecoAgendaSme({ descricao: '' })
	cy.preencher_categoria_endereco_agenda_sme({ nome: categoriaEndereco.nome })
	cy.salvar_categoria_endereco_agenda_sme()
})

Quando('cadastro um endereço preenchendo todos os campos', () => {
	const ascendente = gerarEnderecoAgendaSme()
	cy.criar_categoria_endereco_agenda_sme(ascendente)
	categoriaEndereco = gerarEnderecoAgendaSme({ ascendente: ascendente.nome })
	cy.preencher_categoria_endereco_agenda_sme(categoriaEndereco)
	cy.salvar_categoria_endereco_agenda_sme()
})

Quando('edito nome, slug e descrição do endereço cadastrado', () => {
	cy.abrir_edicao_categoria_endereco_agenda_sme(categoriaEndereco)
	categoriaEnderecoEditada = gerarEnderecoAgendaSme()
	cy.atualizar_categoria_endereco_agenda_sme(categoriaEnderecoEditada)
	categoriaEndereco = categoriaEnderecoEditada
})

Quando('tento cadastrar um endereço sem preencher o nome', () => {
	categoriaEndereco = gerarEnderecoAgendaSme({ nome: '' })
	cy.preencher_categoria_endereco_agenda_sme(categoriaEndereco)
	cy.salvar_categoria_endereco_agenda_sme()
})

Quando('pesquiso pelo nome do endereço cadastrado', () => {
	cy.pesquisar_categoria_endereco_agenda_sme(categoriaEndereco.nome)
})

Entao('devo visualizar nome, descrição, slug e contagem do endereço', () => {
	cy.visitar_enderecos_agenda_sme()
	cy.pesquisar_categoria_endereco_agenda_sme(categoriaEndereco.nome)
	cy.validar_categoria_endereco_agenda_sme(categoriaEndereco)
})

E('a descrição vazia do endereço deve ser representada por um traço', () => {
	expect(categoriaEndereco.descricao).to.eq('')
	cy.validar_categoria_endereco_agenda_sme(categoriaEndereco)
})

E('os campos cadastrados do endereço devem permanecer na edição', () => {
	cy.abrir_edicao_categoria_endereco_agenda_sme(categoriaEndereco)
	cy.visitar_enderecos_agenda_sme()
	cy.abrir_edicao_categoria_endereco_agenda_sme(categoriaEndereco)
})

Entao('devo visualizar a mensagem de nome obrigatório do endereço {string}', (mensagem) => {
	cy.validar_nome_obrigatorio_endereco_agenda_sme(mensagem)
})

E('o endereço sem nome não deve ser cadastrado', () => {
	cy.reload()
	cy.pesquisar_categoria_endereco_agenda_sme(categoriaEndereco.slug, false)
})

Entao('devo visualizar somente o endereço pesquisado', () => {
	cy.validar_pesquisa_categoria_endereco_agenda_sme(categoriaEndereco)
})
