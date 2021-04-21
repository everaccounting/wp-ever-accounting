/**
 * External dependencies
 */
import { STORE_NAME } from '@eaccounting/data';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Fragment, useState } from '@wordpress/element';
import { useDispatch, useSelect } from '@wordpress/data';
import {
	ListTable,
	Drawer,
	Form,
	Date,
	EntitySelect,
} from '@eaccounting/components';
import {
	ToggleControl,
	Modal,
	TextControl,
	Button,
} from '@wordpress/components';
export default function Customers() {
	const [isOpen, setOpen] = useState(false);
	const [query, setQuery] = useState({});
	const { items, total, isLoading, saveError } = useSelect(
		(select) => {
			return {
				items: select(STORE_NAME).getCustomers(query),
				total: select(STORE_NAME).getTotalCustomers(query),
				isLoading: select(STORE_NAME).isRequesting(
					'getCustomers',
					query
				),
				saveError: select(STORE_NAME).getLastEntitySaveError(
					'customer'
				),
			};
		},
		[query]
	);
	console.log(saveError);
	const { saveCustomer } = useDispatch(STORE_NAME);

	const handleQuery = (newQuery) => {
		setQuery({ ...query, ...newQuery });
	};

	const initialValues = { name: '' };

	return (
		<Fragment>
			<EntitySelect onButtonClick={setOpen} />
			{isOpen && (
				<Modal title={'Modal'} onClose={setOpen}>
					<Form
						onSubmitCallback={(values) => saveCustomer(values)}
						initialValues={initialValues}
					>
						{({ getInputProps, values, errors, handleSubmit }) => (
							<div>
								{console.log(saveError)}
								<TextControl
									label={'Name'}
									{...getInputProps('name')}
								/>
								<Button
									isPrimary
									onClick={handleSubmit}
									disabled={Object.keys(errors).length}
								>
									Submit
								</Button>
							</div>
						)}
					</Form>
				</Modal>
			)}
			<ListTable
				title={__('Customers', 'wp-ever-accounting')}
				query={{
					page: 1,
					perPage: 20,
					orderby: 'id',
					order: 'desc',
					search: '',
				}}
				isLoading={isLoading}
				columns={[
					{
						type: 'selection',
						property: 'id',
					},
					{
						label: __('Name', 'wp-ever-account'),
						property: 'name',
						sortable: true,
						render: (row) => {
							return (
								<a id={`customer-${row.id}`} href="#">
									{row.name}
								</a>
							);
						},
					},
					{
						label: __('Contact', 'wp-ever-account'),
						property: 'contact',
					},
					{
						label: __('Address', 'wp-ever-account'),
						property: 'address',
						sortable: true,
					},
					{
						label: __('Paid', 'wp-ever-account'),
						property: 'total_paid',
					},
					{
						label: __('Due', 'wp-ever-account'),
						property: 'total_due',
					},
					{
						label: __('Enabled', 'wp-ever-account'),
						property: 'enabled',
						sortable: true,
						width: 150,
						render: (row) => {
							return (
								<ToggleControl
									checked={row.enabled}
									onChange={(enabled) =>
										saveCustomer({ id: row.id, enabled })
									}
								/>
							);
						},
					},
				]}
				data={items}
				onQueryChange={handleQuery}
				total={total}
			/>
		</Fragment>
	);
}
