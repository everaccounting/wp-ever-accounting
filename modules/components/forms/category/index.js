/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { applyFilters } from '@wordpress/hooks';
/**
 * External dependencies
 */
import { isEmpty, trim } from 'lodash';
/**
 * Internal dependencies
 */
import Modal from '../../modal';
import Form from '../../form';
import TextControl from '../../text-control';
import { useDispatch, useSelect } from '@wordpress/data';
import { CORE_STORE_NAME } from '@eaccounting/data';
import { useEffect } from '@wordpress/element';

export default function CategoryModal(props) {
	const {
		onSave,
		item = { id: undefined },
		onClose,
		type = 'income',
	} = props;
	const { title = item.id ? __('Update Category') : __('Add Category') } =
		props;

	const { isSavingEntityRecord, entityRecordSaveError } = useSelect(
		(select) => {
			const { isSavingEntityRecord, getEntityRecordSaveError } =
				select(CORE_STORE_NAME);
			return {
				isSavingEntityRecord: isSavingEntityRecord(
					'categories',
					item.id
				),
				entityRecordSaveError: getEntityRecordSaveError(
					'categories',
					item.id
				),
			};
		}
	);

	const { saveEntityRecord, createNotice } = useDispatch(CORE_STORE_NAME);

	const validate = (values, errors = {}) => {
		if (isEmpty(trim(values.name))) {
			errors.name = __('Category Name is required');
		}
		if (isEmpty(trim(values.type))) {
			errors.type = __('Category Type is required');
		}
		return applyFilters(
			'EACCOUNTING_VALIDATE_CATEGORY_PARAMS',
			errors,
			values
		);
	};

	const onSubmit = async (item) => {
		const res = await saveEntityRecord('categories', { ...item, type });
		if (!isSavingEntityRecord && res && res.id) {
			createNotice('success', __('Category saved successfully!'));
			onSave(res);
		}
	};

	useEffect(() => {
		// eslint-disable-next-line no-unused-expressions
		entityRecordSaveError &&
			createNotice('error', entityRecordSaveError.message);
	}, [entityRecordSaveError]);

	return (
		<>
			<Modal title={title} onClose={onClose}>
				<Form
					initialValues={{ ...item, type }}
					onSubmitCallback={onSubmit}
					validate={validate}
				>
					{({ getInputProps, isValidForm, handleSubmit }) => (
						<>
							<TextControl
								required={true}
								label={__('Name')}
								{...getInputProps('name')}
							/>
							<Button
								type="submit"
								isPrimary
								disabled={!isValidForm || isSavingEntityRecord}
								onClick={handleSubmit}
							>
								{__('Submit')}
							</Button>
						</>
					)}
				</Form>
			</Modal>
		</>
	);
}
