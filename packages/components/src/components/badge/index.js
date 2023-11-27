/**
 * WordPress dependencies
 */
import { useMemo, useRef } from '@wordpress/element';
/**
 * Internal dependencies
 */
import './style.scss';

export const Badge = ( props ) => {
	const {
		className,
		status,
		text,
		color,
		count = null,
		overflowCount = 99,
		dot = false,
		size = 'default',
		title,
		offset,
		showZero = false,
		...rest
	} = props;
	const numberedDisplayCount = count > overflowCount ? `${ overflowCount }+` : count;
	const isZero = numberedDisplayCount === '0' || numberedDisplayCount === 0;
	const ignoreCount = count === null || ( isZero && ! showZero );
	const hasStatus =
		( ( status !== null && status !== undefined ) ||
			( color !== null && color !== undefined ) ) &&
		ignoreCount;
	const showAsDot = dot && ! isZero;
	const mergedCount = showAsDot ? '' : numberedDisplayCount;
	const isHidden = useMemo( () => {
		const isEmpty = mergedCount === null || mergedCount === undefined || mergedCount === '';
		return ( isEmpty || ( isZero && ! showZero ) ) && ! showAsDot;
	}, [ mergedCount, isZero, showZero, showAsDot ] );
	const countRef = useRef( count );
	if ( ! isHidden ) {
		countRef.current = count;
	}
	const livingCount = countRef.current;
	// We need cache count since remove motion should not change count display
	const displayCountRef = useRef( mergedCount );
	if ( ! isHidden ) {
		displayCountRef.current = mergedCount;
	}
	const displayCount = displayCountRef.current;
	// We will cache the dot status to avoid shaking on leaved motion
	const isDotRef = useRef( showAsDot );
	if ( ! isHidden ) {
		isDotRef.current = showAsDot;
	}
	const titleNode =
		title ??
		( typeof livingCount === 'string' || typeof livingCount === 'number'
			? livingCount
			: undefined );
	const statusTextNode =
		isHidden || ! text ? null : (
			<span className={ `${ prefixCls }-status-text` }>{ text }</span>
		);
	const displayNode =
		! livingCount || typeof livingCount !== 'object'
			? undefined
			: cloneElement( livingCount, ( oriProps ) => ( {
					style: { ...mergedStyle, ...oriProps.style },
			  } ) );
	return (
		<span className={ `eac-badge ${ className }` } { ...props }>
			{ count }
		</span>
	);
};

export default Badge;
