# language: pt
Funcionalidade: Agenda de Eventos SME

  @novo_item
  Cenário: Deve validar os campos do novo item
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando adiciono um evento à Agenda de Eventos SME
    E seleciono o tipo de data por período
    Então devo visualizar os campos do novo item da Agenda de Eventos SME

  @novo_item
  Cenário: Deve criar evento com data única e todos os campos preenchidos
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando preencho todos os campos do evento com uma data única
    E publico o evento da Agenda de Eventos SME
    Então devo visualizar a confirmação de publicação do evento

  @novo_item 
  Cenário: Deve criar evento por período e todos os campos preenchidos
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando preencho todos os campos do evento por período
    E publico o evento da Agenda de Eventos SME
    Então devo visualizar a confirmação de publicação do evento

  @novo_item 
  Cenário: Deve impedir a publicação sem o Tipo do Evento
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando preencho os dados obrigatórios sem o Tipo do Evento
    E tento publicar o evento da Agenda de Eventos SME
    Então a publicação deve ser bloqueada pelo Tipo do Evento
    E o evento não deve constar como publicado na Agenda de Eventos SME

  @novo_item 
  Cenário: Deve impedir a publicação sem a Data do Evento
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando preencho os dados obrigatórios sem a Data do Evento
    E tento publicar o evento da Agenda de Eventos SME
    Então a publicação deve ser bloqueada pela Data do Evento
    E o evento não deve constar como publicado na Agenda de Eventos SME

  @novo_item
  Cenário: Deve impedir a publicação por período sem a Data Final
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando preencho um período sem a Data Final
    E tento publicar o evento da Agenda de Eventos SME
    Então a publicação deve ser bloqueada pela Data Final
    E o evento não deve constar como publicado na Agenda de Eventos SME

  @novo_item 
  Cenário: Deve criar dois compromissos no mesmo dia
    Dado que acesso o formulário de novo item da Agenda de Eventos SME
    Quando preencho uma publicação com dois compromissos no mesmo dia
    E publico o evento da Agenda de Eventos SME
    Então devo visualizar a confirmação de publicação do evento
    E os dois compromissos devem permanecer após recarregar a página
    E devo localizar a publicação com dois eventos na Agenda de Eventos SME
    E ao abrir os detalhes devo visualizar os dois compromissos

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
