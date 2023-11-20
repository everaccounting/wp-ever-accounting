/**
 * WordPress dependencies
 */
import {
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalText,
	Text as TextComponent,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__experimentalHeading,
	Heading as HeadingComponent,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__unstableMotion as MotionComponent,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__unstableMotion,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__unstableAnimatePresence as AnimatePresenceComponent,
	// eslint-disable-next-line @wordpress/no-unsafe-wp-apis
	__unstableAnimatePresence,
} from '@wordpress/components';

/**
 * Export experimental components within the components package to prevent a circular
 * dependency with woocommerce/experimental. Only for internal use.
 */
export const Text = TextComponent || __experimentalText;
export const Heading = HeadingComponent || __experimentalHeading;
export const Motion = MotionComponent || __unstableMotion;
export const AnimatePresence = AnimatePresenceComponent || __unstableAnimatePresence;
