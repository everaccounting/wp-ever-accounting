/**
 * External dependencies
 */
import {__} from '@wordpress/i18n';
import {Component, Fragment, useState} from '@wordpress/element';
import {compose} from '@wordpress/compose';
import PropTypes from 'prop-types';
import {ITEMS_STORE_NAME} from '@eaccounting/data';
import {TextControl, Button} from '@wordpress/components';
import {useDispatch, useSelect} from '@wordpress/data';

import {ListTable, Drawer, Form} from "@eaccounting/components";

const initialValues = { firstName: '' };
function NameColumn(row, column) {
	const [toggle, setToggle] = useState(false);

	const {updateItem} = useDispatch(ITEMS_STORE_NAME);

	const handleSubmit = (values) => {
		console.log(values)
		updateItem('customers', values);
	}

	return (
		<Fragment>
			{toggle && <Fragment>
				<Drawer title={row.name} onClose={()=> setToggle(!toggle)}>

					<Form
						onSubmitCallback={handleSubmit}
						initialValues={ row }
						errors={{}}
					>
						{ ( {
								getInputProps,
								values,
								errors,
								handleSubmit,
							} ) => (
							<div>
								<TextControl
									label={ 'Name' }
									{ ...getInputProps( 'name' ) }
								/>
								<Button
									isPrimary
									onClick={ handleSubmit }
								>
									Submit
								</Button>
							</div>
						) }
					</Form>

				</Drawer>
			</Fragment>}
			<a href="#" onClick={e => {
				e.preventDefault();
				setToggle(!toggle)
			}}>{row.name}</a>
		</Fragment>
	)
}


export const columns = [
	{
		type: 'selection'
	},
	{
		label: "Name",
		property: "name",
		sortable: true,
		render: NameColumn
	},
	{
		label: "Address",
		property: "address",
		sortable: true,
	},
	{
		label: "Phone",
		property: "phone",
		sortable: true,
	},
];

function InvoiceTable(props) {
	const [query, setQuery] = useState({});

	const handleQuery = (newQuery) => {
		setQuery({...query, ...newQuery});
	}

	const {items, total, isLoading, isError} = useSelect((select) => {
		const {getItems, getItemsTotalCount, isResolving, getItemsError} = select(ITEMS_STORE_NAME);
		return {
			items: getItems('customers', query),
			total: getItemsTotalCount('customers', query),
			isLoading: isResolving('getItems', ['customers', query]),
			isError: getItemsError('customers', query),
		}
	}, [query]);

	const result = Array.from(items).map(([name, value]) => (value))


	return (
		<div>

			<ListTable
				title={__('Invoice', 'wp-ever-accounting')}
				query={{
					page: 1,
					per_page: 50,
					orderby: 'id',
					order: 'desc',
					search: '',
				}}
				isLoading={isLoading}
				columns={columns}
				data={result}
				onQueryChange={handleQuery}
				total={total}
				onAction={action => console.log(action)}
				filters={{
					status: {
						input: {
							placeholder: 'Select status',
							defaultVal: '',
							isMulti: true
						},
						//transform: (values) => values.map(value => value.value),
						transform: (value) => value.id,
						component: 'SelectControl'
					}
				}}
				actions={[
					{
						label: __('All'),
						value: 'all'
					},
					{
						label: __('Active'),
						value: 'active'
					},
					{
						label: __('Inactive'),
						value: 'inactive'
					}
				]}
			/>
		</div>
	)
}

export default InvoiceTable;
