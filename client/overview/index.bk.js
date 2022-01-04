/**
 * WordPress dependencies
 */
import { forwardRef } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

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
 * External dependencies
 */

import { useForm, useField, splitFormProps } from 'react-form';
import { useDispatch, useSelect } from '@wordpress/data';
/**
 * Internal dependencies
 */
import FormHook from './form';
import ReactForm from './react-form';

export default function Overview(props) {
	const defaultValues = useSelect(
		(select) => select('ea/core').getEntityRecord('items', 2),
		[]
	);

	const { saveEntityRecord } = useDispatch('ea/core');

	return (
		<>
			<>
				<FormHook />
				<ReactForm />
			</>
			<EntitySelect
				entityName={'items'}
				label={__('Item')}
				creatable={true}
			/>
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
		</>
	);
}
