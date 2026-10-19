import { fakerPT_BR as faker } from '@faker-js/faker'

const formatarData = (data) => {
	const dia = String(data.getDate()).padStart(2, '0')
	const mes = String(data.getMonth() + 1).padStart(2, '0')

	return `${dia}/${mes}/${data.getFullYear()}`
}

const adicionarDias = (data, quantidadeDias) => {
	const novaData = new Date(data)
	novaData.setDate(novaData.getDate() + quantidadeDias)

	return novaData
}

const gerarEventoAgendaSme = () => {
	const dataEvento = faker.date.soon({ days: 365 })
	const dataEventoFinal = adicionarDias(dataEvento, 4)

	return {
		titulo: `Agenda de Eventos SME ${faker.string.alphanumeric(8)}`,
		nome: faker.lorem.words(3),
		descricao: faker.lorem.sentence(),
		enderecoManual: `${faker.location.streetAddress()}, ${faker.location.city()}`,
		participantes: faker.person.fullName(),
		dataEvento: formatarData(dataEvento),
		dataEventoFinal: formatarData(dataEventoFinal),
	}
}

export const gerarCompromissoAgendaSme = (opcoes = {}) => {
	const identificador = faker.string.uuid()
	return {
		nome: `Compromisso SME ${identificador}`,
		slug: `compromisso-sme-${identificador}`,
		descricao: faker.lorem.sentence(),
		ascendente: 'Nenhum',
		contagem: '0',
		...opcoes,
	}
}

export default gerarEventoAgendaSme
