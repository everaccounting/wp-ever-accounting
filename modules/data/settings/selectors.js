/**
 * Retrieves options value from the options store.
 *
 * @param {Object}   state  State param added by wp.data.
 * @return {*}  The value present in the settings state for the given name.
 */
export function getOptions( state){
	return state;
}

/**
 * Retrieves an option value from the options store.
 *
 * @param {Object}   state  State param added by wp.data.
 * @param {string}   name   The identifier for the setting.
 * @param {*}    [fallback=false]  The value to use as a fallback if the setting is not in the state.
 * @param {Function} [filter=( val ) => val]  A callback for filtering the value before it's returned. Receives both the found value (if it exists for the key) and the provided fallback arg.
 *
 * @return {*}  The value present in the settings state for the given name.
 */
export function getOption( state, name, fallback = false, filter = (val) => val ){
	const value = ( state[ name ] && state[ name ] ) || fallback;
	return filter( value, fallback );
}
