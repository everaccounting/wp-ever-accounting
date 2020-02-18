import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import classnames from 'classnames';


import CurrencyInput from "react-currency-input";

export default class CurrencyControl extends Component {
	render() {
		const {label, value, help, className, instanceId, onChange, before, after, type, required, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-currency-field', className, {
			required: !!required,
		});
		const {symbol_position, symbol} = props;
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
					<CurrencyInput{...this.props} suffix={suffix} prefix={prefix} selectAllOnFocus className='components-text-control__input ea-input-group__input'/>
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

CurrencyControl.defaultProps = {
	type: 'text',
	decimalSeparator: '.',
	thousandSeparator: ',',
	precision: 2,
	symbol: '$',
	symbol_position: 'before',
	suffix: '',
	value: '',
};

CurrencyControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	type: PropTypes.string,
	value: PropTypes.string,
	className: PropTypes.string,
	onChange: PropTypes.func,
	symbol: PropTypes.string,
	symbol_position: PropTypes.string,
	required: PropTypes.bool,
	before: PropTypes.node,
	after: PropTypes.node,
};


