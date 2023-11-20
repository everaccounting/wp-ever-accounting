/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import useElement from './use-element';
function Node( { active, children, ...props } ) {
	const element = useElement( 'image', props );
	const classes = classNames( 'eac-placeholder', {
		'eac-placeholder--active': active,
	} );

	return (
		<div className={ classes }>
			<div className={ element?.classes } style={ element?.style }>
				{ children }
			</div>
		</div>
	);
}

export default Node;
