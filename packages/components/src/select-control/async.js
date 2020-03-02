import {Component} from 'react';
import Async from 'react-select/async';
import PropTypes from 'prop-types';
import {BaseControl} from '@wordpress/components';
import classnames from 'classnames';

export default class AsyncSelect extends Component {
	static propTypes = {
		autoload: PropTypes.bool,
		className: PropTypes.string,
		label: PropTypes.string,
		name: PropTypes.string,
		clearable: PropTypes.bool,
		placeholder: PropTypes.string,
		searchable: PropTypes.bool,
		isMulti: PropTypes.bool,
		options: PropTypes.arrayOf(PropTypes.object),
		value: PropTypes.any,
		onChange: PropTypes.func,
		onInputChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		required: PropTypes.bool,
		loadOptions: PropTypes.func
	};

	static defaultProps = {
		onRenderLabel: item => {
			return {
				label: `${item.name}`,
				value: item.id,
			};
		},
		onChange: options => {
		},
	};

	constructor(props) {
		super(props);
		this.state = {
			selected: [],
		};
	}

	onInputChange = value => {
		return value.replace(/\W/g, '');
	};

	render() {
		const {label, help, className, before, after, required, loadOptions, ...props} = this.props;
		const classes = classnames('ea-form-group', 'ea-select-field async', className, {
			required: !!required,
		});

		const id = Math.random().toString(36).substring(7);
		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && (
						<span className="ea-input-group__before">
							{before}
						</span>
					)}

					<Async
						classNamePrefix="ea-react-select"
						className="ea-react-select"
						id={id}
						isLoading
						loadOptions={loadOptions}
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
