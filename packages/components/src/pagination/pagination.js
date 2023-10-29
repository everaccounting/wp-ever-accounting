/**
 * WordPress dependencies
 */
import { SelectControl, TextControl, Button } from '@wordpress/components';
import { next, previous, chevronLeft, chevronRight } from '@wordpress/icons';
import { forwardRef, useState, useEffect } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

const toInt = ( value ) => parseInt( value, 10 );

const Pagination = forwardRef( ( { className, disable, total, showTotal = true, showPageSize = true, pageSizes = [ 10, 20, 50, 100 ], ...props }, ref ) => {
	const [ page, setPage ] = useState( 1 );
	const [ perPage, setPerPage ] = useState( props.defaultPerPage || 20 );
	const dispatchEvent = ( name, ...args ) => props[ name ]?.( ...args );

	// If the page or page props are not provided then work with the state otherwise use the props.
	useEffect( () => {
		if ( props.page ) {
			setPage( toInt( props.page ) );
		}
		if ( props.perPage ) {
			setPerPage( toInt( props.perPage ) );
		}
	}, [ props.page, props.perPage ] );

	const handlePageChange = ( currentPage ) => {
		setPage( currentPage );
		dispatchEvent( 'onPageChange', currentPage );
		dispatchEvent( 'onChange', { page: currentPage, perPage } );
	};

	const handlePageSizeChange = ( currentPageSize ) => {
		setPerPage( currentPageSize );
		dispatchEvent( 'onPageSizeChange', currentPageSize );
		dispatchEvent( 'onChange', { page, perPage: currentPageSize } );
	};

	const maxPage = Math.floor( ( total - 1 ) / perPage ) + 1;
	const classes = classNames( 'eac-pagination', className );
	const currentPage = Math.min( page, maxPage ) || 1;

	return (
		<ul className={ classes } ref={ ref }>
			{ showTotal && (
				<li className="eac-pagination__item eac-pagination__total">
					<span className="eac-pagination__total-text">{ sprintf( __( 'Total %d items' ), total ) }</span>
				</li>
			) }
			<li className="eac-pagination__item eac-pagination__first">
				<Button
					className="eac-pagination__button"
					icon={ previous }
					aria-label={ __( 'First page' ) }
					disabled={ disable || page === 1 }
					onClick={ () => handlePageChange( 1 ) }
				>
					<span className="screen-reader-text">{ __( 'First page' ) }</span>
				</Button>
			</li>
			<li className="eac-pagination__item eac-pagination__previous">
				<Button
					className="eac-pagination__button"
					icon={ chevronLeft }
					aria-label={ __( 'Previous page' ) }
					disabled={ disable || page === 1 }
					onClick={ () => handlePageChange( page - 1 ) }
				>
					<span className="screen-reader-text">{ __( 'Previous page' ) }</span>
				</Button>
			</li>
			<li className="eac-pagination__item eac-pagination__goto">
				<TextControl
					className="eac-pagination__goto-input"
					type="number"
					min="1"
					max={ maxPage }
					step="1"
					value={ currentPage }
					disabled={ disable }
					onChange={ ( value ) => handlePageChange( value ) }
				/>
			</li>
			<li className="eac-pagination__item eac-pagination__next">
				<Button
					className="eac-pagination__button"
					icon={ chevronRight }
					aria-label={ __( 'Next page' ) }
					disabled={ disable || page === maxPage }
					onClick={ () => handlePageChange( page + 1 ) }
				>
					<span className="screen-reader-text">{ __( 'Next page' ) }</span>
				</Button>
			</li>
			<li className="eac-pagination__item eac-pagination__last">
				<Button
					className="eac-pagination__button"
					icon={ next }
					aria-label={ __( 'Last page' ) }
					disabled={ disable || page === maxPage }
					onClick={ () => handlePageChange( parseInt( maxPage, 10 ) ) }
				>
					<span className="screen-reader-text">{ __( 'Last page' ) }</span>
				</Button>
			</li>
			{ showPageSize && (
				<li className="eac-pagination__item eac-pagination__size">
					<SelectControl
						className="eac-pagination__size-select"
						options={ pageSizes.map( ( size ) => ( { label: sprintf( __( '%d / Page' ), size ), value: size } ) ) }
						disabled={ disable }
						value={ perPage }
						onChange={ ( value ) => handlePageSizeChange( parseInt( value, 10 ) ) }
					/>
				</li>
			) }
		</ul>
	);
} );
export default Pagination;

export * from './use-pagination';
