/// <reference types="cypress" />

import { fakerPT_BR as faker } from "@faker-js/faker";

function gerarCpfValidoComMascara() {
    const rand = () => Math.floor(Math.random() * 9);
    const calcDV = (nums) => {
        let sum = nums.reduce(
            (acc, num, idx) => acc + num * (nums.length + 1 - idx),
            0,
        );
        let rest = sum % 11;
        return rest < 2 ? 0 : 11 - rest;
    };

    let n = Array.from({ length: 9 }, rand);
    n.push(calcDV(n));
    n.push(calcDV(n));

    return `${n[0]}${n[1]}${n[2]}.${n[3]}${n[4]}${n[5]}.${n[6]}${n[7]}${n[8]}-${n[9]}${n[10]}`;
}

const cargosPT = [
    "Professor",
    "Coordenador Pedagógico",
    "Diretor Escolar",
    "Assistente Técnico Educacional",
    "Supervisor de Ensino",
    "Auxiliar de Secretaria",
    "Agente Escolar",
    "Orientador Educacional",
];

const disciplinasPT = [
    "Matemática",
    "Português",
    "História",
    "Geografia",
    "Ciências",
    "Educação Física",
    "Inglês",
    "Artes",
    "Filosofia",
    "Sociologia",
];

const unidadesSetoresPT = [
    "E.M.E.F. José de Anchieta",
    "E.M.E.I. Pequeno Príncipe",
    "Centro de Educação Infantil Jardim das Rosas",
    "Setor de Planejamento Pedagógico",
    "Departamento de Recursos Humanos",
    "Secretaria da DRE Penha",
    "Núcleo de Apoio ao Educando",
    "Setor de Tecnologia Educacional",
    "Coordenação de Projetos Especiais",
    "Biblioteca Escolar Central",
];

// Função para pegar aleatório
const pegarAleatorio = (lista) =>
    lista[Math.floor(Math.random() * lista.length)];

describe("Inscrever usuários na oportunidade 5515", () => {
    const senhaPadrao = "h*%VE&tsIR1vURB@70";

    const urlLogin = "https://hom-intranet.sme.prefeitura.sp.gov.br/index.php";
    const urlOportunidade =
        "https://hom-intranet.sme.prefeitura.sp.gov.br/oportunidade/5515/";

    const usuarios = [
        "6904335",
        "7976607",
        "6943705",
        "7776659",
        "7823151",
        "5376335",
        "7930518",
        "8053669",
        "8224501",
        "8358508",
        "8284423",
    ];

    function loginComRf(rf) {
        cy.clearCookies();
        cy.clearLocalStorage();

        cy.visit(urlLogin);

        cy.get("#user").should("be.visible").clear().type(rf);

        cy.get("#pass")
            .should("be.visible")
            .clear()
            .type(senhaPadrao, { parseSpecialCharSequences: false });

        cy.get("#wp-submit").should("be.visible").click();

        cy.location("href", { timeout: 20000 }).should(
            "not.include",
            "wp-login",
        );
    }

    function clicarSeExistir(selector) {
        cy.get("body").then(($body) => {
            const elemento = $body.find(selector);

            if (elemento.length && elemento.is(":visible")) {
                cy.get(selector).click({ force: true });
            }
        });
    }

    usuarios.forEach((rf) => {
        it(`Deve logar com o RF ${rf} e se inscrever na oportunidade 5515`, () => {
            loginComRf(rf);

            cy.visit(urlOportunidade);

            cy.contains(
                "button.btn-inscricao",
                "Inscrever-se nesta Oportunidade",
                {
                    timeout: 20000,
                },
            )
                .should("be.visible")
                .scrollIntoView()
                .click({ force: true });

            cy.get(".swal2-popup", { timeout: 10000 }).should("be.visible");

            cy.contains("button.swal2-confirm", "Confirmar inscrição", {
                timeout: 10000,
            })
                .should("be.visible")
                .click({ force: true });

            // Caso apareça uma segunda modal de sucesso com botão de OK/Confirmar,
            // este trecho fecha a modal automaticamente.
            cy.wait(1000);

            clicarSeExistir("button.swal2-confirm");
        });
    });
});
