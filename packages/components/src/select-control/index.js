import classnames from 'classnames';
import {Component} from '@wordpress/element';
import PropTypes from 'prop-types';
import {noop} from 'lodash';
import {SelectControl as BaseSelect, BaseControl} from '@wordpress/components';
import {withInstanceId} from '@wordpress/compose';


class SelectControl extends Component {

	render() {
		const {label, value, help, className, instanceId, onChange, before, after, type, required, ...props} = this.props;
		const classes = classnames('ea-form-group','ea-select-field', className, {
			required: !!required,
		});
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

		return (
			<BaseControl label={label} id={id} help={help} className={classes}>
				<div className="ea-input-group">
					{before && (
						<span id={`${id}__before`} className="ea-input-group__before">
							{before}
						</span>
					)}

					<BaseSelect {...props} aria-describedby={describedby.join(' ')}/>

					{after && (
						<span id={`${id}__after`} className="ea-input-group__after">
							{after}
						</span>
					)}
				</div>
			</BaseControl>
		);
	}
}

SelectControl.propTypes = {
	className: PropTypes.string,
	disabled: PropTypes.bool,
	label: PropTypes.string,
	help: PropTypes.string,
	onClick: PropTypes.func,
	onChange: PropTypes.func,
	options: PropTypes.arrayOf(PropTypes.object),
	value: PropTypes.string,
	before: PropTypes.node,
	after: PropTypes.node,
	required: PropTypes.bool,
};
SelectControl.defaultProps = {
	onClick: noop,
	onChange: noop,
};
export default withInstanceId(SelectControl);
