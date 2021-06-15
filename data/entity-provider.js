/**
 * WordPress dependencies
 */
import { createContext, useContext } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { getRoute } from './selectors';

/**
 * Internal dependencies
 */

/**
 * Context provider component for providing
 * an entity for a specific entity type.
 *
 * @param {Object} props          The component's props.
 * @param {string} props.name     The entity name.
 * @param {number} props.id       The entity ID.
 * @param {*}      props.children The children to wrap.
 *
 * @return {Object} The provided children, wrapped with
 *                   the entity's context provider.
 */
export default function EntityProvider({ name, id, children }) {
	const Provider = getRoute(name).context.Provider;
	return <Provider value={id}>{children}</Provider>;
}

/**
 * Hook that returns the ID for the nearest
 * provided entity of the specified type.
 *
 * @param {string} name The entity name.
 */
export function useEntityId(name) {
	return useContext(getRoute(name).context);
}
