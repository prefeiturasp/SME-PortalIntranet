import { Given, When, Then } from 'cypress-cucumber-preprocessor/steps'

const Dado = Given
const Quando = When
const Entao = Then

Dado('eu acesso o sistema com a visualização {string}', function (device) {
	cy.login_intranet(device)
	cy.visit('/')
})

Dado('informo os dados de {string} e {string}', function (usuario, senha) {
	cy.dados_de_login(usuario, senha)
})

Quando('clico no botão acessar', function () {
	cy.clicar_botao()
})

Entao('devo visualizar a tela inicial do sistema logado', function () {
	cy.validar_visualização_conta_logada()
})

Entao(
	'devo visualizar a mensagem alertando obrigatoriedade no campo de usuário',
	function () {
		cy.validar_mensagem_obrigatoriedade_usuario()
	},
)
Entao(
	'devo visualizar a mensagem alertando obrigatoriedade no campo de senha',
	function () {
		cy.validar_mensagem_obrigatoriedade_senha()
	},
)

Entao(
	'devo visualizar a mensagem alertando usuario ou senha invalidos',
	function () {
		cy.validar_mensagem_usuario_ou_senha_invalidos()
	},
)
