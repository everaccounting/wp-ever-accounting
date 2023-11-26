/**
 * WordPress dependencies
 */
import { useSelect } from '@wordpress/data';
import { useCallback, useMemo } from '@wordpress/element';
import { partial } from 'lodash';
/**
 * Internal dependencies
 */
import useQuerySelect from './use-query-select';
import { STORE_NAME } from '../entitites/constants';

function useCurrencies() {
	return useQuerySelect( ( select ) => {
		const { getRecord } = select( 'eac/entities' );
		return partial( getRecord, 'currency' );
	}, [] );
}

export default useCurrencies;
