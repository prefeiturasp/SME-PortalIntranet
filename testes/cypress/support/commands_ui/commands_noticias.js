import {
	Adicionar_Noticia_Localizadores,
	Categorias_Localizadores,
	Lista_Noticias_Localizadores,
	Visualizar_Noticia_Publicada_Localizadores,
} from '../locators/noticias_locators'
import 'cypress-iframe'

const adicionar_Noticia_Localizadores = new Adicionar_Noticia_Localizadores()
const categorias_Localizadores = new Categorias_Localizadores()
const lista_Noticias_Localizadores = new Lista_Noticias_Localizadores()
const visualizar_Noticia_Publicada_Localizadores =
	new Visualizar_Noticia_Publicada_Localizadores()

Cypress.Commands.add('ir_para_formulario_adicionar_noticias', (device) => {
	cy.configurar_visualizacao(device)
	cy.visit('/wp-admin/post-new.php?post_type=noticia')
})

Cypress.Commands.add('preencher_titulo', (titulo) => {
	cy.get(adicionar_Noticia_Localizadores.campo_titulo()).type(titulo, {
		force: true,
	})
})
Cypress.Commands.add('preencher_subtitulo', (subtitulo) => {
	cy.get(adicionar_Noticia_Localizadores.campo_subtitulo())
		.focus()
		.type(subtitulo, {
			force: true,
		})
})

Cypress.Commands.add('preencher_conteudo_noticia', (conteudo) => {
	cy.frameLoaded(adicionar_Noticia_Localizadores.iframe_conteudo_visual())
	cy.iframe(adicionar_Noticia_Localizadores.iframe_conteudo_visual())
		.clear()
		.focus()
		.type(conteudo, { force: true })
})

Cypress.Commands.add('preencher_resumo', (resumo) => {
	cy.get(adicionar_Noticia_Localizadores.campo_resumo()).focus().type(resumo)
})

Cypress.Commands.add('editar_titulo', (titulo) => {
	cy.get(adicionar_Noticia_Localizadores.campo_titulo()).clear().type(titulo, {
		force: true,
	})
})
Cypress.Commands.add('editar_subtitulo', (subtitulo) => {
	cy.get(adicionar_Noticia_Localizadores.campo_subtitulo())
		.focus()
		.clear()
		.type(subtitulo, {
			force: true,
		})
})

Cypress.Commands.add('editar_conteudo_noticia', (conteudo) => {
	cy.frameLoaded(adicionar_Noticia_Localizadores.iframe_conteudo_visual())
	cy.iframe(adicionar_Noticia_Localizadores.iframe_conteudo_visual())
		.clear()
		.focus()
		.type(conteudo, { force: true })
})

Cypress.Commands.add('editar_resumo', (resumo) => {
	cy.get(adicionar_Noticia_Localizadores.campo_resumo())
		.focus()
		.clear()
		.type(resumo)
})

Cypress.Commands.add('clicar_botao_publicar', () => {
	cy.intercept('/wp-admin/admin-ajax.php').as('plublicacaoNoticia')
	cy.get(adicionar_Noticia_Localizadores.botao_publicar())
		.should('be.visible')
		.click()
	cy.wait('@plublicacaoNoticia')
})
Cypress.Commands.add('clicar_link_permanente', () => {
	cy.get(adicionar_Noticia_Localizadores.link_permanente())
		.should('be.visible')
		.invoke('removeAttr', 'target')
		.click()
})

Cypress.Commands.add('obter_link_da_noticia', () => {
	cy.get(adicionar_Noticia_Localizadores.link_permanente())
		.should('be.visible')
		.invoke('attr', 'href')
		.then((href) => {
			Cypress.env('urlNoticia', href)
		})
})
Cypress.Commands.add('obter_link_da_noticia_editada', () => {
	cy.get(adicionar_Noticia_Localizadores.link_permanente())
		.should('be.visible')
		.invoke('attr', 'href')
		.then((href) => {
			Cypress.env('urlNoticiaEditada', href)
		})
})
Cypress.Commands.add('visitar_noticia_criada', () => {
	cy.visit(Cypress.env('urlNoticia'))
})
Cypress.Commands.add('visitar_noticia_editada', () => {
	cy.visit(Cypress.env('urlNoticiaEditada'))
})
Cypress.Commands.add('visitar_noticia_excluida', () => {
	cy.visit(Cypress.env('urlNoticiaEditada'), { failOnStatusCode: false })
})
Cypress.Commands.add('visitar_listagem_noticias_intranet', () => {
	cy.visit('wp-admin/edit.php?post_type=noticia')
})

