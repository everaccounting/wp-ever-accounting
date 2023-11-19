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
	const triggerPaginationEvent = ( eventName ) => ( page, perPage ) => {
		dispatchEvent( eventName, page, perPage );
	};
}

export default List;
