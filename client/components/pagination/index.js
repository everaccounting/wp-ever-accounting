import {useState} from "@wordpress/element";
import PropTypes from 'prop-types';
import classNames from "classnames";
import {numberFormat} from "../lib";
import {__, sprintf, _n} from '@wordpress/i18n';

import './style.scss';

const Nav = props => {
	const {title, button, className, enabled, onClick} = props;

	if (enabled) {
		return (
			<a className={className + ' button'} href="#" onClick={onClick}>
				<span className="screen-reader-text">{title}</span>
				<span aria-hidden="true">{button}</span>
			</a>
		);
	}
	return (
		<span className="tablenav-pages-navspan button disabled" aria-hidden="true">
			{button}
		</span>
	);
};

const PageLinks = props => {
	const {total, per_page, current, onSetPage, isDisabled} = props;
	const max = Math.ceil(total / per_page);

	const setPage = (page) => {
		page = parseInt(page, 10);
		if (page !== current && !isNaN(page) && page > 0 && page <= max) {
			onSetPage(page);
		}
	}

	return (
		<span className="pagination-links">
			<Nav title={__('First page')} button="«" className="first-page" enabled={(current > 0 && current !== 1) && !isDisabled} onClick={() => setPage(1)}/>
			&nbsp;
			<Nav title={__('Prev page')} button="‹" className="prev-page" enabled={(current > 0 && current !== 1) && !isDisabled} onClick={() => setPage(current - 1)}/>
			&nbsp;
			<span className="paging-input">
					<label htmlFor="current-page-selector" className="screen-reader-text">
						{__('Current Page')}
					</label>
				&nbsp;
				<span className="tablenav-paging-text">
						{sprintf(__('%d of %d', 'wp-ever-accounting'), numberFormat(current), numberFormat(max))}
					</span>
			</span>
			&nbsp;
			<Nav title={__('Next page')} button="›" className="next-page" enabled={current < max && !isDisabled} onClick={() => setPage(current + 1)}/>
			&nbsp;
			<Nav title={__('Last page')} button="»" className="last-page" enabled={current < max - 1 && !isDisabled} onClick={() => setPage(max)}/>
		</span>
	)
}


const Pagination = (props) => {
	const {per_page, page = 1, total, isDisabled} = props;
	const [current, setPage] = useState(page);
	const onePage = total <= per_page;

	const handlePageChange = page => {
		setPage(page);
		console.log('Now page ' + page);
	}

	const classes = classNames({
		'tablenav-pages': true,
		'one-page': onePage,
		'ea-tablenav':true,
	});
	return (
		<div className={classes}>
			<span className="displaying-num">
				{sprintf(_n('%s item', '%s items', numberFormat(total), 'wp-ever-accounting'), numberFormat(total))}
			</span>

			{!onePage && (
				<PageLinks
					onSetPage={handlePageChange}
					total={total}
					per_page={per_page}
					current={current}
					isDisabled={isDisabled}/>
			)}

		</div>
	);
}

Pagination.propTypes = {
	total: PropTypes.number.isRequired,
	per_page: PropTypes.number.isRequired,
	page: PropTypes.number.isRequired,
	onPageChange: PropTypes.func.isRequired,
	isDisabled: PropTypes.bool.isRequired,
};
Pagination.defaultProps = {
	total: 0,
	per_page: 20,
	page: 1,
	isDisabled: false
}

export default Pagination;
