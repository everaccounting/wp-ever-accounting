/**
 * External dependencies
 */
import { range } from 'lodash';
/**
 * Internal dependencies
 */
import Table from './table';
/**
 * `TablePlaceholder` behaves like `Table` but displays placeholder boxes instead of data. This can be used while loading.
 * @param root0
 * @param root0.query
 * @param root0.caption
 * @param root0.headers
 * @param root0.numberOfRows
 */
const TablePlaceholder = ({ query, caption, headers, numberOfRows = 5, ...props }) => {
	const rows = range(numberOfRows).map(() =>
		headers.map(() => ({
			display: <span className="is-placeholder" />,
		}))
	);
	const tableProps = { query, caption, headers, numberOfRows, ...props };
	return <Table ariaHidden={true} className="is-loading" rows={rows} {...tableProps} />;
};
export default TablePlaceholder;