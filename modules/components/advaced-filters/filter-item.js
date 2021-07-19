/**
 * Internal dependencies
 */
import EntityFilter from './entity-filter';
import DateFilter from './date-filter';
import NumberFilter from './number-filter';
import SelectFilter from './select-filter';
/**
 * WordPress dependencies
 */
import { Button, Icon } from '@wordpress/components';

export default function FilterItem( props ) {
	const { filter, config, onFilterChange, query, removeFilter } = props;
	const { input } = config;
	const componentMap = {
		EntityFilter,
		DateFilter,
		NumberFilter,
		SelectFilter,
	};

	if ( ! componentMap.hasOwnProperty( input.component ) ) {
		return;
	}
	const FilterComponent = componentMap[ input.component ];
	return (
		<li className="ea-advanced-filters__list-item">
			<FilterComponent
				className="ea-advanced-filters__fieldset-item"
				config={ config }
				filter={ filter }
				onFilterChange={ onFilterChange }
				query={ query }
			/>

			<Button
				className="ea-advanced-filters__remove"
				onClick={ removeFilter }
			>
				<Icon icon="no-alt" />
			</Button>
		</li>
	);
}
