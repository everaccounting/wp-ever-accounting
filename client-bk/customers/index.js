/**
 * External dependencies
 */
import { STORE_NAME, useEntities } from '@eaccounting/data';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { Fragment, useState } from '@wordpress/element';
import { useDispatch } from '@wordpress/data';
import { ListTable, Form } from '@eaccounting/components';
import {
	ToggleControl,
	Modal,
	TextControl,
	Button,
} from '@wordpress/components';

export default function Customers() {
	const [isOpen, setOpen] = useState(false);
	const [query, setQuery] = useState({});
	const { entities, total, isLoading, updateEntity } = useEntities(
		'customers',
		query
	);

	const { saveCustomer } = useDispatch(STORE_NAME);

	const handleQuery = (newQuery) => {
		setQuery({ ...query, ...newQuery });
	};

	const initialValues = { name: '' };

	return (
		<Fragment>
			{/*<EntitySelect onButtonClick={setOpen} />*/}
			{isOpen && (
				<Modal title={'Modal'} onClose={setOpen}>
					<Form
						onSubmitCallback={(values) => saveCustomer(values)}
						initialValues={initialValues}
					>
						{({ getInputProps, errors, handleSubmit }) => (
							<div>
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
										updateEntity({ id: row.id, enabled })
									}
								/>
							);
						},
					},
				]}
				data={entities}
				onQueryChange={handleQuery}
				total={total}
			/>
		</Fragment>
	);
}
