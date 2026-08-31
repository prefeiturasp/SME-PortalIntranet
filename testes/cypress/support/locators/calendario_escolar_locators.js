export class Menu_Calendario_Escolar_Localizadores {
	menu_calendario_escolar = () => '#menu-posts-agendanew'
	link_menu_calendario_escolar = () => '#menu-posts-agendanew > a'
	submenu_todos_os_eventos = () =>
		'#menu-posts-agendanew .wp-submenu-wrap a[href="edit.php?post_type=agendanew"]'
	submenu_novo_item = () =>
		'#menu-posts-agendanew .wp-submenu-wrap a[href="post-new.php?post_type=agendanew"]'
}

export class Adicionar_Calendario_Escolar_Localizadores {
	painel_publicar = () => '#submitdiv'
	painel_imagem_destacada = () => '#postimagediv'
	painel_calendario_escolar = () => '#acf-group_618d768f3821d'
	painel_grupos_usuarios = () => '#acf-group_620bb83997f6c'
	aviso_inicial = () => '.notice-success.inline'

	campo_titulo = () => '#title'
	botao_salvar_rascunho = () => '#save-post'
	botao_visualizar = () => '#post-preview'
	botao_publicar = () => '#publish'
	mensagem_sucesso = () => '#message'
	link_imagem_destacada = () => '#set-post-thumbnail'

	// Calendário e datas
	checkbox_tipo_periodo = () => '#acf-field_6391fee3b4fba'
	campo_data_inicio = () =>
		'.acf-field[data-key="field_61940b357ddd3"] input.hasDatepicker'
	campo_data_fim = () =>
		'.acf-field[data-key="field_6390edd164c4d"] input.hasDatepicker'
	campo_data_evento = () => '#acf-field_61940b357ddd3'
	campo_data_evento_final = () => '#acf-field_6390edd164c4d'
	campo_eventos_do_dia = () => '.acf-field[data-key="field_618d77ccf3fdf"]'
	link_adicionar_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] .acf-actions a[data-event="add-row"]'
	linhas_eventos = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] table.acf-table > tbody > tr.acf-row:not(.acf-clone)'
	botao_remover_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) a[data-event="remove-row"]'

	// Campos do primeiro evento do dia
	select_tipo_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_618d77f0f3fe0"] select'
	campo_nome_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_618ea638a3274"] input'
	campo_hora_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_618ea4ff8e7e4"] input.input'
	campo_fim_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_618ea53c8e7e5"] input.input'
	campo_descricao_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_618ea5578e7e6"] textarea'
	select_endereco_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_6193e1800f0a1"] select'
	campo_endereco_manual = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_6193f0a1d7a1e"] input'
	iframe_participantes_evento = () =>
		'.acf-field[data-key="field_6193c14c3131f"] iframe'
	textarea_participantes_evento = () =>
		'.acf-field[data-key="field_618d77ccf3fdf"] tr.acf-row:not(.acf-clone) .acf-field[data-key="field_6193c14c3131f"] textarea.wp-editor-area'

	// Visão dos grupos de usuários
	toggle_servidores_publicos = () => '#acf-field_620bb843d5755'
	toggle_rede_parceira = () => '#acf-field_620bb86cd5757'
}

export class Lista_Calendario_Escolar_Localizadores {
	botao_novo_item = () => '#wpbody-content .wrap > a.page-title-action'
	botao_opcoes_tela = () => '#show-settings-link'
	aba_opcoes_tela = () => '#screen-options-wrap'
	botao_aplicar_opcoes_tela = () => '#screen-options-apply'

	campo_busca = () => '#post-search-input'
	botao_buscar = () => '#search-submit'

	select_acoes_massa_top = () => '#bulk-action-selector-top'
	botao_aplicar_massa_top = () => '#doaction'
	select_acoes_massa_bot = () => '#bulk-action-selector-bottom'
	botao_aplicar_massa_bot = () => '#doaction2'

	select_categoria_filtro = () => '.tablenav.top .actions select:eq(0)'
	select_mes_filtro = () => '.tablenav.top .actions select:eq(1)'
	select_ano_filtro = () => '.tablenav.top .actions select:eq(2)'
	botao_filtrar = () => '#post-query-submit'

	input_pagina_atual = () => '#current-page-selector'
	info_total_paginas = () => '.total-pages'
	botao_pagina_proxima = () => '.next-page'
	botao_pagina_ultima = () => '.last-page'
	contador_itens = () => '.displaying-num'

	checkbox_selecionar_todos_top = () => '#cb-select-all-1'
	checkbox_selecionar_todos_bot = () => '#cb-select-all-2'

	tabela_eventos = () => '.wp-list-table.posts'
	linhas_eventos = () => '#the-list > tr'
	celulas_eventos = () => '#the-list > tr > td'
	linha_por_id = (id) => `#post-${id}`
	checkbox_evento_por_id = (id) => `#cb-select-${id}`

	cabecalho_titulo = () => 'th#title a'
	cabecalho_autor = () => 'th#author'
	cabecalho_data_publicacao = () => 'th#date a'
	cabecalho_data_evento = () => 'th#data_evento a'
	cabecalho_quantidade_eventos = () => 'th#eventos'

	link_editar_por_id = (id) => `#post-${id} .row-actions .edit a`
	link_visualizar_por_id = (id) => `#post-${id} .row-actions .view a`
	link_lixeira_por_id = (id) => `#post-${id} .row-actions .trash a`
	link_lixeira = () => '.row-actions .trash a'
	link_restaurar_primeiro = () =>
		'#the-list > tr:first-child .row-actions .untrash a'
	link_excluir_permanentemente_primeiro = () =>
		'#the-list > tr:first-child .row-actions .delete a'
	excluir_permanentemente = () => '.delete .submitdelete'
	link_editar_primeiro = () => '#the-list > tr:first-child .row-actions .edit a'
	link_visualizar_primeiro = () =>
		'#the-list > tr:first-child .row-actions .view a'
	checkbox_primeiro_evento = () =>
		'#the-list > tr:first-child th.check-column input'

	mensagem_sucesso = () => '#message'
}
