/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';

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

export default function FormHook() {
	const { register, handleSubmit } = useForm();
	const defaultValues = useSelect(
		(select) => select('ea/core').getEntityRecord('items', 2),
		[]
	);
	const onSubmit = (data) => console.log(data);



	return (
		/* "handleSubmit" will validate your inputs before invoking "onSubmit" */
		<form onSubmit={handleSubmit(onSubmit)}>
			<Controller
				control={control}
				name="name"
				render={({ field: { value, onChange } }) => (
					<TextControl
						value={value}
						onChange={onChange}
						before={'YSD'}
					/>
				)}
			/>
			<input type="submit" />
		</form>
	);
}
