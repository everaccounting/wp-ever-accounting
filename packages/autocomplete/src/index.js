/**
 * External dependencies
 */
import Select from 'react-select';

export const Autocomplete = ( props ) => {
	return <Select { ...props } classNamePrefix="eac-autocomplete" />;
};

export default Autocomplete;
