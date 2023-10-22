/**
 * External dependencies
 */
import PropTypes from 'prop-types';
/**
 * Internal dependencies
 */
import Header from './header';

const EMPTY_DATA = [];

function Table(props) {
	const {
		className,
		bordered,
		columns,
		data,
		loading,
		pagination,
		rowClassName,
		rowKey,
		rowSelection,
		showHeader,
		size,
		onChange,
		onHeaderRow,
		onRow,
		style,
	} = props;

	const headerProps = {
		columns,
		data,
	};

	return <Header {...headerProps} />;
}

export default Table;
