import { Component } from '@wordpress/element';
import PropTypes from 'prop-types';
import { BaseControl } from '@wordpress/components';
import classnames from 'classnames';
import MaskedInput from 'react-text-mask';
import createNumberMask from 'text-mask-addons/dist/createNumberMask';


export default class PriceControl extends Component {

	onChange = (e) => {
		this.props.onChange && this.props.onChange(e.target.value);
	};

	render() {
		const { label, code = 'USD', help, className, before, after, required, value, ...props } = this.props;
		const classes = classnames('ea-form-group', 'ea-price-field', className, {
			required: !!required,
		});

		const currency = eAccountingi10n.data.currencies[code];

		const suffix = 'before' !== currency.position ? currency.symbol : '';
		const prefix = 'before' === currency.position ? currency.symbol : '';

		const maskOptions = {
			prefix:prefix,
			suffix:suffix,
			allowDecimal: !!currency.precision,
			decimalSymbol: currency.decimalSeparator,
			decimalLimit: currency.precision,
			thousandsSeparatorSymbol: currency.thousandSeparator,
		};

		const currencyMask = createNumberMask(maskOptions);

		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && <span className="ea-input-group__before">{before}</span>}

					<MaskedInput
						required={required}
						placeholder={`${currency.symbol} 0.00`}
						className="components-text-control__input ea-input-group__input"
						mask={currencyMask}
						value={value}
						onChange={this.onChange}
						inputMode="numeric"
					/>
					{after && <span className="ea-input-group__after">{after}</span>}
				</div>
			</BaseControl>
		);
	}
}

PriceControl.defaultProps = {
	code: 'USD',
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
