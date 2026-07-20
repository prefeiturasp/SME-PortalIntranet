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

module.exports = defineConfig({
  e2e: {
    supportFile: "cypress/support/e2e.js",
    watchForFileChanges: true,
    baseUrl:
      process.env.BASE_URL || "https://hom-intranet.sme.prefeitura.sp.gov.br/",
    viewportWidth: 1600,
    viewportHeight: 1050,
    video: false,
    screenshotOnRunFailure: false,
    trashAssetsBeforeRuns: false,
    chromeWebSecurity: false,
    experimentalRunAllSpecs: true,
    failOnStatusCode: false,
    specPattern: "cypress/e2e/intranet/**/*.{feature,cy.js,cy.jsx}",
    defaultCommandTimeout: 120000,
    requestTimeout: 120000,
    execTimeout: 120000,
    pageLoadTimeout: 300000,
    waitForAnimations: true,
    animationDistanceThreshold: 5,

    env: {
      TAGS: process.env.TAGS || "not @skip",
      CI: !!(
        process.env.JENKINS_HOME ||
        process.env.CI ||
        process.env.CI_BUILD_ID
      ),
    },

    setupNodeEvents(on, config) {
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
