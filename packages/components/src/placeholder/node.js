/**
 * External dependencies
 */
import classNames from 'classnames';

export const Node = ( props ) => {
	const { className, rootClassName, style, active, children } = props;

	const classes = classNames( 'eac-placeholder', 'eac-placeholder-element', className, {
		'eac-placeholder--active': active,
	} );

	return (
		<div className={ classes }>
			<div className={ classNames( 'eac-placeholder-image', rootClassName ) } style={ style } />
			{ children }
		</div>
	);
};

export default Node;
