import { Component } from 'react';
import propTypes from 'prop-types';
import classnames from 'classnames';
import { __, sprintf, _n } from '@wordpress/i18n';

const Nav = props => {
	const { title, button, className, enabled, onClick } = props;

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

class PaginationLinks extends Component {
	constructor(props) {
		super(props);

		this.onChange = this.handleChange.bind(this);
		this.onSetPage = this.handleSetPage.bind(this);
		this.setClickers(props);
		this.state = { currentPage: props.page };
	}

	setClickers(props) {
		this.onFirst = this.handleClick.bind(this, 1);
		this.onLast = this.handleClick.bind(this, this.getTotalPages());
		this.onNext = this.handleClick.bind(this, props.page + 1);
		this.onPrev = this.handleClick.bind(this, props.page - 1);
	}

	handleClick(page, ev) {
		ev.preventDefault();
		this.setState({ currentPage: page });
		this.props.onChangePage(page);
	}

	handleChange(ev) {
		const value = parseInt(ev.target.value, 10);
		const totalPage = this.getTotalPages();
		if (value !== this.state.currentPage && !isNaN(value) && value > 0 && value <= totalPage) {
			this.setState({ currentPage: value });
		}
	}

	handleSetPage() {
		this.props.onChangePage(this.state.currentPage);
	}

	getTotalPages = () => {
		const { total, per_page } = this.props;

		return Math.ceil(total / per_page);
	};

	render() {
		const { page, inProgress } = this.props;
		const max = this.getTotalPages();

		return (
			<span className="pagination-links">
				<Nav
					title={__('First page', 'wp-ever-crm')}
					button="«"
					className="first-page"
					enabled={page > 0 && page !== 1 && !inProgress}
					onClick={this.onFirst}
				/>
				&nbsp;
				<Nav
					title={__('Prev page', 'wp-ever-crm')}
					button="‹"
					className="prev-page"
					enabled={page > 0 && page !== 1 && !inProgress}
					onClick={this.onPrev}
				/>
				<span className="paging-input">
					<label htmlFor="current-page-selector" className="screen-reader-text">
						{__('Current Page', 'wp-ever-crm')}
					</label>
					&nbsp;
					<input
						className="current-page"
						type="number"
						min="1"
						max={max}
						name="paged"
						value={this.state.currentPage}
						size="2"
						aria-describedby="table-paging"
						onBlur={this.onSetPage}
						onChange={this.onChange}
					/>
					<span className="tablenav-paging-text">
						<span className="total-pages" />
						{sprintf(_n('of %d', 'of %d', max, 'wp-ever-crm'), max)}
					</span>
				</span>
				&nbsp;
				<Nav
					title={__('Next page', 'wp-ever-crm')}
					button="›"
					className="next-page"
					enabled={page < max && !inProgress}
					onClick={this.onNext}
				/>
				&nbsp;
				<Nav
					title={__('Last page', 'wp-ever-crm')}
					button="»"
					className="last-page"
					enabled={page < max - 1 && !inProgress}
					onClick={this.onLast}
				/>
			</span>
		);
	}
}

class Pagination extends Component {
	static propTypes = {
		total: propTypes.number.isRequired,
		per_page: propTypes.number.isRequired,
		page: propTypes.number.isRequired,
		onChangePage: propTypes.func.isRequired,
		status: propTypes.string.isRequired,
	};
	render() {
		const { total, per_page, page, onChangePage, status } = this.props;
		const onePage = total <= per_page;
		const inProgress = status !== 'ready';
		const classes = classnames({
			'ea-pagination': true,
			'tablenav-pages': true,
			'one-page': onePage,
		});

		return (
			<div className={classes}>
				<span className="displaying-num">
					{sprintf(_n('%d item', '%d items', total, 'wp-ever-crm'), total)}
				</span>

				{!onePage && (
					<PaginationLinks
						onChangePage={onChangePage}
						total={total}
						per_page={per_page}
						page={page}
						inProgress={inProgress}
						key={page}
					/>
				)}
			</div>
		);
	}
}
export default Pagination;
