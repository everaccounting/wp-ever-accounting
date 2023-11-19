function tableSummery( props ) {
	const { columns, dataSource, renderSummary } = props;
	return (
		<tr className="eac-table__row eac-table__row--summary">
			{ columns.map( ( column, index ) => {
				return (
					<td key={ index } colSpan={ column.colSpan } rowSpan={ column.rowSpan } className="eac-table__cell eac-table__cell--summary">
						{ renderSummary( column, dataSource, index ) }
					</td>
				);
			} ) }
		</tr>
	);
}

export default tableSummery;
