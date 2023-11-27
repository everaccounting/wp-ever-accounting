/**
 * External dependencies
 */
import { Autocomplete, SectionHeader } from '@eac/components';
/**
 * WordPress dependencies
 */
import apiFetch from '@wordpress/api-fetch';
import { useState } from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { addQueryArgs } from '@wordpress/url';

import Select from './Select';

function List() {
	// fetch from wp/v2/settings.
	// const settings = apiFetch( { path: '/wp/v2/statuses' } ).then( ( data ) => {
	// 	console.log( data );
	// } );
	// console.log( settings );
	const [ selected, setSelected ] = useState( [] );

	const fetchSuggestions = ( search ) => {
		return apiFetch( {
			url: addQueryArgs( '/wp-json/eac/v1/items', {
				search,
				per_page: 20,
			} ),
		} ).then( function ( posts ) {
			return posts.map( ( post ) => ( {
				value: post.id,
				label: post.name || __( '(no title)', 'beebom-features' ),
			} ) );
		} );
	};

	return (
		<>
			<SectionHeader title={ __( 'List', 'wp-ever-accounting' ) } />
			<Autocomplete
				tokens={ selected || [] }
				onChange={ ( tokens ) => setSelected( tokens ) }
				fetchSuggestions={ fetchSuggestions }
				fetchSavedInfo={ fetchSuggestions }
				label={ __( 'Selected Products', 'beebom-features' ) }
				help={ __( 'Select some product for showing here', 'beebom-features' ) }
			/>
		</>
	);
}

export default List;
