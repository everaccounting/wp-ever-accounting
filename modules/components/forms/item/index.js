/**
 * WordPress dependencies
 */
// import { Button } from '@wordpress/components';
// import { useSelect, useDispatch } from '@wordpress/data';
import { __ } from '@wordpress/i18n';
// import { useEffect } from '@wordpress/element';
// import { applyFilters } from '@wordpress/hooks';
/**
 * External dependencies
 */
// import { isEmpty } from 'lodash';
import { useForm, Controller } from 'react-hook-form';
/**
 * Internal dependencies
 */
import Modal from '../../modal';
import TextControl from '../../text-control';
import { forwardRef, useMemo } from '@wordpress/element';
import EntitySelect from '../../entity-select';
import { useSelect } from '@wordpress/data';
import { CORE_STORE_NAME } from '@eaccounting/data';
// import TabPanel from '../../tab-panel';
// import { CORE_STORE_NAME } from '@eaccounting/data';
// import TextareaControl from '../../textarea-control';
// import InputControl from '../../input-control';

export default function ItemModal(props) {
	console.log(props);
	const { item = { id: undefined }, onSave = (x) => x, onClose } = props;
	const { title = item.id ? __('Update Item') : __('Add Item') } = props;

	const { isSavingEntityRecord, entityRecordSaveError, defaultCurrency } =
		useSelect((select) => {
			const {
				isSavingEntityRecord,
				getEntityRecordSaveError,
				getDefaultCurrency,
			} = select(CORE_STORE_NAME);
			return {
				isSavingEntityRecord: isSavingEntityRecord('items'),
				entityRecordSaveError: getEntityRecordSaveError('items'),
				defaultCurrency: getDefaultCurrency(),
			};
		});

	const {
		handleSubmit,
		control,
		formState: { isValid },
	} = useForm({
		defaultValues: item,
		mode: 'onChange',
	});
	const onSubmit = (data) => {
		console.log(data);
	};

	console.log(defaultCurrency);

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
					/>
					<Controller
						render={({ field }) => (
							<TextControl
								before={defaultCurrency.code}
								label={__('Sale Price')}
								{...field}
							/>
						)}
						control={control}
						name="sale_price"
						rules={{ required: true }}
					/>
					<Controller
						render={({ field }) => (
							<EntitySelect
								{...field}
								creatable={true}
								label={__('Category')}
								entityName={'itemCategories'}
							/>
						)}
						control={control}
						name="category"
						rules={{ required: true }}
					/>
					<input type="submit" />
				</form>
			</Modal>
		</>
	);
}
