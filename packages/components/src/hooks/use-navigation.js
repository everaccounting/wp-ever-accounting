// This hook will be used for navigation, adding and removing query params. This will accept the current location and will return the updated location.

/**
 *
 * @param {Object} history - React router history object.
 */
function useNavigation( history ) {
	const addParam = ( key, value ) => {
		const params = new URLSearchParams( history.location.search );
		params.set( key, value );
		history.push( {
			search: params.toString(),
		} );
	};
	const removeParam = ( key ) => {
		const params = new URLSearchParams( history.location.search );
	};
    const
}

export default useNavigation;
