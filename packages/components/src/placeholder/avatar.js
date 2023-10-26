/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Element from './element';

function Avatar( { className, active, shape = 'circle', size = 'default', ...props } ) {
	const classes = classNames( 'eac-placeholder-avatar', className, {
		'eac-placeholder--active': active,
		'eac-placeholder--element': active,
	} );

	return (
		<div className={ classes }>
			<Element prefixCls="eac-placeholder-avatar" shape={ shape } size={ size } { ...props } />
		</div>
	);
}

export default Avatar;
