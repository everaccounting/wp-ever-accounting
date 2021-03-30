'use strict';

const fs = require( 'fs-extra' );
const path = require( 'path' );
const promptly = require( 'promptly' );
const chalk = require( 'chalk' );
const clone = require( 'git-clone' );
const replace = require( 'replace-in-file' );
const repo = 'git@github.com:everaccounting/eaccounting-addon.git';

( async () => {
	console.log( '\n' );
	console.log(
		chalk.green( '🎉 Welcome to EverAccounting Extension Generator 🎉' )
	);
	// Addon
	// addon
	// ADDON
	const inputName = await promptly.prompt(
		chalk.green(
			'What is the name of your extension? eg. for Ever Accounting - Slack, type only slack'
		)
	);

	const nameCapitalized = inputName.replace(
		/\b\w+/g,
		( l ) => l.charAt( 0 ).toUpperCase() + l.slice( 1 )
	);
	const nameCamelCase = nameCapitalized.replace( / /g, '' );
	const nameUnderscored = nameCapitalized
		.replace( /[^a-zA-Z]/g, '_' )
		.toLowerCase();
	const nameDashed = nameUnderscored.replace( /_/g, '-' );
	const nameSpaces = nameCapitalized.replace( / /g, '_' );
	const nameCaps = nameCamelCase.toUpperCase();
	const directoryName = 'eaccounting-' + nameDashed;
	const directoryPath = '../' + directoryName;
	const textToReplace = [
		{
			from: /Ever Accounting - Addon/g,
			to: `Ever Accounting - ${ nameCapitalized }`,
		},
		{
			from: /EverAccounting_Addon/g,
			to: `EverAccounting_${ nameCamelCase }`,
		},
		{
			from: /EACCOUNTING_ADDON/g,
			to: `EACCOUNTING_${ nameCaps }`,
		},
		{
			from: /eaccounting-addon/g,
			to: `eaccounting-${ nameDashed }`,
		},
		{
			from: /eaccounting_addon/g,
			to: `eaccounting_${ nameUnderscored }`,
		},
		{
			from: /addon/g,
			to: nameDashed,
		},
		{
			from: /Addon/g,
			to: nameSpaces,
		},
	];

	// An array of directories to remove
	const directoriesToRemove = [ '.git' ];

	// Objects of directories that need to be renamed
	const directoriesToRename = [
		{
			from: 'eaccounting-addon.php',
			to: directoryName + '.php',
		},
		{
			from: 'eaccounting-addon',
			to: directoryName,
		},
		{
			from: 'languages/eaccounting-addon.pot',
			to: 'languages/' + directoryName + '.pot',
		},
		{
			from: 'languages/eaccounting-addon.po',
			to: 'languages/' + directoryName + '.po',
		},
	];

	if ( fs.existsSync( directoryPath ) ) {
		console.log(
			chalk.yellow.bold( '✘ Warning: ' ) +
				'"' +
				directoryName +
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
	clone( repo, '../' + directoryName, function ( err ) {
		if ( err ) {
			console.log( err );
		} else {
			console.log( chalk.green( '✔ Clone Successful' ) );

			// Delete unnecessary directories
			if ( directoriesToRemove.length ) {
				directoriesToRemove.forEach( function ( dir ) {
					if ( fs.existsSync( directoryPath + '/' + dir ) ) {
						deleteDirectory(
							directoryPath + '/' + dir,
							function () {
								console.log(
									chalk.green( `✔ ${ dir } deleted` )
								);
							}
						);
					}
				} );
			}

			// Synchronously find and replace text within files
			textToReplace.forEach( function ( text ) {
				try {
					const changes = replace.sync( {
						files: directoryPath + '/**/*.*',
						from: text.from,
						to: text.to,
						encoding: 'utf8',
					} );

					console.log( chalk.green.bold( `✔ Modified files` ) );
				} catch ( error ) {
					console.error( 'Error occurred:', error );
				}
			} );

			// Rename directories
			directoriesToRename.forEach( function ( dir ) {
				if ( fs.existsSync( directoryPath + '/' + dir.from ) ) {
					fs.rename(
						directoryPath + '/' + dir.from,
						directoryPath + '/' + dir.to,
						function ( err ) {
							if ( err ) throw err;
							console.log(
								chalk.green( '✔ Renamed ' + dir.from )
							);
						}
					);
				}
			} );
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
