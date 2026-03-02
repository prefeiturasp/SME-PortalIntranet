      # language: pt
      Funcionalidade: Noticias

      backgroud

      Cenário: Deve validar publicação de noticia com sucesso
      Dado eu realizo login na intranet no wp-admin 
      E acesso a página de adição de notícias
      Quando preencho todos os campos do formulário
      E clico no botão publicar
      Então devo visualizar a mensagem informando que o post foi publicado com sucesso

      Cenário: Deve validar mensagem de obrigatoriedade do campo de subtitulo
      Dado eu realizo login na intranet no wp-admin 
      E acesso a página de adição de notícias
      Quando eu não preencho o campo subtitulo 
      E clico no botão publicar
      Então devo visualizar a mensagem informando que o campo de subtitulo é obrigatório

      Cenário: Deve validar se a notícia foi publicada no portal da intranet
      Dado eu publiquei uma notícia 
      Quando eu clico na URL da notícia criada
      Então devo visualizar a notícia publicada no portal da intranet

      @validar_noticia_criada

      Cenário: Deve validar exibição do título da notícia publicada no portal da intranet
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a notícia criada na intranet
      Então devo visualizar a exibição do título da notícia publicada no portal da intranet

      Cenário: Deve validar exibição do subtítulo da notícia publicada no portal da intranet
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a notícia criada na intranet
      Então devo visualizar a exibição do subtítulo da notícia publicada no portal da intranet

      Cenário: Deve validar exibição do corpo da notícia publicada no portal da intranet
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a notícia criada na intranet
      Então devo visualizar a exibição do corpo da notícia publicada no portal da intranet

      Cenário: Deve validar exibição da notícia criada na listagem da intranet
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a listagem de notícias na intranet
      Então devo visualizar o título da notícia na listagem da intranet

      Cenário: Deve validar edição de uma notícia  
      Dado eu realizo login na intranet no wp-admin
      E acesso uma notícia publicada
      Quando edito todos os campos do formulário 
      E clico no botão publicar
      Então devo visualizar a mensagem informando que o post foi atualizado com sucesso

      @validar_noticia_editada

      Cenário: Deve validar exibição do título da notícia publicada no portal da intranet após edição
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a notícia editada na intranet
      Então devo visualizar a exibição do título da notícia editada no portal da intranet

      Cenário: Deve validar exibição do subtítulo da notícia publicada no portal da intranet após edição
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a notícia editada na intranet
      Então devo visualizar a exibição do subtítulo da notícia editada no portal da intranet

      Cenário: Deve validar exibição do corpo da notícia publicada no portal da intranet após edição
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a notícia editada na intranet
      Então devo visualizar a exibição do corpo da notícia editada no portal da intranet

      @validar_exclusao_noticia

      Cenário: Deve validar envio da notícia para a lixeira
      Dado eu acesso a listagem de noticias no wp-admin
      Quando eu envio a noticia para a lixeira
      Então devo visualizar a mensagem de exclusão da notícia com sucesso

      Cenário: Deve validar a não exibição da notícia que foi enviada para a lixeira
      Dado eu acesso a listagem de noticias no wp-admin
      Quando eu pesquiso a noticia que foi enviada para a lixeira
      Então não devo visualizar a notícia na listagem

      Cenário: Deve validar a remoção da página da notícia que foi enviada para a lixeira
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a pagina da notícia que foi enviada para a lixeira
      Então não devo visualizar a exibição da notícia no portal da intranet

      Cenário: Deve validar a exclusão permanente da página da notícia que foi enviada para a lixeira
      Dado eu realizo login na intranet no wp-admin
      Quando eu acesso a lixeira
      E excluo a notícia permanente
      Então devo visualizar a mensagem informando que o post foi excluído permanentemente





      