/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import {__} from '@wordpress/i18n';
import classnames from 'classnames';
import Select from 'react-select';
import {merge} from "lodash";

export default class SelectControl extends Component {
	transformValue = (value, options, isMulti) => {
		if (isMulti && typeof value === 'string') return [];
		const filteredOptions = options.filter(option => {
			return isMulti
				? value.indexOf(option.value) !== -1
				: option.value === value;
		});

		return isMulti ? filteredOptions : filteredOptions[0];
	};

	singleChangeHandler = (value) => {
		this.props.onChange && this.props.onChange(value ? value.value : '')
	};

	multiChangeHandler = (values) => {
		this.props.onChange && this.props.onChange(values.map(value => value.value))
	};

	render() {
		const {label, help, className, before, after, required, value = '', options, isMulti = false, OnChange, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-select-field', className, {
			required: !!required,
		});

		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && <span className="ea-input-group__before">{before}</span>}

					<Select
						{...props}
						classNamePrefix="ea-react-select"
						className="ea-react-select"
						required={required}
						value={this.transformValue(value, options, isMulti)}
						options={options}
						isMulti={isMulti}
						onChange={isMulti
							? this.multiChangeHandler
							: this.singleChangeHandler
						}/>

					{after && <span className="ea-input-group__after">{after}</span>}
				</div>
			</BaseControl>
		);
	}
}
SelectControl.propTypes = {
	autoload: PropTypes.bool,
	className: PropTypes.string,
	label: PropTypes.string,
	name: PropTypes.string,
	clearable: PropTypes.bool,
	placeholder: PropTypes.string,
	searchable: PropTypes.bool,
	multi: PropTypes.bool,
	options: PropTypes.arrayOf(PropTypes.object).isRequired,
	value: PropTypes.any,
	onChange: PropTypes.func,
	onInputChange: PropTypes.func,
	before: PropTypes.node,
	after: PropTypes.node,
	required: PropTypes.bool,
};

SelectControl.defaultProps = {
	autoload: false,
};
