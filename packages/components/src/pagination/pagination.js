/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * WordPress dependencies
 */
import { Button, TextControl, SelectControl } from '@wordpress/components';
import { __, sprintf } from '@wordpress/i18n';

const NavLink = ( props ) => {
	const { label, className, enabled = true, onClick, ...rest } = props;
	return (
		<Button isSecondary={ true } className={ className } disabled={ ! enabled } onClick={ onClick } { ...rest }>
			{ label }
			<span className="screen-reader-text">{ label }</span>
		</Button>
	);
};

const Total = ( props ) => {
	return typeof props.total === 'number' ? (
		<span className="el-pagination__total">{ sprintf( __( '%d items', 'wp-ever-accounting' ), props.total ) }</span>
	) : (
		<span />
	);
};

const Jumper = ( props ) => {
	const { maxPages } = props;
	return (
		<span className="el-pagination__jump">
			<input
				className="el-pagination__editor is-in-pagination"
				type="number"
				min={ 1 }
				max={ maxPages }
				value={ props.currentPage }
				onChange={ ( e ) => props.setPageChanged( e.target.value ) }
			/>
		</span>
	);
};

const Sizes = ( props ) => {
	const { pageSize, pageSizes } = props;
	if ( pageSizes.indexOf( pageSize ) === -1 ) {
		return null;
	}

	return (
		<SelectControl
			className="el-pagination__sizes"
			value={ pageSize }
			options={ pageSizes.map( ( item ) => {
				return { value: item, label: item };
			} ) }
			onChange={ ( value ) => props.sizeChange( value ) }
		/>
	);
};

const Pagination = ( props ) => {
	const { currentPage: _currentPage = 1, pageSizes = [ 10, 20, 30, 40, 50, 100 ], pageSize = 20, total, onChange } = props;
	const onePage = total <= pageSize;
	const currentPage = parseInt( _currentPage, 10 );
	const maxPages = Math.ceil( parseInt( total, 10 ) / parseInt( pageSize, 10 ) );
	const setPageChanged = ( page ) => {
		// page = parseInt(page, 10);
		// if (page !== currentPage && !isNaN(page) && page > 0 && page <= maxPages) {
		// 	console.log('page', page);
		// }
		console.log( 'page', page );
	};

	const classes = classNames( {
		'el-pagination': true,
		'el-pagination__rightwrapper': false,
	} );
	console.log( currentPage, maxPages, classes );

	return (
		<div className={ classes }>
			<NavLink label="«" className="first-page" enabled={ currentPage > 0 && currentPage !== 1 } onClick={ () => setPageChanged( 1 ) } />
			&nbsp;
			<NavLink
				icon="arrow-left"
				className="prev-page"
				enabled={ currentPage > 0 && currentPage !== 1 }
				onClick={ () => setPageChanged( currentPage - 1 ) }
			/>
			&nbsp;
			<Jumper maxPages={ maxPages } currentPage={ currentPage } setPageChanged={ setPageChanged } />
			<NavLink
				label="›"
				icon="arrow-right"
				className="next-page"
				enabled={ currentPage < maxPages }
				onClick={ () => setPageChanged( currentPage + 1 ) }
			/>
			&nbsp;
			<NavLink
				// label="»"
				icon="arrow-right"
				className="last-page"
				enabled={ currentPage < maxPages - 1 }
				onClick={ () => setPageChanged( maxPages ) }
			/>
			<Total total={ total } />
			<Sizes pageSize={ pageSize } pageSizes={ pageSizes } sizeChange={ setPageChanged } />
		</div>
	);
};

export default Pagination;
