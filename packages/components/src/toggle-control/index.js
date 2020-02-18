import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {BaseControl, FormToggle as BaseToggle} from '@wordpress/components';
import classnames from 'classnames';


export default class ToggleControl extends Component {
	render() {
		const {label, value, help, className, onChange,  ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-toggle-field', className);

		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					<BaseToggle {...props} onChange={onChange}/>
				</div>
			</BaseControl>
		)
	}
}

ToggleControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	checked: PropTypes.bool,
	className: PropTypes.string,
	onChange: PropTypes.func,
	// checked: PropTypes.bool,
};
