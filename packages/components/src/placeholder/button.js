/**
 * External dependencies
 */
import classNames from 'classnames';
/**
 * Internal dependencies
 */
import Element from './element';

function SkeletonButton(props) {
	const { className, active, block = false, size = 'default', ...otherProps } = props;
	const classes = classNames('eac-placeholder-button', 'eac-placeholder-element', className, {
		'eac-placeholder--active': active,
		'eac-placeholder--block': block,
	});

	return (
		<div className={classes}>
			<Element classPrefix="eac-placeholder-button" size={size} {...otherProps} />
		</div>
	);
}

export default SkeletonButton;
