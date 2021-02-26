module.exports = function (grunt) {
	'use strict';
	const pkg = grunt.file.readJSON('package.json');
	// Project configuration
	grunt.initConfig({
		addtextdomain: {
			options: {
				textdomain: 'wp-ever-accounting',
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
					exclude: ['.git/*', 'bin/*', 'node_modules/*', 'tests/*'],
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

		wp_readme_to_markdown: {
			your_target: {
				files: {
					'README.md': 'readme.txt',
				},
			},
		},

		// Clean up build directory
		clean: {
			main: ['build/'],
		},
		copy: {
			main: {
				src: [
					'**',
					'!node_modules/**',
					'!**/js/src/**',
					'!**/css/src/**',
					'!**/js/vendor/**',
					'!**/css/vendor/**',
					'!**/css/*.scss',
					'!**/images/src/**',
					'!**/sass/**',
					'!build/**',
					'!**/*.md',
					'!**/*.map',
					'!**/*.sh',
					'!.idea/**',
					'!bin/**',
					'!.git/**',
					'!Gruntfile.js',
					'!package.json',
					'!composer.json',
					'!composer.lock',
					'!package-lock.json',
					'!debug.log',
					'!none',
					'!.gitignore',
					'!.gitmodules',
					'!phpcs.xml.dist',
					'!npm-debug.log',
					'!plugin-deploy.sh',
					'!export.sh',
					'!tests/**',
					'!.csscomb.json',
					'!.editorconfig',
					'!.jshintrc',
					'!.tmp',
				],
				dest: 'build/',
			},
		},

		compress: {
			main: {
				options: {
					mode: 'zip',
					archive:
						'./build/' + pkg.name + '-v' + pkg.version + '.zip',
				},
				expand: true,
				cwd: 'build/',
				src: ['**/*'],
				dest: pkg.name,
			},
		},
	});

	// Saves having to declare each dependency
	require('matchdep').filterDev('grunt-*').forEach(grunt.loadNpmTasks);

	grunt.registerTask('default', ['i18n', 'readme']);
	grunt.registerTask('build', ['i18n', 'readme']);
	grunt.registerTask('i18n', ['addtextdomain', 'checktextdomain', 'makepot']);
	grunt.registerTask('readme', ['wp_readme_to_markdown']);
	grunt.registerTask('zip', ['clean', 'copy', 'compress']);
	grunt.util.linefeed = '\n';
};
