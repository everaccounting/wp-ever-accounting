module.exports = {
	map: process.env.NODE_ENV === 'production' ? false : {
		inline: false,
		annotation: true,
	},
	plugins: [
		require('postcss-import'),
		require('postcss-url'),
		require('autoprefixer'),
		require('tailwindcss'),
		...(process.env.NODE_ENV === 'production' ? [require('cssnano')] : []),
	],
};
