/**
 * WordPress dependencies
 */
import { useDispatch, useSelect } from '@wordpress/data';
import { useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import useQuerySelect from './use-query-select';
import { store as coreStore } from '../';

/**
 * Resolves the specified entity record.
 *
 * @since 6.1.0 Introduced in WordPress core.
 *
 * @param  name     Name of the entity, e.g. `plugin` or a `post`. See rootEntitiesConfig in ../entities.ts for a list of available names.
 * @param  recordId ID of the requested entity record.
 * @param  options  Optional hook options.
 * @example
 * ```js
 * import { useEntityRecord } from '@wordpress/core-data';
 *
 * function PageTitleDisplay( { id } ) {
 *   const { record, isResolving } = useEntityRecord( 'postType', 'page', id );
 *
 *   if ( isResolving ) {
 *     return 'Loading...';
 *   }
 *
 *   return record.title;
 * }
 *
 * // Rendered in the application:
 * // <PageTitleDisplay id={ 1 } />
 * ```
 *
 * In the above example, when `PageTitleDisplay` is rendered into an
 * application, the page and the resolution details will be retrieved from
 * the store state using `getEntityRecord()`, or resolved if missing.
 *
 * @example
 * ```js
 * import { useDispatch } from '@wordpress/data';
 * import { useCallback } from '@wordpress/element';
 * import { __ } from '@wordpress/i18n';
 * import { TextControl } from '@wordpress/components';
 * import { store as noticeStore } from '@wordpress/notices';
 * import { useEntityRecord } from '@wordpress/core-data';
 *
 * function PageRenameForm( { id } ) {
 * 	const page = useEntityRecord( 'postType', 'page', id );
 * 	const { createSuccessNotice, createErrorNotice } =
 * 		useDispatch( noticeStore );
 *
 * 	const setTitle = useCallback( ( title ) => {
 * 		page.edit( { title } );
 * 	}, [ page.edit ] );
 *
 * 	if ( page.isResolving ) {
 * 		return 'Loading...';
 * 	}
 *
 * 	async function onRename( event ) {
 * 		event.preventDefault();
 * 		try {
 * 			await page.save();
 * 			createSuccessNotice( __( 'Page renamed.' ), {
 * 				type: 'snackbar',
 * 			} );
 * 		} catch ( error ) {
 * 			createErrorNotice( error.message, { type: 'snackbar' } );
 * 		}
 * 	}
 *
 * 	return (
 * 		<form onSubmit={ onRename }>
 * 			<TextControl
 * 				label={ __( 'Name' ) }
 * 				value={ page.editedRecord.title }
 * 				onChange={ setTitle }
 * 			/>
 * 			<button type="submit">{ __( 'Save' ) }</button>
 * 		</form>
 * 	);
 * }
 *
 * // Rendered in the application:
 * // <PageRenameForm id={ 1 } />
 * ```
 *
 * In the above example, updating and saving the page title is handled
 * via the `edit()` and `save()` mutation helpers provided by
 * `useEntityRecord()`;
 *
 * @return {Object} Object with the following properties:
 * - `record` (`Object`): The resolved entity record.
 * - `editedRecord` (`Object`): The edited entity record.
 * - `hasEdits` (`boolean`): Whether the entity record has edits.
 * - `isResolving` (`boolean`): Whether the entity record is being resolved.
 * - `isSaving` (`boolean`): Whether the entity record is being saved.
 * - `error` (`Error`): The error that occurred while resolving or saving the entity record.
 */
export default function useEntityRecord(
	name,
	recordId,
	options = { enabled: true }
) {
	const { editEntityRecord, saveEditedEntityRecord, deleteEntityRecord } =
		useDispatch(coreStore);

	const mutations = useMemo(
		() => ({
			edit: (record) => editEntityRecord(name, recordId, record),
			save: (saveOptions = {}) =>
				saveEditedEntityRecord(name, recordId, {
					throwOnError: true,
					...saveOptions,
				}),
			delete: () => deleteEntityRecord(name, recordId),
		}),
		[
			editEntityRecord,
			name,
			recordId,
			saveEditedEntityRecord,
			deleteEntityRecord,
		]
	);

	const { editedRecord, hasEdits } = useSelect(
		(select) => ({
			editedRecord: select(coreStore).getEditedEntityRecord(
				name,
				recordId
			),
			hasEdits: select(coreStore).hasEditsForEntityRecord(name, recordId),
		}),
		[name, recordId]
	);

	const { data: record, ...querySelectRest } = useQuerySelect(
		(query) => {
			if (!options.enabled) {
				return {
					data: null,
				};
			}
			return query(coreStore).getEntityRecord(name, recordId);
		},
		[name, recordId, options.enabled]
	);

	return {
		record,
		editedRecord,
		hasEdits,
		...querySelectRest,
		...mutations,
	};
}