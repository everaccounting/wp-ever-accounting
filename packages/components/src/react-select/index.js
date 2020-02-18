import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import classnames from 'classnames';
import Select from 'react-select';



export default class ReactSelect extends Component {
	render() {
		const {label, help, className, before, after, required, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-rs-field', className, {
			required: !!required,
		});


		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && (
						<span className="ea-input-group__before">
							{before}
						</span>
					)}

					<Select
						classNamePrefix="ea-react-select"
						className="ea-react-select"
						{...props}
					/>

					{after && (
						<span className="ea-input-group__after">
							{after}
						</span>
					)}
				</div>
			</BaseControl>
		);
	}
}
ReactSelect.propTypes = {
	autoload:PropTypes.bool,
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

ReactSelect.defaultProps = {
	autoload: false,
};
