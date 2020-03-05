import { Component } from '@wordpress/element';
import PropTypes from 'prop-types';
import { BaseControl, TextareaControl as BaseToggle } from '@wordpress/components';
import classnames from 'classnames';

export default class TextareaControl extends Component {
	render() {
		const { label, help, className, required, ...props } = this.props;
		const classes = classnames('ea-form-group', 'ea-textarea-field', className, {
			required: !!required,
		});

		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					<BaseToggle {...props} required={required} />
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
