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
	'CEI Jardim das Rosas',
	'Setor de Planejamento Pedagógico',
	'Secretaria da DRE Penha',
	'Setor de Tecnologia Educacional',
	'Coordenação de Projetos Especiais',
	'Biblioteca Escolar Central',
]

const pegarAleatorio = (lista) =>
	lista[Math.floor(Math.random() * lista.length)]

beforeEach(() => {
	cy.session('loginManual', () => {
		cy.visit(
			'https://hom-educacao.sme.prefeitura.sp.gov.br/wp-login.php?loggedout=true&wp_lang=pt_BR',
		)
		cy.get('#user_login').type('acessosme')
		cy.get('#user_pass').type('oXGZhkO9jZQ)oUoecUn&tdsC')
		cy.get('#wp-submit').click()
	})
})

describe('Preenchimento do segundo formulário com dados PT-BR válidos', () => {
	for (let i = 0; i < 10; i++) {
		it(`Submissão #${i + 1}`, () => {
			const cpfValido = gerarCpfValidoComMascara()
			const nomeCompleto = faker.person.fullName()
			const nomeEmail = nomeCompleto
				.toLowerCase()
				.replace(/[^a-zA-Z]/g, '.')
				.slice(0, 20)
			const emailInstitucional = `${nomeEmail}@edu.sme.prefeitura.sp.gov.br`
			// cy.visit(
			// 	'https://hom-educacao.sme.prefeitura.sp.gov.br/sorteio/2683-nova-noticia-teste-sorteio/',
			// )

			cy.visit(
				'https://hom-educacao.sme.prefeitura.sp.gov.br/sorteio/4143-sorteio-para-validar-demanda-137816-periodo/',
			)

			cy.get('#nomeComp').type(faker.person.fullName())
			cy.get('#emailInsti').type(emailInstitucional)
			cy.get('#cpf').type(cpfValido)
			//cy.get('#emailSec').type(faker.internet.email())
			cy.get('#emailSec').type('marcelo.jesus@spassu.com.br')
			cy.get('#celular').type(faker.phone.number('(##) ####-####'))
			cy.get('#telCom').type(faker.phone.number('(##) ####-####'))

			cy.get('#dre').select('DRE Itaquera')

			// Escolher aleatoriamente um dos três programas de estágio
			const programaIndex = Math.floor(Math.random() * 3) + 1
			cy.get(`#programa${programaIndex}`).check({ force: true })

			cy.get('#uniSetor').type(pegarAleatorio(unidadesSetoresPT))
			cy.get('#ciente').check()
			//cy.get('#grupo-datas .form-check-input').check(),

			cy.document().then((doc) => {
				const label = [...doc.querySelectorAll('label')].find((el) =>
					el.textContent.includes(
						'Selecione a(s) data(s) que deseja participar',
					),
				)

				if (label) {
					cy.get('.form-check-input[type="checkbox"]').each(($el) => {
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

			// Força o clique no botão desabilitado
			cy.get('#botaoEnviar').invoke('removeAttr', 'disabled').click()
		})
	}
})
