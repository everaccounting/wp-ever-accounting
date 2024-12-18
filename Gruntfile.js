module.exports = function ( grunt ) {
	'use strict';
	// Load all grunt tasks matching the `grunt-*` pattern.
	require( 'load-grunt-tasks' )( grunt );

	// Show elapsed time.
	require( '@lodder/time-grunt' )( grunt );

	// Project configuration
	grunt.initConfig( {
		addtextdomain: {
			options: {
				textdomain: 'wp-ever-accounting',
				updateDomains: [ 'bytekit-textdomain' ],
			},
			update_all_domains: {
				options: {
					updateDomains: true,
				},
				src: [
					'*.php',
					'**/*.php',
					'!.git/**/*',
					'!bin/**/*',
					'!node_modules/**/*',
					'!tests/**/*',
				],
			},
		},

		// Check textdomain errors.
		checktextdomain: {
			options: {
				text_domain: 'wp-ever-accounting',
				keywords: [
					'__:1,2d',
					'_e:1,2d',
					'_x:1,2c,3d',
					'esc_html__:1,2d',
					'esc_html_e:1,2d',
					'esc_html_x:1,2c,3d',
					'esc_attr__:1,2d',
					'esc_attr_e:1,2d',
					'esc_attr_x:1,2c,3d',
					'_ex:1,2c,3d',
					'_n:1,2,4d',
					'_nx:1,2,4c,5d',
					'_n_noop:1,2,3d',
					'_nx_noop:1,2,3c,4d',
				],
			},
			files: {
				src: [
					'**/*.php', //Include all files
					'!apigen/**', // Exclude apigen/
					'!node_modules/**', // Exclude node_modules/
					'!tests/**', // Exclude tests/
					'!vendor/**', // Exclude vendor/
					'!tmp/**', // Exclude tmp/
				],
				expand: true,
			},
		},

		makepot: {
			target: {
				options: {
					domainPath: '/languages',
					exclude: [ '.git/*', 'bin/*', 'node_modules/*', 'tests/*', 'vendor/*' ],
					mainFile: 'wp-ever-accounting.php',
					potFilename: 'wp-ever-accounting.pot',
					potHeaders: {
						poedit: true,
						'x-poedit-keywordslist': true,
					},
					type: 'wp-plugin',
					updateTimestamp: true,
				},
			},
		},
	} );

	grunt.registerTask( 'default', [ 'i18n' ] );
	grunt.registerTask( 'build', [ 'i18n' ] );
	grunt.registerTask( 'i18n', [ 'addtextdomain', 'checktextdomain', 'makepot' ] );
	grunt.util.linefeed = '\n';
};
