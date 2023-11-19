/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import { Fragment, useState } from '@wordpress/element';
import { find, first, without } from 'lodash';
import {
	Card,
	CardBody,
	CardFooter,
	CardHeader,
} from '@wordpress/components';
/**
 * Internal dependencies
 */
import EllipsisMenu from '../ellipsis-menu';
import MenuItem from '../ellipsis-menu/menu-item';
import MenuTitle from '../ellipsis-menu/menu-title';
import { Pagination_v1 } from '../pagination';
import Table from './table';
import TablePlaceholder from './placeholder';
import TableSummary, { TableSummaryPlaceholder } from './summary';
const defaultOnQueryChange = () => () => {};
const defaultOnColumnsChange = () => {};
/**
 * This is an accessible, sortable, and scrollable table for displaying tabular data (like revenue and other analytics data).
 * It accepts `headers` for column headers, and `rows` for the table content.
 * `rowHeader` can be used to define the index of the row header (or false if no header).
 *
 * `TableCard` serves as Card wrapper & contains a card header, `<Table />`, `<TableSummary />`, and `<Pagination />`.
 * This includes filtering and comparison functionality for report pages.
 * @param root0
 * @param root0.actions
 * @param root0.className
 * @param root0.hasSearch
 * @param root0.headers
 * @param root0.ids
 * @param root0.isLoading
 * @param root0.onQueryChange
 * @param root0.onColumnsChange
 * @param root0.onSort
 * @param root0.query
 * @param root0.rowHeader
 * @param root0.rows
 * @param root0.rowsPerPage
 * @param root0.showMenu
 * @param root0.summary
 * @param root0.title
 * @param root0.totalRows
 * @param root0.rowKey
 * @param root0.emptyMessage
 */
const TableCard = ({
	actions,
	className,
	hasSearch,
	headers = [],
	ids,
	isLoading = false,
	onQueryChange = defaultOnQueryChange,
	onColumnsChange = defaultOnColumnsChange,
	onSort,
	query = {},
	rowHeader = 0,
	rows = [],
	rowsPerPage,
	showMenu = true,
	summary,
	title,
	totalRows,
	rowKey,
	emptyMessage = undefined,
	...props
}) => {
	// eslint-disable-next-line no-console
	const getShowCols = (_headers = []) => {
		return _headers
			.map(({ key, visible }) => {
				if (typeof visible === 'undefined' || visible) {
					return key;
				}
				return false;
			})
			.filter(Boolean);
	};
	const [showCols, setShowCols] = useState(getShowCols(headers));
	const onColumnToggle = (key) => {
		return () => {
			const hasKey = showCols.includes(key);
			if (hasKey) {
				// Handle hiding a sorted column
				if (query.orderby === key) {
					const defaultSort = find(headers, {
						defaultSort: true,
					}) ||
						first(headers) || { key: undefined };
					onQueryChange('sort')(defaultSort.key, 'desc');
				}
				const newShowCols = without(showCols, key);
				onColumnsChange(newShowCols, key);
				setShowCols(newShowCols);
			} else {
				const newShowCols = [...showCols, key];
				onColumnsChange(newShowCols, key);
				setShowCols(newShowCols);
			}
		};
	};
	const onPageChange = (newPage, direction) => {
		if (props.onPageChange) {
			props.onPageChange(newPage, direction);
		}
		if (onQueryChange) {
			onQueryChange('paged')(newPage.toString(), direction);
		}
	};
	const allHeaders = headers;
	const visibleHeaders = headers.filter(({ key }) => showCols.includes(key));
	const visibleRows = rows.map((row) => {
		return headers
			.map(({ key }, i) => {
				return showCols.includes(key) && row[i];
			})
			.filter(Boolean);
	});
	const classes = classnames('woocommerce-table', className, {
		'has-actions': !!actions,
		'has-menu': showMenu,
		'has-search': hasSearch,
	});
	return (
		<Card className={classes}>
			<CardHeader>
				{title}
				<div className="woocommerce-table__actions">{actions}</div>
				{showMenu && (
					<EllipsisMenu
						label={__('Choose which values to display', 'wp-ever-accounting')}
						renderContent={() => (
							<Fragment>
								<MenuTitle>{__('Columns:', 'wp-ever-accounting')}</MenuTitle>
								{allHeaders.map(({ key, label, required }) => {
									if (required) {
										return null;
									}
									return (
										<MenuItem
											checked={showCols.includes(key)}
											isCheckbox
											isClickable
											key={key}
											onInvoke={key !== undefined ? onColumnToggle(key) : undefined}
										>
											{label}
										</MenuItem>
									);
								})}
							</Fragment>
						)}
					/>
				)}
			</CardHeader>
			{/* Ignoring the error to make it backward compatible for now. */}
			{/* @ts-expect-error: size must be one of small, medium, largel, xSmall, extraSmall. */}
			<CardBody size={null}>
				{isLoading ? (
					<Fragment>
						<span className="screen-reader-text">
							{__('Your requested data is loading', 'wp-ever-accounting')}
						</span>
						<TablePlaceholder
							numberOfRows={rowsPerPage}
							headers={visibleHeaders}
							rowHeader={rowHeader}
							caption={title}
							query={query}
						/>
					</Fragment>
				) : (
					<Table
						rows={visibleRows}
						headers={visibleHeaders}
						rowHeader={rowHeader}
						caption={title}
						query={query}
						onSort={onSort || onQueryChange('sort')}
						rowKey={rowKey}
						emptyMessage={emptyMessage}
					/>
				)}
			</CardBody>

			{/* @ts-expect-error: justify is missing from the latest @types/wordpress__components */}
			<CardFooter justify="center">
				{isLoading ? (
					<TableSummaryPlaceholder />
				) : (
					<Fragment>
						<Pagination
							key={parseInt(query.paged, 10) || 1}
							page={parseInt(query.paged, 10) || 1}
							perPage={rowsPerPage}
							total={totalRows}
							onPageChange={onPageChange}
							onPerPageChange={(perPage) => onQueryChange('per_page')(perPage.toString())}
						/>

						{summary && <TableSummary data={summary} />}
					</Fragment>
				)}
			</CardFooter>
		</Card>
	);
};
export default TableCard;
