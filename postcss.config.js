module.exports = () => {
	return {
		map:
			process.env.NODE_ENV === 'production'
				? false
				: {
						inline: false,
						annotation: true,
				  },
		plugins: [
			require( 'autoprefixer' ),
			require( 'tailwindcss' ),
			...( process.env.NODE_ENV === 'production' ? [ require( 'cssnano' ) ] : [] ),
		],
	};
};
