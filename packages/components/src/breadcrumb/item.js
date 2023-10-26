/**
 * WordPress dependencies
 */
import { useContext } from '@wordpress/element';
/**
 * Internal dependencies
 */
import { BreadcrumbContext } from './context';

function Item({ children, props }) {
	const { separator } = useContext(BreadcrumbContext);
	console.log('separator item', separator);
	return (
		<li style={props?.style} className={props?.className}>
			<span className="eac-breadcrumb__item" ref={props?.linkRef}>
				{children}
			</span>
			<span className="eac-breadcrumb__separator">{separator}</span>
		</li>
	);
}

export default Item;
