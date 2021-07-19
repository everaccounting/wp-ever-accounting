/**
 * External dependencies
 */
/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalNavigation,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalNavigationBackButton,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalNavigationGroup,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalNavigationMenu,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalNavigationItem,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalText,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalUseSlot,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalFlex as ExperimentalFlex,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalFlexBlock as ExperimentalFlexBlock,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalFlexItem as ExperimentalFlexItem,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	Navigation as NavigationComponent,
	NavigationBackButton as NavigationBackButtonComponent,
	NavigationGroup as NavigationGroupComponent,
	NavigationMenu as NavigationMenuComponent,
	NavigationItem as NavigationItemComponent,
	Text as TextComponent,
	useSlot as useSlotHook,
	Flex as FlexComponent,
	FlexBlock as FlexBlockComponent,
	FlexItem as FlexItemComponent,
} from '@wordpress/components';

/**
 * Prioritize exports of non-experimental components over experimental.
 */
export const Navigation = NavigationComponent || __experimentalNavigation;
export const NavigationBackButton =
	NavigationBackButtonComponent || __experimentalNavigationBackButton;
export const NavigationGroup =
	NavigationGroupComponent || __experimentalNavigationGroup;
export const NavigationMenu =
	NavigationMenuComponent || __experimentalNavigationMenu;
export const NavigationItem =
	NavigationItemComponent || __experimentalNavigationItem;
export const Text = TextComponent || __experimentalText;
export const useSlot = useSlotHook || __experimentalUseSlot;
export const Flex = FlexComponent || ExperimentalFlex;
export const FlexBlock = FlexBlockComponent || ExperimentalFlexBlock;
export const FlexItem = FlexItemComponent || ExperimentalFlexItem;
