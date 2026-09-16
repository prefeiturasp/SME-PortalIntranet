# language: pt
Funcionalidade: Agenda de Eventos SME

  # @novo_item
  # Cenário: Deve validar os campos do novo item
  #   Dado que acesso o formulário de novo item da Agenda de Eventos SME
  #   Quando adiciono um evento à Agenda de Eventos SME
  #   E seleciono o tipo de data por período
  #   Então devo visualizar os campos do novo item da Agenda de Eventos SME

  # @novo_item
  # Cenário: Deve criar evento com data única e todos os campos preenchidos
  #   Dado que acesso o formulário de novo item da Agenda de Eventos SME
  #   Quando preencho todos os campos do evento com uma data única
  #   E publico o evento da Agenda de Eventos SME
  #   Então devo visualizar a confirmação de publicação do evento

  # @novo_item 
  # Cenário: Deve criar evento por período e todos os campos preenchidos
  #   Dado que acesso o formulário de novo item da Agenda de Eventos SME
  #   Quando preencho todos os campos do evento por período
  #   E publico o evento da Agenda de Eventos SME
  #   Então devo visualizar a confirmação de publicação do evento

  @todos_os_eventos
  Cenário: Deve pesquisar evento pelo título
    Dado que possuo um evento publicado na Agenda de Eventos SME
    E acesso a listagem de eventos da Agenda de Eventos SME
    Quando pesquiso o evento pelo título
    Então devo visualizar somente o evento pesquisado

  @todos_os_eventos
  Cenário: Deve visualizar na listagem um evento publicado
    Dado que possuo um evento publicado na Agenda de Eventos SME
    Quando acesso a listagem de eventos da Agenda de Eventos SME
    Então devo visualizar o evento publicado na listagem

  @todos_os_eventos
  Cenário: Deve editar um item publicado
    Dado que possuo um evento publicado na Agenda de Eventos SME
    E acesso a listagem de eventos da Agenda de Eventos SME
    Quando edito o item publicado da Agenda de Eventos SME
    E salvo as alterações do evento da Agenda de Eventos SME
    Então devo visualizar a confirmação de atualização do evento
    E devo visualizar o evento atualizado na listagem

