import { useEntityRecord, useEntityRecords } from '@eac/store';
import { TextControl } from '@wordpress/components';
import { useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { store as noticeStore } from '@wordpress/notices';

export function App() {
	const record = useEntityRecord('category', 2);
	const { createSuccessNotice, createErrorNotice } = useDispatch(noticeStore);
	// const { records } = useEntityRecords('category');

	async function onRename(event) {
		event.preventDefault();
		try {
			await record.save();
			createSuccessNotice(__('Page renamed.'), {
				type: 'snackbar',
			});
		} catch (error) {
			createErrorNotice(error.message, { type: 'snackbar' });
		}
	}

	const setName = useCallback(
		(name) => {
			record.edit({ name });
		},
		[record]
	);

	if (record.isResolving) {
		return 'Loading...';
	}

	return (
		<div>
			Hello World!
			{JSON.stringify(record)}
			{!!record && (
				<form onSubmit={onRename}>
					<TextControl
						label={__('Name')}
						value={record.editedRecord.name}
						onChange={setName}
					/>
					<button type="submit">{__('Save')}</button>
				</form>
			)}
		</div>
	);
}

export default App;
