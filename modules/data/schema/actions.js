/**
 * Returns an action object used to update the store with the provided list
 * of model schema.
 *
 * @param {Object} schema An array of schema to add to the store state.
 * @return {{schema: *, type: string}} Routes.
 */
export function receiveSchema( schema ) {
	return {
		type: 'RECEIVE_SCHEMA',
		schema,
	};
}
