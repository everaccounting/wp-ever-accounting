/**
 * WordPress dependencies
 */
import { createContext, useContext } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { defaultEntities } from './entities';

// const entities = {
// 	...defaultEntities.reduce((acc, entity) => {
// 		acc[entity.name] = { context: createContext() };
// 		return acc;
// 	}, {}),
// };

// const getEntity = (name) => {
// 	if (!entities[name]) {
// 		throw new Error(`Missing entity config for name: ${name}.`);
// 	}
//
// 	if (!entities[name]) {
// 		entities[name] = { context: createContext() };
// 	}
//
// 	return entities[name];
// };

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
	const Provider = getEntity(name).context.Provider;
	return <Provider value={id}>{children}</Provider>;
}

/**
 * Hook that returns the ID for the nearest
 * provided entity of the specified type.
 *
 * @param {string} name The entity name.
 */
export function useEntityId(name) {
	return useContext(getEntity(name).context);
}
