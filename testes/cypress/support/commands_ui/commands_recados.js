import Login_Intranet_Localizadores from '../locators/login_locators'
import { Lista_Recados_Localizadores, Editar_Recados_Localizadores } from '../locators/recados_locators'

const login = new Login_Intranet_Localizadores()
const lista = new Lista_Recados_Localizadores()
const editor = new Editar_Recados_Localizadores()
const rotaLista = '/wp-admin/edit.php?post_type=destaque'
let sequenciaBusca = 0

const parametros = (body) => new URLSearchParams(body)
const preencher = (seletor, valor) => cy.get(seletor).should('be.visible').clear().type(valor, { parseSpecialCharSequences: false })
const aguardarResposta = (alias, status) => cy.wait(alias).then(({ response }) => {
	expect(response, `resposta de ${alias}`).to.exist
	expect(response.statusCode, `HTTP de ${alias}`).to.be.oneOf(Array.isArray(status) ? status : [status])
})
const definirToggle = (seletor, habilitado) => {
	// O input nativo do ACF tem opacity: 0; interagir com o controle visível.
	cy.get(seletor).then(($input) => {
		if ($input.prop('checked') !== habilitado) {
			cy.get(seletor).parent(editor.rotulo_toggle()).find(editor.controle_toggle()).should('be.visible').click()
		}
	})
	cy.get(seletor).should(habilitado ? 'be.checked' : 'not.be.checked')
}
const textoVisivel = ($elemento) => {
	const copia = $elemento.clone()
	copia.find(lista.texto_acessivel()).remove()
	return copia.text().replace(/\s+/g, ' ').trim()
}

Cypress.Commands.add('autenticar_recados', () => {
	cy.session('sessao-recados', () => {
		const { usuario, senha } = Cypress.env('intranet')
		cy.visit('/')
		cy.get(login.campo_usuario()).type(usuario, { log: false })
		cy.get(login.campo_senha()).type(senha, { log: false })
		cy.get(login.botao_acessar()).click()
		cy.get(login.barra_de_usuario_logado()).should('be.visible')
	})
})

Cypress.Commands.add('visitar_listagem_recados', () => {
	cy.visit(rotaLista)
	cy.get(lista.titulo_pagina()).should(($titulo) => expect($titulo.text().trim()).to.eq('Recado'))
	cy.get(lista.formulario()).should('be.visible')
})

Cypress.Commands.add('pesquisar_recado', (titulo) => {
	// Sempre iniciar na listagem, sem herdar edição rápida, filtros ou paginação.
	cy.visitar_listagem_recados()
	const alias = `buscarRecado${++sequenciaBusca}`
	cy.intercept({ method: 'GET', pathname: '/wp-admin/edit.php', query: { post_type: 'destaque', s: titulo }, times: 1 }).as(alias)
	preencher(lista.campo_busca(), titulo)
	cy.get(lista.botao_buscar()).click()
	// A homologação responde diretamente ou redireciona antes da listagem final.
	// Capturar só a primeira requisição desta busca, sem acumular o redirect.
	aguardarResposta(`@${alias}`, [200, 302])
	cy.get(lista.campo_busca()).should('have.value', titulo)
})

Cypress.Commands.add('preencher_recado', (dados) => {
	preencher(editor.campo_titulo(), dados.titulo)
	preencher(editor.campo_subtitulo(), dados.subtitulo)
	cy.get(editor.aba_codigo()).click()
	preencher(editor.campo_conteudo(), dados.conteudo)
})

Cypress.Commands.add('salvar_recado', (dados, atualizacao = false) => {
	cy.intercept('POST', '**/wp-admin/post.php', (req) => {
		const body = parametros(req.body)
		if (body.get('action') === 'editpost' && body.get('post_type') === 'destaque' && body.get('post_ID') === dados.id) {
			req.alias = 'salvarRecadoProprio'
		}
	})
	cy.get(editor.botao_publicar()).should('be.visible').and('not.be.disabled').click()
	aguardarResposta('@salvarRecadoProprio', 302)
	cy.get(editor.mensagem()).should('be.visible').and('contain.text', atualizacao ? 'Post atualizado.' : 'Post publicado.')
	cy.get(editor.status()).should('have.value', 'publish')
	cy.then(() => { dados.publicado = true })
})

