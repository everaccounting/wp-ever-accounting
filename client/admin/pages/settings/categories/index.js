/**
 * External dependencies
 */
import { Text, Card, Button, List, Drawer } from '@eac/components';
import { navigate, getQuery } from '@eac/navigation';
import { useEntityRecords, useEntityRecord } from '@eac/data';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

/**
 * Internal dependencies
 */
import Edit from './edit';

function Categories() {
	const params = getQuery();
	const query = useMemo( () => {
		return {
			per_page: params?.per_page || 20,
			page: params?.page || 1,
		};
	}, [ params ] );
	const items = useEntityRecords( 'item', query );
	// console.log( 'List query', query );
	// console.log( 'List result', items );
	const categoryId = params?.category;
	return (
		<div>
			{ categoryId && <Edit categoryId={ categoryId } /> }
			<Card
				title={
					<Text as="h3" size="16" lineHeight="32px">
						Categories
					</Text>
				}
				actions={
					<>
						<Button size="xmall" variant="primary">
							Add Category
						</Button>
					</>
				}
			>
				<List
					bordered={ false }
					loading={ items.status === 'resolving' }
					data={ items.records }
					rowKey="id"
					renderItem={ ( item ) => (
						<List.Item
							actions={ [
								<Button
									key="edit"
									variant="link"
									onClick={ () => navigate( { category: item.id }, null, {} ) }
								>
									{ __( 'Edit' ) }
								</Button>,
							] }
						>
							<span>{ item.name }</span>
							<span>{ item.type }</span>
						</List.Item>
					) }
					pagination={ {
						page: query?.page || 1,
						perPage: query?.perPage || 20,
						total: items.recordsCount,
						onChange: ( page, per_page ) => {
							navigate( { page, per_page }, null, {} );
						},
					} }
				/>
			</Card>
		</div>
	);
}

export default Categories;
