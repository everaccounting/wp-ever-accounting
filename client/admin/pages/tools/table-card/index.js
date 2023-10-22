/**
 * WordPress dependencies
 */
import { Card, CardBody, CardHeader, CardTitle } from '@wordpress/components';

function TableCard(props) {
	return (
		<Card className="eac-table">
			<CardHeader title="Contacts">
				<div>
					<h2
						data-wp-c16t="true"
						data-wp-component="Text"
						className="components-truncate components-text css-10feu0u e19lxcc00"
						style={{
							margin: 0,
						}}
					>
						Products
					</h2>
				</div>
			</CardHeader>
			<CardBody>{props.children}</CardBody>
		</Card>
	);
}

export default TableCard;
