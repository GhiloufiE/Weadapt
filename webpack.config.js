const glob = require('glob');
const path = require('path');
const entryPlus = require('webpack-entry-plus');
const entryFiles = [
	{
		entryFiles: glob.sync('./wp-content/themes/**/parts/**/*index.js'),
		outputName(item) { return item.replace('./', '').replace('index.js', 'index'); },
	},
	{
		entryFiles: glob.sync('./wp-content/themes/**/js/script.js'),
		outputName(item) {
			return item.replace('./', '').replace('script.js', 'bundle');
		},
	},
	{
		entryFiles: glob.sync('./wp-content/themes/**/js/script-global.js'),
		outputName(item) {
			return item.replace('./', '').replace('script-global.js', 'bundle-global');
		},
	},
	{
		entryFiles: glob.sync('./wp-content/themes/**/js/script-load.js'),
		outputName(item) {
			return item.replace('./', '').replace('script-load.js', 'bundle-load');
		},
	},
];
const settings = {
	entry: entryPlus(entryFiles),
	output: {
		path: path.resolve(__dirname, ''),
		filename: '[name].min.js',
	},
	resolve: {
		extensions: ['.js'],
		alias: { Base: path.resolve(glob.sync('./wp-content/themes/**/js')[0]), },
		modules: [ path.resolve(glob.sync('./wp-content/themes/**/js')[0]), path.resolve(__dirname, 'node_modules'), ],
	},
	module: {
		rules: [
			{
				test: /\.js$/,
				exclude: /node_modules/,
				use: {
					loader: 'babel-loader',
					options: {
						plugins: [
							[
								'@babel/plugin-proposal-class-properties',
								{ loose: true },
							],
							[
								'@babel/plugin-proposal-object-rest-spread',
								{ loose: true },
							],
						],
					},
				},
			},
		],
	},
	mode: 'production',
};
module.exports = settings;
