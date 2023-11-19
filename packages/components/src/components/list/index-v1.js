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
import Pagination from '../pagination';
import Item from './item';
import './style.scss';

function List( props ) {
	const {
		className,
		bordered,
		striped,
		direction = 'vertical',
		split = true,
		loading,
		header,
		data,
		total,
		footer,
		renderItem,
		rowKey,
		size = 'small',
		pagination = false,
		style,
		children,
	} = props;

	const classes = classNames( 'eac-list', className, {
		'eac-list--loading': loading,
		'eac-list--bordered': bordered,
		'eac-list--striped': striped,
		'eac-list--vertical': direction === 'vertical',
		'eac-list--horizontal': direction === 'horizontal',
		'eac-list--split': split,
		'eac-list--small': size === 'small',
		'eac-list--large': size === 'large',
		'eac-list--has-header': header,
		'eac-list--has-footer': footer,
		'eac-list--has-pagination': pagination,
		'eac-list--has-something-after': !! ( pagination || footer ),
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
		if ( loading ) {
			return <div style={ { minHeight: 53 } } />;
		} else if ( data && data.length ) {
			const items = data.map( ( item, index ) => {
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

	return (
		<div className={ classes } style={ style }>
			{ header && <div className="eac-list__header">{ header }</div> }
			<Spinner isActive={ loading }>
				{ renderList() }
				{ children }
				{ pagination && (
					<Pagination
						page={ pagination?.page ?? 1 }
						{ ...pagination }
						total={ total }
						onChange={ ( page, perPage ) => props?.onChange?.( { page, perPage } ) }
						{ ...( typeof pagination === 'object' ? pagination : {} ) }
					/>
				) }
			</Spinner>
			{ footer && <div className="eac-list__footer">{ footer }</div> }
		</div>
	);
}

List.Item = Item;
export default List;
