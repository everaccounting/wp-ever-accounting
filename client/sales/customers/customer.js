/**
 * External dependencies
 */
import { FlexItem, FlexBlock } from '@eaccounting/components';
import { getHistory } from '@eaccounting/navigation';
/**
 * WordPress dependencies
 */
import {
	Button,
	Card,
	CardBody,
	CardHeader,
	CardFooter,
	CardMedia,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';

export default function Customer( props ) {
	console.log( props );
	return (
		<>
			<h1 className="wp-heading-inline">{ __( 'Customers' ) }</h1>
			<Button
				isSmall
				onClick={ () => getHistory().goBack() }
				className="page-title-action"
			>
				{ __( 'Go Back' ) }
			</Button>
			<div style={ { maxWidth: '230px', marginTop: '20px' } }>
				<Card size="small">
					<CardHeader>Header</CardHeader>
					<CardBody>Body</CardBody>
					<CardBody size="large">...</CardBody>
					<CardMedia>
						<img
							src="http://2.gravatar.com/avatar/?s=100&amp;d=mm&amp;r=g"
							alt="Gary Chong"
						/>
					</CardMedia>
					<CardFooter>
						<FlexBlock>Content</FlexBlock>
						<FlexItem>
							<Button>Action</Button>
						</FlexItem>
					</CardFooter>
				</Card>
			</div>
		</>
	);
}
