/**
 * External dependencies
 */
import classNames from 'classnames';
import {Button} from '@wordpress/components';

const NavLink = (props) => {
	const { label, className, enabled = true, onClick } = props;
	return (
		<Button className={classNames(className, 'btn-quicknext')} disabled={!enabled} onClick={onClick}>
			{label}
			<span className="screen-reader-text">{label}</span>
		</Button>
	);
};

const Sizes = (props) => {
	const { pageSize, pageSizes } = props;
	if (pageSizes.indexOf(pageSize) === -1) {
		return null;
	}

	return (
		<span className="el-pagination__sizes">
			<span className="el-pagination__sizes-wrap">
				{pageSizes.map((item, idx) => {
					return (
						<span
							key={idx}
							className={`el-pagination__size ${item === pageSize ? 'is-active' : ''}`}
							onClick={() => props.sizeChange(item)}
						>
							{item}
						</span>
					);
				})}
			</span>
		</span>
	);
};

const Total = (props) => {
	return typeof props.total === 'number' ? (
		<span className="el-pagination__total">{locale.t('el.pagination.total', { total: props.total })}</span>
	) : (
		<span />
	);
};

const Jumper = (props) => {
	const { maxPages } = props;
	return (
		props.showJumper &&
		span({ className: 'el-pagination__jump' }, [
			locale.t('el.pagination.goto'),
			input({
				className: 'el-pagination__editor is-in-pagination',
				type: 'number',
				min: 1,
				max: maxPages,
				value: props.currentPage,
				onChange: props.quickprev,
			}),
			locale.t('el.pagination.pageClassifier'),
		])
	);
};

const Pagination = (props) => {
	const { currentPage = 1, pageSizes = [10, 20, 30, 40, 50, 100], pageSize = 20, total, onChange } = props;
	const onePage = total <= pageSize;
	const current = parseInt(currentPage, 10);
	const maxPages = Math.ceil(parseInt(total, 10) / parseInt(pageSize, 10));
	const setPageChanged = (page) => {
		// page = parseInt(page, 10);
		// if (page !== currentPage && !isNaN(page) && page > 0 && page <= maxPages) {
		// 	console.log('page', page);
		// }
		console.log('page', page);
	};

	const classes = classNames({
		'el-pagination': true,
		'el-pagination__rightwrapper': false,
	});

	return (
		<div className={classes}>
			<NavLink
				label="Previous"
				className="first-page"
				enabled={current > 0 && current !== 1}
				onClick={() => setPageChanged(1)}
			/>
			&nbsp;
			<NavLink
				label="Next"
				className="prev-page"
				enabled={current > 0 && current !== 1}
				onClick={() => setPageChanged(current - 1)}
			/>
		</div>
	);
};

export default Pagination;
