/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import Select, { components } from 'react-select';
/**
 * WordPress dependencies
 */
import { BaseControl } from '@wordpress/components';
import classnames from 'classnames';
import { withInstanceId } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import './style.scss';
function SelectControl(props) {
	const {
		label,
		help,
		className,
		required,
		before,
		after,
		setRef,
		instanceId,
		button,
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

	const selectorClasses = classnames('ea-advance-select', {
		has__before: !!before,
		has__after: !!after,
	});

	const id = `inspector-ea-input-group-${instanceId}`;
	const describedby = [];
	if (help) {
		describedby.push(`${id}__help`);
	}
	// eslint-disable-next-line no-unused-vars
	const SelectContainer = ({ children, ...innerProps }) => {
		return (
			<components.SelectContainer {...innerProps}>
				{!!before && (
					<span
						id={`${id}__before`}
						className="ea-input-group__before"
					>
						{before}
					</span>
				)}
				<>{children}</>
				{!!after && (
					<span id={`${id}__after`} className="ea-input-group__after">
						{after}
					</span>
				)}
			</components.SelectContainer>
		);
	};

	const DropdownIndicator = (dropDownProps) => {
		return <components.DropdownIndicator {...dropDownProps} />;
	};

	const Menu = (menuProps) => {
		return (
			<components.Menu {...menuProps}>
				<div>
					{menuProps.children}
					{!!button && <>{button}</>}
				</div>
			</components.Menu>
		);
	};

	return (
		<BaseControl id={id} label={label} help={help} className={classes}>
			<Select
				classNamePrefix="ea-advance-select"
				className={selectorClasses}
				required={required}
				ref={setRef}
				aria-describedby={describedby.join(' ')}
				styles={{
					menuPortal: (base) => ({ ...base, zIndex: 9999999 }),
				}}
				menuPortalTarget={document.getElementById('eaccounting-root')}
				menuPosition={'fixed'}
				{...restProps}
				components={{ Menu, DropdownIndicator }}
			/>
		</BaseControl>
	);
}

SelectControl.propTypes = {
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
};

export default withInstanceId(SelectControl);
