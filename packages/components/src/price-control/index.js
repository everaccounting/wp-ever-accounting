import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import classnames from 'classnames';


import CurrencyInput from "react-currency-input";

export default class PriceControl extends Component {
	render() {
		const {label, currency, help, className, onChange, before, after, required, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-price-field', className, {
			required: !!required,
		});

		const {precision = 2, symbol = '$', decimal_mark = '.', thousands_separator = '', rate = '1', symbol_position = 'before'} = currency;

		const suffix = ('before' !== symbol_position) ? symbol : '';
		const prefix = ('before' === symbol_position) ? symbol : '';

		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && (
						<span className="ea-input-group__before">
							{before}
						</span>
					)}
					<CurrencyInput
						{...this.props}
						suffix={suffix}
						prefix={prefix}
						precision={precision}
						decimal_mark={decimal_mark}
						thousands_separator={thousands_separator}
						selectAllOnFocus
						required={required}
						className='components-text-control__input ea-input-group__input'/>
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
	currency: {},
	value: 0,
};

PriceControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.string,
	currency: PropTypes.object,
	className: PropTypes.string,
	onChange: PropTypes.func,
	required: PropTypes.bool,
	before: PropTypes.node,
	after: PropTypes.node,
};


