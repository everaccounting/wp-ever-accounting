/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { useState } from '@wordpress/element';
export function usePagination({ totalCount, defaultPerPage = 25, onPageChange, onPerPageChange }) {
	const [currentPage, setCurrentPage] = useState(1);
	const [perPage, setPerPage] = useState(defaultPerPage);
	const pageCount = Math.ceil(totalCount / perPage);
	const start = perPage * (currentPage - 1) + 1;
	const end = Math.min(perPage * currentPage, totalCount);
	return {
		start,
		end,
		currentPage,
		perPage,
		pageCount,
		setCurrentPage: (newPage) => {
			setCurrentPage(newPage);
			if (onPageChange) {
				onPageChange(newPage);
			}
		},
		setPerPageChange: (newPerPage) => {
			setPerPage(newPerPage);
			if (onPerPageChange) {
				onPerPageChange(newPerPage);
			}
		},
	};
}
