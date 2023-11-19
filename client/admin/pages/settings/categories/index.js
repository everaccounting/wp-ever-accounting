/**
 * External dependencies
 */
import { Text, Panel, Button, List } from '@eac/components';
import { navigate, getQuery } from '@eac/navigation';
import { useEntityRecords } from '@eac/data';
/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { __ } from '@wordpress/i18n';

function Categories() {
	const params = getQuery();
	const query = useMemo( () => {
		return {
			per_page: params?.per_page || 20,
			page: params?.page || 1,
		};
	}, [ params ] );
	const items = useEntityRecords( 'item', query );
	return (
		<div>
			<Panel
				title={
					<Text as="h3" size="16" lineHeight="32px">
						Categories
					</Text>
				}
				actions={
					<>
						<Button variant="primary">Add Category</Button>
					</>
				}
			>
				<Panel.Body>
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
										onClick={ () =>
											navigate( { category: item.id }, null, {} )
										}
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
				</Panel.Body>
			</Panel>
		</div>
	);
}

export default Categories;
