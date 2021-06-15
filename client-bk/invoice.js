/**
 * External dependencies
 */
import { Card, CurrencySelect } from '@eaccounting/components';
/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';

export default function Invoice() {
	return (
		<>
			<Card>
				<div className="ea-row">
					<CurrencySelect label={__('Currency')} required={true} />
				</div>
			</Card>
		</>
	);
}
