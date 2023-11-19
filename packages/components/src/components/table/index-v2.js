/**
 * WordPress dependencies
 */
import { forwardRef, Fragment, useMemo, useRef, useState } from '@wordpress/element';
import { Button, Tooltip, Icon, SearchControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';
/**
 * External dependencies
 */
import classNames from 'classnames';
import { pickBy, identity, debounce } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import Dropdown from '../dropdown';
import Input from '../input';
import Result from '../result';
import Empty from '../empty';
import { usePrevious, useControlledValue } from '../../hooks';
import { useQuery, useColumns, useSelection, useExpandable } from './hooks';

// import Empty from '../empty';

function Table( props, ref ) {
	const {
		query: rawQuery,
		columns: rawColumns,
		data: rawData,
		totalCount: rawTotalCount,
		loading,
		caption,
		search = true,
		actions,
		renderTools,
		onChange,
		rowKey,
		rowStyle,
		renderExpanded,
		showSummary,
		renderSummary,
		pagination = true,
		emptyMessage,
		errorMessage,
		style,
		className,
		bordered,
	} = props;
	const query = rawQuery || {};
	const data = rawData || [];
	const hasData = data && data.length > 0;
	const showSearch = false !== search;
	const showActions = false !== actions && actions?.length > 0;
	const showToolbar = showSearch || showActions;
	const totalCount = parseInt( rawTotalCount, 10 ) || 0;
	const showPagination = false !== pagination && totalCount > 0;
	const [ searchWord, setSearchWord ] = useState( query?.search || '' );

	// ====================== Methods ======================
	const handleChange = ( newQuery ) => {
		onChange( newQuery );
	};

	const handleSearch = ( keyword ) => {
		props.onSearch?.( keyword );
		handleChange( { ...query, search: keyword, page: 1 } );
	};

	// ====================== Render ======================
	const renderToolbar = () => {
		if ( ! showToolbar ) {
			return null;
		}
		return (
			<div className="eac-table__section eac-table__section--toolbar">
				{ showActions && (
					<Dropdown
						className="eac-table__actions"
						// renderToggle={ ( { isOpen, onToggle } ) => (
						// 	<Button
						// 		className="eac-table__actions__toggle"
						// 		isSecondary={ true }
						// 		size="compact"
						// 		disabled={ loading }
						// 		isPressed={ isOpen }
						// 		onClick={ onToggle }
						// 		aria-expanded={ isOpen }
						// 		icon={ isOpen ? 'arrow-up-alt2' : 'arrow-down-alt2' }
						// 		iconPosition="right"
						// 		iconSize={ 16 }
						// 		style={ {
						// 			width: 'unset',
						// 			flexDirection: 'row-reverse',
						// 			paddingLeft: '6px',
						// 		} }
						// 	>
						// 		<span>{ __( 'Actions', 'wp-ever-accounting' ) }</span>
						// 	</Button>
						// ) }
						renderContent={ ( { onClose } ) => (
							<div className="components-dropdown-menu__menu">
								<Dropdown.Group>
									{ actions.map( ( action, index ) => (
										<Dropdown.Item
											key={ index }
											onClick={ () => {
												action.onClick?.();
												onClose();
											} }
											focusOnMount={ true }
										>
											{ action.label }
										</Dropdown.Item>
									) ) }
								</Dropdown.Group>
							</div>
						) }
					/>
				) }
				{ showSearch && (
					<SearchControl
						className="eac-table__search"
						disabled={ loading }
						value={ searchWord }
						onChange={ setSearchWord }
						size="compact"
						onBlur={ () => handleSearch( searchWord ) }
						onClose={ () => handleSearch( '' ) }
						__next40pxDefaultSize={ false }
						__nextHasNoMarginBottom={ true }
						placeholder={ __( 'Search', 'wp-ever-accounting' ) }
						{ ...( typeof search === 'object' ? search : {} ) }
					/>
				) }
			</div>
		);
	};

	const classes = classNames( 'eac-table', className, {
		'eac-table--empty': ! hasData && ! loading,
		'eac-table--bordered': !! bordered,
		'eac-table--loading': !! loading,
	} );

	return (
		<div className={ classes } style={ style } ref={ ref }>
			{ renderToolbar() }
		</div>
	);
}

export default forwardRef( Table );
