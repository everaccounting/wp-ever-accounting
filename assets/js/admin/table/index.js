import {STORE_NAME} from '@eaccounting/data';
import {__} from '@wordpress/i18n';
import {Fragment, useState} from '@wordpress/element';
import {useDispatch, useSelect} from '@wordpress/data';
import {ListTable, Drawer, Form, Date} from "@eaccounting/components";
import {Button, TextControl} from "@wordpress/components";


export default function Products() {
	const [query, setQuery] = useState({});
	const {result, total, isLoading} = useSelect( ( select ) => {
		return {
			result:select( STORE_NAME ).getProducts(query ),
			total: select( STORE_NAME ).getTotalProducts(query ),
			isLoading: select( STORE_NAME ).isRequesting( 'getProducts', query ),
		}
	}, [query] );
	console.log(STORE_NAME);
	console.log(result);
	console.log(total);
	console.log(isLoading);
	// return null;

	const handleQuery = (newQuery) => {
		setQuery({...query, ...newQuery});
	}

	const updateProduct = (fields) => {
		useDispatch(STORE_NAME).saveProduct(fields)
	}

	return(
		<>
			<ListTable
				title={__('Invoice', 'wp-ever-accounting')}
				query={{
					page: 1,
					per_page: 20,
					orderby: 'id',
					order: 'desc',
					search: '',
				}}
				isLoading={isLoading}
				columns={[
					{
						label: __('Name', 'wp-ever-account'),
						property: "name",
						sortable: true,
						render: (row) => {
							return (
								<a href={row.permalink}>{row.name}</a>
							);
						}
					},
					{
						label: __('Sales', 'wp-ever-account'),
						property: "total_sales",
						sortable: true,
					},
					{
						label: __('SKU', 'wp-ever-account'),
						property: "sku",
						sortable: true,
					},
					{
						label: __('Update', 'wp-ever-account'),
						property: "update",
						render: (row) => {
							return (
								<>
									<TextControl value={row.name} />
								</>
							);
						}
					},
				]}
				data={result}
				onQueryChange={handleQuery}
				total={total}
			/>
		</>
	)
}
