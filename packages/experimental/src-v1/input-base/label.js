/**
 * WordPress dependencies
 */
import { VisuallyHidden } from '@wordpress/components';

/**
 * Internal dependencies
 */
import { Label as BaseLabel, LabelWrapper } from './styles';
export default function Label( { children, hideLabelFromVision, htmlFor, ...props } ) {
	if ( ! children ) return null;
	if ( hideLabelFromVision ) {
		return (
			<VisuallyHidden as="label" htmlFor={ htmlFor }>
				{ children }
			</VisuallyHidden>
		);
	}
	return (
		<LabelWrapper>
			<BaseLabel htmlFor={ htmlFor } { ...props }>
				{ children }
			</BaseLabel>
		</LabelWrapper>
	);
}
