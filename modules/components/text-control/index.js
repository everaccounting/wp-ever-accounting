/**
 * External dependencies
 */
import PropTypes from 'prop-types';
/**
 * WordPress dependencies
 */
import { BaseControl } from '@wordpress/components';
// eslint-disable-next-line import/no-extraneous-dependencies
import { withInstanceId } from '@wordpress/compose';
import classnames from 'classnames';

/**
 * Internal dependencies
 */
import './style.scss';

function TextControl( props ) {
	const {
		label,
		value,
		help,
		className,
		instanceId,
		onChange,
		before,
		after,
		type,
		placeholder,
		required,
		isLoading,
		validate = ( x ) => x,
		...restProps
	} = props;

	const id = `inspector-ea-input-group-${ instanceId }`;
	const onChangeValue = ( event ) => onChange( event.target.value );

	const describedby = [];
	if ( help ) {
		describedby.push( `${ id }__help` );
	}
	if ( before ) {
		describedby.push( `${ id }__before` );
	}
	if ( after ) {
		describedby.push( `${ id }__after` );
	}

	const newPlaceholder =
		! placeholder && !! label ? `Enter ${ label }` : placeholder;

	const wrapperClasses = classnames(
		'ea-form-group',
		'ea-text-field',
		className,
		{
			required: !! required,
			'is-loading': !! isLoading,
		}
	);

	const inputClasses = classnames(
		'ea-input-group__input',
		'components-text-control__input',
		{
			'has--before': !! before,
			'has--after': !! after,
		}
	);

	return (
		<BaseControl
			label={ label }
			id={ id }
			help={ help }
			className={ wrapperClasses }
		>
			<div className="ea-input-group">
				{ before && (
					<span
						id={ `${ id }__before` }
						className="ea-input-group__before"
					>
						{ before }
					</span>
				) }

				<input
					className={ inputClasses }
					type={ type }
					id={ id }
					value={ ( value && value ) || '' }
					onChange={ onChangeValue }
					required={ required }
					autoComplete="off"
					placeholder={ newPlaceholder }
					aria-describedby={ describedby.join( ' ' ) }
					onKeyPress={ ( e ) => validate( e.target.value ) }
					{ ...restProps }
				/>

				{ after && (
					<span
						id={ `${ id }__after` }
						className="ea-input-group__after"
					>
						{ after }
					</span>
				) }
			</div>
		</BaseControl>
	);
}

TextControl.defaultProps = {
	type: 'text',
};

TextControl.propTypes = {
	label: PropTypes.string,
	help: PropTypes.string,
	type: PropTypes.string,
	value: PropTypes.any,
	className: PropTypes.string,
	onChange: PropTypes.func.isRequired,
	before: PropTypes.node,
	after: PropTypes.node,
	required: PropTypes.bool,
	validate: PropTypes.func,
};

export default withInstanceId( TextControl );
