/* eslint-disable */
module.exports = function ( grunt ) {
	'use strict';
	var sass = require( 'node-sass' );

	grunt.initConfig( {
		// Setting folder templates.
		dirs: {
			css: 'assets/css',
			fonts: 'assets/fonts',
			images: 'assets/images',
			js: 'assets/js',
			php: 'includes',
		},

		// JavaScript linting with ESLint.
		eslint: {
			src: [
				'<%= dirs.js %>/admin/*.js',
				'!<%= dirs.js %>/admin/*.min.js',
				'<%= dirs.js %>/frontend/*.js',
				'!<%= dirs.js %>/frontend/*.min.js',
			],
		},

		// Sass linting with Stylelint.
		stylelint: {
			options: {
				configFile: '.stylelintrc',
			},
			all: [ '<%= dirs.css %>/*.scss' ],
		},

		// Minify .js files.
		uglify: {
			options: {
				ie8: true,
				parse: {
					strict: false,
				},
				output: {
					comments: /@license|@preserve|^!/,
				},
			},
			core: {
				files: [
					{
						expand: true,
						cwd: '<%= dirs.js %>/eaccounting/',
						src: [ '*.js', '!*.min.js' ],
						dest: '<%= dirs.js %>/eaccounting/',
						ext: '.min.js',
					},
				],
			},
			admin: {
				files: [
					{
						expand: true,
						cwd: '<%= dirs.js %>/admin/',
						src: [ '*.js', '!*.min.js' ],
						dest: '<%= dirs.js %>/admin/',
						ext: '.min.js',
					},
				],
			},
			vendor: {
				files: {
					'<%= dirs.js %>/jquery-blockui/jquery.blockUI.min.js': [
						'<%= dirs.js %>/jquery-blockui/jquery.blockUI.js',
					],
					'<%= dirs.js %>/inputmask/jquery.inputmask.min.js': [
						'<%= dirs.js %>/inputmask/jquery.inputmask.js',
					],
					'<%= dirs.js %>/jquery-tiptip/jquery.tipTip.min.js': [
						'<%= dirs.js %>/jquery-tiptip/jquery.tipTip.js',
					],
					'<%= dirs.js %>/pace/pace.min.js': [
						'<%= dirs.js %>/pace/pace.js',
					],
					'<%= dirs.js %>/select2/select2.full.min.js': [
						'<%= dirs.js %>/select2/select2.full.js',
					],
					'<%= dirs.js %>/select2/select2.min.js': [
						'<%= dirs.js %>/select2/select2.js',
					],
					'<%= dirs.js %>/daterange/daterangepicker.min.js': [
						'<%= dirs.js %>/daterange/daterangepicker.js',
					],
					'<%= dirs.js %>/chartjs/chartjs.min.js': [
						'<%= dirs.js %>/chartjs/chartjs.js',
					],
					'<%= dirs.js %>/chartjs/chart.bundle.min.js': [
						'<%= dirs.js %>/chartjs/chart.bundle.js',
					],
				},
			},
			frontend: {
				files: [
					{
						expand: true,
						cwd: '<%= dirs.js %>/frontend/',
						src: [ '*.js', '!*.min.js' ],
						dest: '<%= dirs.js %>/frontend/',
						ext: '.min.js',
					},
				],
			},
		},

		// Compile all .scss files.
		sass: {
			compile: {
				options: {
					implementation: sass,
					sourceMap: true,
					map: true,
				},
				files: [
					{
						expand: true,
						cwd: '<%= dirs.css %>/',
						src: [ '*.scss' ],
						dest: '<%= dirs.css %>/',
						ext: '.css',
					},
				],
			},
		},

		// Generate RTL .css files.
		rtlcss: {
			eaccounting: {
				expand: true,
				cwd: '<%= dirs.css %>',
				src: [ '*.css', '!select2.css', '!*-rtl.css' ],
				dest: '<%= dirs.css %>/',
				ext: '-rtl.css',
			},
		},

		// Minify all .css files.
		cssmin: {
			minify: {
				files: [
					{
						expand: true,
						cwd: '<%= dirs.css %>/',
						src: [ '*.css' ],
						dest: '<%= dirs.css %>/',
						ext: '.css',
					},
				],
			},
		},

		// Concatenate select2.css onto the admin.css files.
		concat: {
			admin: {
				files: {
					'<%= dirs.css %>/admin.css': [
						'<%= dirs.css %>/select2.css',
						'<%= dirs.css %>/admin.css',
					],
					'<%= dirs.css %>/admin-rtl.css': [
						'<%= dirs.css %>/select2.css',
						'<%= dirs.css %>/admin-rtl.css',
					],
				},
			},
		},

		// Watch changes for assets.
		watch: {
			css: {
				files: [ '<%= dirs.css %>/**/*.scss' ],
				tasks: [ 'sass', 'rtlcss', 'postcss', 'cssmin', 'concat' ],
			},
			js: {
				files: [
					'GruntFile.js',
					'<%= dirs.js %>/admin/*js',
					'<%= dirs.js %>/eaccounting/*js',
					'<%= dirs.js %>/frontend/*js',
					'!<%= dirs.js %>/admin/*.min.js',
					'!<%= dirs.js %>/eaccounting/*.min.js',
					'!<%= dirs.js %>/frontend/*.min.js',
				],
				tasks: [ 'eslint', 'uglify' ],
			},
		},

		// PHP Code Sniffer.
		phpcs: {
			options: {
				bin: 'vendor/bin/phpcs',
			},
			dist: {
				src: [
					'**/*.php', // Include all php files.
					'!node_modules/**',
					'!tests/cli/**',
					'!tmp/**',
					'!vendor/**',
				],
			},
		},

		// Autoprefixer.
		postcss: {
			options: {
				map: true,
				annotation: false,
				processors: [ require( 'autoprefixer' ) ],
			},
			dist: {
				src: [ '<%= dirs.css %>/*.css' ],
			},
		},
	} );

	// Load NPM tasks to be used here.
	grunt.loadNpmTasks( 'grunt-sass' );
	grunt.loadNpmTasks( 'grunt-phpcs' );
	grunt.loadNpmTasks( 'grunt-rtlcss' );
	grunt.loadNpmTasks( 'grunt-postcss' );
	grunt.loadNpmTasks( 'grunt-stylelint' );
	grunt.loadNpmTasks( 'gruntify-eslint' );
	grunt.loadNpmTasks( 'grunt-contrib-uglify' );
	grunt.loadNpmTasks( 'grunt-contrib-cssmin' );
	grunt.loadNpmTasks( 'grunt-contrib-concat' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-contrib-watch' );
	grunt.loadNpmTasks( 'grunt-contrib-clean' );

	// Register tasks.
	grunt.registerTask( 'default', [ 'js', 'css' ] );

	grunt.registerTask( 'js', [ 'eslint', 'uglify'] );

	grunt.registerTask( 'css', [
		'sass',
		'rtlcss',
		'postcss',
		'cssmin',
		'concat',
	] );

	grunt.registerTask( 'assets', [ 'js', 'css' ] );

	grunt.registerTask( 'e2e-build', [
		'uglify:admin',
		'uglify:frontend',
		'uglify:flexslider',
		'css',
	] );

	// Only an alias to 'default' task.
	grunt.registerTask( 'dev', [ 'default' ] );
};
