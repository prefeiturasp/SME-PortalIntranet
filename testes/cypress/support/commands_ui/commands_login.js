import Login_Intranet_Localizadores from '../locators/login_locators'

const login_Intranet_Localizadores = new Login_Intranet_Localizadores()

Cypress.Commands.add('login_intranet', (device) => {
	cy.configurar_visualizacao(device)
	cy.visit('/')
})

// Resolve placeholders do .feature (ex.: {{INTRANET_USUARIO}}) a partir do .env,
// evitando credenciais em texto puro nos cenarios.
const resolver_credencial = (valor) => {
	const credenciais = {
		'{{INTRANET_USUARIO}}': Cypress.env('intranet')?.usuario,
		'{{INTRANET_SENHA}}': Cypress.env('intranet')?.senha,
	}
	return Object.prototype.hasOwnProperty.call(credenciais, valor)
		? credenciais[valor]
		: valor
}

Cypress.Commands.add('dados_de_login', (usuario, senha) => {
	const usuarioResolvido = resolver_credencial(usuario)
	const senhaResolvida = resolver_credencial(senha)

	usuarioResolvido
		? cy
				.get(login_Intranet_Localizadores.campo_usuario())
				.type(usuarioResolvido, { force: true })
		: ''
	senhaResolvida
		? cy
				.get(login_Intranet_Localizadores.campo_senha())
				.type(senhaResolvida, { force: true, log: false })
		: ''
})

Cypress.Commands.add('clicar_botao', () => {
	cy.get(login_Intranet_Localizadores.botao_acessar())
		.should('be.visible')
		.click()
})

Cypress.Commands.add('validar_visualização_conta_logada', () => {
	cy.get(login_Intranet_Localizadores.barra_de_usuario_logado())
		.should('be.visible')
		.and('contain', 'Olá, ')
})

Cypress.Commands.add('validar_mensagem_obrigatoriedade_usuario', () => {
	cy.get(login_Intranet_Localizadores.mensagem_obrigatoriedade_usuario())
		.should('be.visible')
		.and('contain', 'Insira o seu RF/Usuário')
})
Cypress.Commands.add('validar_mensagem_obrigatoriedade_senha', () => {
	cy.get(login_Intranet_Localizadores.mensagem_obrigatoriedade_senha())
		.should('be.visible')
		.and('contain', 'Campo obrigatório')
})
Cypress.Commands.add('validar_mensagem_usuario_ou_senha_invalidos', () => {
	cy.get(login_Intranet_Localizadores.mensagem_usuario_ou_senha_invalidos())
		.should('be.visible')
		.and('contain', 'Usuário e/ou senha inválidos.')
})
