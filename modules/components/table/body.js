/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Button } from '@wordpress/components';

function ColumnActions({ actions, column, row }) {
	actions = actions
		.filter((item) => item)
		.map((action) => {
			const { label, onClick } = action;
			return (
				<Button
					isLink={true}
					style={{ textDecoration: 'none' }}
					key={label}
					onClick={(event) => {
						event.preventDefault();
						onClick(row, column);
					}}
				>
					{label}
				</Button>
			);
		});

	return actions.length ? (
		<div className="row-actions">
			{actions.reduce((prev, curr) => [prev, ' | ', curr])}
		</div>
	) : null;
}

function TableBody({
	columns,
	rows,
	selected,
	isRequesting,
	onSelect,
	saving,
}) {
	const CheckColumn = ({ row, column }) => {
		const { property } = column;
		return (
			<th className="manage-column column-cb">
				{/* eslint-disable-next-line jsx-a11y/label-has-for */}
				<label className="screen-reader-text">{__('Select All')}</label>
				<input
					type="checkbox"
					disabled={!!isRequesting || saving.includes(row[property])}
					checked={selected.includes(row)}
					onChange={(ev) => onSelect(row, ev.target.checked, column)}
				/>
			</th>
		);
	};

	if (isRequesting) {
		return (
			<tr className="is-placeholder">
				{columns.map((item, pos) => (
					<td key={pos}>
						<div className="ea-placeholder__loading" />
					</td>
				))}
			</tr>
		);
	}

	if (!isRequesting && rows && rows.length === 0) {
		return (
			<tr className="no-results">
				<td colSpan={columns.length}>{__('No results')}</td>
			</tr>
		);
	}

	return (
		<>
			{rows &&
				rows.map((row, index) => {
					return (
						<tr className={`level-${index}`} key={index}>
							{columns.map((column, idx) => {
								if (column.type === 'selection') {
									return (
										<CheckColumn
											key={idx}
											row={row}
											column={column}
										/>
									);
								}

								return (
									<td
										key={idx}
										className={classNames({
											'check-column':
												column.type === 'selection',
											'has-row-actions':
												!!column.actions.length,
											'column-primary':
												!!column.isPrimary,
											['column-' + column.columnKey]:
												!!column.columnKey,
										})}
									>
										{column.render(row, column, index)}

										{!!column.actions.length && (
											<ColumnActions
												actions={column.actions}
												column={column}
												row={row}
											/>
										)}
									</td>
								);
							})}
						</tr>
					);
				})}
		</>
	);
}

export default TableBody;
