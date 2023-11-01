/**
 * External dependencies
 */
import classNames from 'classnames';

export const Node = ( props ) => {
	const { className, style, active, children } = props;

	const classes = classNames(
		'eac-placeholder',
		'eac-placeholder-element',
		className,
		{
			'eac-placeholder--active': active,
		}
	);

	return (
		<div className={ classes }>
			<div className="eac-placeholder-image" style={ style } />
			{ children }
		</div>
	);
};

export default Node;
