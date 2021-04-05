/**
 * WordPress dependencies
 */
import { Component, Fragment } from '@wordpress/element';
/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { BaseControl, Dashicon } from '@wordpress/components';
import classnames from 'classnames';
import Select, { components } from 'react-select';

export default class SelectControl extends Component {
	constructor( props ) {
		super( props );
	}

	render() {
		const {
			label,
			help,
			className,
			required,
			options,
			isMulti = false,
			...props
		} = this.props;

		const classes = classnames(
			'ea-form-group',
			'ea-select-field',
			className,
			{
				required: !! required,
			}
		);

		const MenuList = ( props ) => {
			return (
				<components.MenuList { ...props }>
					{ props.children }
					{ this.props.children && (
						<div
							className="ea-react-select__option ea-react-select__custom_option"
							tabIndex="-1"
							onClick={ () => {
								if ( this.ref.current.state.menuIsOpen ) {
									this.ref.current.select.blur();
								}
								this.props.onClickChild();
							} }
						>
							<Dashicon icon="plus" size={ 20 } />{ ' ' }
							<span>{ this.props.children }</span>
						</div>
					) }
				</components.MenuList>
			);
		};

		return (
			<BaseControl label={ label } help={ help } className={ classes }>
				<div className="ea-input-group">
					<Select
						{ ...props }
						closeMenuOnSelect={ true }
						classNamePrefix="ea-react-select"
						className="ea-react-select"
						required={ required }
						options={ options }
						isMulti={ isMulti }
						ref={ ( node ) => {
							this.select = node;
						} }
						components={ { MenuList } }
					/>
				</div>
			</BaseControl>
		);
	}
}
