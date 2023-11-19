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
import { ListContext } from './context';
import './style.scss';

function List( {
	bordered = false,
	children,
	className,
	dataSource = [],
	footer,
	grid,
	header,
	itemLayout,
	loadMore,
	loading = false,
	pagination = false,
	renderItem,
	rowKey,
	size,
	split = true,
	style,
	...props
} ) {
	const paginationProps = pagination && typeof pagination === 'object' ? pagination : {};
	const loadingProps = loading && typeof loading === 'object' ? loading : {};
	const isLoading = loadingProp && loadingProp.spinning;
	const [ state, setState ] = useState( {
		page: paginationProps.defaultPage || 1,
		perPage: paginationProps.defaultPerPage || 20,
		total: paginationProps.total || 0,
	} );
	let splitDataSource = [ ...dataSource ];
	if ( pagination ) {
		if ( dataSource.length > ( paginationProps.page - 1 ) * paginationProps.perPage ) {
			splitDataSource = [ ...dataSource ].splice( ( paginationProps.page - 1 ) * paginationProps.perPage, paginationProps.perPage );
		}
	}
	const childrenContent = isLoading && <div style={ { minHeight: 53 } } />;
	if (splitDataSource.length > 0) {
		const items = splitDataSource.map( ( item, index ) => {
			return renderItem( item, index );
		} );
		return (
			<ListContext.Provider value={ contextValue }>
				<div className={ classes }>
					{ header && <div className="eac-list__header">{ header }</div> }
					<Spinner { ...loadingProps }>{ items }</Spinner>
					{ footer && <div className="eac-list__footer">{ footer }</div> }
				</div>
			</ListContext.Provider>
		);
	}

	// const paginationOnChange = ( eventName ) => ( newPage, newPerPage ) => {
	// 	setPage( newPage );
	// 	setPerPage( newPerPage );
	// 	if ( pagination && pagination[ eventName ] ) {
	// 		pagination?.[ eventName ]?.( newPage, newPerPage );
	// 	}
	// };
	// const onPaginationChange = paginationOnChange( 'onChange' );
	// const onPaginationShowSizeChange = triggerPaginationEvent( 'onShowSizeChange' );

	// const renderInnerItem = ( item, index ) => {
	// 	if ( ! renderItem ) return null;
	// 	let key;
	// 	if ( typeof rowKey === 'function' ) {
	// 		key = rowKey( item );
	// 	} else if ( rowKey ) {
	// 		key = item[ rowKey ];
	// 	} else {
	// 		key = item.key;
	// 	}
	// 	if ( ! key ) {
	// 		key = `list-item-${ index }`;
	// 	}
	//
	// 	return <Fragment key={ key }>{ renderItem( item, index ) }</Fragment>;
	// };

	// const paginationPosition = paginationProps.position || 'bottom';
	const isLoading = loading?.loading || false;
	const classes = classNames( 'eac-list', className, {
		'eac-list--bordered': !! bordered,
		'eac-list--loading': !! isLoading,
		'eac-list--vertical': itemLayout === 'vertical',
		'eac-list--lg': size === 'large',
		'eac-list--sm': size === 'small',
		'eac-list--split': !! split,
	} );
	const contextValue = useMemo( () => ( { grid, itemLayout } ), [ grid, itemLayout ] );

	return (
		<ListContext.Provider value={ contextValue }>
			<div className={ classes }>
				{ header && <div className="eac-list__header">{ header }</div> }
				<Spinner { ...loadingProps }>{ children }</Spinner>
				{ footer && <div className="eac-list__footer">{ footer }</div> }
			</div>
		</ListContext.Provider>
	);
}

export default List;
