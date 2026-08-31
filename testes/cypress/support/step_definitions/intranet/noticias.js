import {
	Given,
	When,
	Then,
	And,
	Before,
} from 'cypress-cucumber-preprocessor/steps'
import gerarNoticia from '../../utils/intranet/gerar_noticias'
import { gerarConteudo } from '../../utils/intranet/gerar_paginas'

const Dado = Given
const Quando = When
const Entao = Then
const E = And

const novaNoticia = gerarNoticia()
const editarNoticia = gerarNoticia()
const novaCategoria = gerarConteudo()
const categoriaEditada = gerarConteudo()
const categoriaParaExclusao = gerarConteudo()
const categoriaParaPesquisa = gerarConteudo()
const primeiraCategoriaEmMassa = gerarConteudo()
const segundaCategoriaEmMassa = gerarConteudo()
const categoriaSemPublicacoes = gerarConteudo()

//----------------------------Dado----------------------------------------//

Dado('eu realizo login na intranet no wp-admin', () => {
	cy.realizar_login_intranet()
})

Dado('eu publiquei uma notícia', () => {
	cy.realizar_login_intranet()
	cy.ir_para_formulario_adicionar_noticias()
	cy.preencher_titulo(novaNoticia.titulo)
	cy.preencher_subtitulo(novaNoticia.subtitulo)
	cy.preencher_conteudo_noticia(novaNoticia.conteudo)
	cy.preencher_resumo(novaNoticia.resumo)
	cy.clicar_botao_publicar()
})

Dado('eu acesso a listagem de noticias no wp-admin', () => {
	cy.realizar_login_intranet()
	cy.visitar_listagem_noticias_intranet()
})

Dado('eu possuo uma categoria cadastrada', () => {
	cy.realizar_login_intranet()
	cy.visitar_pagina_categorias()
	cy.preencher_formulario_categoria(
		novaCategoria.nome,
		novaCategoria.descricao,
	)
	cy.clicar_botao_adicionar_categoria()
	cy.validar_categoria_na_listagem(
		novaCategoria.nome,
		novaCategoria.descricao,
	)
})

Dado('eu possuo uma categoria cadastrada para exclusão', () => {
	cy.realizar_login_intranet()
	cy.visitar_pagina_categorias()
	cy.preencher_formulario_categoria(
		categoriaParaExclusao.nome,
		categoriaParaExclusao.descricao,
	)
	cy.clicar_botao_adicionar_categoria()
	cy.validar_categoria_na_listagem(
		categoriaParaExclusao.nome,
		categoriaParaExclusao.descricao,
	)
})

Dado('eu possuo uma categoria cadastrada para pesquisa', () => {
	cy.realizar_login_intranet()
	cy.visitar_pagina_categorias()
	cy.cadastrar_categoria(
		categoriaParaPesquisa.nome,
		categoriaParaPesquisa.descricao,
	)
})

Dado('eu possuo duas categorias cadastradas para ações em massa', () => {
	cy.realizar_login_intranet()
	cy.visitar_pagina_categorias()
	cy.cadastrar_categoria(
		primeiraCategoriaEmMassa.nome,
		primeiraCategoriaEmMassa.descricao,
	)
	cy.cadastrar_categoria(
		segundaCategoriaEmMassa.nome,
		segundaCategoriaEmMassa.descricao,
	)
})

Dado('eu possuo uma categoria sem publicações cadastrada', () => {
	cy.realizar_login_intranet()
	cy.visitar_pagina_categorias()
	cy.cadastrar_categoria(
		categoriaSemPublicacoes.nome,
		categoriaSemPublicacoes.descricao,
	)
})
//----------------------------E----------------------------------------//

E('acesso a página de adição de notícias', () => {
	cy.ir_para_formulario_adicionar_noticias()
})

E('clico no botão publicar', () => {
	cy.clicar_botao_publicar()
})

E('acesso uma notícia publicada', () => {
	cy.visitar_listagem_noticias_intranet()
	cy.acessar_noticia_na_listagem_intranet(novaNoticia.titulo)
})
E('excluo a notícia permanente', () => {
	cy.excluir_noticia_permanentemente(editarNoticia.titulo)
})

E('acesso a página de categorias', () => {
	cy.visitar_pagina_categorias()
})

