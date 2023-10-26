/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';

/**
 * External dependencies
 */
import classNames from 'classnames';
import { toArray } from 'lodash';
/**
 * Internal dependencies
 */
import './style.scss';
import Item from './item';
import { BreadcrumbContext } from './context';

function Breadcrumb({ style, className, separator = '/', children, linkRef, ...props }) {
	const childNodes = toArray(children, { keepEmpty: true });
	const breadcrumbContext = useMemo(() => ({ separator }), [separator]);
	if (childNodes.length === 0) {
		return null;
	}
	return (
		<nav
			style={style}
			className={classNames('eac-breadcrumb', className)}
			ref={linkRef}
			role="navigation"
			aria-label="Breadcrumbs"
			{...props}
		>
			{childNodes && childNodes.length > 0 && (
				<ol>
					{childNodes.map((child, index) => {
						if (child === null) {
							return null;
						}
						return (
							<BreadcrumbContext.Provider value={breadcrumbContext} key={index}>
								{child}
							</BreadcrumbContext.Provider>
						);
					})}
				</ol>
			)}
		</nav>
	);
}

Breadcrumb.Item = Item;
export default Breadcrumb;
