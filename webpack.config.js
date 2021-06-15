/**
 * External dependencies
 */
const path = require( 'path' );
const sass = require( 'node-sass' );
const postcss = require( 'postcss' );
const fastGlob = require( 'fast-glob' );
const UglifyJS = require( 'uglify-es' );
const WebpackBar = require( 'webpackbar' );
const TerserPlugin = require( 'terser-webpack-plugin' );
const WebpackRTLPlugin = require( 'webpack-rtl-plugin' );
const CopyWebpackPlugin = require( 'copy-webpack-plugin' );
const { BundleAnalyzerPlugin } = require( 'webpack-bundle-analyzer' );
const MiniCSSExtractPlugin = require( 'mini-css-extract-plugin' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );
const { CleanWebpackPlugin } = require( 'clean-webpack-plugin' );
const FixStyleOnlyEntriesPlugin = require( 'webpack-fix-style-only-entries' );
/**
 * WordPress dependencies
 */
const CustomTemplatedPathPlugin = require( '@wordpress/custom-templated-path-webpack-plugin' );

/**
 * Internal dependencies
 */
const { localhost, files } = require( './package.json' );
const DependencyExtractionWebpackPlugin = require( './packages/dependency-extraction-webpack-plugin/src/index' );

const isProduction = process.env.NODE_ENV === 'production';
const NODE_ENV = process.env.NODE_ENV || 'development';

const styleTransform = ( content, filePath ) => {
	const compiled = sass.renderSync( {
		file: filePath,
	} );

	return postcss( [
		require( 'cssnano' )( require( './postcss.config.js' ) ),
	] )
		.process( compiled.css.toString() )
		.then( ( result ) => result.css );
};

const scriptTransform = ( content ) => {
	return Promise.resolve(
		Buffer.from( UglifyJS.minify( content.toString() ).code )
	);
};

const modules = fastGlob
	.sync( './modules/*/index.js' )
	.reduce( ( memo, file ) => {
		const name = path.basename( path.dirname( file ) );
		return {
			...memo,
			[ name ]: file,
		};
	}, {} );

const config = {
	mode: NODE_ENV,
	entry: {
		app: './client/index.js',
		...modules,
	},
	output: {
		filename: '[name]/index.js',
		path: path.join( __dirname, 'dist' ),
		chunkFilename: 'chunks/[name].js',
		library: [ 'eaccounting', '[camelName]' ],
	},
	resolve: {
		extensions: [ '.js', '.jsx', '.json', '.scss', '.css' ],
		modules: [ path.resolve( __dirname, 'modules' ), 'node_modules' ],
		alias: {
			'@eaccounting': path.resolve( __dirname, 'modules' ),
			'lodash-es': 'lodash',
		},
	},
	module: {
		rules: [
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
				test: /\.(png|jpe?g|gif|svg|eot|ttf|woff|woff2)$/,
				loader: 'url-loader',
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
		].filter( Boolean ),
	},
	plugins: [
		new CleanWebpackPlugin(),
		// MiniCSSExtractPlugin creates JavaScript assets for CSS that are
		// obsolete and should be removed. Related webpack issue:
		// https://github.com/webpack-contrib/mini-css-extract-plugin/issues/85
		new FixStyleOnlyEntriesPlugin( {
			silent: true,
		} ),

		new CustomTemplatedPathPlugin( {
			// eslint-disable-next-line no-shadow
			camelName( path, data ) {
				return data.chunk.name.replace(
					/-([a-z])/g,
					( match, letter ) => letter.toUpperCase()
				);
			},
		} ),

		// MiniCSSExtractPlugin to extract the CSS that's gets imported into JavaScript.
		new MiniCSSExtractPlugin( {
			filename: `./[name]/style.css`,
			chunkFilename: './chunks/[id].style.css',
		} ),

		new CopyWebpackPlugin( [
			...fastGlob
				.sync( './assets/css/!(*.min.*|*.map).scss' )
				.map( ( file ) => ( {
					from: file,
					to: path.resolve(
						__dirname,
						'./assets/css/[name].min.css'
					),
					flatten: true,
					transform: styleTransform,
				} ) ),
		] ),

		new CopyWebpackPlugin( [
			...fastGlob
				.sync( './assets/js/admin/!(*.min.js|*.map)' )
				.map( ( file ) => ( {
					from: file,
					to: path.resolve(
						__dirname,
						'./assets/js/admin/[name].min.[ext]'
					),
					transform: ( content ) => scriptTransform( content ),
				} ) ),
		] ),

		new WebpackRTLPlugin( {
			filename: [ /(\.css)/i, '-rtl$1' ],
		} ),

		process.env.ANALYZE && new BundleAnalyzerPlugin(),

		// Browser sync.
		! isProduction &&
			new BrowserSyncPlugin(
				{
					host: 'localhost',
					port: 3000,
					proxy: localhost,
					open: false,
					files,
				},
				{
					injectCss: true,
					reload: false,
				}
			),

		new DependencyExtractionWebpackPlugin(),
		// Fancy WebpackBar.
		new WebpackBar(),
	].filter( Boolean ),
	watchOptions: {
		ignored: [ '**/node_modules', '**/packages/*/src' ],
		aggregateTimeout: 500,
	},
	optimization: {
		concatenateModules: isProduction && ! process.env.WP_BUNDLE_ANALYZER,
		minimizer: [
			new TerserPlugin( {
				cache: true,
				parallel: true,
				sourceMap: ! isProduction,
				terserOptions: {
					output: {
						comments: /translators:/i,
					},
					compress: {
						passes: 2,
					},
					mangle: {
						reserved: isProduction
							? []
							: [ '__', '_n', '_nx', '_x' ],
						safari10: true,
					},
				},
				extractComments: false,
			} ),
		],
	},
	stats: {
		// Copied from `'minimal'`.
		all: false,
		errors: true,
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
		maxAssetSize: 500000,
	},
};
if ( ! isProduction ) {
	// WP_DEVTOOL global variable controls how source maps are generated.
	// See: https://webpack.js.org/configuration/devtool/#devtool.
	config.devtool = 'source-map';
	config.module.rules.unshift( {
		test: /\.js$/,
		exclude: [ /node_modules/ ],
		use: require.resolve( 'source-map-loader' ),
		enforce: 'pre',
	} );
}
module.exports = config;
