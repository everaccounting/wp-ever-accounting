/**
 * External dependencies
 */
import { Autocomplete, SectionHeader, Input } from '@eac/components';
/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { useState, useCallback } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';
import { resolveSelect, useDispatch } from '@wordpress/data';
/**
 * Internal dependencies
 */
import Select from './Select';

function List() {
	// fetch from wp/v2/settings.
	// const settings = apiFetch( { path: '/wp/v2/statuses' } ).then( ( data ) => {
	// 	console.log( data );
	// } );
	// console.log( settings );
	const [ selected, setSelected ] = useState( [] );

	const SearchItems = useCallback( ( search ) => {
		return resolveSelect( 'eac/entities' )
			.getRecords( 'item', {
				search,
			} )
			.then( ( items ) => {
				console.log( items );
				return items.map( ( item ) => ( {
					label: item.name,
					value: item.id,
				} ) );
			} );
	}, [] );

	console.log( SearchItems( 'item' ) );

	return (
		<>
			<SectionHeader title={ __( 'List', 'wp-ever-accounting' ) } />
		</>
	);
}

export default List;
