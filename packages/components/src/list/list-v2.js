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
import Pagination from '../pagination';
import { ListContext } from './context';
import './style.scss';

function List( {
	pagination = false,
	bordered = false,
	striped = false,
	split = true,
	dataSource = [],
	children,
	className,
	footer,
	header,
	itemLayout,
	loadMore,
	loading = false,
	renderItem,
	rowKey,
	size,
	style,
	...props
} ) {
	const paginationProps = pagination && typeof pagination === 'object' ? pagination : {};
	const loadingProps = loading && typeof loading === 'object' ? loading : {};
	const isLoading = loadingProps && loadingProps.active;
	const dispatchEvent = ( name, ...args ) => props[ name ]?.( ...args );
	const paginationContent = pagination ? (
		<div className={ classNames( 'eac-list__pagination', `eac-list__pagination--align-${ paginationProps?.align ?? 'right' }` ) }>
			<Pagination { ...paginationProps } onChange={ ( page, perPage ) => dispatchEvent( 'onChange', { page, perPage } ) } />
		</div>
	) : null;
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
	let childrenContent = isLoading && <div style={ { minHeight: 53 } } />;
	if ( dataSource.length > 0 ) {
		const items = dataSource.map( ( item, index ) => renderInnerItem( item, index ) );
		childrenContent = <ul className="eac-list__items">{ items }</ul>;
	} else if ( ! children && ! isLoading ) {
		childrenContent = <div className="eac-list__empty-text">{ props?.emptyText ?? 'No Data' }</div>;
	}
	const paginationPosition = paginationProps.position || 'bottom';
	const contextValue = useMemo( () => ( { grid, itemLayout } ), [ grid, itemLayout ] );
	const classes = classNames( 'eac-list', className, {
		'eac-list--bordered': !! bordered,
		'eac-list--striped': !! striped,
		'eac-list--loading': !! isLoading,
		'eac-list--vertical': itemLayout === 'vertical',
		'eac-list--large': size === 'large',
		'eac-list--small': size === 'small',
		'eac-list--split': !! split,
	} );
	return (
		<ListContext.Provider value={ contextValue }>
			<div className={ classes } style={ style }>
				{ ( paginationPosition === 'top' || paginationPosition === 'both' ) && paginationContent }
				{ header && <div className="eac-list__header">{ header }</div> }
				<Spinner { ...loadingProps } className="eac-list__spin">
					{ childrenContent }
					{ children }
				</Spinner>
				{ footer && <div className="eac-list__footer">{ footer }</div> }
				{ ( paginationPosition === 'bottom' || paginationPosition === 'both' ) && paginationContent }
			</div>
		</ListContext.Provider>
	);
}

export default List;
