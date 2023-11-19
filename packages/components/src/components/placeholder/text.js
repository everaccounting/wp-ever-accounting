/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import useElement from './use-element';

export const Text = ( { active, as, ...props } ) => {
	const element = useElement( 'text', props );
	const classes = classNames( 'eac-placeholder', {
		'eac-placeholder--active': active,
	} );

	const TagName = as || 'span';
	return (
		<div className={ classes }>
			<TagName className={ element?.classes } style={ element?.style }>
				{ props.children }
			</TagName>
		</div>
	);
};

export default Text;
