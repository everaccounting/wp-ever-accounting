/**
 * External dependencies
 */
import classNames from 'classnames';
import pickAttrs from 'rc-util/lib/pickAttrs';

function getBreadcrumbName(route, params) {
	if (route.title === undefined || route.title === null) {
		return null;
	}
	const paramsKeys = Object.keys(params).join('|');
	return typeof route.title === 'object'
		? route.title
		: String(route.title).replace(
				new RegExp(`:(${paramsKeys})`, 'g'),
				(replacement, key) => params[key] || replacement
		  );
}
export function renderItem(prefixCls, item, children, href) {
	if (children === null || children === undefined) {
		return null;
	}
	const { className, onClick, ...restItem } = item;
	const passedProps = {
		...pickAttrs(restItem, {
			data: true,
			aria: true,
		}),
		onClick,
	};
	if (href !== undefined) {
		return (
			<a {...passedProps} className={classNames(`${prefixCls}-link`, className)} href={href}>
				{children}
			</a>
		);
	}
	return (
		<span {...passedProps} className={classNames(`${prefixCls}-link`, className)}>
			{children}
		</span>
	);
}
export default function useItemRender(prefixCls, itemRender) {
	return (item, params, routes, path, href) => {
		if (itemRender) {
			return itemRender(item, params, routes, path);
		}
		const name = getBreadcrumbName(item, params);
		return renderItem(prefixCls, item, name, href);
	};
}
