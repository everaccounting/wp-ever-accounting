/**
 * WordPress dependencies
 */
import {Component} from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import {BaseControl, TextareaControl as BaseToggle} from '@wordpress/components';
import classnames from 'classnames';
import Placeholder from "../placeholder";

export default class TextareaControl extends Component {
	render() {
		const {label, help, className, required, value, isLoading, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-textarea-field', className, {
			required: !!required,
			'is-loading': !!isLoading
		});

		return (
			<BaseControl label={label} help={help} className={classes}>
				{isLoading ? <Placeholder className="ea-input-group"/> :
					<div className="ea-input-group">
						<BaseToggle value={(value && value) || ''} {...props} required={required}/>
					</div>}
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
