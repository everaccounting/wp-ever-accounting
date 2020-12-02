/* eslint-disable */
module.exports = function (grunt) {
    'use strict';
    var sass = require('node-sass');

    grunt.initConfig(
        {
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
                '<%= dirs.js %>/eaccounting/*.js',
                ],
            },

            // Sass linting with Stylelint.
            stylelint: {
                options: {
                    configFile: '.stylelintrc',
                },
                all: ['<%= dirs.css %>/*.scss'],
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
                        src: ['*.js', '!*.min.js'],
                        dest: '<%= dirs.js %>/eaccounting/',
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
                        src: ['*.scss'],
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
                    src: ['*.css', '!select2.css', '!*-rtl.css'],
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
                        src: ['*.css'],
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
                    files: ['<%= dirs.css %>/**/*.scss'],
                    tasks: ['sass', 'rtlcss', 'postcss', 'cssmin', 'concat'],
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
                    tasks: ['eslint', 'uglify'],
                },
            },

            // Generate POT files.
            makepot: {
                options: {
                    type: 'wp-plugin',
                    domainPath: 'i18n/languages',
                    potHeaders: {
                        'language-team': 'LANGUAGE <EMAIL@ADDRESS>'
                    }
                },
                dist: {
                    options: {
                        potFilename: 'wp-ever-accounting.pot',
                        exclude: [
                        'apigen/.*',
                        'vendor/.*',
                        'tests/.*',
                        'tmp/.*'
                        ]
                    }
                }
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
                    '_nx_noop:1,2,3c,4d'
                    ]
                },
                files: {
                    src: [
                    '**/*.php',               // Include all files
                    '!apigen/**',             // Exclude apigen/
                    '!build/**',             // Exclude build/
                    '!includes/libraries/**', // Exclude libraries/
                    '!node_modules/**',       // Exclude node_modules/
                    '!tests/**',              // Exclude tests/
                    '!vendor/**',             // Exclude vendor/
                    '!tmp/**'                 // Exclude tmp/
                    ],
                    expand: true
                }
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
                    processors: [require('autoprefixer')],
                },
                dist: {
                    src: ['<%= dirs.css %>/*.css'],
                },
            },
        }
    );

    // Load NPM tasks to be used here.
    grunt.loadNpmTasks('grunt-sass');
    grunt.loadNpmTasks('grunt-phpcs');
    grunt.loadNpmTasks('grunt-rtlcss');
    grunt.loadNpmTasks('grunt-postcss');
    grunt.loadNpmTasks('grunt-stylelint');
    grunt.loadNpmTasks('gruntify-eslint');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-copy');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-clean');
    grunt.loadNpmTasks('grunt-wp-i18n');
    grunt.loadNpmTasks('grunt-checktextdomain');

    // Register tasks.
    grunt.registerTask('default', ['js', 'css', 'i18n']);

    grunt.registerTask('js', ['eslint', 'uglify']);

    grunt.registerTask(
        'css', [
        'sass',
        'rtlcss',
        'postcss',
        'cssmin',
        'concat',
        ]
    );

    // Only an alias to 'default' task.
    grunt.registerTask(
        'dev', [
        'default'
        ]
    );

    grunt.registerTask(
        'i18n', [
        'checktextdomain',
        'makepot'
        ]
    );

    grunt.registerTask(
        'release',
        [
        'default',
        'i18n'
        ]
    );

    grunt.registerTask(
        'build',
        [
        'clean',
        'copy'
        ]
    );

};
