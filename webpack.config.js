const defaultConfig = require('@wordpress/scripts/config/webpack.config');
const path = require('path');

module.exports = {
	...defaultConfig,
	entry: {
		'divi5-apex27-modules': './assets/src/js/divi5-apex27-modules.jsx',
		'divi5-filterable-portfolio': './assets/src/js/divi5-filterable-portfolio.js',
		'frontend': './assets/src/js/frontend.js',
        'admin': './assets/src/js/admin.js'
	},
	output: {
		...defaultConfig.output,
		path: path.resolve(__dirname, 'assets/js'),
		filename: '[name].js'
	},
	externals: {
		...(defaultConfig.externals || {}),
		'@divi/module-library': 'divi.moduleLibrary',
		'@divi/module': 'divi.module',
		'react': 'React',
		'react-dom': 'ReactDOM'
	}
};
