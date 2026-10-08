const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const ESLintPlugin = require('eslint-webpack-plugin');

// drop the RTL stylesheet copies wp-scripts emits
const plugins = defaultConfig.plugins.filter(p => !(Object.values(p).length === 2 && Object.values(p)?.[1]?.filename === '[name]-rtl.css'));

module.exports = {
	...defaultConfig,
	plugins: [
		...plugins,
		new ESLintPlugin()
	]
};
