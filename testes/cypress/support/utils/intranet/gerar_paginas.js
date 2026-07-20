import { fakerPT_BR as faker } from '@faker-js/faker'

function gerarPagina() {
	const pagina = {
		titulo: faker.lorem.words(2),
		conteudo: faker.lorem.words(2),
	}
	return pagina
}

export default gerarPagina
