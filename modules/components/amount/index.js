/**
 * External dependencies
 */
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { formatAmount, CORE_STORE_NAME } from '@eaccounting/data';
import { isObject } from 'lodash';
/**
 * Internal dependencies
 */

/**
 * Use the `Amount` component to display formatted amount.
 *
 * @param {Object} props
 * @param {string} props.amount
 * @param {string|Object} props.currency
 * @return {Object} -
 */
const Amount = ({ amount, currency }) => {
	const { AmountCurrency, isRequesting = true } = useSelect((select) => {
		const currency_code = isObject(currency) ? currency.code : currency;
		const { getEntityRecord, isResolving } = select(CORE_STORE_NAME);
		return {
			AmountCurrency: getEntityRecord('currencies', currency_code),
			isRequesting: isResolving('getEntityRecord', [
				'currencies',
				currency_code,
			]),
		};
	});

	return (
		<span className="ea-amount" data-amount={amount}>
			{isRequesting || !AmountCurrency
				? '....'
				: formatAmount(amount, AmountCurrency)}
		</span>
	);
};

Amount.propTypes = {
	// Amount to display
	amount: PropTypes.PropTypes.oneOfType([PropTypes.string, PropTypes.number]),
	// Currency code.
	currency_code: PropTypes.PropTypes.oneOfType([
		PropTypes.string,
		PropTypes.object,
	]).isRequired,
};

Amount.defaultProps = {
	currency_code: '',
};

export default Amount;
