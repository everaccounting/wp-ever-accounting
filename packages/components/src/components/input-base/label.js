/**
 * Internal dependencies
 */
import { VisuallyHidden } from '../visually-hidden';
import { Label as BaseLabel, LabelWrapper } from './styles/input-control-styles';
export default function Label({ children, hideLabelFromVision, htmlFor, ...props }) {
	if (!children) return null;
	if (hideLabelFromVision) {
		return (
			<VisuallyHidden as="label" htmlFor={htmlFor}>
				{children}
			</VisuallyHidden>
		);
	}
	return (
		<LabelWrapper>
			<BaseLabel htmlFor={htmlFor} {...props}>
				{children}
			</BaseLabel>
		</LabelWrapper>
	);
}
