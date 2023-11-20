export function route( path, parts = {} ) {
	let url = path;

	for ( const part in parts ) {
		url = url.replace( `:${ part }`, parts[ part ] );
	}

	return url;
}
