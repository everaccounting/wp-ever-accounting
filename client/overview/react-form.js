/**
 * External dependencies
 */

import {
	TextControl,
	EntitySelect,
	Modal,
	// InputControl,
} from '@eaccounting/components';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { useSelect } from '@wordpress/data';
import { __ } from '@wordpress/i18n';

import { useForm, Field } from 'react-form';

export default function FormHook() {
	const defaultValues = useSelect(
		(select) => select('ea/core').getEntityRecord('items', 2),
		[]
	);

	console.log(defaultValues);

	const {
		Form,
		meta: { canSubmit, isSubmitting },
	} = useForm({
		defaultValues,
		debugForm: true,
		onSubmit: (values) => {
			console.log('Huzzah!', values);
		},
	});

	return (
		<>
			<p>React form start</p>
			<Form>
				<Field />
				{/*<InputField*/}
				{/*	required*/}
				{/*	label={__('Sale Price')}*/}
				{/*	field="sale_price"*/}
				{/*	filterValue={(val) => val.replace(/[^0-9.]/g, '')}*/}
				{/*	component={TextControl}*/}
				{/*/>*/}
				{/*<InputField*/}
				{/*	required*/}
				{/*	label={__('Purchase Price')}*/}
				{/*	field="purchase_price"*/}
				{/*	component={TextControl}*/}
				{/*	filterValue={(val) => val.replace(/[^0-9.]/g, '')}*/}
				{/*/>*/}

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
			<p>React form end</p>
		</>
	);
}
