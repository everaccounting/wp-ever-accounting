/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';

/**
 * Internal dependencies
 */
import memoize from './memoize';
import { Status } from './constants';

export const META_SELECTORS = [
	'getIsResolving',
	'hasStartedResolution',
	'hasFinishedResolution',
	'isResolving',
	'getCachedResolvers',
];

/**
 * Like useSelect, but the selectors return objects containing
 * both the original data AND the resolution info.
 *
 * @since 6.1.0 Introduced in WordPress core.
 * @private
 *
 * @param {Function} mapQuerySelect see useSelect
 * @param {Array}    deps           see useSelect
 *
 * @example
 * ```js
 * import { useQuerySelect } from '@wordpress/data';
 * import { store as coreDataStore } from '@wordpress/core-data';
 *
 * function PageTitleDisplay( { id } ) {
 *   const { data: page, isResolving } = useQuerySelect( ( query ) => {
 *     return query( coreDataStore ).getEntityRecord( 'postType', 'page', id )
 *   }, [ id ] );
 *
 *   if ( isResolving ) {
 *     return 'Loading...';
 *   }
 *
 *   return page.title;
 * }
 *
 * // Rendered in the application:
 * // <PageTitleDisplay id={ 10 } />
 * ```
 *
 * In the above example, when `PageTitleDisplay` is rendered into an
 * application, the page and the resolution details will be retrieved from
 * the store state using the `mapSelect` callback on `useQuerySelect`.
 *
 * If the id prop changes then any page in the state for that id is
 * retrieved. If the id prop doesn't change and other props are passed in
 * that do change, the title will not change because the dependency is just
 * the id.
 * @see useSelect
 *
 * @return {Object} An object containing the data and resolution info.
 */
export default function useQuerySelect(mapQuerySelect, deps) {
	return useSelect((select, registry) => {
		const resolve = (store) => enrichSelectors(select(store));
		return mapQuerySelect(resolve, registry);
	}, deps);
}

/**
 * Transform simple selectors into ones that return an object with the
 * original return value AND the resolution info.
 *
 * @param {Object} selectors Selectors to enrich
 * @return {Object} Enriched selectors
 */
const enrichSelectors = memoize((selectors) => {
	const resolvers = {};
	for (const selectorName in selectors) {
		if (META_SELECTORS.includes(selectorName)) {
			continue;
		}
		Object.defineProperty(resolvers, selectorName, {
			get:
				() =>
				(...args) => {
					const { getIsResolving, hasFinishedResolution } = selectors;
					const isResolving = !!getIsResolving(selectorName, args);
					const hasResolved =
						!isResolving &&
						hasFinishedResolution(selectorName, args);
					const data = selectors[selectorName](...args);

					let status;
					if (isResolving) {
						status = Status.Resolving;
					} else if (hasResolved) {
						if (data) {
							status = Status.Success;
						} else {
							status = Status.Error;
						}
					} else {
						status = Status.Idle;
					}

					return {
						data,
						status,
						isResolving,
						hasResolved,
					};
				},
		});
	}
	return resolvers;
});