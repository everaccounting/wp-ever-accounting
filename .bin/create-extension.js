'use strict';

const fs = require( 'fs-extra' );
const path = require( 'path' );
const promptly = require( 'promptly' );
const chalk = require( 'chalk' );
const clone = require( 'git-clone' );
const repo = 'git@github.com:everaccounting/eaccounting-addon.git';

( async () => {
	console.log( '\n' );
	console.log(
		chalk.green( '🎉 Welcome to EverAccounting Extension Generator 🎉' )
	);
	const extensionName = await promptly.prompt(
		chalk.green(
			'What is the name of your extension? eg. for Ever Accounting - Slack, type only slack'
		)
	);

	const directoryDashed = 'eaccounting-' + extensionName;

	if ( fs.existsSync( '../' + directoryDashed ) ) {
		console.log(
			chalk.yellow.bold( '✘ Warning: ' ) +
				'"' +
				directoryDashed +
				'" directory already exists, please remove it or change the path'
		);

		// Bail out so you don't delete the directory or error out
		process.exit( 1 );
	} else {
		console.log(
			chalk.yellow( `Setting up your project. This might take a bit.` )
		);
	}

	// Clone the repo and get to work
	clone( repo, '../' + directoryDashed, function ( err ) {
		if ( err ) {
			console.log( err );
		} else {
			console.log( chalk.green( '✔ Clone Successful' ) );
		}
	} ); // clone()
} )();

/**
 * Delete files
 *
 * @param {string} dir Directory path
 * @param {string} [file] Filename to delete (optional, deletes directory if undefined)
 * @param {Function} [cb] Callback
 * @return {Promise}
 */
function deleteFile( dir, file, cb ) {
	return new Promise( function ( resolve, reject ) {
		const filePath = path.join( dir, file );
		fs.lstat( filePath, function ( err, stats ) {
			if ( err ) {
				return reject( err );
			}
			if ( stats.isDirectory() ) {
				resolve( deleteDirectory( filePath ) );
			} else {
				fs.unlink( filePath, function ( err ) {
					if ( err ) {
						return reject( err );
					}
					resolve();
				} );
			}
		} );

		if ( 'function' === typeof cb ) {
			cb.call( this );
		}
	} );
} // deleteFile()

/**
 * Delete directories
 *
 * @param {string} dir Directory
 * @param {Function} [cb] Callback
 * @return {Promise}
 */
function deleteDirectory( dir, cb ) {
	return new Promise( function ( resolve, reject ) {
		fs.access( dir, function ( err ) {
			if ( err ) {
				return reject( err );
			}
			fs.readdir( dir, function ( err, files ) {
				if ( err ) {
					return reject( err );
				}
				Promise.all(
					files.map( function ( file ) {
						return deleteFile( dir, file );
					} )
				)
					.then( function () {
						fs.rmdir( dir, function ( err ) {
							if ( err ) {
								return reject( err );
							}
							resolve();
						} );
					} )
					.catch( reject );
			} );
		} );

		if ( 'function' === typeof cb ) {
			cb.call( this );
		}
	} );
} // deleteDirectory()
