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

// Edite esta URL para escolher a cortesia ou o sorteio que será testado.
// Também pode sobrescrever pela linha de comando: --env URL_EVENTO=https://...
const urlEvento =
	Cypress.env('URL_EVENTO') ||
	'https://hom-intranet.sme.prefeitura.sp.gov.br/index.php/cortesias/ordem-de-inscricao-para-validar-demanda-156782-2-data-definida-2/'
const paginaEvento = new URL(urlEvento, Cypress.config('baseUrl')).href
// Altere este número para definir quantas inscrições o teste fará.
const repeticoes = 5

function selecionarOpcoesEvento() {
	cy.get('#form-inscri')
		.should('be.visible')
		.then(($form) => {
			// Sorteios usam checkboxes; cortesias usam radios nos dois grupos.
			const $grupos = $form.find('#grupo-datas, #grupo-data-selecionada')
			$grupos.each((_indice, grupo) => {
				const $opcoes = Cypress.$(grupo)
					.find('input[type="checkbox"], input[type="radio"]')
					.filter(':visible:enabled')
				expect(
					$opcoes.length,
					'opções disponíveis para data ou prêmio',
				).to.be.greaterThan(0)

				const $checks = $opcoes.filter('[type="checkbox"]')
				if ($checks.length) {
					cy.wrap($checks).check().should('be.checked')
				}

				// Seleciona apenas uma opção por grupo de radios (atributo name).
				const nomes = [
					...new Set(
						$opcoes
							.filter('[type="radio"]')
							.toArray()
							.map((el) => el.name),
					),
				]
				nomes.forEach((nome) => {
					const $radio = $opcoes
						.filter('[type="radio"]')
						.filter((_i, el) => el.name === nome)
						.first()
					cy.wrap($radio).check().should('be.checked')
				})
			})

			// No período não h? opção para marcar: a data vem em um input oculto.
			const $dataOculta = $form.find('input[type="hidden"][name="data"]')
			if ($dataOculta.length) {
				cy.wrap($dataOculta).should('not.have.value', '')
			}
		})
}

function preencherQuantidade() {
	cy.get('#form-inscri').then(($form) => {
		const $qtd = $form.find('#qtd')
		// Sorteios não possuem quantidade; algumas cortesias já a trazem oculta.
		if (!$qtd.length) return
		if ($qtd.is('[type="hidden"]')) {
			expect(Number($qtd.val()), 'quantidade predefinida').to.be.greaterThan(0)
			return
		}

		const minimo = Number($qtd.attr('min') || 1)
		const maximo = $qtd.attr('max')
		if (maximo !== undefined && maximo !== '') {
			expect(Number(maximo), 'limite de quantidade disponível').to.be.at.least(
				minimo,
			)
		}
		cy.wrap($qtd)
			.should('be.visible')
			.clear()
			.type(String(minimo))
			.trigger('change')
	})
}

describe('Preenchimento automático de ordens de inscrição e sorteios SME', () => {
	// Repete as submissões na URL configurada acima.
	for (let i = 0; i < repeticoes; i++) {
		const pagina = paginaEvento
		const slug = new URL(pagina).pathname.split('/').filter(Boolean).pop()
		it(`Submissão #${i + 1} - ${slug}`, () => {
			const cpfValido = gerarCpfValidoComMascara()
			cy.visit(pagina)
			cy.get('#form-inscri').should('be.visible')
			cy.get('#nomeComp')
				.invoke('val', faker.person.fullName())
				.trigger('change')
			cy.get('#emailInsti')
				.invoke('val', faker.internet.email())
				.trigger('change')
			cy.get('#cpf').invoke('val', cpfValido).trigger('change')
			cy.get('#emailSec')
				.invoke('val', 'marcelo.jesus@spassu.com.br')
				.trigger('change')
			cy.get('#celular')
				.invoke('val', faker.phone.number('(##) 9####-####'))
				.trigger('change')
			cy.get('#telCom')
				.invoke('val', faker.phone.number('(##) ####-####'))
				.trigger('change')
			cy.get('#dre').select(1)
			cy.get('#cargo_principal')
				.invoke('val', pegarAleatorio(cargosPT))
				.trigger('change')
			cy.get('#uniSetor')
				.invoke('val', pegarAleatorio(unidadesSetoresPT))
				.trigger('change')
			cy.get('#disciplina')
				.invoke('val', pegarAleatorio(disciplinasPT))
				.trigger('change')

			selecionarOpcoesEvento()
			preencherQuantidade()
			cy.get('#ciente').check().should('be.checked')
			cy.get('#botaoEnviar').should('be.enabled').click()
			cy.contains('Confirme seus e-mails').should('be.visible')
			cy.contains('button', /^Sim$/)
				.should('be.visible')
				.should('be.enabled')
				.click()
			cy.contains('Sua inscrição foi realizada com sucesso!').should(
				'be.visible',
			)
		})
	}
})
