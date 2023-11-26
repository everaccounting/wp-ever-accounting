/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { Popover } from '@wordpress/components';
import classnames from 'classnames';
import { useEffect, useRef, createPortal, Children, useLayoutEffect } from '@wordpress/element';
export const Menu = ( {
	children,
	getMenuProps,
	isOpen,
	className,
	position = 'bottom right',
	scrollIntoViewOnOpen = false,
} ) => {
	const selectControlMenuRef = useRef( null );
	const popoverRef = useRef( null );
	useLayoutEffect( () => {
		const comboboxWrapper = selectControlMenuRef.current?.closest(
			'.woocommerce-experimental-select-control__combo-box-wrapper'
		);
		const popoverContent = popoverRef.current?.querySelector( '.components-popover__content' );
		if ( comboboxWrapper && comboboxWrapper?.clientWidth > 0 ) {
			if ( popoverContent ) {
				popoverContent.style.width = `${ comboboxWrapper.getBoundingClientRect().width }px`;
			}
		}
	}, [
		selectControlMenuRef.current,
		selectControlMenuRef.current?.clientWidth,
		popoverRef.current,
	] );
	// Scroll the selected item into view when the menu opens.
	useEffect( () => {
		if ( isOpen && scrollIntoViewOnOpen ) {
			selectControlMenuRef.current?.scrollIntoView();
		}
	}, [ isOpen, scrollIntoViewOnOpen ] );
	/* eslint-disable jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/click-events-have-key-events */
	/* Disabled because of the onmouseup on the ul element below. */
	return (
		<div ref={ selectControlMenuRef } className="woocommerce-experimental-select-control__menu">
			<div>
				<Popover
					// @ts-expect-error this prop does exist, see: https://github.com/WordPress/gutenberg/blob/trunk/packages/components/src/popover/index.tsx#L180.
					__unstableSlotName="woocommerce-select-control-menu"
					focusOnMount={ false }
					className={ classnames(
						'woocommerce-experimental-select-control__popover-menu',
						{
							'is-open': isOpen,
							'has-results': Children.count( children ) > 0,
						}
					) }
					position={ position }
					animate={ false }
					resize={ false }
					ref={ popoverRef }
				>
					<ul
						{ ...getMenuProps() }
						className={ classnames(
							'woocommerce-experimental-select-control__popover-menu-container',
							className
						) }
						onMouseUp={ ( e ) =>
							// Fix to prevent select control dropdown from closing when selecting within the Popover.
							e.stopPropagation()
						}
					>
						{ isOpen && children }
					</ul>
				</Popover>
			</div>
		</div>
	);
	/* eslint-enable jsx-a11y/no-noninteractive-element-interactions, jsx-a11y/click-events-have-key-events */
};
export const MenuSlot = () =>
	createPortal(
		<div aria-live="off">
			{ /* @ts-expect-error name does exist on PopoverSlot see: https://github.com/WordPress/gutenberg/blob/trunk/packages/components/src/popover/index.tsx#L555 */ }
			<Popover.Slot name="woocommerce-select-control-menu" />
		</div>,
		document.body
	);
