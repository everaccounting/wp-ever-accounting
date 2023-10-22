/**
 * External dependencies
 */
import classNames from 'classnames';
import { PageSizePicker } from '@app/components/pagination/page-size-picker.js';
export const DEFAULT_PER_PAGE_OPTIONS = [25, 50, 75, 100];

function Pagination({
	page,
	total,
	perPage,
	perPageOptions = DEFAULT_PER_PAGE_OPTIONS,
	showTotal = true,
	showPerPagePicker = true,
	showPageArrowsLabel = true,
	onChange = () => {},
	className,
	children,
	...props
}) {
	const pageCount = Math.ceil(total / perPage);
	if (children && typeof children === 'function') {
		return children({
			pageCount,
		});
	}
	const classes = classNames('eac-pagination', className);
	if (pageCount <= 1) {
		return (
			(total > perPageOptions[0] && (
				<div className={classes}>
					<PageSizePicker
						currentPage={page}
						perPage={perPage}
						setCurrentPage={onPageChange}
						total={total}
						setPerPageChange={onPerPageChange}
						perPageOptions={perPageOptions}
					/>
				</div>
			)) ||
			null
		);
	}
	return <div className="eac-pagination">pagination</div>;
}

export default Pagination;
