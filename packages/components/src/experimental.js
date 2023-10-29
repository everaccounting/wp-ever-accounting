/**
 * WordPress dependencies
 */
import { __experimentalText, Text as TextComponent, __experimentalHeading, Heading as HeadingComponent } from '@wordpress/components';

/**
 * Export experimental components within the components package to prevent a circular
 * dependency with woocommerce/experimental. Only for internal use.
 */
export const Text = TextComponent || __experimentalText;
export const Heading = HeadingComponent || __experimentalHeading;
