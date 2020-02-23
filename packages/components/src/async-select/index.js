import {Component} from 'react';
import {AsyncSelect} from 'react-select';
import propTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import classnames from 'classnames';

export default class AsyncSelect extends Component {
	constructor(props, context) {
		super(props, context);
		this.state = {
			selected: [],
		};
	}

	render() {
		const {label, help, className, before, after, required, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-rs-field', className, {
			required: !!required,
		});
		console.log(this.props);
		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && (
						<span className="ea-input-group__before">
							{before}
						</span>
					)}

					<AsyncSelect
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
