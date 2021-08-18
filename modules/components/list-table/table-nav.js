/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classNames from 'classnames';
import { noop } from 'lodash';
/**
 * WordPress dependencies
 */
import { __, sprintf, _n } from '@wordpress/i18n';
import { Button } from '@wordpress/components';
/**
 * Internal dependencies
 */
import { numberFormat } from '../lib';
// eslint-disable-next-line no-unused-vars
const PER_PAGE_OPTIONS = [20, 50, 75, 100];

const NavLink = (props) => {
	const { label, className, enabled = true, onClick } = props;
	return (
		<Button
			className={classNames(className, 'button')}
			disabled={!enabled}
			onClick={onClick}
			label={label}
			style={{
				height: '30px',
			}}
		>
			{label}
			<span className="screen-reader-text">{label}</span>
		</Button>
	);
};

const NavLinks = (props) => {
	const { total, per_page, paged, onPageChange } = props;
	const current = parseInt(paged, 10);
	const max = Math.ceil(parseInt(total, 10) / parseInt(per_page, 10));

	const setPageChanged = (page) => {
		page = parseInt(page, 10);
		if (page !== current && !isNaN(page) && page > 0 && page <= max) {
			onPageChange(page);
		}
	};

	return (
		<span className="pagination-links">
			<NavLink
				label="«"
				className="first-page"
				enabled={current > 0 && current !== 1}
				onClick={() => setPageChanged(1)}
			/>
			&nbsp;
			<NavLink
				label="‹"
				className="prev-page"
				enabled={current > 0 && current !== 1}
				onClick={() => setPageChanged(current - 1)}
			/>
			&nbsp;
			<span className="paging-input">
				<label
					htmlFor="current-page-selector"
					className="screen-reader-text"
				>
					{__('Current Page')}
				</label>
				<span className="tablenav-paging-text">
					{/* eslint-disable-next-line @wordpress/valid-sprintf */}
					{sprintf(
						// eslint-disable-next-line @wordpress/i18n-translator-comments
						__('%d of %d', 'wp-ever-accounting'),
						numberFormat(current),
						numberFormat(max)
					)}
				</span>
			</span>
			&nbsp;
			<NavLink
				label="›"
				className="next-page"
				enabled={current < max}
				onClick={() => setPageChanged(current + 1)}
			/>
			&nbsp;
			<NavLink
				label="»"
				className="last-page"
				enabled={current < max - 1}
				onClick={() => setPageChanged(max)}
			/>
		</span>
	);
};

const TableNav = (props) => {
	const { total, per_page } = props;
	const onePage = total <= per_page;
	const classes = classNames({
		'tablenav-pages': true,
		'one-page': onePage,
		'ea-tablenav': true,
	});

	return (
		<div className={classes}>
			{!onePage && (
				<span className="displaying-num">
					{sprintf(
						// eslint-disable-next-line @wordpress/i18n-translator-comments
						_n(
							'%s item',
							'%s items',
							numberFormat(total),
							'wp-ever-accounting'
						),
						numberFormat(total)
					)}
				</span>
			)}

			{!onePage && <NavLinks {...props} />}
		</div>
	);
};

TableNav.propTypes = {
	// The current page of the collection.
	paged: PropTypes.any.isRequired,
	// The total number of results.
	total: PropTypes.number.isRequired,
	// The amount of results that are being displayed per page.
	per_page: PropTypes.number.isRequired,
	// A function to execute when the page is changed.
	onPageChange: PropTypes.func,
	// A function to execute when the per page option is changed.
	onPerPageChange: PropTypes.func,
	// Additional classNames.
	className: PropTypes.string,
	// Whether the per_page picker should be rendered.
	showPerPagePicker: PropTypes.bool,
};
TableNav.defaultProps = {
	paged: 1,
	total: 0,
	per_page: 20,
	onPageChange: noop,
	onPerPageChange: noop,
	showPerPagePicker: true,
};

export default TableNav;