Cypress.Commands.add('criar_recado_proprio', (dados) => {
	cy.visit('/wp-admin/post-new.php?post_type=destaque')
	cy.get(editor.campo_id()).invoke('val').then((id) => { dados.id = id })
	cy.preencher_recado(dados)
	definirToggle(editor.checkbox_destaque(), dados.destaque)
	definirToggle(editor.checkbox_servidores(), true)
	definirToggle(editor.checkbox_rede_parceira(), false)
	if (dados.comTaxonomias) {
		cy.intercept('POST', '**/wp-admin/admin-ajax.php', (req) => {
			const body = parametros(req.body)
			if (body.get('action') === 'add-categorias-destaque' && body.get('newcategorias-destaque') === dados.categoria) req.alias = 'criarCategoriaRecado'
		})
		cy.get(editor.adicionar_categoria()).click()
		preencher(editor.campo_nova_categoria(), dados.categoria)
		cy.get(editor.botao_adicionar_categoria()).click()
		aguardarResposta('@criarCategoriaRecado', 200)
		cy.get(editor.lista_categorias()).contains(editor.rotulo_categoria(), dados.categoria).find(editor.checkbox_categoria()).should('be.checked')
		preencher(editor.campo_nova_tag(), dados.tag)
		cy.get(editor.botao_adicionar_tag()).click()
		cy.get(editor.lista_tags()).should('contain.text', dados.tag)
	}
	cy.get(editor.select_autor()).find(editor.opcao_selecionada()).invoke('text').then((autor) => {
		dados.autor = autor.replace(/\s*\([^)]*\)\s*$/, '').trim()
	})
	cy.salvar_recado(dados)
	cy.get(editor.formulario()).then(($form) => {
		const valor = (seletor) => String($form.find(seletor).val()).padStart(2, '0')
		dados.dataPublicacao = `${valor(editor.campo_dia())}/${valor(editor.campo_mes())}/${valor(editor.campo_ano())} às ${valor(editor.campo_hora())}:${valor(editor.campo_minuto())}`
	})
})

Cypress.Commands.add('validar_recado_na_listagem', (dados) => {
	cy.get(lista.cabecalhos()).within(() => {
		;['Título', 'Autor', 'Destaque home', 'Categoria', 'Tags/Etiquetas', 'Data'].forEach((coluna) => cy.contains(lista.cabecalho_coluna(), coluna).should('be.visible'))
	})
	cy.get(lista.linha_por_id(dados.id)).should('be.visible').and('have.class', 'status-publish').within(() => {
		cy.get(lista.link_titulo()).should('have.text', dados.titulo)
		cy.get(lista.coluna_autor()).should(($e) => expect(textoVisivel($e)).to.eq(dados.autor))
		cy.get(lista.coluna_destaque()).should(($e) => expect(textoVisivel($e)).to.eq(dados.destaque ? 'Sim' : '—'))
		cy.get(lista.coluna_categoria()).should(($e) => expect(textoVisivel($e)).to.eq(dados.comTaxonomias ? dados.categoria : '-'))
		cy.get(lista.coluna_tags()).should(($e) => expect(textoVisivel($e)).to.eq(dados.comTaxonomias ? dados.tag : '-'))
		cy.get(lista.coluna_data()).should('contain.text', 'Publicado').and('contain.text', dados.dataPublicacao)
	})
})

Cypress.Commands.add('validar_busca_recado', (dados) => {
	cy.get(lista.linhas()).should('have.length', 1).and('have.id', `post-${dados.id}`)
	cy.get(lista.linhas()).find(lista.link_titulo()).should('have.text', dados.titulo)
})

Cypress.Commands.add('abrir_recado_proprio', (dados) => {
	cy.pesquisar_recado(dados.titulo)
	cy.get(lista.linha_por_id(dados.id)).find(lista.link_titulo()).should('have.text', dados.titulo).click()
	cy.get(editor.campo_id()).should('have.value', dados.id)
	cy.get(editor.campo_titulo()).should('have.value', dados.titulo)
})

Cypress.Commands.add('validar_recado_persistido', (dados) => {
	cy.reload()
	cy.get(editor.campo_id()).should('have.value', dados.id)
	cy.get(editor.campo_titulo()).should('have.value', dados.titulo)
	cy.get(editor.campo_subtitulo()).should('have.value', dados.subtitulo)
	cy.get(editor.campo_conteudo()).should(($campo) => {
		const documento = new DOMParser().parseFromString($campo.val(), 'text/html')
		expect(documento.body.textContent.trim(), 'conteúdo persistido').to.eq(dados.conteudo)
	})
	cy.get(editor.status()).should('have.value', 'publish')
})

Cypress.Commands.add('remover_recado_proprio', (dados) => {
	// O ID veio do formulário criado neste cenário; o UUID protege dados alheios.
	cy.visit(`/wp-admin/post.php?post=${dados.id}&action=edit`)
	cy.get(editor.campo_id()).should('have.value', dados.id)
	cy.get(editor.campo_titulo()).should('include.value', dados.identificador)
	cy.intercept({ method: 'GET', pathname: '/wp-admin/post.php', query: { action: 'trash', post: dados.id } }).as('limparRecadoProprio')
	cy.get(editor.link_lixeira()).click()
	aguardarResposta('@limparRecadoProprio', 302)
})
