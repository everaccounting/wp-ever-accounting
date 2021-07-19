/**
 * External dependencies
 */
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { formatAmount, CORE_STORE_NAME } from '@eaccounting/data';
/**
 * Internal dependencies
 */

/**
 * Use the `Amount` component to display formatted amount.
 *
 * @param {Object} props
 * @param {string} props.amount
 * @param {string} props.currency_code
 * @return {Object} -
 */
const Amount = ( { amount, currency_code } ) => {
	const { currency, isRequesting = true } = useSelect( ( select ) => {
		const { getEntityRecord, isResolving } = select( CORE_STORE_NAME );
		return {
			currency: getEntityRecord( 'currencies', currency_code ),
			isRequesting: isResolving( 'getEntityRecord', [
				'currencies',
				currency_code,
			] ),
		};
	} );

	return (
		<span className="ea-amount" data-amount={ amount }>
			{ isRequesting || ! currency
				? '....'
				: formatAmount( amount, currency ) }
		</span>
	);
};

Amount.propTypes = {
	// Amount to display
	amount: PropTypes.string,
	// Currency code.
	currency_code: PropTypes.string.isRequired,
};

Amount.defaultProps = {
	screenReaderFormat: 'F j, Y',
	currency_code: '',
};

export default Amount;
