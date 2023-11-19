/**
 * Internal dependencies
 */
import Cell from './cell';

function Header( { columns, isLoading, onSort, query, isAllSelected, onSelectAll } ) {
	return (
		<tr>
			{ columns.map( ( column, index ) => (
				<Cell
					index={ index }
					key={ column.key }
					loading={ isLoading }
					column={ column }
					onSort={ onSort }
					query={ query }
					isAllSelected={ isAllSelected }
					onSelectAll={ onSelectAll }
				/>
			) ) }
		</tr>
	);
}

export default Header;
