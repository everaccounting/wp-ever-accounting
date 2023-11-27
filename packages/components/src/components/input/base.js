/**
 * External dependencies
 */
import { css } from '@emotion/react';
import styled from '@emotion/styled';

/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { Flex, FlexItem } from '@wordpress/components';

export function Base(props, ref) {
	const {
		children,
		className,
		disabled = false,
		hideLabelFromVision = false,
		id: idProp,
		inputWidth,
		isFocused = false,
		label,
		labelPosition,
		prefix,
		suffix,
		...restProps
	} = props;
	const instanceId = useInstanceId(Base);
	const id = `input-base-control-${instanceId}` || idProp;
	const hideLabel = hideLabelFromVision || !label;
	const size = {
		height: 40,
		lineHeight: 1,
		minHeight: 40,
		paddingLeft: 8,
		paddingRight: 8,
	};
	const Root = styled(Flex)`
		box-sizing: border-box;
		position: relative;
		border-radius: 2px;
		padding-top: 0;
		z-index: ${isFocused ? 1 : null};
	`;

	return (
		<Root
			className={className}
			gap={2}
			isFocused={isFocused}
			labelPosition={labelPosition}
			ref={ref}
		>
			{children}
		</Root>
	);
}
