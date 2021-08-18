/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function TableHeader(props) {
	const {
		className,
		columns,
		sort,
		onSort,
		isAllSelected = false,
		isRequesting = false,
		onSelectAll,
	} = props;
	const { orderby, order } = sort;

	return (
		<tr className={className}>
			{columns.map((column, index) => {
				const { type, columnKey, sortable, isPrimary } = column;
				const Tag = type === 'selection' ? 'td' : 'th';
				const colClasses = classNames(
					column.headerAlign,
					column.className,
					column.labelClassName,
					column.columnKey,
					{
						'manage-column': true,
						'column-primary': !!isPrimary,
						'column-cb': column.type === 'selection',
						sortable,
						['column-' + column.columnKey]: !!column.columnKey,
						asc: orderby === columnKey && order === 'asc',
						desc:
							(orderby === columnKey && order === 'desc') ||
							orderby !== columnKey,
					}
				);
				return (
					<Tag
						key={index + columnKey}
						colSpan={column.colSpan}
						rowSpan={column.rowSpan}
						scope="col"
						className={colClasses}
						style={{ width: column.width }}
					>
						{(type === 'selection' && (
							<>
								{/* eslint-disable-next-line jsx-a11y/label-has-for */}
								<label className="screen-reader-text">
									{__('Select All')}
								</label>

								<input
									type="checkbox"
									checked={isAllSelected}
									disabled={!!isRequesting}
									onChange={(ev) =>
										onSelectAll(ev.target.checked)
									}
								/>
							</>
						)) ||
							(column.renderHeader &&
								column.renderHeader(column)) ||
							(sortable && (
								<>
									{/* eslint-disable-next-line jsx-a11y/anchor-is-valid */}
									<a
										href="#"
										onClick={(e) => {
											e.preventDefault();
											onSort({
												orderby: column.columnKey,
												order:
													orderby ===
														column.columnKey &&
													order === 'desc'
														? 'asc'
														: 'desc',
											});
										}}
									>
										<span>{column.label}</span>
										<span className="sorting-indicator" />
									</a>
								</>
							)) ||
							column.label}
					</Tag>
				);
			})}
		</tr>
	);
}

export default TableHeader;
