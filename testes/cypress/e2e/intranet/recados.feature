# language: pt
Funcionalidade: Recados - Todos os Recados

  @recados_todos
  Cenário: Deve exibir as informações de um recado na listagem
    Dado que possuo um recado próprio publicado com categoria, tag e destaque na home
    Quando acesso Todos os Recados e localizo meu recado
    Então devo visualizar título, autor, destaque, categoria, tags e data do meu recado

  @recados_todos
  Cenário: Deve pesquisar um recado existente pelo título
    Dado que possuo um recado próprio publicado para Todos os Recados
    Quando pesquiso pelo título único do meu recado
    Então devo visualizar somente meu recado pesquisado

  @recados_todos
  Cenário: Deve persistir a edição do recado após recarregar
    Dado que possuo um recado próprio publicado para Todos os Recados
    Quando edito título, conteúdo e subtítulo do meu recado
    E salvo as alterações do meu recado
    Então as alterações do recado devem permanecer após recarregar e reabrir
