import PropTypes from 'prop-types';
import Select, { components } from 'react-select';
import Async from 'react-select/async';
import { BaseControl, Icon } from '@wordpress/components';
import classnames from "classnames";
import {withInstanceId} from "@wordpress/compose";
import './style.scss';
function SelectControl(props){
	const {
		async = false,
		label,
		help,
		className,
		required,
		isMulti = false,
		before,
		after,
		innerRef,
		instanceId,
		...restProps
	} = props;

	const classes = classnames(
		'ea-form-group',
		'ea-advance-select-wrap',
		className,
		{
			required: !!required,
		}
	);

	const selectorClasses = classnames(
		'ea-advance-select',
		{
			has__before: !!before,
			has__after: !!after,
		}
	);

	const Control = async ? Async : Select;

	const id = `inspector-ea-input-group-${instanceId}`;
	const describedby = [];
	if (help) {
		describedby.push(`${id}__help`);
	}
	if (before) {
		describedby.push(`${id}__before`);
	}
	if (after) {
		describedby.push(`${id}__after`);
	}
	const SelectContainer = ({ children, ...containerProps }) => {
		return (
			<components.SelectContainer {...containerProps}>
				{!!before && (
					<span
						id={`${id}__before`}
						className="ea-input-group__before"
					>
						{before}
					</span>
				)}

				{children}
				{!!after && (
					<span
						id={`${id}__after`}
						className="ea-input-group__after"
					>
								{after}
					</span>
				)}
			</components.SelectContainer>
		);
	};

	const DropdownIndicator = (dropDownProps) => {
		return (
			<components.DropdownIndicator {...dropDownProps}>
			</components.DropdownIndicator>
		);
	};

	return(
		<BaseControl label={label} help={help} className={classes}>
			<Control
				{...restProps}
				classNamePrefix="ea-advance-select"
				className={selectorClasses}
				required={required}
				isMulti={isMulti}
				components={{SelectContainer, DropdownIndicator}}
				ref={innerRef}
				aria-describedby={describedby.join(' ')}
			/>
		</BaseControl>
	)
}

SelectControl.propTypes = {
	async: PropTypes.bool,
	className: PropTypes.string,
	label: PropTypes.string,
	name: PropTypes.string,
	clearable: PropTypes.bool,
	placeholder: PropTypes.string,
	searchable: PropTypes.bool,
	isMulti: PropTypes.bool,
	options: PropTypes.arrayOf(PropTypes.object),
	disabledOption: PropTypes.object,
	value: PropTypes.any,
	onChange: PropTypes.func,
	onInputChange: PropTypes.func,
	before: PropTypes.node,
	after: PropTypes.node,
	required: PropTypes.bool,
	loadOptions: PropTypes.func,
}

export default withInstanceId(SelectControl);
