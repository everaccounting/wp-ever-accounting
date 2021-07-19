/**
 * Internal dependencies
 */
import CurrencyModal from '../../forms/currency';

export default {
	entityName: 'currencies',
	getOptionLabel: ( currency ) => `${ currency.name } (${ currency.symbol })`,
	getOptionValue: ( currency ) => currency && currency.code,
	modal: <CurrencyModal />,
};
