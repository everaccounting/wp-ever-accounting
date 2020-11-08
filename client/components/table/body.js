import {useState, useEffect} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";
import {__} from '@wordpress/i18n';

/**
 * Display table body
 *
 * @param columns
 * @param data
 * @param selected
 * @param isLoading
 * @param onSelect
 * @param noResult
 * @returns {*}
 * @constructor
 */
function TableBody({columns, data, selected, isLoading, onSelect, noResult}) {

	const CheckColumn = ({row, column}) => {
		return (
			<th className="manage-column column-cb">
				<label className="screen-reader-text">{__('Select All')}</label>
				<input
					type="checkbox"
					disabled={!!isLoading}
					checked={ selected.includes(row) }
					onChange={(ev) => onSelect(row, ev.target.checked)}
				/>
			</th>
		);
	}

	if (isLoading) {
		return(
			<tr className="is-placeholder">
				{ columns.map( ( item, pos ) => (
					<td key={ pos }>
						<div className="ea-placeholder__loading" />
					</td>
				) ) }

			</tr>
		)
	}

	if (!isLoading && data.length === 0) {
		return (
			<tr className='no-results'>
				<td colSpan={columns.length}>{__('No results')}</td>
			</tr>
		)
	}

	return (
		<>
			{data.map((row, index) => {
				return (
					<tr className={`level-${index}`} key={index}>

						{columns.map((column, idx) => {
							if (column.type === 'selection') {
								return (<CheckColumn key={idx} row={row} column={column}/>)
							}

							return (
								<td
									key={idx}
									className={classNames({
										'check-column': 'selection' === column.type,
										['column-' + column.columnKey]: !!column.columnKey,
									})}>
									{column.render(row, column, index)}
								</td>
							)
						})}

					</tr>
				)
			})}
		</>
	)
}

export default TableBody;
