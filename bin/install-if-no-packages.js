const spawnSync = require( 'child_process' ).spawnSync;
/**
 * External dependencies
 */
const fs = require( 'fs' );

if ( ! fs.existsSync( 'node_modules' ) ) {
	console.log( 'No "node_modules" present, installing dependencies...' );
	const installResult = spawnSync( 'npm', [ 'install' ], {
		shell: true,
		stdio: 'inherit',
	} ).status;
	if ( installResult ) {
		process.exit( installResult );
	}
}
