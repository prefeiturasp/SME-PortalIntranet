import './commands/commands_login'
import './commands/commands_globais'
import './commands/commands_noticias'
import '@shelex/cypress-allure-plugin';
import "cypress-cloud/support";

Cypress.on('uncaught:exception', (err, runnable) => {
	return false
})
