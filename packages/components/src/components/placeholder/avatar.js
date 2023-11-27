/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import useElement from './use-element';

function Avatar( { active, ...props } ) {
	const element = useElement( 'avatar', props );
	const classes = classNames( 'eac-placeholder', {
		'eac-placeholder--active': active,
	} );
	return (
		<div className={ classes }>
			<span className={ element?.classes } style={ element?.style } />
		</div>
	);
}

export default Avatar;