E('clico no botão adicionar categoria', () => {
	cy.clicar_botao_adicionar_categoria()
})

//----------------------------Quando----------------------------------------//

Quando('eu não preencho o campo subtitulo', () => {
	cy.preencher_titulo(novaNoticia.titulo)
	cy.preencher_conteudo_noticia(novaNoticia.conteudo)
	cy.preencher_resumo(novaNoticia.resumo)
})

Quando('eu clico na URL da notícia criada', () => {
	cy.clicar_link_permanente()
})

Quando('eu acesso a notícia criada na intranet', () => {
	cy.visitar_noticia_criada()
})
Quando('eu acesso a notícia editada na intranet', () => {
	cy.visitar_noticia_editada()
})
Quando('eu acesso a notícia excluida na intranet', () => {
	cy.visitar_noticia_excluida()
})
Quando('eu acesso a listagem de notícias na intranet', () => {
	cy.visitar_listagem_noticias_intranet()
})

Quando('preencho todos os campos do formulário', () => {
	cy.preencher_titulo(novaNoticia.titulo)
	cy.preencher_subtitulo(novaNoticia.subtitulo)
	cy.preencher_conteudo_noticia(novaNoticia.conteudo)
	cy.preencher_resumo(novaNoticia.resumo)
})

Quando('edito todos os campos do formulário', () => {
	cy.editar_titulo(editarNoticia.titulo)
	cy.editar_subtitulo(editarNoticia.subtitulo)
	cy.editar_conteudo_noticia(editarNoticia.conteudo)
	cy.editar_resumo(editarNoticia.resumo)
})
Quando('eu envio a noticia para a lixeira', () => {
	cy.enviar_noticia_para_lixeira_intranet(editarNoticia.titulo)
})
Quando('eu pesquiso a noticia que foi enviada para a lixeira', () => {
	cy.visitar_listagem_noticias_intranet()
	cy.pesquisar_noticia_na_listagem_intranet(editarNoticia.titulo)
})
Quando('eu acesso a pagina da notícia que foi enviada para a lixeira', () => {
	cy.visitar_noticia_excluida()
})
Quando('eu acesso a lixeira', () => {
	cy.visitar_listagem_noticias_intranet()
	cy.acessar_lixo_noticia()
})

Quando('preencho os campos do formulário de categoria', () => {
	cy.preencher_formulario_categoria(
		novaCategoria.nome,
		novaCategoria.descricao,
	)
})

Quando('edito os campos da categoria', () => {
	cy.editar_categoria(
		novaCategoria.nome,
		categoriaEditada.nome,
		categoriaEditada.descricao,
	)
})

Quando('excluo a categoria', () => {
	cy.excluir_categoria(categoriaParaExclusao.nome)
})

Quando('tento adicionar uma categoria sem informar o nome', () => {
	cy.adicionar_categoria_sem_nome()
})

Quando('pesquiso pela categoria cadastrada', () => {
	cy.pesquisar_categoria(categoriaParaPesquisa.nome)
})

Quando('ordeno a tabela de categorias pelo nome', () => {
	cy.ordenar_categorias_por_nome()
})

Quando('excluo as categorias por meio das ações em massa', () => {
	cy.excluir_categorias_em_massa([
		primeiraCategoriaEmMassa.nome,
		segundaCategoriaEmMassa.nome,
	])
})

//----------------------------Então----------------------------------------//
Entao(
	'devo visualizar a mensagem informando que o post foi publicado com sucesso',
	() => {
		cy.validar_mensagem_sucesso_ao_postar_noticia()
	},
)
Entao(
	'devo visualizar a mensagem informando que o post foi atualizado com sucesso',
	() => {
		cy.validar_mensagem_sucesso_ao_atualizar_noticia()
	},
)

Entao(
	'devo visualizar a mensagem informando que o campo de subtitulo é obrigatório',
	() => {
		cy.validar_mensagem_obrigatoriedade_campo_subtitulo()
	},
)
Entao('devo visualizar a notícia publicada no portal da intranet', () => {
	cy.validar_titulo_noticia_publicada(novaNoticia.titulo)
})

