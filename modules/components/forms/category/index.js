/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
import { useDispatch, useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
import Modal from '../../modal';
import TextControl from '../../text-control';

/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';
import { CORE_STORE_NAME } from '@eaccounting/data';

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
				isSavingEntityRecord: isSavingEntityRecord('categories'),
				entityRecordSaveError: getEntityRecordSaveError('categories'),
			};
		}
	);

	const { saveEntityRecord, createNotice } = useDispatch(CORE_STORE_NAME);

	const {
		handleSubmit,
		control,
		formState: { isValid },
	} = useForm({
		defaultValues: item,
		mode: 'onChange',
	});

	const onSubmit = async (item) => {
		const res = await saveEntityRecord('categories', { ...item, type });
		if (!isSavingEntityRecord(res.id) && res && res.id) {
			createNotice('success', __('Category saved successfully!'));
			onSave(res);
		}
	};

	const validateName = (name) => {
		console.log(name);
		return name;
	};

	const saveError = entityRecordSaveError(item.id);
	if (saveError && saveError.message) {
		createNotice('error', entityRecordSaveError.message);
	}

	return (
		<>
			<Modal title={title} onClose={onClose}>
				<form onSubmit={handleSubmit(onSubmit)}>
					<Controller
						render={({ field }) => (
							<TextControl label={__('Name')} {...field} />
						)}
						control={control}
						name="name"
						rules={{ required: true, validate: validateName }}
					/>

					<Button
						type="submit"
						isPrimary
						disabled={!isValid || isSavingEntityRecord(item.id)}
						onClick={handleSubmit}
					>
						{__('Submit')}
					</Button>
				</form>
			</Modal>
		</>
	);
}
