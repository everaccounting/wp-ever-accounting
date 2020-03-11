/**
 * External dependencies
 */

import PropTypes from 'prop-types';

/**
 * Internal dependencies
 */
import SortableColumn from './sortable-column';
import Column from './column';
import CheckColumn from './check-column';

const TableHeader = props => {
	const { isDisabled, onSetAllSelected, onSetOrderBy, isSelected, headers } = props;
	const setSelected = ev => {
		onSetAllSelected(ev.target.checked);
	};

	return (
		<tr>
			{headers.map(item => {
				const { primary = false, check = false, sortable = true } = item;

				if (check === true) {
					return (
						<CheckColumn
							onSetAllSelected={setSelected}
							isDisabled={isDisabled}
							isSelected={isSelected}
							key={item.name}
						/>
					);
				}

				if (sortable === false) {
					return <Column name={item.name} text={item.title} key={item.name} primary={primary} />;
				}

				return (
					<SortableColumn
						name={item.name}
						text={item.title}
						key={item.name}
						onSetOrderBy={onSetOrderBy}
						primary={primary}
					/>
				);
			})}
		</tr>
	);
};

TableHeader.propTypes = {
	isDisabled: PropTypes.bool.isRequired,
	isSelected: PropTypes.bool.isRequired,
	headers: PropTypes.array.isRequired,
	onSetAllSelected: PropTypes.func,
	onSetOrderBy: PropTypes.func,
};

export default TableHeader;