Entao(
	'devo visualizar a exibição do título da notícia publicada no portal da intranet',
	() => {
		cy.validar_titulo_noticia_publicada(novaNoticia.titulo)
	},
)
Entao(
	'devo visualizar a exibição do subtítulo da notícia publicada no portal da intranet',
	() => {
		cy.validar_subtitulo_noticia_publicada(novaNoticia.subtitulo)
	},
)
Entao(
	'devo visualizar a exibição do corpo da notícia publicada no portal da intranet',
	() => {
		cy.validar_conteudo_noticia_publicada(novaNoticia.conteudo)
	},
)
Entao('devo visualizar o título da notícia na listagem da intranet', () => {
	cy.validar_exibicao_noticia_na_listagem_intranet(novaNoticia.titulo)
})

Entao(
	'devo visualizar a exibição do título da notícia editada no portal da intranet',
	() => {
		cy.validar_titulo_noticia_publicada(editarNoticia.titulo)
	},
)
Entao('não devo visualizar a exibição da notícia no portal da intranet', () => {
	cy.validar_nao_exibicao_pagina_da_noticia()
})

Entao(
	'devo visualizar a exibição do subtítulo da notícia editada no portal da intranet',
	() => {
		cy.validar_subtitulo_noticia_publicada(editarNoticia.subtitulo)
	},
)
Entao(
	'devo visualizar a exibição do corpo da notícia editada no portal da intranet',
	() => {
		cy.validar_conteudo_noticia_publicada(editarNoticia.conteudo)
	},
)
Entao('devo visualizar a mensagem de exclusão da notícia com sucesso', () => {
	cy.validar_sucesso_exclusao_noticia_da_listagem_intranet()
})
Entao(
	'devo visualizar a mensagem informando que o post foi excluído permanentemente',
	() => {
		cy.validar_sucesso_exclusao_permanente_noticia()
	},
)
Entao('não devo visualizar a notícia na listagem', () => {
	cy.validar_nao_exibicao_noticia_na_listagem_intranet()
})

Entao('devo visualizar a categoria cadastrada na listagem', () => {
	cy.validar_categoria_na_listagem(
		novaCategoria.nome,
		novaCategoria.descricao,
	)
})

Entao('devo visualizar a categoria editada na listagem', () => {
	cy.validar_categoria_na_listagem(
		categoriaEditada.nome,
		categoriaEditada.descricao,
	)
})

Entao('não devo visualizar a categoria excluída na listagem', () => {
	cy.validar_categoria_excluida(categoriaParaExclusao.nome)
})

Entao('devo visualizar os campos do formulário de categoria', () => {
	cy.validar_campos_formulario_categoria()
})

Entao('devo visualizar a validação de obrigatoriedade do nome da categoria', () => {
	cy.validar_nome_categoria_obrigatorio()
})

Entao('devo visualizar a tabela com as categorias cadastradas', () => {
	cy.validar_listagem_categorias()
})

Entao('devo visualizar somente a categoria pesquisada', () => {
	cy.validar_resultado_pesquisa_categoria(categoriaParaPesquisa.nome)
})

Entao('devo visualizar as categorias ordenadas pelo nome', () => {
	cy.validar_ordenacao_categorias_por_nome()
})

Entao('não devo visualizar as categorias excluídas na listagem', () => {
	cy.validar_categorias_excluidas_em_massa([
		primeiraCategoriaEmMassa.nome,
		segundaCategoriaEmMassa.nome,
	])
})

Entao('devo visualizar zero publicações para a categoria', () => {
	cy.validar_contagem_publicacoes_categoria(categoriaSemPublicacoes.nome, 0)
})

//----------------------------Hooks----------------------------------------//

Before({ tags: '@validar_noticia_criada' }, () => {
	cy.realizar_login_intranet()
	cy.ir_para_formulario_adicionar_noticias()
	cy.preencher_titulo(novaNoticia.titulo)
	cy.preencher_subtitulo(novaNoticia.subtitulo)
	cy.preencher_conteudo_noticia(novaNoticia.conteudo)
	cy.preencher_resumo(novaNoticia.resumo)
	cy.clicar_botao_publicar()
	cy.obter_link_da_noticia()
})
Before({ tags: '@validar_noticia_editada' }, () => {
	cy.realizar_login_intranet()
	cy.visitar_listagem_noticias_intranet()
	cy.acessar_noticia_na_listagem_intranet(editarNoticia.titulo)
	cy.obter_link_da_noticia_editada()
})

Before({ tags: '@validar_exclusao_noticia' }, () => {})
