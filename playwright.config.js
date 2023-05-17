require('dotenv').config({ path: '.env.e2e' });

const config = {
	testDir: './tests/e2e',
	timeout: 60000,
	use: {
		baseURL: process.env.BASEURL,
		ignoreHTTPSErrors: true,
	},
	// workers: process.env.CI || process.env.GITPOD_MEMORY ? 1 : undefined,
};

module.exports = config;
