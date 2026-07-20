import {
	Adicionar_Noticia_Localizadores,
	Lista_Noticias_Localizadores,
	Visualizar_Noticia_Publicada_Localizadores,
} from '../locators/noticias_locators'
import 'cypress-iframe'

const adicionar_Noticia_Localizadores = new Adicionar_Noticia_Localizadores()
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
