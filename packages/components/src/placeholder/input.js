/**
 * External dependencies
 */
import classNames from 'classnames';

export const Input = ( props ) => {
	const { className, block, style, active, children } = props;

	const classes = classNames( 'eac-placeholder', 'eac-placeholder-element', className, {
		'eac-placeholder--active': active,
		'eac-placeholder--block': block,
	} );

	return (
		<div className={ classes }>
			<div className="eac-placeholder-input" style={ style } />
			{ children }
		</div>
	);
};

export default Input;
