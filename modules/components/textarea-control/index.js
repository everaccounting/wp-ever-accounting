/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import classnames from 'classnames';

/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { withInstanceId } from '@wordpress/compose';
import {
	BaseControl,
	TextareaControl as TextArea,
} from '@wordpress/components';
/**
 * Internal dependencies
 */
import './style.scss';
class TextareaControl extends Component {
	render() {
		const {
			label,
			help,
			className,
			required,
			value,
			isLoading,
			instanceId,
			...props
		} = this.props;
		const classes = classnames(
			'ea-form-group',
			'ea-textarea-field',
			className,
			{
				required: !!required,
				'is-loading': !!isLoading,
			}
		);
		const id = `textarea-${instanceId}`;
		return (
			<BaseControl id={id} label={label} help={help} className={classes}>
				<div className="ea-input-group">
					<TextArea
						value={(value && value) || ''}
						{...props}
						required={required}
					/>
				</div>
			</BaseControl>
		);
	}
}
TextareaControl.defaultProps = {};

TextareaControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	value: PropTypes.string,
	className: PropTypes.string,
	onChange: PropTypes.func.isRequired,
	required: PropTypes.bool,
};

export default withInstanceId(TextareaControl);