Cypress.Commands.add('validar_mensagem_sucesso_ao_postar_noticia', () => {
	cy.get(adicionar_Noticia_Localizadores.mensagem_sucesso())
		.contains('Post publicado.')
		.should('be.visible')
})
Cypress.Commands.add('validar_mensagem_sucesso_ao_atualizar_noticia', () => {
	cy.get(adicionar_Noticia_Localizadores.mensagem_sucesso())
		.contains('Post atualizado.')
		.should('be.visible')
})
Cypress.Commands.add('validar_mensagem_obrigatoriedade_campo_subtitulo', () => {
	cy.get(adicionar_Noticia_Localizadores.mensagem_obrigatoriedade())
		.contains('O valor Insira o subtítulo é obrigatório')
		.should('be.visible')
})
Cypress.Commands.add('validar_titulo_noticia_publicada', (titulo) => {
	cy.get(visualizar_Noticia_Publicada_Localizadores.titulo_principal())
		.contains(titulo)
		.should('be.visible')
})
Cypress.Commands.add('validar_subtitulo_noticia_publicada', (subtitulo) => {
	cy.get(visualizar_Noticia_Publicada_Localizadores.subtitulo())
		.contains(subtitulo)
		.should('be.visible')
})
Cypress.Commands.add('validar_conteudo_noticia_publicada', (conteudo) => {
	cy.get(visualizar_Noticia_Publicada_Localizadores.conteudo_principal())
		.contains(conteudo)
		.should('be.visible')
})
Cypress.Commands.add(
	'validar_exibicao_noticia_na_listagem_intranet',
	(tituloNoticia) => {
		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
			force: true,
		})
		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
		cy.get(lista_Noticias_Localizadores.celula_posts())
			.contains(tituloNoticia)
			.should('be.visible')
	},
)
Cypress.Commands.add(
	'validar_sucesso_exclusao_noticia_da_listagem_intranet',
	() => {
		cy.get(lista_Noticias_Localizadores.mensagem_sucesso())
			.contains('1 post movido para a lixeira.')
			.should('be.visible')
	},
)
Cypress.Commands.add('validar_sucesso_exclusao_permanente_noticia', () => {
	cy.get(lista_Noticias_Localizadores.mensagem_sucesso())
		.contains('1 post excluído permanentemente.')
		.should('be.visible')
})
Cypress.Commands.add(
	'validar_nao_exibicao_noticia_na_listagem_intranet',
	() => {
		cy.get(lista_Noticias_Localizadores.celula_posts())
			.contains('Nenhum registro encontrado')
			.should('be.visible')
			.click()
	},
)
Cypress.Commands.add('validar_nao_exibicao_pagina_da_noticia', () => {
	cy.get(visualizar_Noticia_Publicada_Localizadores.erro_404())
		.contains('error')
		.should('be.visible')
})
Cypress.Commands.add(
	'acessar_noticia_na_listagem_intranet',
	(tituloNoticia) => {
		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
			force: true,
		})
		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
		cy.get(lista_Noticias_Localizadores.celula_posts())
			.contains(tituloNoticia)
			.should('be.visible')
			.click()
	},
)
Cypress.Commands.add('acessar_lixo_noticia', () => {
	cy.get(lista_Noticias_Localizadores.acessar_lixos())
		.should('be.visible')
		.click()
})
Cypress.Commands.add(
	'pesquisar_noticia_na_listagem_intranet',
	(tituloNoticia) => {
		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
			force: true,
		})
		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
	},
)
Cypress.Commands.add(
	'enviar_noticia_para_lixeira_intranet',
	(tituloNoticia) => {
		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
			force: true,
		})
		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
		cy.get(lista_Noticias_Localizadores.link_lixeira())
			.should('be.visible')
			.click({ force: true })
		cy.on('window:confirm', () => true)
	},
)
Cypress.Commands.add('excluir_noticia_permanentemente', (tituloNoticia) => {
	cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
		force: true,
	})
	cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
	cy.get(lista_Noticias_Localizadores.excluir_permanentemente())
		.should('be.visible')
		.click({ force: true })
	cy.on('window:confirm', () => true)
})

Cypress.Commands.add('visitar_pagina_categorias', () => {
	cy.visit('/wp-admin/edit-tags.php?taxonomy=categorias-noticias&post_type=noticia')
})

Cypress.Commands.add('preencher_formulario_categoria', (nome, descricao) => {
	cy.get(categorias_Localizadores.campo_nome()).clear().type(nome)
	cy.get(categorias_Localizadores.campo_descricao()).clear().type(descricao)
})

Cypress.Commands.add('clicar_botao_adicionar_categoria', () => {
	cy.get(categorias_Localizadores.botao_adicionar_categoria())
		.should('be.visible')
		.click()
})

Cypress.Commands.add('validar_categoria_na_listagem', (nome, descricao) => {
	cy.contains(categorias_Localizadores.linhas_categorias(), nome)
		.should('be.visible')
		.within(() => {
			cy.get(categorias_Localizadores.coluna_descricao()).should(
				'contain.text',
				descricao,
			)
		})
})

Cypress.Commands.add('editar_categoria', (nomeAtual, nome, descricao) => {
	cy.contains(categorias_Localizadores.linhas_categorias(), nomeAtual)
		.should('be.visible')
		.within(() => {
			cy.get(categorias_Localizadores.link_editar()).click({ force: true })
		})

	cy.get(categorias_Localizadores.campo_nome_edicao()).clear().type(nome)
	cy.get(categorias_Localizadores.campo_descricao_edicao())
		.clear()
		.type(descricao)
	cy.get(categorias_Localizadores.botao_editar_categoria())
		.should('be.visible')
		.click()
	cy.visitar_pagina_categorias()
})

