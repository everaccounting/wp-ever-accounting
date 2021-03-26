/**
 * External dependencies
 */
const path = require( 'path' );
const webpack = require( 'webpack' );
// eslint-disable-next-line import/no-extraneous-dependencies
const glob = require( 'glob' );
const WebpackBar = require( 'webpackbar' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
// eslint-disable-next-line import/no-extraneous-dependencies
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const WebpackRTLPlugin = require( 'webpack-rtl-plugin' );
const ImageminPlugin = require( 'imagemin-webpack-plugin' ).default;
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
/**
 * Internal dependencies
 */
const pkg = require( './package.json' );
const { get, find } = require( 'lodash' );

/**
 * WordPress dependencies
 */
const CustomTemplatedPathPlugin = require( '@wordpress/custom-templated-path-webpack-plugin' );
const defaultConfig = require( '@wordpress/scripts/config/webpack.config.js' );
const DependencyExtractionWebpackPlugin = require( '@wordpress/dependency-extraction-webpack-plugin' );
const {
	camelCaseDash,
} = require( '@wordpress/dependency-extraction-webpack-plugin/lib/util' );

const isProduction = process.env.NODE_ENV === 'production';
const externals = [];

// Webpack configuration.
const config = {
	...defaultConfig,
	entry: {
		admin: './assets/css/admin.scss',
		public: './assets/css/public.scss',
		release: './assets/css/release.scss',
		setup: './assets/css/setup.scss',
		'jquery-ui': './assets/css/jquery-ui/jquery-ui.css',
		...glob.sync( './assets/js/admin/*.js' ).reduce( ( memo, filepath ) => {
			memo[ path.parse( filepath ).name ] = filepath;
			return memo;
		}, {} ),
		// ...glob.sync( './packages/**/index.js' ).reduce( ( memo, filepath ) => {
		// 	const name = path.basename( path.dirname( filepath ) );
		// 	externals[ `@eaccounting/${ name }` ] = {
		// 		this: [ 'eaccounting', camelCaseDash( name ) ],
		// 	};
		// 	memo[ name ] = filepath;
		// 	return memo;
		// }, {} ),
	},
	output: {
		...defaultConfig.output,
		filename: 'js/[name].js',
		chunkFilename: `js/chunks/[name].js`,
		library: [ 'eaccounting', '[modulename]' ],
		libraryTarget: 'this',
		path: path.resolve( process.cwd(), 'dist' ),
	},
	externals: {
		jquery: 'jQuery',
		$: 'jQuery',
		...externals,
	},
	resolve: {
		...defaultConfig.resolve,
		alias: { '@eaccounting': path.resolve( __dirname, 'packages' ) },
		// modules: [ `${ __dirname }/assets/js/admin`, 'node_modules' ],
		modules: [ path.resolve( __dirname, 'packages' ), 'node_modules' ],
	},
	module: {
		rules: [
			{
				parser: {
					amd: false,
				},
			},
			// Lint JS.
			{
				test: /\.js$/,
				enforce: 'pre',
				loader: 'eslint-loader',
				options: {
					fix: true,
				},
			},
			{
				test: /\.jsx?$/,
				exclude: /node_modules/,
				use: [
					require.resolve( 'thread-loader' ),
					{
						loader: require.resolve( 'babel-loader' ),
						options: {
							// Babel uses a directory within local node_modules
							// by default. Use the environment variable option
							// to enable more persistent caching.
							cacheDirectory:
								process.env.BABEL_CACHE_DIRECTORY || true,
						},
					},
				],
			},
			{
				test: /\.s?css$/,
				use: [
					{
						loader: MiniCSSExtractPlugin.loader,
					},
					{
						loader: require.resolve( 'css-loader' ),
						options: {
							sourceMap: ! isProduction,
							url: false,
						},
					},
					{
						loader: require.resolve( 'postcss-loader' ),
					},
					{
						loader: require.resolve( 'sass-loader' ),
						options: {
							sourceMap: ! isProduction,
						},
					},
				],
			},
			{
				test: /\.svg$/,
				use: [ '@svgr/webpack', 'url-loader' ],
			},
		].filter( Boolean ),
	},
	plugins: [
		...defaultConfig.plugins.filter(
			( plugin ) =>
				! [
					'MiniCssExtractPlugin',
					'FixStyleOnlyEntriesPlugin',
					'LiveReloadPlugin',
					'DependencyExtractionWebpackPlugin',
				].includes( plugin.constructor.name )
		),

		// MiniCSSExtractPlugin to extract the CSS thats gets imported into JavaScript.
		new MiniCSSExtractPlugin( {
			esModule: false,
			moduleFilename: ( chunk ) =>
				`css/${ chunk.name.replace( '-style', '' ) }.min.css`,
		} ),
		new WebpackRTLPlugin( {
			filename: [ /(\.min\.css)/i, '-rtl$1' ],
		} ),
		// MiniCSSExtractPlugin creates JavaScript assets for CSS that are
		// obsolete and should be removed. Related webpack issue:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/85
		new FixStyleOnlyEntriesPlugin( {
			silent: true,
		} ),

		new webpack.ProvidePlugin( {
			$: 'jquery',
			jQuery: 'jquery',
		} ),

		// Copy vendor files to ensure 3rd party plugins relying on a script
		// handle to exist continue to be enqueued.
		new CopyWebpackPlugin( {
			patterns: [
				// Styles.
				// Scripts.
				{
					from: 'assets/js/admin-legacy',
					to: 'js',
				},
				{
					from: 'assets/js/vendor',
					to: 'js',
				},
				{
					from: './node_modules/chart.js/dist/Chart.min.js',
					to: 'js/chart.bundle.js',
				},
				{
					from: './node_modules/moment/min/moment.min.js',
					to: 'js/moment.js',
				},
				{
					from: './node_modules/select2/dist/js/select2.full.min.js',
					to: 'js/select2.full.js',
				},
				{
					from:
						'./node_modules/inputmask/dist/jquery.inputmask.min.js',
					to: 'js/jquery.inputmask.js',
				},
				//Fonts
				{
					from: 'assets/fonts',
					to: 'fonts',
				},
				{
					from: 'assets/images',
					to: 'images',
				},
				{
					from: 'assets/css/jquery-ui/images',
					to: 'images',
				},
			],
		} ),

		// Compress images
		// Must happen after CopyWebpackPlugin
		new ImageminPlugin( {
			disable: ! isProduction,
			test: /\.(jpe?g|png|gif|svg)$/i,
		} ),

		// Browser sync.
		! isProduction &&
			new BrowserSyncPlugin(
				{
					host: 'localhost',
					port: 3000,
					proxy: pkg.localhost,
					open: false,
					files: [
						'**/*.php',
						'dist/js/**/*.js',
						'dist/css/**/*.css',
						'dist/svg/**/*.svg',
						'dist/images/**/*.{jpg,jpeg,png,gif}',
						'dist/fonts/**/*.{eot,ttf,woff,woff2,svg}',
					],
				},
				{
					injectCss: true,
					reload: false,
				}
			),

		new DependencyExtractionWebpackPlugin( { injectPolyfill: true } ),

		//Set plugin information run build
		new webpack.BannerPlugin( pkg.name + ' v' + pkg.version ),
		new webpack.DefinePlugin( {
			'process.env': {
				NODE_ENV: JSON.stringify(
					process.env.NODE_ENV || 'development'
				),
			},
		} ),

		// Process custom modules.
		new CustomTemplatedPathPlugin( {
			modulename( outputPath, data ) {
				const entryName = get( data, [ 'chunk', 'name' ] );
				if ( entryName ) {
					return entryName.replace( /-([a-z])/g, ( match, letter ) =>
						letter.toUpperCase()
					);
				}
				return outputPath;
			},
		} ),

		// Fancy WebpackBar.
		new WebpackBar(),
	].filter( Boolean ),
	stats: {
		// Copied from `'minimal'`.
		all: false,
		errors: true,
		maxModules: 0,
		modules: true,
		warnings: true,
		// Our additional options.
		assets: true,
		errorDetails: true,
		excludeAssets: /\.(jpe?g|png|gif|svg|woff|woff2)$/i,
		moduleTrace: true,
		performance: true,
	},
	// Performance settings.
	performance: {
		maxAssetSize: 100000,
	},
};

// Remove automatic split of style- imports.
// @link https://github.com/WordPress/gutenberg/blob/master/packages/scripts/config/webpack.config.js#L67-L77
delete config.optimization.splitChunks.cacheGroups;
module.exports = config;
