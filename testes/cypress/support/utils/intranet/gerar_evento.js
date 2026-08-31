import { fakerPT_BR as faker } from '@faker-js/faker'

function formatarData(data) {
	const dia = String(data.getDate()).padStart(2, '0')
	const mes = String(data.getMonth() + 1).padStart(2, '0')
	return `${dia}/${mes}/${data.getFullYear()}`
}

function gerarEvento() {
	const dataEvento = faker.date.soon({ days: 365 })
	const dataEventoFinal = new Date(dataEvento)
	dataEventoFinal.setDate(dataEventoFinal.getDate() + 4)

	const evento = {
		titulo: `Calendário Escolar ${faker.string.alphanumeric(8)}`,
		nome: faker.lorem.words(3),
		descricao: faker.lorem.sentence(),
		enderecoManual: `${faker.location.streetAddress()}, ${faker.location.city()}`,
		participantes: faker.person.fullName(),
		dataEvento: formatarData(dataEvento),
		dataEventoFinal: formatarData(dataEventoFinal),
	}
	return evento
}

export default gerarEvento
