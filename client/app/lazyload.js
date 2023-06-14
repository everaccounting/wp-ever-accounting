import {lazy, Suspense} from "@wordpress/element";

// A wrapper for lazy loading components with Suspense. Example call below.
// const MyComponent = lazyLoad('path/to/component');

export default function LazyLoad(componentPath) {
	const LazyComponent = lazy(() => import(`${componentPath}`));

	return function LazyComponentWrapper() {
		return (
			<Suspense fallback={<div>Loading...</div>}>
				<LazyComponent />
			</Suspense>
		);
	};
}
