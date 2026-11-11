// currents.config.js
require("dotenv").config({ path: require("path").resolve(__dirname, ".env") });

module.exports = {
  projectId: process.env.CURRENTS_PROJECT_ID || "SME-INTRANET",
  recordKey: process.env.CYPRESS_RECORD_KEY,
  cloudServiceUrl: process.env.CURRENTS_CLOUD_SERVICE_URL,
};
