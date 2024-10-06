/**
 * WordPress dependencies
 */
import { useCallback, useEffect, useRef, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { addQueryArgs } from '@wordpress/url';

/**
 * External dependencies
 */
import { isEmpty, isObject } from 'lodash';

export function useAsync( {
	fetch: propsFetch,
	query: propsQuery = {},
	onChangeQuery: propsOnChangeQuery,
	cacheResult = false,
	...props
} ) {
	// === Refs ===
	const lastRequest = useRef( undefined );
	const mounted = useRef( false );
}
