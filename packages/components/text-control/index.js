/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import PropTypes from 'prop-types';
import { BaseControl } from '@wordpress/components';
import { withInstanceId } from '@wordpress/compose';
import classnames from 'classnames';
import { sprintf } from '@wordpress/i18n';
/**
 * Internal dependencies
 */
import Placeholder from '../placeholder';
import './style.scss';
class TextControl extends Component {
	render() {
		const {
			label,
			value,
			help,
			className,
			instanceId,
			onChange,
			before,
			after,
			type,
			placeholder,
			required,
			isLoading,
			...props
		} = this.props;

		const classes = classnames('ea-form-group', 'ea-text-field', className, {
			required: !!required,
			'is-loading': !!isLoading,
		});

		const id = `inspector-ea-input-group-${instanceId}`;

		const onChangeValue = event => onChange(event.target.value);

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

		const newPlaceholder = !placeholder && label ? sprintf(`Enter ${label}`) : placeholder;

		return (
			<BaseControl label={label} id={id} help={help} className={classes}>
				{isLoading ? (
					<Placeholder className="ea-input-group" />
				) : (
					<div className="ea-input-group">
						{before && (
							<span id={`${id}__before`} className="ea-input-group__before">
								{before}
							</span>
						)}

						<input
							className="ea-input-group__input components-text-control__input"
							type={type}
							id={id}
							value={(value && value) || ''}
							onChange={onChangeValue}
							required={required}
							autoComplete="off"
							placeholder={newPlaceholder}
							aria-describedby={describedby.join(' ')}
							{...props}
						/>

						{after && (
							<span id={`${id}__after`} className="ea-input-group__after">
								{after}
							</span>
						)}
					</div>
				)}
			</BaseControl>
		);
	}
}

TextControl.defaultProps = {
	type: 'text',
};

TextControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	type: PropTypes.string,
	value: PropTypes.any,
	className: PropTypes.string,
	onChange: PropTypes.func.isRequired,
	before: PropTypes.node,
	after: PropTypes.node,
	required: PropTypes.bool,
};

export default withInstanceId(TextControl);
