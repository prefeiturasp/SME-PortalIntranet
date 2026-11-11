/* eslint-disable @typescript-eslint/no-var-requires */
/* eslint-disable @typescript-eslint/no-require-imports */
const { defineConfig } = require("cypress");
const dotenv = require("dotenv");
const path = require("path");
const cucumber = require("cypress-cucumber-preprocessor").default;
const allureWriter = require("@shelex/cypress-allure-plugin/writer");

let cloudPlugin = (_on, config) => Promise.resolve(config);
try {
  cloudPlugin = require("cypress-cloud/plugin").cloudPlugin;
} catch (_e) {
  // Cypress Cloud is optional for local intranet runs.
}

dotenv.config({ path: path.resolve(__dirname, ".env") });

const isCI = !!(
  process.env.JENKINS_HOME ||
  process.env.CI ||
  process.env.CI_BUILD_ID
);

// Lista de RFs vinda do .env: "123,456" -> ["123", "456"]
const listaDoEnv = (valor) =>
  (valor || "")
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean);

module.exports = defineConfig({
  e2e: {
    supportFile: "cypress/support/e2e.js",
    watchForFileChanges: false,
    baseUrl:
      process.env.BASE_URL || "https://hom-intranet.sme.prefeitura.sp.gov.br/",
    viewportWidth: 1600,
    viewportHeight: 1050,
    video: false,
    screenshotOnRunFailure: false,
    trashAssetsBeforeRuns: true,
    chromeWebSecurity: false,
    experimentalRunAllSpecs: true,
    // Evita manter snapshots de DOM de cada comando em memoria
    numTestsKeptInMemory: 0,
    failOnStatusCode: false,
    specPattern: "cypress/e2e/intranet/**/*.{feature,cy.js,cy.jsx}",
    // Timeouts menores: uma falha deixa de custar 2 minutos por comando
    defaultCommandTimeout: 30000,
    requestTimeout: 30000,
    responseTimeout: 60000,
    execTimeout: 60000,
    pageLoadTimeout: 120000,
    waitForAnimations: true,
    animationDistanceThreshold: 5,
    // Reexecuta apenas em CI, onde a instabilidade de rede e maior
    retries: { runMode: isCI ? 1 : 0, openMode: 0 },

    env: {
      TAGS: process.env.TAGS || "not @skip",
      CI: isCI,
    },

    setupNodeEvents(on, config) {
      // Credenciais vem do .env (nunca versionado) e ficam apenas em Cypress.env
      config.env.intranet = {
        usuario: process.env.INTRANET_USUARIO,
        senha: process.env.INTRANET_SENHA,
      };
      config.env.scripts = {
        sorteio: {
          usuario: process.env.SCRIPT_SORTEIO_USUARIO,
          senha: process.env.SCRIPT_SORTEIO_SENHA,
        },
        estagio: {
          urlLogin: process.env.SCRIPT_ESTAGIO_URL_LOGIN,
          usuario: process.env.SCRIPT_ESTAGIO_USUARIO,
          senha: process.env.SCRIPT_ESTAGIO_SENHA,
        },
        oportunidade: {
          senha: process.env.SCRIPT_OPORTUNIDADE_SENHA,
          usuarios: listaDoEnv(process.env.SCRIPT_OPORTUNIDADE_USUARIOS),
        },
      };

      allureWriter(on, config);
      on("file:preprocessor", cucumber());

      on("task", {
        lerArquivoSeguro(caminho) {
          try {
            const fs = require("fs");
            const path = require("path");
            const caminhoAbsoluto = path.isAbsolute(caminho)
              ? caminho
              : path.join(process.cwd(), caminho);
            if (fs.existsSync(caminhoAbsoluto)) {
              return fs.readFileSync(caminhoAbsoluto, "utf8");
            }
            return null;
          } catch (e) {
            return null;
          }
        },
      });

      return cloudPlugin(on, config);
    },
  },
});
