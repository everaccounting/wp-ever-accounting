/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
/**
 * Internal dependencies
 */
import { useEntity, useSettings } from '@eaccounting/data';
import { Money } from '@eaccounting/currency';

export default function Accounts( props ) {
	const { settings, getOption, defaultCurrency } = useSettings();
	return (
		<>
			<h1>Accounts</h1>
			{ Money().formatAmount( 199999 ) }
		</>
	);
}