Cypress.Commands.add('excluir_categoria', (nome) => {
	cy.on('window:confirm', () => true)
	cy.contains(categorias_Localizadores.linhas_categorias(), nome)
		.should('be.visible')
		.within(() => {
			cy.get(categorias_Localizadores.link_excluir()).click({ force: true })
		})
})

Cypress.Commands.add('validar_categoria_excluida', (nome) => {
	cy.get(categorias_Localizadores.lista_categorias()).should(
		'not.contain.text',
		nome,
	)
})

Cypress.Commands.add('validar_campos_formulario_categoria', () => {
	cy.get(categorias_Localizadores.formulario_adicionar_categoria()).should(
		'be.visible',
	)
	cy.get(categorias_Localizadores.campo_nome()).should('be.visible')
	cy.get(categorias_Localizadores.campo_slug()).should('be.visible')
	cy.get(categorias_Localizadores.select_categoria_ascendente()).should(
		'be.visible',
	)
	cy.get(categorias_Localizadores.campo_descricao()).should('be.visible')
	cy.get(categorias_Localizadores.botao_adicionar_categoria()).should(
		'be.visible',
	)
})

Cypress.Commands.add('adicionar_categoria_sem_nome', () => {
	cy.get(categorias_Localizadores.campo_nome()).clear()
	cy.get(categorias_Localizadores.botao_adicionar_categoria()).click()
})

Cypress.Commands.add('validar_nome_categoria_obrigatorio', () => {
	cy.get(categorias_Localizadores.mensagem_retorno())
		.should('be.visible')
		.and('not.be.empty')
})

Cypress.Commands.add('cadastrar_categoria', (nome, descricao) => {
	cy.preencher_formulario_categoria(nome, descricao)
	cy.clicar_botao_adicionar_categoria()
	cy.validar_categoria_na_listagem(nome, descricao)
})

Cypress.Commands.add('validar_listagem_categorias', () => {
	cy.get(categorias_Localizadores.tabela_categorias()).should('be.visible')
	cy.get(categorias_Localizadores.linhas_categorias())
		.should('have.length.greaterThan', 0)
		.and('be.visible')
})

Cypress.Commands.add('pesquisar_categoria', (nome) => {
	cy.get(categorias_Localizadores.campo_busca()).clear().type(nome)
	cy.get(categorias_Localizadores.botao_pesquisar()).click()
})

Cypress.Commands.add('validar_resultado_pesquisa_categoria', (nome) => {
	cy.get(categorias_Localizadores.linhas_categorias()).should(
		'have.length',
		1,
	)
	cy.get(categorias_Localizadores.link_nome_categoria())
		.should('be.visible')
		.and('have.text', nome)
})

Cypress.Commands.add('ordenar_categorias_por_nome', () => {
	cy.get(categorias_Localizadores.cabecalho_nome()).click()
})

Cypress.Commands.add('validar_ordenacao_categorias_por_nome', () => {
	cy.location('search').then((queryString) => {
		// Define a direção usada na comparação conforme a ordenação da URL.
		const direcao =
			new URLSearchParams(queryString).get('order') === 'desc' ? -1 : 1

		cy.get(categorias_Localizadores.link_nome_categoria()).then(($nomes) => {
			// Converte os elementos da tabela em uma lista contendo apenas os nomes.
			const nomesExibidos = [...$nomes].map((elemento) =>
				elemento.innerText.trim(),
			)

			// Cria a lista esperada respeitando acentos e a direção selecionada.
			const nomesOrdenados = [...nomesExibidos].sort((nomeA, nomeB) =>
				direcao *
					nomeA.localeCompare(nomeB, 'pt-BR', { sensitivity: 'base' }),
			)

			expect(nomesExibidos).to.deep.equal(nomesOrdenados)
		})
	})
})

Cypress.Commands.add('excluir_categorias_em_massa', (nomes) => {
	nomes.forEach((nome) => {
		cy.contains(categorias_Localizadores.linhas_categorias(), nome)
			.should('be.visible')
			.within(() => {
				cy.get(categorias_Localizadores.checkbox_categoria_na_linha()).check()
			})
	})

	cy.get(categorias_Localizadores.select_acao_em_massa()).select('delete')
	cy.on('window:confirm', () => true)
	cy.get(categorias_Localizadores.botao_aplicar_acao_em_massa()).click()
})

Cypress.Commands.add('validar_categorias_excluidas_em_massa', (nomes) => {
	nomes.forEach((nome) => {
		cy.get(categorias_Localizadores.lista_categorias()).should(
			'not.contain.text',
			nome,
		)
	})
})

Cypress.Commands.add('validar_contagem_publicacoes_categoria', (nome, total) => {
	cy.contains(categorias_Localizadores.linhas_categorias(), nome)
		.should('be.visible')
		.within(() => {
			cy.get(categorias_Localizadores.coluna_contagem()).should(
				'have.text',
				String(total),
			)
		})
})
