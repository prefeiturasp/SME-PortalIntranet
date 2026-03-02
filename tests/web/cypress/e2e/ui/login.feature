      # language: pt
      Funcionalidade: Login

      backgroud

      Esquema do Cenário: Deve validar Login com sucesso
      Dado eu acesso o sistema com a visualização "<device>"
      E informo os dados de "<usuario>" e "<senha>"
      Quando clico no botão acessar
      Então devo visualizar a tela inicial do sistema logado

      Exemplos:
      | usuario     | senha              | device |
      | smeintranet | h*%VE&tsIR1vURB@70 | web    |    


      Cenário: Deve validar mensagem de obrigatoriedade do campo usuário
      Dado eu acesso o sistema com a visualização "web"
      E informo os dados de "" e "senha"
      Quando clico no botão acessar
      Então devo visualizar a mensagem alertando obrigatoriedade no campo de usuário

      Cenário: Deve validar mensagem de obrigatoriedade do campo senha
      Dado eu acesso o sistema com a visualização "web"
      E informo os dados de "usuario" e ""
      Quando clico no botão acessar
      Então devo visualizar a mensagem alertando obrigatoriedade no campo de senha

      Cenário: Deve validar mensagem de usuario ou senha invalidos
      Dado eu acesso o sistema com a visualização "web"
      E informo os dados de "usuario123" e "senhateste"
      Quando clico no botão acessar
      Então devo visualizar a mensagem alertando usuario ou senha invalidos