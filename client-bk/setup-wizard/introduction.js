/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */
import {
	Button,
	Card,
	CardBody,
	CardFooter,
	CardHeader,
} from '@wordpress/components';

export default function Introduction({ goToNextStep, skip }) {
	return (
		<Card>
			<CardHeader>Lorem ipsum dolor sit amet.</CardHeader>
			<CardBody>
				Lorem ipsum dolor sit amet, consectetur adipisicing elit.
				Commodi, impedit.
			</CardBody>
			<CardFooter>
				<Button onClick={goToNextStep}>Go</Button>
			</CardFooter>
		</Card>
	);
}
