const { defineConfig } = require('cypress');
const cucumber = require('cypress-cucumber-preprocessor').default;
const allureWriter = require('@shelex/cypress-allure-plugin/writer');
const { cloudPlugin } = require('cypress-cloud/plugin');

module.exports = defineConfig({
  e2e: {
    setupNodeEvents(on, config) {
      // Suporte ao Cucumber
      on('file:preprocessor', cucumber());

      // Suporte ao Allure
      allureWriter(on, config);

      return cloudPlugin(on, config);
    },
    specPattern: 'cypress/e2e/**/**/*.{feature,cy.{js,jsx,ts,tsx}}',
    supportFile: 'cypress/support/e2e.js',
    baseUrl: 'https://hom-intranet.sme.prefeitura.sp.gov.br/',
    reporter: 'mocha-allure-reporter',
    reporterOptions: {
      overwrite: false,
      outputDir: 'allure-results',
    },
  },
});
