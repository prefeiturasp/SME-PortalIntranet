import { fakerPT_BR as faker } from '@faker-js/faker'

function gerarPagina() {
	const pagina = {
		titulo: faker.lorem.words(2),
		conteudo: faker.lorem.words(2),
	}
	return pagina
}

function gerarConteudo() {
	const conteudo = {
		nome: faker.lorem.words(2),
		descricao: faker.lorem.words(2),
	}
	return conteudo
}

export { gerarConteudo }
export default gerarPagina
