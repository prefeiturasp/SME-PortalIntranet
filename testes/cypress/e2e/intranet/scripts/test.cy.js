/// <reference types="cypress" />

import { fakerPT_BR as faker } from '@faker-js/faker'

function gerarCpfValidoComMascara() {
	const rand = () => Math.floor(Math.random() * 9)
	const calcDV = (nums) => {
		let sum = nums.reduce(
			(acc, num, idx) => acc + num * (nums.length + 1 - idx),
			0,
		)
		let rest = sum % 11
		return rest < 2 ? 0 : 11 - rest
	}

	let n = Array.from({ length: 9 }, rand)
	n.push(calcDV(n))
	n.push(calcDV(n))

	return `${n[0]}${n[1]}${n[2]}.${n[3]}${n[4]}${n[5]}.${n[6]}${n[7]}${n[8]}-${n[9]}${n[10]}`
}

const cargosPT = [
	'Professor',
	'Coordenador Pedagógico',
	'Diretor Escolar',
	'Assistente Técnico Educacional',
	'Supervisor de Ensino',
	'Auxiliar de Secretaria',
	'Agente Escolar',
	'Orientador Educacional',
]

const disciplinasPT = [
	'Matemática',
	'Português',
	'História',
	'Geografia',
	'Ciências',
	'Educação Física',
	'Inglês',
	'Artes',
	'Filosofia',
	'Sociologia',
]

const unidadesSetoresPT = [
	'E.M.E.F. José de Anchieta',
	'E.M.E.I. Pequeno Príncipe',
	'Centro de Educação Infantil Jardim das Rosas',
	'Setor de Planejamento Pedagógico',
	'Departamento de Recursos Humanos',
	'Secretaria da DRE Penha',
	'Núcleo de Apoio ao Educando',
	'Setor de Tecnologia Educacional',
	'Coordenação de Projetos Especiais',
	'Biblioteca Escolar Central',
]

// Função para pegar aleatório
const pegarAleatorio = (lista) =>
	lista[Math.floor(Math.random() * lista.length)]

beforeEach(() => {
	cy.session('loginManual', () => {
		cy.visit('https://hom-intranet.sme.prefeitura.sp.gov.br/index.php')
		cy.get('#user').type('309395')
		cy.get('#pass').type('Sgp@1234')
		cy.get('#wp-submit').click()
	})
})

describe('Preenchimento automático do formulário SME', () => {
	for (let i = 0; i < 50; i++) {
		const cpfValido = gerarCpfValidoComMascara()
		// você pode ajustar esse número conforme o número de vezes desejado
		it(`Submissão #${i + 1}`, () => {
			// cy.visit(
			// 	'https://hom-intranet.sme.prefeitura.sp.gov.br/index.php/nova-noticia-teste-sorteio/',
			// )

			cy.visit(
				'https://hom-intranet.sme.prefeitura.sp.gov.br/index.php/sroteio-para-validar-demanda-143589-premiacao/',
			)
			cy.get('#nomeComp').invoke('val', faker.person.fullName())
			cy.get('#emailInsti').invoke('val', faker.internet.email())
			cy.get('#cpf').invoke('val', cpfValido)
			cy.get('#emailSec').invoke('val', 'marcelo.jesus@spassu.com.br')
			cy.get('#celular').invoke('val', faker.phone.number('(##) ####-####'))
			cy.get('#telCom').invoke('val', faker.phone.number('(##) ####-####'))

			cy.get('#dre').select(1)

			cy.get('#cargo_principal').invoke('val', pegarAleatorio(cargosPT))
			cy.get('#uniSetor').invoke('val', pegarAleatorio(unidadesSetoresPT))
			cy.get('#disciplina').invoke('val', pegarAleatorio(disciplinasPT))
			cy.get('#ciente').check()
			//cy.get('#grupo-datas .form-check-input').check()

			cy.document().then((doc) => {
				const label = [...doc.querySelectorAll('label')].find((el) =>
					el.textContent.includes(
						'Selecione a(s) data(s) que deseja participar',
					),
				)

				if (label) {
					cy.get('#grupo-datas .form-check-input').each(($el) => {
						cy.wrap($el).check({ force: true })
					})
				} else {
					cy.log('Label não existe')
				}
			})

			cy.get('body').then(($body) => {
				const texto = 'Selecione os prêmios que deseja participar do sorteio:'

				if ($body.text().includes(texto)) {
					// Pega todos os checkboxes dentro do grupo
					cy.get('#grupo-datas .form-check-input').then(($checks) => {
						if ($checks.length > 0) {
							cy.log(
								`Foram encontrados ${$checks.length} checkboxes. Marcando todos...`,
							)
							cy.wrap($checks).check({ force: true })
						} else {
							cy.log('Nenhum checkbox encontrado dentro do grupo.')
						}
					})
				} else {
					cy.log('Texto não encontrado, nenhum checkbox será clicado.')
				}
			})

			cy.get('body').then(($body) => {
				const texto = 'Selecione os prêmios que deseja participar do sorteio:'

				if ($body.text().includes(texto)) {
					// Pega todos os checkboxes dentro do grupo
					cy.get('#grupo-datas .form-check-input').then(($checks) => {
						if ($checks.length > 0) {
							cy.log(
								`Foram encontrados ${$checks.length} checkboxes. Marcando todos...`,
							)
							cy.wrap($checks).check({ force: true })
						} else {
							cy.log('Nenhum checkbox encontrado dentro do grupo.')
						}
					})
				} else {
					cy.log('Texto não encontrado, nenhum checkbox será clicado.')
				}
			})

			cy.get('body').then(($body) => {
				const texto = 'Selecione a data que deseja participar:'
				const $grupo = $body.find('#grupo-data-selecionada')

				if ($grupo.length > 0) {
					if ($grupo.text().includes(texto)) {
						const $radios = $grupo.find('.form-check-input[type="radio"]')

						if ($radios.length > 0) {
							cy.log(
								`Foram encontrados ${$radios.length} radios. Marcando o primeiro...`,
							)
							cy.wrap($radios.first()).check({ force: true })
						} else {
							cy.log('Nenhum radio button encontrado dentro do grupo.')
						}
					} else {
						cy.log('Texto não encontrado dentro do grupo-data-selecionada.')
					}
				} else {
					cy.log(
						'Grupo #grupo-data-selecionada não encontrado. O teste continua sem falhar.',
					)
				}
			})

			cy.get('body').then(($body) => {
				const texto = 'Selecione o prêmio que deseja participar:'
				const $grupo = $body.find('#grupo-datas')

				if ($grupo.length > 0) {
					if ($grupo.text().includes(texto)) {
						const $radios = $grupo.find('.form-check-input[type="radio"]')

						if ($radios.length > 0) {
							cy.log(
								`Foram encontrados ${$radios.length} radios. Marcando o primeiro...`,
							)
							cy.wrap($radios.first()).check({ force: true })
						} else {
							cy.log('Nenhum radio button encontrado dentro do grupo-datas.')
						}
					} else {
						cy.log('Texto não encontrado dentro do grupo-datas.')
					}
				} else {
					cy.log(
						'Grupo #grupo-datas não encontrado. O teste continua sem falhar.',
					)
				}
			})

			cy.get('#botaoEnviar').click() // força o envio
		})
	}
})
