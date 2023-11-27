/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import { BaseControl, FormToggle, MenuItem as MenuItemComponent } from '@wordpress/components';
import { DOWN, ENTER, SPACE, UP } from '@wordpress/keycodes';
import { useRef } from '@wordpress/element';

const MenuItem = ( {
	checked,
	children,
	isCheckbox = false,
	isClickable = true,
	onInvoke = () => {},
} ) => {
	const container = useRef( null );
	const onClick = ( event ) => {
		if ( isClickable ) {
			event.preventDefault();
			onInvoke();
		}
	};

	const onKeyDown = ( event ) => {
		const eventTarget = event.target;
		if ( eventTarget.isSameNode( event.currentTarget ) ) {
			if ( event.keyCode === ENTER || event.keyCode === SPACE ) {
				event.preventDefault();
				onInvoke();
			}
			if ( event.keyCode === UP ) {
				event.preventDefault();
			}
			if ( event.keyCode === DOWN ) {
				event.preventDefault();
				const nextElementToFocus =
					eventTarget.nextSibling ||
					eventTarget.parentNode?.querySelector( '.eac-dropdown__item' );
				nextElementToFocus.focus();
			}
		}
	};

	const onFocusFormToggle = () => {
		container?.current?.focus();
	};

	if ( isCheckbox ) {
		return (
			<div
				aria-checked={ checked }
				ref={ container }
				role="menuitemcheckbox"
				tabIndex={ 0 }
				onKeyDown={ onKeyDown }
				onClick={ onClick }
				className="eac-dropdown__item eac-dropdown__item--checkbox"
			>
				{ /* id props is actuall an optional prop. It looks like DefinitelyTyped has out-of-date types*/ }
				{ /* @ts-expect-error: Suprressing `id` is required prop error.  */ }
				<BaseControl className="components-toggle-control">
					<FormToggle
						aria-hidden="true"
						checked={ checked }
						onChange={ onInvoke }
						onFocus={ onFocusFormToggle }
						onClick={ ( e ) => e.stopPropagation() }
						tabIndex={ -1 }
					/>
					{ children }
				</BaseControl>
			</div>
		);
	}

	return (
		<MenuItemComponent
			role="menuitem"
			tabIndex={ 0 }
			onKeyDown={ onKeyDown }
			onClick={ onClick }
			className="eac-dropdown__item"
		>
			{ children }
		</MenuItemComponent>
	);
};

export default MenuItem;
