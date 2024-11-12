/**
 * WordPress dependencies
 */
import { next, previous, chevronLeft, chevronRight } from '@wordpress/icons';
import { forwardRef } from '@wordpress/element';
import { __, sprintf } from '@wordpress/i18n';

/**
 * External dependencies
 */
import classNames from 'classnames';

/**
 * Internal dependencies
 */
import Button from '../button';
import Input from '../input';
import usePagination from './use-pagination';
import './style.scss';

const toInt = ( value ) => parseInt( value, 10 ) || 0;

const Pagination = forwardRef( ( props, ref ) => {
	const {
		className,
		page: rawPage = 1,
		total: rawTotal = 0,
		perPage: rawPerPage = 20,
		showTotal = true,
		showPageSize = true,
		pageSizes = [ 20, 50, 100 ],
		onChange,
		onPageChange,
		onPerPageChange,
		disable,
		style,
	} = props;

	const total = toInt( rawTotal );
	if ( total <= 0 ) return null;
	const page = toInt( rawPage ) || 1;
	const perPageInt = toInt( rawPerPage );
	const perPage = pageSizes.includes( perPageInt ) ? perPageInt : pageSizes[ 0 ];
	const pageCount = Math.ceil( total / perPage );
	const currentPage = Math.min( page, pageCount ) || 1;
	const start = ( page - 1 ) * perPage + 1;
	const end = Math.min( page * perPage, total );

	const onPaginationChange = ( newPage, newPerPage ) => {
		onChange?.( newPage, newPerPage );
		onPageChange?.( newPage );
		onPerPageChange?.( newPerPage );
	};

	const classes = classNames( 'eac-pagination', className );
	return (
		<ul className={ classes } ref={ ref } style={ style }>
			{ showTotal && (
				<li className="eac-pagination__item eac-pagination__total">
					<span className="eac-pagination__total-text">
						{ sprintf(
							// Translators: %1$d is the start number, %2$d is the end number, %3$d is the total number.
							__( 'Showing %1$d-%2$d of %3$d', 'wp-ever-accounting' ),
							start,
							end,
							total
						) }
					</span>
				</li>
			) }
			<li className="eac-pagination__item eac-pagination__first">
				<Button
					className="eac-pagination__button"
					icon={ previous }
					aria-label={ __( 'First page' ) }
					disabled={ disable || currentPage === 1 }
					onClick={ () => onPaginationChange( 1, perPage ) }
				>
					<span className="screen-reader-text">{ __( 'First page' ) }</span>
				</Button>
			</li>

			<li className="eac-pagination__item eac-pagination__previous">
				<Button
					className="eac-pagination__button"
					icon={ chevronLeft }
					aria-label={ __( 'Previous page' ) }
					disabled={ disable || currentPage === 1 }
					onClick={ () => onPaginationChange( currentPage - 1, perPage ) }
				>
					<span className="screen-reader-text">{ __( 'Previous page' ) }</span>
				</Button>
			</li>
			<li className="eac-pagination__item eac-pagination__goto">
				<Input
					className="eac-pagination__goto-input"
					type="number"
					min="1"
					max={ pageCount }
					step="1"
					value={ currentPage }
					disabled={ disable }
					onChange={ ( value ) => onPaginationChange( toInt( value ), perPage ) }
				/>
			</li>
			<li className="eac-pagination__item eac-pagination__next">
				<Button
					className="eac-pagination__button"
					icon={ chevronRight }
					aria-label={ __( 'Next page' ) }
					disabled={ disable || currentPage === pageCount }
					onClick={ () => onPaginationChange( currentPage + 1, perPage ) }
				>
					<span className="screen-reader-text">{ __( 'Next page' ) }</span>
				</Button>
			</li>
			<li className="eac-pagination__item eac-pagination__last">
				<Button
					className="eac-pagination__button"
					icon={ next }
					aria-label={ __( 'Last page' ) }
					disabled={ disable || currentPage === pageCount }
					onClick={ () => onPaginationChange( pageCount, perPage ) }
				>
					<span className="screen-reader-text">{ __( 'Last page' ) }</span>
				</Button>
			</li>
			{ showPageSize && (
				<li className="eac-pagination__item eac-pagination__size">
					<Input.Select
						className="eac-pagination__size-select"
						options={ pageSizes.map( ( size ) => ( {
							label: sprintf(
								// Translators: %d is the number of items per page.
								__( '%d / Page' ),
								size
							),
							value: size,
						} ) ) }
						disabled={ disable }
						value={ perPage }
						onChange={ ( value ) => onPaginationChange( currentPage, toInt( value ) ) }
					/>
				</li>
			) }
		</ul>
	);
} );

export default Pagination;
export { usePagination };
