module.exports = {
	map: process.env.NODE_ENV === 'production' ? false : {
		inline: false,
		annotation: true,
	},
	plugins: [
		// require('postcss-url'),
		// require('postcss-import'),
		require('autoprefixer'),
		require('tailwindcss'),
		...(process.env.NODE_ENV === 'production' ? [require('cssnano')] : []),
	],
};
