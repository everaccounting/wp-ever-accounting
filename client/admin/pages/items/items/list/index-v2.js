/**
 * External dependencies
 */
import { SectionHeader, Steps } from '@eac/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

function List() {
	const description = 'This is a description.';
	return (
		<>
			<SectionHeader title={ __( 'List', 'wp-ever-accounting' ) } />
			<Steps
				current={ 1 }
				items={ [
					{
						title: 'Finished',
						description,
					},
					{
						title: 'In Progress',
						description,
						subTitle: 'Left 00:00:08',
					},
					{
						title: 'Waiting',
						description,
					},
				] }
			/>
			<Steps
				direction="vertical"
				current={ 1 }
				items={ [
					{
						title: 'Finished',
						description,
					},
					{
						title: 'In Progress',
						description,
						subTitle: 'Left 00:00:08',
					},
					{
						title: 'Waiting',
						description,
					},
				] }
			/>
		</>
	);
}

export default List;
