/**
 * WordPress dependencies
 */
import { __, _x } from '@wordpress/i18n';
import { Button, ToggleControl, Notice, Spacer } from '@wordpress/components';
import { withSelect, withDispatch } from '@wordpress/data';
import { compose } from '@wordpress/compose';
import { useState } from '@wordpress/element';
/**
 * External dependencies
 */
import { isEmpty } from 'lodash';
import {
	Text,
	H,
	ListTable,
	Amount,
	Link,
	Date,
} from '@eaccounting/components';
import {
	getTableQuery,
	updateQueryString,
	generatePath,
	getActiveFiltersFromQuery,
} from '@eaccounting/navigation';

/**
 * Internal dependencies
 */
import Revenue from './revenue';

const entityName = 'transactions';

const filters = {
	status: {
		title: __('Status'),
		mixedString: '{{title}}Status{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'SelectFilter',
			options: [
				{ label: '== Select ==', value: '' },
				{ label: 'Afghanistan', value: 'AF' },
				{ label: 'Åland Islands', value: 'AX' },
				{ label: 'Albania', value: 'AL' },
				{ label: 'Algeria', value: 'DZ' },
				{ label: 'American Samoa', value: 'AS' },
				{ label: 'AndorrA', value: 'AD' },
				{ label: 'Angola', value: 'AO' },
				{ label: 'Anguilla', value: 'AI' },
				{ label: 'Antarctica', value: 'AQ' },
				{ label: 'Antigua and Barbuda', value: 'AG' },
				{ label: 'Argentina', value: 'AR' },
				{ label: 'Armenia', value: 'AM' },
				{ label: 'Aruba', value: 'AW' },
			],
		},
		rules: [
			{
				value: 'in',
				label: __('In'),
			},
			{
				value: 'not_in',
				label: __('No In'),
			},
		],
		allowMultiple: true,
	},
	account: {
		title: __('Account'),
		mixedString: '{{title}}Account{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'EntityFilter',
			entityName: 'accounts',
			isMulti: true,
		},
		rules: [
			{
				value: '_in',
				label: __('In'),
			},
			{
				value: '_not_in',
				label: __('No In'),
			},
		],
	},
	payment_date: {
		title: __('Payment Date'),
		mixedString: '{{title}}Payment Date{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'DateFilter',
		},
		rules: [
			{
				value: 'before',
				/* translators: Sentence fragment, logical, "Before" refers to customers registered before a given date. Screenshot for context: https://cloudup.com/cCsm3GeXJbE */
				label: _x('Before', 'date'),
			},
			{
				value: 'after',
				/* translators: Sentence fragment, logical, "after" refers to customers registered after a given date. Screenshot for context: https://cloudup.com/cCsm3GeXJbE */
				label: _x('After', 'date'),
			},
			{
				value: 'between',
				/* translators: Sentence fragment, logical, "Between" refers to average order value of a customer, between two given amounts. Screenshot for context: https://cloudup.com/cCsm3GeXJbE */
				label: _x('Between', 'date'),
			},
		],
	},
	amount: {
		title: __('Amount'),
		mixedString: '{{title}}Amount{{/title}} {{rule /}} {{filter /}}',
		input: {
			component: 'NumberFilter',
		},
		rules: [
			{
				value: 'max',
				label: __('Less Than'),
			},
			{
				value: 'min',
				label: __('More Than'),
			},
			{
				value: 'between',
				label: __('Between'),
			},
		],
	},
};

