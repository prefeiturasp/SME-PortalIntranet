import Login_Intranet_Localizadores from '../locators/login_locators'

const login_Intranet_Localizadores = new Login_Intranet_Localizadores()

Cypress.Commands.add('configurar_visualizacao', (device) => {
	switch (device) {
		case 'web':
			cy.viewport(1920, 1080)
			break
		default:
			break
	}
})

Cypress.Commands.add('realizar_login_intranet', () => {
	cy.session('sessão-intranet', () => {
		const usuario = Cypress.env('intranet').usuario
		const senha = Cypress.env('intranet').senha
		cy.visit('/')
		cy.get(login_Intranet_Localizadores.campo_usuario()).type(usuario)
		cy.get(login_Intranet_Localizadores.campo_senha()).type(senha)
		cy.clicar_botao()
	})
})
