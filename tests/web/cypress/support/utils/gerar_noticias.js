import { faker } from '@faker-js/faker'
faker.locale = 'pt_BR'

function gerarNoticia() {
	const noticia = {
		titulo: faker.lorem.words(2),
		subtitulo: faker.lorem.words(2),
		conteudo: faker.lorem.words(2),
		resumo: faker.lorem.words(2),
	}
	return noticia
}

export default gerarNoticia
