/**
 * External dependencies
 */
import { useForm, Controller } from 'react-hook-form';
import { TextControl, EntitySelect } from '@eaccounting/components';

export default function Overview() {
	return (
		<>
			<EntitySelect
				entityName={'incomeCategories'}
				label="incomeCategories"
				creatable
			/>
		</>
	);
}
