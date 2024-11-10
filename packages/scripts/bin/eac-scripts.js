#!/usr/bin/env node

/**
 * WordPress dependencies
 */
const { getNodeArgsFromCLI, spawnScript } = require( '@wordpress/scripts/utils' );

const { scriptName, scriptArgs, nodeArgs } = getNodeArgsFromCLI();

spawnScript( scriptName, scriptArgs, nodeArgs );
