import "@shelex/cypress-allure-plugin";
import "./commands_ui/commands_globais";
import "./commands_ui/commands_login";
import "./commands_ui/commands_noticias";
import "./commands_ui/commands_paginas";
import "cypress-xpath";
import "cypress-plugin-tab";

require("events").EventEmitter.defaultMaxListeners = 30;

Cypress.on("uncaught:exception", (err) => {
    return false;
});

let screenshotTaken = false;

Cypress.on("test:before:run", () => {
    screenshotTaken = false;
});

Cypress.on("fail", (error, runnable) => {
    if (!screenshotTaken) {
        const fileName =
            `ERRO - ${runnable.parent.title} - ${runnable.title}`.replace(
                /[^a-z0-9]/gi,
                "_",
            );

        cy.document().then((doc) => {
            const activeElement = doc.activeElement;
            const bodyElement = doc.body;

            if (activeElement && activeElement !== bodyElement) {
                activeElement.style.border = "3px solid red";
                activeElement.style.boxShadow = "0 0 10px red";
            }

            cy.screenshot(fileName, {
                capture: "fullPage",
                overwrite: true,
            });
        });

        screenshotTaken = true;
    }

    throw error;
});