function Revenues(props) {
	const {
		tab,
		query,
		items,
		total,
		isRequesting,
		fetchError,
		defaultCurrency,
		saveEntityRecord,
		deleteEntityRecord,
		isSavingEntityRecord,
	} = props;
	const { search, revenue_id } = query;
	const [editingItemId, setEditingItemId] = useState(false);
	return (
		<>
			<H className="wp-heading-inline">{__('Revenues')}</H>
			<Button
				className="page-title-action"
				isSecondary
				onClick={() => setEditingItemId(null)}
			>
				{__('Add Revenue')}
			</Button>

			{revenue_id && (
				<Revenue
					payment_id={revenue_id}
					onClose={() => updateQueryString({ revenue_id: undefined })}
				/>
			)}

			{!isEmpty(fetchError) && (
				<>
					<Notice isDismissible={false} status="error">
						<Text>{fetchError.message}</Text>
					</Notice>
					<Spacer marginBottom={20} />
				</>
			)}

			<ListTable
				query={query}
				isRequesting={isRequesting}
				rows={items}
				total={total}
				onQueryChange={(query) =>
					updateQueryString(query, '/sales', {})
				}
				bulkActions={[
					{
						label: __('Delete'),
						value: __('delete'),
					},
					{
						label: __('Enable'),
						value: __('enable'),
					},
					{
						label: __('Disable'),
						value: __('disable'),
					},
					{
						label: __('Export'),
						value: __('export'),
					},
				]}
				onBulkAction={(action, selected) => {
					console.log(action, selected);
				}}
				filters={filters}
				columns={[
					{
						type: 'selection',
						property: 'id',
					},
					{
						label: __('Date'),
						property: 'date',
						isPrimary: true,
						sortable: true,
						render: (row) => {
							const highlightWords = !!search ? [search] : '';
							return (
								<Link
									href={generatePath(
										{ tab, revenue_id: row.id },
										'/sales',
										{}
									)}
								>
									<Text
										color="var(--wp-admin-theme-color)"
										style={{
											cursor: 'pointer',
											fontWeight: '600',
										}}
										highlightWords={highlightWords}
									>
										<Date date={row.payment_date} />
									</Text>
								</Link>
							);
						},
						actions: [
							{
								label: __('Edit'),
								onClick: (row) => setEditingItemId(row),
							},
							{
								label: __('Delete'),
								onClick: (row) => {
									if (
										window.confirm(
											__(
												'Do you really want to delete the item?'
											)
										)
									) {
										deleteEntityRecord(row.id);
									}
								},
							},
						],
					},
					{
						label: __('Amount'),
						property: 'amount',
						sortable: true,
						render: (row) => {
							const { amount, currency_code } = row;
							return (
								<Amount
									amount={amount}
									currency={currency_code}
								/>
							);
						},
					},
					{
						label: __('Account'),
						property: 'account_id',
						sortable: true,
						render: (row) => {
							const { account } = row;
							return (
								<>
									{account && account.name && (
										<Text>{account.name}</Text>
									)}
								</>
							);
						},
					},
					{
						label: __('Category'),
						property: 'category_id',
						sortable: true,
						render: (row) => {
							const { category } = row;
							return (
								<>
									{category && category.name && (
										<Text>{category.name}</Text>
									)}
								</>
							);
						},
					},
					{
						label: __('Vendor'),
						property: 'vendor_id',
						sortable: true,
						render: (row) => {
							const { vendor } = row;
							return (
								<>
									{vendor && vendor.name && (
										<Text>{vendor.name}</Text>
									)}
								</>
							);
						},
					},
					{
						label: __('Reference'),
						property: 'reference',
						sortable: true,
						render: (row) => {
							return <Text>{row.reference}</Text>;
						},
					},
				]}
			/>
		</>
	);
}

const applyWithSelect = withSelect((select, props) => {
	const { tab } = props.query;
	const tableQuery = {
		...getTableQuery([
			...Object.keys(getActiveFiltersFromQuery(filters, props.query)),
		]),
		type: 'income',
	};
	const {
		getEntityRecords,
		getTotalEntityRecords,
		getEntityFetchError,
		isResolving,
		getDefaultCurrency,
		isSavingEntityRecord,
	} = select('ea/core');
	return {
		items: getEntityRecords(entityName, tableQuery),
		total: getTotalEntityRecords(entityName, tableQuery),
		isRequesting: isResolving('getEntityRecords', [entityName, tableQuery]),
		fetchError: getEntityFetchError(entityName, tableQuery),
		isSavingEntityRecord: isSavingEntityRecord(entityName),
		defaultCurrency: getDefaultCurrency(),
		tab,
	};
});

const applyWithDispatch = withDispatch((dispatch) => {
	const { deleteEntityRecord, saveEntityRecord } = dispatch('ea/core');
	return {
		deleteEntityRecord: (id) => deleteEntityRecord(entityName, id),
		saveEntityRecord: (item) => saveEntityRecord(entityName, item),
	};
});

export default compose([applyWithSelect, applyWithDispatch])(Revenues);
