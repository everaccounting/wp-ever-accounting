/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
/**
 * External dependencies
 */
import moment from 'moment';

export const DATE_RANGE_OPTION = [
	{
		label: __('All Time'),
		value: '',
	},
	{
		label: __('Today'),
		value: `${moment().format('YYYY-MM-DD')}_${moment().format('YYYY-MM-DD')}`,
	},
	{
		label: __('Yesterday'),
		value: `${moment()
			.subtract(1, 'days')
			.format('YYYY-MM-DD')}_${moment()
			.subtract(1, 'days')
			.format('YYYY-MM-DD')}`,
	},
	{
		label: __('Last 7 Days'),
		value: `${moment()
			.subtract(6, 'days')
			.format('YYYY-MM-DD')}_${moment().format('YYYY-MM-DD')}`,
	},
	{
		label: __('Last 30 Days'),
		value: `${moment()
			.subtract(30, 'days')
			.format('YYYY-MM-DD')}_${moment().format('YYYY-MM-DD')}`,
	},
	{
		label: __('This Month'),
		value: `${moment()
			.startOf('month')
			.format('YYYY-MM-DD')}_${moment()
			.endOf('month')
			.format('YYYY-MM-DD')}`,
	},
	{
		label: __('Last Month'),
		value: `${moment()
			.subtract(1, 'month')
			.startOf('month')
			.format('YYYY-MM-DD')}_${moment()
			.subtract(1, 'month')
			.endOf('month')
			.format('YYYY-MM-DD')}`,
	},
	{
		label: __('This Year'),
		value: `${moment()
			.startOf('year')
			.format('YYYY-MM-DD')}_${moment().format('YYYY-MM-DD')}`,
	},
	{
		label: __('Last Year'),
		value: `${moment()
			.subtract(1, 'year')
			.startOf('year')
			.format('YYYY-MM-DD')}_${moment()
			.subtract(1, 'year')
			.endOf('year')
			.format('YYYY-MM-DD')}`,
	},
];
