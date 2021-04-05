/**
 * External dependencies
 */
import classnames from 'classnames';

/**
 * An arrow attached to a popovor.
 *
 * @param {Object} props - Component props.
 * @param {Object} props.style - Any style properties to attach to the arrow.
 * @param {string} props.align - The current dropdown alignment (`left`, `right`, `centre`).
 */
export default function PopoverArrows( { style, align } ) {
	const classes = classnames( 'ea-popover__arrows', {
		'ea-popover__arrows__left': align === 'left',
		'ea-popover__arrows__right': align === 'right',
		'ea-popover__arrows__centre': align === 'centre',
	} );

	return <div className={ classes } style={ style } />;
}
