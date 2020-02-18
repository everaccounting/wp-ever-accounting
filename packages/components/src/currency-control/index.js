import classnames from 'classnames';
import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {noop} from 'lodash';
import {BaseControl} from '@wordpress/components';
import CurrencyInput from 'react-currency-input';

export default class CurrencyControl extends Component {
	static propTypes = {
		className: PropTypes.string,
		disabled: PropTypes.bool,
		label: PropTypes.string,
		onClick: PropTypes.func,
		onChange: PropTypes.func,
		value: PropTypes.string,
		decimalSeparator: PropTypes.string,
		thousandSeparator: PropTypes.string,
		precision: PropTypes.number,
		prefix: PropTypes.string,
		suffix: PropTypes.string,
	};

	static defaultProps = {
		type: 'text',
		onClick: noop,
		onChange: noop,
		decimalSeparator: '.',
		thousandSeparator: ',',
		precision: 2,
		prefix: '$',
		suffix: '',
	};

	render() {
		const {className, onClick, decimalSeparator, thousandSeparator, precision, prefix, suffix, ...otherProps} = this.props;

		const {label, value, disabled} = otherProps;
		return (
			<BaseControl
				className={classnames('ea-field ea-text-control', className, {
					disabled: disabled,
				})}
				placeholder={label}
				{...otherProps}
			>
				<CurrencyInput decimalSeparator={decimalSeparator} thousandSeparator={thousandSeparator} precision={2}
							   prefix={prefix} suffix={suffix} value={value}/>
			</BaseControl>
		);
	}
}
