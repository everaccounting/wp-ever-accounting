/**
 * WordPress dependencies
 */
import { useSelect, useDispatch } from '@wordpress/data';
import { useCallback } from '@wordpress/element';
function useSettings() {
	const options = useSelect( ( select ) => select( 'eac/settings' ).getOptions() );
	const { saveOptions, editOptions } = useDispatch( 'eac/settings' );
	const getOption = useCallback(
		( name ) => {
			return options[ name ];
		},
		[ options ]
	);

	const updateOptions = useCallback(
		( newOptions ) => {
			return saveOptions( newOptions );
		},
		[ saveOptions ]
	);

	const updateOption = useCallback(
		( name, value ) => {
			return editOptions( name, value );
		},
		[ editOptions ]
	);

	return {
		options,
		getOption,
		updateOptions,
		updateOption,
	};
}

export default useSettings;
