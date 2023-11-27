/**
 * WordPress dependencies
 */
import { useInstanceId } from '@wordpress/compose';
import { forwardRef, useMemo } from '@wordpress/element';

/**
 * Internal dependencies
 */
import { Root, getSizeConfig, Container, Prefix, Suffix } from './styles';
import Label from './label';
import Backdrop from './backdrop';

function useUniqueId( idProp ) {
	const instanceId = useInstanceId( InputBase );
	const id = `input-base-control-${ instanceId }`;
	return idProp || id;
}

// Adapter to map props for the new ui/flex component.
function getUIFlexProps( labelPosition ) {
	const props = {};
	switch ( labelPosition ) {
		case 'top':
			props.direction = 'column';
			props.expanded = false;
			props.gap = 0;
			break;
		case 'bottom':
			props.direction = 'column-reverse';
			props.expanded = false;
			props.gap = 0;
			break;
		case 'edge':
			props.justify = 'space-between';
			break;
	}
	return props;
}

export function InputBase( props, ref ) {
	const {
		children,
		className,
		disabled = false,
		hideLabelFromVision = false,
		labelPosition,
		id: idProp,
		inputWidth,
		isFocused = false,
		label,
		prefix,
		size = 'default',
		suffix,
		...restProps
	} = props;
	const id = useUniqueId( idProp );
	const hideLabel = hideLabelFromVision || ! label;
	const { paddingLeft, paddingRight } = getSizeConfig( size );

	return (
		<Root
			{ ...restProps }
			{ ...getUIFlexProps( labelPosition ) }
			className={ className }
			gap={ 2 }
			isFocused={ isFocused }
			labelPosition={ labelPosition }
			ref={ ref }
		>
			<Label
				className="components-input-control__label"
				hideLabelFromVision={ hideLabelFromVision }
				labelPosition={ labelPosition }
				htmlFor={ id }
			>
				{ label }
			</Label>
			<Container
				inputWidth={ inputWidth }
				className="components-input-control__container"
				disabled={ disabled }
				hideLabel={ hideLabel }
				labelPosition={ labelPosition }
			>
				{ prefix && (
					<Prefix className="components-input-control__prefix">{ prefix }</Prefix>
				) }
				{ children }
				{ suffix && (
					<Suffix className="components-input-control__suffix">{ suffix }</Suffix>
				) }
				<Backdrop disabled={ disabled } isFocused={ isFocused } />
			</Container>
		</Root>
	);
}

export default forwardRef( InputBase );
