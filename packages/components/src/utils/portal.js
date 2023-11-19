/**
 * Get a portal node, or create it if it doesn't exist.
 *
 * @param {string} portalName    DOM ID of the portal
 * @param {string} portalWrapper
 * @return {Function} Element
 */
export function getPortal( portalName, portalWrapper = 'wpbody' ) {
	let node = document.getElementById( portalName );

	if ( node === null ) {
		const wrapper = document.getElementById( portalWrapper );

		node = document.createElement( 'div' );

		if ( wrapper && wrapper.parentNode ) {
			node.setAttribute( 'id', portalName );
			wrapper.parentNode.appendChild( node );
		}
	}

	return node;
}
