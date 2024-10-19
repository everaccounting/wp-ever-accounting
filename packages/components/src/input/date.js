/**
 * WordPress dependencies
 */
import { forwardRef, Suspense, lazy, memo } from '@wordpress/element';
import { useInstanceId } from '@wordpress/compose';
import { Spinner, __experimentalInputControl as InputControl } from '@wordpress/components';
import { Icon, calendar } from '@wordpress/icons';

/**
 * External dependencies
 */
const DatePicker = lazy( () => import( 'react-datepicker' ) );
import 'react-datepicker/dist/react-datepicker.css';

/**
 * Internal dependencies
 */
import { useControlledValue } from '../utils';

const Date = forwardRef( ( { label, help, placeholder, prefix, disabled, ...props }, ref ) => {
	// const [ value, setValue ] = useControlledValue( {
	// 	defaultValue: undefined,
	// 	value: props.value,
	// 	// onChange: props.onChange,
	// } );
	const id = useInstanceId( Date, 'eac-input-date', props.id );

	return (
		<Suspense fallback={ <Spinner /> }>
			<DatePicker
				{ ...{
					dateFormat: 'yyyy-MM-dd',
					toggleCalendarOnIconClick: true,
					popperPlacement: 'bottom-start',
					// inline: false,
					popperModifiers: [
						{
							name: 'arrow',
							options: {
								padding: ( { popper } ) => ( {
									right: popper.width - 32,
								} ),
							},
							fn( state ) {
								if ( state.placement === 'top-start' ) {
									state.y = help ? state.y + 30 : state.y + 10;
								} else if ( state.placement === 'bottom-start' ) {
									state.y = help ? state.y - 30 : state.y - 10;
								}
								return state;
							},
						},
					],

					...props,
				} }
				ref={ ref }
				disabled={ disabled }
				// value={ value }
				// onChange={ ( newValue ) => setValue( newValue ) }
				icon={ calendar }
				customInput={
					<InputControl
						id={ id }
						label={ label }
						help={ help }
						placeholder={ placeholder }
						disabled={ disabled }
						prefix={ prefix }
						suffix={
							<Icon icon={ calendar } size={ 16 } style={ { marginRight: '8px' } } />
						}
					/>
				}
			/>
		</Suspense>
	);
} );

export default memo( Date );
