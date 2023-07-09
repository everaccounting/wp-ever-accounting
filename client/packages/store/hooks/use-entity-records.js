/**
 * WordPress dependencies
 */
import { addQueryArgs } from '@wordpress/url';

/**
 * Internal dependencies
 */
import useQuerySelect from './use-query-select';
import { store as coreStore } from '../index';
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

const EMPTY_ARRAY = [];

/**
 * Resolves the specified entity records.
 *
 * @since 6.1.0 Introduced in WordPress core.
 *
 * @param  name      Name of the entity, e.g. `plugin` or a `post`. See rootEntitiesConfig in ../entities.ts for a list of available names.
 * @param  queryArgs Optional HTTP query description for how to fetch the data, passed to the requested API endpoint.
 * @param  options   Optional hook options.
 * @example
 * ```js
 * import { useEntityRecords } from '@wordpress/core-data';
 *
 * function PageTitlesList() {
 *   const { records, isResolving } = useEntityRecords( 'postType', 'page' );
 *
 *   if ( isResolving ) {
 *     return 'Loading...';
 *   }
 *
 *   return (
 *     <ul>
 *       {records.map(( page ) => (
 *         <li>{ page.title }</li>
 *       ))}
 *     </ul>
 *   );
 * }
 *
 * // Rendered in the application:
 * // <PageTitlesList />
 * ```
 *
 * In the above example, when `PageTitlesList` is rendered into an
 * application, the list of records and the resolution details will be retrieved from
 * the store state using `getEntityRecords()`, or resolved if missing.
 *
 * @return {Object} An object with the following properties:
 * - `records`: The resolved records.
 * - `isResolving`: Whether the records are being resolved.
 * - `isError`: Whether the records are being resolved.
 * - `error`: The error object if the records failed to resolve.
 * - `query`: The query object used to resolve the records.
 * - `invalidateCache`: A function that can be called to invalidate the cache for the query.
 * - `totalRecords`: The total number of records matching the query.
 * - `totalPages`: The total number of pages matching the query.
 */
export default function useEntityRecords(
	name,
	queryArgs = {},
	options = { enabled: true }
) {
	// Serialize queryArgs to a string that can be safely used as a React dep.
	// We can't just pass queryArgs as one of the deps, because if it is passed
	// as an object literal, then it will be a different object on each call even
	// if the values remain the same.
	const queryAsString = addQueryArgs('', queryArgs);
	const { deleteEntityRecord } = useDispatch(coreStore);

	const mutations = useMemo(
		() => ({
			deleteRecord: (recordId) => deleteEntityRecord(name, recordId),
		}),
		[name, deleteEntityRecord]
	);

	const { data: records, ...rest } = useQuerySelect(
		(query) => {
			if (!options.enabled) {
				return {
					// Avoiding returning a new reference on every execution.
					data: EMPTY_ARRAY,
				};
			}
			return query(coreStore).getEntityRecords(name, queryArgs);
		},
		[name, queryAsString, options.enabled]
	);

	const total = useSelect(
		(select) => {
			if (!options.enabled) {
				return 0;
			}
			return select(coreStore).getEntityRecordsTotal(name, queryArgs);
		},
		[options.enabled, name, queryArgs]
	);

	return {
		records,
		totalRecords: total,
		...rest,
		...mutations,
	};
}
