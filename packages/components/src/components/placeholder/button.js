/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import useElement from './use-element';

function Button( { active, children, ...props } ) {
	const element = useElement( 'button', props );
	const classes = classNames( 'eac-placeholder', {
		'eac-placeholder--active': active,
	} );

	return (
		<div className={ classes }>
			<span className={ element?.classes } style={ element?.style }>
				{ children }
			</span>
		</div>
	);
}

export default Button;
