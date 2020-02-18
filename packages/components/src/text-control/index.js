import classnames from 'classnames';
import { Component } from '@wordpress/element';
import PropTypes from 'prop-types';
import { noop } from 'lodash';
import { TextControl as BaseComponent } from '@wordpress/components';


export default class TextControl extends Component {
	static propTypes = {
		className: PropTypes.string,
		type: PropTypes.string,
		disabled: PropTypes.bool,
		label: PropTypes.string,
		onClick: PropTypes.func,
		onChange: PropTypes.func,
		value: PropTypes.string,
		icon:PropTypes.string,
	};

	static defaultProps = {
		type: 'text',
		onClick: noop,
		onChange: noop,
	};

	render() {
		const { className, onClick, ...otherProps } = this.props;

		const { label, value, disabled } = otherProps;
		return (
			<BaseComponent
				className={classnames('ea-field ea-text-control', className, {
					disabled: disabled,
				})}
				placeholder={label}
				{...otherProps}
			/>
		);
	}
}
