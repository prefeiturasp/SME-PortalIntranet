# language: pt
Funcionalidade: Calendário Escolar

  @todos_os_eventos
  Cenário: Deve pesquisar evento pelo título
    Dado eu acesso a listagem do calendário escolar no wp-admin
    Quando eu pesquiso um evento pelo título
    Então devo visualizar o resultado da pesquisa do calendário escolar

  @todos_os_eventos
  Cenário: Deve editar um evento existente
    Dado eu acesso a listagem do calendário escolar no wp-admin
    Quando eu edito o primeiro evento do calendário escolar
    Então devo visualizar o formulário de edição do calendário escolar

  @todos_os_eventos
  Cenário: Deve visualizar um evento publicado
    Dado eu acesso a listagem do calendário escolar no wp-admin
    Quando eu visualizo o primeiro evento publicado do calendário escolar
    Então devo visualizar o evento do calendário escolar no portal

  @todos_os_eventos
  Cenário: Deve editar um item publicado
    Dado eu acesso ao formulário de edição de um evento do calendário escolar
    Quando eu altero o título do calendário escolar
    E eu salvo a alteração do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar

  @todos_os_eventos
  Cenário: Deve acessar o item publicado no portal
    Dado eu acesso a listagem do calendário escolar no wp-admin
    Quando eu visualizo o primeiro evento publicado do calendário escolar
    Então devo visualizar o evento do calendário escolar no portal

  @novo_item
  Cenário: Deve validar os campos do novo item
    Dado eu acesso ao formulário de novo item do calendário escolar
    Então devo visualizar os campos do calendário escolar

  @novo_item
  Cenário: Deve criar evento com data única e todos os campos preenchidos
    Dado eu acesso ao formulário de novo item do calendário escolar
    Quando eu crio um evento com data única e todos os campos preenchidos
    E eu publico o item do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar

  @novo_item
  Cenário: Deve criar evento por período e todos os campos preenchidos
    Dado eu acesso ao formulário de novo item do calendário escolar
    Quando eu crio um evento por período e todos os campos preenchidos
    E eu publico o item do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar

  @novo_item
  Cenário: Deve criar evento com servidores públicos definido como sim
    Dado eu acesso ao formulário de novo item do calendário escolar
    Quando eu crio um evento com servidores públicos definido como sim
    E eu publico o item do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar

  @novo_item
  Cenário: Deve criar evento com servidores públicos definido como não
    Dado eu acesso ao formulário de novo item do calendário escolar
    Quando eu crio um evento com servidores públicos definido como não
    E eu publico o item do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar

  @novo_item
  Cenário: Deve criar evento com profissionais da rede parceira definido como sim
    Dado eu acesso ao formulário de novo item do calendário escolar
    Quando eu crio um evento com profissionais da rede parceira definido como sim
    E eu publico o item do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar

  @novo_item
  Cenário: Deve criar evento com profissionais da rede parceira definido como não
    Dado eu acesso ao formulário de novo item do calendário escolar
    Quando eu crio um evento com profissionais da rede parceira definido como não
    E eu publico o item do calendário escolar
    Então devo visualizar a mensagem de sucesso do calendário escolar
