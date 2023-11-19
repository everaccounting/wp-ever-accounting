/**
 * External dependencies
 */
import { List } from '@eac/components';
import { useEntityRecords } from '@eac/data';
import { navigate, getQuery } from '@eac/navigation';

function Rates() {
	const query = getQuery();
	const items = useEntityRecords( 'item', query );
	return (
		<List
			loading={ items.status === 'resolving' }
			data={ items.records }
			rowKey="id"
			header={ 'Tax Rates' }
			footer={ 'Footer' }
			bordered={ false }
			renderItem={ ( item ) => <List.Item>{ item.name }</List.Item> }
			pagination={ {
				page: query?.page || 1,
				perPage: query?.perPage || 20,
				total: items.recordsCount,
				onChange: ( page, per_page ) => {
					navigate( { page, per_page } );
				},
			} }
		/>
	);
}

export default Rates;
