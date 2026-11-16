import { fakerPT_BR as faker } from '@faker-js/faker'

export const gerarRecado = (opcoes = {}) => {
	const identificador = faker.string.uuid()
	return {
		identificador,
		titulo: `QA Recado ${identificador}`,
		subtitulo: `Subtítulo ${faker.lorem.words(4)}`,
		conteudo: `Conteúdo do recado ${identificador}. ${faker.lorem.sentence()}`,
		categoria: `QA Categoria Recado ${identificador}`,
		tag: `qa-recado-${identificador}`,
		destaque: false,
		comTaxonomias: false,
		...opcoes,
	}
}
