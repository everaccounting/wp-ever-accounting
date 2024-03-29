module.exports = ( { file, options, env } ) => ( { /* eslint-disable-line */
	plugins: {
		precss: {},
		'postcss-color-function': {},
		// Prefix editor styles with class `editor-styles-wrapper`.
		'postcss-editor-styles':
			file.basename === 'editor-style.css'
				? {
						scopeTo: '.editor-styles-wrapper',
						ignore: [
							':root',
							'.edit-post-visual-editor.editor-styles-wrapper',
							'.wp-toolbar',
						],
						remove: [
							'html',
							':disabled',
							'[readonly]',
							'[disabled]',
						],
						tags: [
							'button',
							'input',
							'label',
							'select',
							'textarea',
							'form',
						],
				  }
				: false,
		// Minify style on production using cssano.
		cssnano:
			env === 'production'
				? {
						preset: [
							'default',
							{
								autoprefixer: false,
								calc: {
									precision: 8,
								},
								convertValues: true,
								discardComments: {
									removeAll: true,
								},
								mergeLonghand: false,
								zindex: false,
							},
						],
				  }
				: false,
	},
} );
