/**
 * External dependencies
 */
import { isEqual } from 'lodash';

/**
 * WordPress dependencies
 */
import { useEffect, useRef } from '@wordpress/element';

const useDeepCompareMemoize = (value) => {
	const valueRef = useRef();

	if (!isEqual(value, valueRef.current)) {
		valueRef.current = value;
	}
	return valueRef.current;
};

function useOutsideClick(elementRefs, onOutsideClick, isListening, listeningElementRef) {
	const mouseDownTargetRef = useRef();
	const elementRefsMemoized = useDeepCompareMemoize([elementRefs].flat());
	useEffect(() => {
		const handleMouseDown = (event) => {
			mouseDownTargetRef.current = event.target;
		};
		const handleMouseUp = (event) => {
			const isAnyIgnoredElementAncestorOfTarget = elementRefsMemoized.some(
				(elementRef) =>
					elementRef.current.contains(mouseDownTargetRef.current) ||
					elementRef.current.contains(event.target)
			);
			if (event.button === 0 && !isAnyIgnoredElementAncestorOfTarget) {
				onOutsideClick();
			}
		};
		const handleKeyDown = (event) => {
			if (event.key === 'Escape') {
				onOutsideClick();
			}
		};

		const listeningElement = (listeningElementRef || {}).current || document;

		if (isListening) {
			listeningElement.addEventListener('mousedown', handleMouseDown);
			listeningElement.addEventListener('mouseup', handleMouseUp);
			listeningElement.addEventListener('keydown', handleKeyDown);
		}
		return () => {
			listeningElement.removeEventListener('mousedown', handleMouseDown);
			listeningElement.removeEventListener('mouseup', handleMouseUp);
			listeningElement.removeEventListener('keydown', handleKeyDown);
		};
	}, [elementRefs, listeningElementRef, isListening, onOutsideClick, elementRefsMemoized]);
}

export default useOutsideClick;
