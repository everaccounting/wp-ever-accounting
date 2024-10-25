module.exports = ( { file } ) => {
	const isPublic = file.includes( 'frontend' );
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
			require( 'tailwindcss' )(
				isPublic ? './tailwind.public.config.js' : './tailwind.admin.config.js'
			),
			...( process.env.NODE_ENV === 'production' ? [ require( 'cssnano' ) ] : [] ),
		],
	};
};
