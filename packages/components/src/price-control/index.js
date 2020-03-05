import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import classnames from 'classnames';
import MaskedInput from 'react-text-mask'
import createNumberMask from 'text-mask-addons/dist/createNumberMask'
import {getCurrencyData} from "lib/currency/currencies";

export default class PriceControl extends Component {
	render() {
		const {label, code = "USD", help, className, onChange, before, after, required, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-price-field', className, {
			required: !!required,
		});

		const currency = getCurrencyData(code);
		console.log(currency);

		const suffix = ('before' !== currency.position) ? currency.symbol : '';
		const prefix = ('before' === currency.position) ? currency.symbol : '';

		const maskOptions = {
			prefix,
			suffix,
			allowDecimal: !!currency.precision,
			decimalSymbol: currency.decimalSeparator,
			decimalLimit: currency.precision,
			thousandsSeparatorSymbol: currency.thousandSeparator,
		};

		const currencyMask = createNumberMask({
			...maskOptions,
		});


		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && (
						<span className="ea-input-group__before">
							{before}
						</span>
					)}

					<MaskedInput
						required={required}
						className='components-text-control__input ea-input-group__input'
						mask={currencyMask}
						{...props}
						inputMode="numeric"/>
					{after && (
						<span className="ea-input-group__after">
							{after}
						</span>
					)}
				</div>
			</BaseControl>
		)
	}
}

PriceControl.defaultProps = {
	code: "USD",
	value: 0,
};

PriceControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.any,
	code: PropTypes.string,
	className: PropTypes.string,
	onChange: PropTypes.func,
	required: PropTypes.bool,
	before: PropTypes.node,
	after: PropTypes.node,
};


