/**
 * WordPress dependencies
 */
import { useMemo, useState, Fragment } from '@wordpress/element';
/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Spinner from '../spinner';
import Empty from '../empty';
import Pagination, { usePagination } from '../pagination';
import Item from './item';
import { usePrevious } from '../../hooks';
import './style.scss';

function List( props ) {
	const {
		className,
		bordered = true,
		striped,
		direction = 'vertical',
		split = true,
		loading,
		header,
		data,
		footer,
		renderItem,
		rowKey,
		size = 'small',
		pagination = true,
		style,
		children,
	} = props;

	const listData = usePrevious( data );

	const paginationProps = usePagination( {
		total: listData?.length,
		pagination,
		onChange: pagination?.onChange,
	} );

	const renderInnerItem = ( item, index ) => {
		if ( ! renderItem ) return null;
		let key;
		if ( typeof rowKey === 'function' ) {
			key = rowKey( item );
		} else if ( rowKey ) {
			key = item[ rowKey ];
		} else {
			key = item.key;
		}
		if ( ! key ) {
			key = `list-item-${ index }`;
		}
		return <Fragment key={ key }>{ renderItem( item, index ) }</Fragment>;
	};

	const renderList = () => {
		if ( listData && listData.length ) {
			const items = listData.map( ( item, index ) => {
				return renderInnerItem( item, index );
			} );

			return <ul className="eac-list__items">{ items }</ul>;
		} else if ( ! children && ! loading ) {
			return (
				<div className="eac-list__empty-text">
					{ props?.emptyMessage ?? <Empty description="No Data" /> }
				</div>
			);
		}

		return null;
	};

	const classes = classNames( 'eac-list', className, {
		'eac-list--loading': loading,
		'eac-list--bordered': bordered,
		'eac-list--striped': striped,
		'eac-list--split': split,
		'eac-list--vertical': direction === 'vertical',
		'eac-list--small': size === 'small',
		'eac-list--large': size === 'large',
		'eac-list--has-something-after': !! ( pagination || footer ),
	} );

	const renderHeader = () => {
		if ( header ) {
			return <div className="eac-list__header">{ header }</div>;
		}
		return null;
	};

	const renderFooter = () => {
		if ( footer ) {
			return <div className="eac-list__footer">{ footer }</div>;
		}
		return null;
	};

	const renderPagination = () => {
		if ( pagination ) {
			return (
				<div className="eac-list__pagination">
					<Pagination { ...paginationProps } disable={ loading } />
				</div>
			);
		}
		return null;
	};

	return (
		<>
			<div className={ classes } style={ style }>
				{ renderHeader() }
				<Spinner isActive={ !! loading }>
					{ renderList() }
					{ children }
				</Spinner>
				{ renderFooter() }
			</div>
			{ renderPagination() }
		</>
	);
}

List.Item = Item;
export default List;
