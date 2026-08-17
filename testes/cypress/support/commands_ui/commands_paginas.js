import { Visualizar_Noticia_Publicada_Localizadores } from '../locators/noticias_locators'
import {
	Adicionar_Pagina_Localizadores,
	Categorias_Localizadores,
	Visualizar_Pagina_Publicada_Localizadores,
} from '../locators/paginas_locators'
import 'cypress-iframe'

const adicionar_Pagina_Localizadores = new Adicionar_Pagina_Localizadores()
const visualizar_Pagina_Publicada_Localizadores =
	new Visualizar_Pagina_Publicada_Localizadores()
const categorias_Localizadores = new Categorias_Localizadores()
// const lista_Noticias_Localizadores = new Lista_Noticias_Localizadores()
// const visualizar_Noticia_Publicada_Localizadores =
// 	new Visualizar_Noticia_Publicada_Localizadores()

Cypress.Commands.add('ir_para_formulario_adicionar_paginas', (device) => {
	cy.configurar_visualizacao(device)
	cy.visit('/wp-admin/post-new.php?post_type=page')
})

Cypress.Commands.add('preencher_titulo_pagina', (titulo) => {
	cy.get(adicionar_Pagina_Localizadores.campo_titulo()).type(titulo, {
		force: true,
	})
})

Cypress.Commands.add('preencher_conteudo_pagina', (conteudo) => {
	cy.frameLoaded(adicionar_Pagina_Localizadores.iframe_conteudo_visual())
	cy.iframe(adicionar_Pagina_Localizadores.iframe_conteudo_visual())
		.clear()
		.focus()
		.type(conteudo, { force: true })
})

// Cypress.Commands.add('editar_titulo', (titulo) => {
// 	cy.get(adicionar_Noticia_Localizadores.campo_titulo()).clear().type(titulo, {
// 		force: true,
// 	})
// })

// Cypress.Commands.add('editar_conteudo_noticia', (conteudo) => {
// 	cy.frameLoaded(adicionar_Noticia_Localizadores.iframe_conteudo_visual())
// 	cy.iframe(adicionar_Noticia_Localizadores.iframe_conteudo_visual())
// 		.clear()
// 		.focus()
// 		.type(conteudo, { force: true })
// })

Cypress.Commands.add('clicar_botao_publicar', () => {
	cy.intercept('/wp-admin/admin-ajax.php').as('plublicacaoPagina')
	cy.get(adicionar_Pagina_Localizadores.botao_publicar())
		.should('be.visible')
		.click()
	cy.wait('@plublicacaoPagina')
})

Cypress.Commands.add('clicar_link_permanente_pagina', () => {
	cy.get(adicionar_Pagina_Localizadores.link_permanente())
		.should('be.visible')
		.invoke('removeAttr', 'target')
		.click()
})

// Cypress.Commands.add('clicar_link_permanente', () => {
// 	cy.get(adicionar_Noticia_Localizadores.link_permanente())
// 		.should('be.visible')
// 		.invoke('removeAttr', 'target')
// 		.click()
// })

// Cypress.Commands.add('obter_link_da_noticia', () => {
// 	cy.get(adicionar_Noticia_Localizadores.link_permanente())
// 		.should('be.visible')
// 		.invoke('attr', 'href')
// 		.then((href) => {
// 			Cypress.env('urlNoticia', href)
// 		})
// })
// Cypress.Commands.add('obter_link_da_noticia_editada', () => {
// 	cy.get(adicionar_Noticia_Localizadores.link_permanente())
// 		.should('be.visible')
// 		.invoke('attr', 'href')
// 		.then((href) => {
// 			Cypress.env('urlNoticiaEditada', href)
// 		})
// })
// Cypress.Commands.add('visitar_noticia_criada', () => {
// 	cy.visit(Cypress.env('urlNoticia'))
// })
// Cypress.Commands.add('visitar_noticia_editada', () => {
// 	cy.visit(Cypress.env('urlNoticiaEditada'))
// })
// Cypress.Commands.add('visitar_noticia_excluida', () => {
// 	cy.visit(Cypress.env('urlNoticiaEditada'), { failOnStatusCode: false })
// })
// Cypress.Commands.add('visitar_listagem_noticias_intranet', () => {
// 	cy.visit('wp-admin/edit.php?post_type=noticia')
// })

