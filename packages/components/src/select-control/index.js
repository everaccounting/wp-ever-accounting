/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { BaseControl, Dashicon } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import classnames from 'classnames';
import Select, {components} from 'react-select';


export default class SelectControl extends Component {
	transformValue = (value, options, isMulti) => {
		if (isMulti && typeof value === 'string') return [];
		const filteredOptions = options.filter(option => {
			return isMulti ? value.indexOf(option.value) !== -1 : option.value === value;
		});

		return isMulti ? filteredOptions : filteredOptions[0];
	};

	singleChangeHandler = value => {
		this.props.onChange && this.props.onChange(value ? value.value : '');
	};

	multiChangeHandler = values => {
		this.props.onChange && this.props.onChange((values && values.map(value => value.value)) || []);
	};

	onClick = () => {
		this.props.onFooterClick && this.props.onFooterClick();
	};

	render() {
		const {
			label,
			help,
			className,
			before,
			after,
			required,
			value = '',
			options,
			innerRef,
			footer,
			addText,
			addIcon,
			isMulti = false,
			OnChange,
			...props
		} = this.props;
		const classes = classnames('ea-form-group', 'ea-select-field', className, {
			required: !!required,
		});

		const MenuList = props => {
			return (
				<Fragment>
					<components.MenuList {...props}>{props.children}</components.MenuList>
					{footer && this.props.onFooterClick && (
						<div className="ea-react-select__footer ea-react-select__option" onClick={this.onClick}>
							<Dashicon icon={addIcon} size={20} /> <span>{addText}</span>
						</div>
					)}
				</Fragment>
			);
		};

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
						components={{ MenuList }}
						ref={innerRef}
						onChange={isMulti ? this.multiChangeHandler : this.singleChangeHandler}
					/>

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
	onFooterClick: PropTypes.func,
	footer: PropTypes.bool,
	addText: PropTypes.string,
	addIcon: PropTypes.string,
};

SelectControl.defaultProps = {
	autoload: false,
	addText: __('Add New'),
	addIcon: 'plus',
};
