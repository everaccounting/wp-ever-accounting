/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
import Async from 'react-select/async';
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { BaseControl, Dashicon } from '@wordpress/components';
import classnames from 'classnames';
import { __ } from '@wordpress/i18n';
import isShallowEqual from '@wordpress/is-shallow-equal';
import cuid from 'cuid';
import { components } from 'react-select';

export default class AsyncSelect extends Component {
	static propTypes = {
		className: PropTypes.string,
		label: PropTypes.string,
		name: PropTypes.string,
		clearable: PropTypes.bool,
		placeholder: PropTypes.string,
		searchable: PropTypes.bool,
		isMulti: PropTypes.bool,
		options: PropTypes.arrayOf(PropTypes.object),
		disabledOption: PropTypes.object,
		value: PropTypes.any,
		onChange: PropTypes.func,
		onInputChange: PropTypes.func,
		before: PropTypes.node,
		after: PropTypes.node,
		required: PropTypes.bool,
		loadOptions: PropTypes.func,
		onFooterClick: PropTypes.func,
		footer: PropTypes.bool,
		addText: PropTypes.string,
		addIcon: PropTypes.string,
	};

	static defaultProps = {
		onChange: options => {},
		addText: __('Add New'),
		addIcon: 'plus',
	};

	constructor(props) {
		super(props);
		this.state = {
			value: [],
		};
	}

	shouldComponentUpdate(nextProps, nextState, nextContext) {
		return !isShallowEqual(nextProps, this.props);
	}

	onInputChange = value => {
		return value.replace(/\W/g, '');
	};

	onClick = () => {
		this.props.onFooterClick && this.props.onFooterClick();
	};

	render() {
		const {
			label,
			help,
			className,
			before,
			after,
			required,
			loadOptions,
			footer,
			addText,
			addIcon,
			disabledOption = {},
			innerRef,
			...props
		} = this.props;
		const classes = classnames('ea-form-group', 'ea-select-field async', className, {
			required: !!required,
		});

		const MenuList = props => {
			return (
				<Fragment>
					<components.MenuList {...props}>{props.children}</components.MenuList>
					{footer && this.props.onFooterClick && (
						<div className="ea-react-select__footer ea-react-select__option" onClick={this.onClick}>
							<Dashicon icon={addIcon} size={20} /> <span>{addText}</span>
						</div>
					)}
				</Fragment>
			);
		};

		const Option = props => {
			const { data, getStyles, innerRef, innerProps } = props;
			return null;
		};

		const id = cuid();
		return (
			<BaseControl label={label} help={help} className={classes}>
				<div className="ea-input-group">
					{before && <span className="ea-input-group__before">{before}</span>}

					<Async
						classNamePrefix="ea-react-select"
						className="ea-react-select"
						id={id}
						components={{ MenuList }}
						ref={this.props.innerRef}
						isOptionDisabled={option =>
							option && option.id && disabledOption && disabledOption.id && option.id === disabledOption.id
						}
						loadOptions={loadOptions}
						{...props}
					/>

					{after && <span className="ea-input-group__after">{after}</span>}
				</div>
			</BaseControl>
		);
	}
}
