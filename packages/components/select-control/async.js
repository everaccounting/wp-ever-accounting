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
		options: PropTypes.arrayOf( PropTypes.object ),
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

	static defaultProps = {};

	constructor( props ) {
		super( props );
		this.state = {
			value: [],
		};
	}

	onInputChange = ( value ) => {
		return value.replace( /\W/g, '' );
	};

	render() {
		const {
			label,
			help,
			className,
			required,
			loadOptions,
			...props
		} = this.props;
		const classes = classnames(
			'ea-form-group',
			'ea-select-field async',
			className,
			{
				required: !! required,
			}
		);

		const id = cuid();
		return (
			<BaseControl
				label={ label }
				help={ help }
				required={ required }
				className={ classes }
			>
				<div className="ea-input-group">
					<Async
						// DropdownIndicator={false}
						// openMenuOnFocus={false}
						// openMenuOnClick={false}
						classNamePrefix="ea-react-select"
						className="ea-react-select"
						id={ id }
						required={ required }
						loadOptions={ loadOptions }
						{ ...props }
					/>
				</div>
			</BaseControl>
		);
	}
}
