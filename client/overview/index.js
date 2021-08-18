/**
 * WordPress dependencies
 */
import { render, forwardRef, useMemo } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import { __ } from '@wordpress/i18n';

/**
 * External dependencies
 */
import { TextControl, EntitySelect } from '@eaccounting/components';
/**
 * External dependencies
 */

import { useForm, useField, splitFormProps } from 'react-form';
import { useDispatch, useSelect } from '@wordpress/data';

const InputField = forwardRef((props, ref) => {
	const { component = 'input', ...otherProps } = props;
	// Let's use splitFormProps to get form-specific props
	const [field, fieldOptions, rest] = splitFormProps(otherProps);

	// Use the useField hook with a field and field options
	// to access field state
	const {
		meta: { error },
		getInputProps,
		setValue,
	} = useField(field, fieldOptions);
	// Build the field
	const Comp = component;
	return (
		<Comp
			{...getInputProps({ ref, ...rest })}
			onChange={setValue}
			help={error}
		/>
	);
});

export default function Overview(props) {
	const defaultValues = useSelect(
		(select) => select('ea/core').getEntityRecord('items', 2),
		[]
	);

	const { saveEntityRecord } = useDispatch('ea/core');

	const {
		Form,
		meta: { isSubmitting, canSubmit },
		reset,
	} = useForm({
		defaultValues,
		validate: (values) => {
			return false;
		},
		onSubmit: async (values) => {
			await saveEntityRecord('items', values);
			reset(true);
		},
		debugForm: true,
	});

	return (
		<Form>
			<InputField
				required
				label={__('Name')}
				field="name"
				component={TextControl}
			/>
			<InputField
				required
				label={__('Sale Price')}
				field="sale_price"
				component={TextControl}
				filterValue={(val) => val.replace(/[^0-9.]/g, '')}
			/>
			<InputField
				required
				label={__('Purchase Price')}
				field="purchase_price"
				component={TextControl}
				filterValue={(val) => val.replace(/[^0-9.]/g, '')}
			/>
			<EntitySelect entityName={'items'} label={__('Item')} />
			<EntitySelect
				entityName={'incomeCategories'}
				label={__('incomeCategories')}
			/>
			<EntitySelect
				entityName={'expenseCategories'}
				label={__('expenseCategories')}
			/>
			<EntitySelect
				entityName={'itemCategories'}
				label={__('itemCategories')}
			/>
			<EntitySelect entityName={'customers'} label={__('customers')} />
			<EntitySelect entityName={'vendors'} label={__('vendors')} />
			<EntitySelect entityName={'accounts'} label={__('accounts')} />

			{isSubmitting ? (
				'Submitting...'
			) : (
				<div>
					<button type="submit" disabled={!canSubmit}>
						Submit
					</button>
				</div>
			)}
		</Form>
	);
}
