import { Given as Dado, When as Quando, Then as Entao, And as E, After } from 'cypress-cucumber-preprocessor/steps'
import { gerarRecado } from '../../utils/intranet/gerar_recado'

let recado

Dado('que possuo um recado próprio publicado com categoria, tag e destaque na home', () => {
	recado = gerarRecado({ comTaxonomias: true, destaque: true })
	cy.autenticar_recados()
	cy.criar_recado_proprio(recado)
})

Dado('que possuo um recado próprio publicado para Todos os Recados', () => {
	recado = gerarRecado()
	cy.autenticar_recados()
	cy.criar_recado_proprio(recado)
})

Quando('acesso Todos os Recados e localizo meu recado', () => {
	cy.pesquisar_recado(recado.titulo)
})

Quando('pesquiso pelo título único do meu recado', () => {
	cy.pesquisar_recado(recado.titulo)
})

Quando('edito título, conteúdo e subtítulo do meu recado', () => {
	cy.abrir_recado_proprio(recado)
	cy.then(() => {
		const alteracoes = gerarRecado()
		recado.titulo = `${recado.titulo} editado`
		recado.conteudo = alteracoes.conteudo
		recado.subtitulo = alteracoes.subtitulo
		cy.preencher_recado(recado)
	})
})

E('salvo as alterações do meu recado', () => {
	cy.salvar_recado(recado, true)
})

Entao('devo visualizar título, autor, destaque, categoria, tags e data do meu recado', () => {
	cy.validar_recado_na_listagem(recado)
})

Entao('devo visualizar somente meu recado pesquisado', () => {
	cy.validar_busca_recado(recado)
})

Entao('as alterações do recado devem permanecer após recarregar e reabrir', () => {
	cy.validar_recado_persistido(recado)
	cy.pesquisar_recado(recado.titulo)
	cy.validar_busca_recado(recado)
	cy.validar_recado_na_listagem(recado)
	cy.abrir_recado_proprio(recado)
	cy.validar_recado_persistido(recado)
})

After({ tags: '@recados_todos' }, () => {
	if (recado?.publicado) cy.remover_recado_proprio(recado)
	recado = undefined
})