Cypress.Commands.add('validar_mensagem_sucesso_ao_postar_pagina', () => {
	cy.get(adicionar_Pagina_Localizadores.mensagem_sucesso())
		.contains('Página publicada.')
		.should('be.visible')
})
// Cypress.Commands.add('validar_mensagem_sucesso_ao_atualizar_noticia', () => {
// 	cy.get(adicionar_Noticia_Localizadores.mensagem_sucesso())
// 		.contains('Post atualizado.')
// 		.should('be.visible')
// })
// Cypress.Commands.add('validar_mensagem_obrigatoriedade_campo_subtitulo', () => {
// 	cy.get(adicionar_Noticia_Localizadores.mensagem_obrigatoriedade())
// 		.contains('O valor Insira o subtítulo é obrigatório')
// 		.should('be.visible')
// })
// Cypress.Commands.add('validar_titulo_noticia_publicada', (titulo) => {
// 	cy.get(visualizar_Noticia_Publicada_Localizadores.titulo_principal())
// 		.contains(titulo)
// 		.should('be.visible')

Cypress.Commands.add('validar_titulo_pagina_publicada', (titulo) => {
	cy.get(visualizar_Pagina_Publicada_Localizadores.titulo_principal())
		.contains(titulo)
		.should('be.visible')
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
			cy.get(categorias_Localizadores.link_editar()).click({
				force: true,
			})
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
// })
// Cypress.Commands.add('validar_subtitulo_noticia_publicada', (subtitulo) => {
// 	cy.get(visualizar_Noticia_Publicada_Localizadores.subtitulo())
// 		.contains(subtitulo)
// 		.should('be.visible')
// })
// Cypress.Commands.add('validar_conteudo_noticia_publicada', (conteudo) => {
// 	cy.get(visualizar_Noticia_Publicada_Localizadores.conteudo_principal())
// 		.contains(conteudo)
// 		.should('be.visible')
// })
// Cypress.Commands.add(
// 	'validar_exibicao_noticia_na_listagem_intranet',
// 	(tituloNoticia) => {
// 		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
// 			force: true,
// 		})
// 		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
// 		cy.get(lista_Noticias_Localizadores.celula_posts())
// 			.contains(tituloNoticia)
// 			.should('be.visible')
// 	},
// )
// Cypress.Commands.add(
// 	'validar_sucesso_exclusao_noticia_da_listagem_intranet',
// 	() => {
// 		cy.get(lista_Noticias_Localizadores.mensagem_sucesso())
// 			.contains('1 post movido para a lixeira.')
// 			.should('be.visible')
// 	},
// )
// Cypress.Commands.add('validar_sucesso_exclusao_permanente_noticia', () => {
// 	cy.get(lista_Noticias_Localizadores.mensagem_sucesso())
// 		.contains('1 post excluído permanentemente.')
// 		.should('be.visible')
// })
// Cypress.Commands.add(
// 	'validar_nao_exibicao_noticia_na_listagem_intranet',
// 	() => {
// 		cy.get(lista_Noticias_Localizadores.celula_posts())
// 			.contains('Nenhum registro encontrado')
// 			.should('be.visible')
// 			.click()
// 	},
// )
// Cypress.Commands.add('validar_nao_exibicao_pagina_da_noticia', () => {
// 	cy.get(visualizar_Noticia_Publicada_Localizadores.erro_404())
// 		.contains('error')
// 		.should('be.visible')
// })
// Cypress.Commands.add(
// 	'acessar_noticia_na_listagem_intranet',
// 	(tituloNoticia) => {
// 		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
// 			force: true,
// 		})
// 		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
// 		cy.get(lista_Noticias_Localizadores.celula_posts())
// 			.contains(tituloNoticia)
// 			.should('be.visible')
// 			.click()
// 	},
// )
// Cypress.Commands.add('acessar_lixo_noticia', () => {
// 	cy.get(lista_Noticias_Localizadores.acessar_lixos())
// 		.should('be.visible')
// 		.click()
// })
// Cypress.Commands.add(
// 	'pesquisar_noticia_na_listagem_intranet',
// 	(tituloNoticia) => {
// 		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
// 			force: true,
// 		})
// 		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
// 	},
// )
// Cypress.Commands.add(
// 	'enviar_noticia_para_lixeira_intranet',
// 	(tituloNoticia) => {
// 		cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
// 			force: true,
// 		})
// 		cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
// 		cy.get(lista_Noticias_Localizadores.link_lixeira())
// 			.should('be.visible')
// 			.click({ force: true })
// 		cy.on('window:confirm', () => true)
// 	},
// )
// Cypress.Commands.add('excluir_noticia_permanentemente', (tituloNoticia) => {
// 	cy.get(lista_Noticias_Localizadores.campo_busca()).type(tituloNoticia, {
// 		force: true,
// 	})
// 	cy.get(lista_Noticias_Localizadores.botao_buscar()).click()
// 	cy.get(lista_Noticias_Localizadores.excluir_permanentemente())
// 		.should('be.visible')
// 		.click({ force: true })
// 	cy.on('window:confirm', () => true)
// })
