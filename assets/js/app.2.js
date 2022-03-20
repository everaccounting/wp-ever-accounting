
/**
 * WordPress dependencies
 */
import { useState, useEffect, Suspense } from '@wordpress/element';
import { Spinner } from '@wordpress/components';
/**
 * External dependencies
 */
import { useApi } from '@eaccounting/data';
import { Form } from '@eaccounting/components';
/**
 * Internal dependencies
 */
import useQuery from './data.js';

export default function App() {
	const [ query, setQuery ] = useState( {} );
	const [ { data = [], isRquesting } ] = useQuery(
		'ea/v1/currencies',
		query
	);

	// useEffect( () => {
	// 	fetch( query );
	// }, [ query ] );

	return (
		<Suspense fallback={ <p>Loading...</p> }>
			<h1>Hello World</h1>

			{ JSON.stringify( data ) }

			{/*<Form*/}
			{/*	enableReinitialize={ true }*/}
			{/*	initialValues={ {*/}
			{/*		name: '',*/}
			{/*		code: '',*/}
			{/*		symbol: '',*/}
			{/*		decimal_places: '',*/}
			{/*		is_active: '',*/}
			{/*	} }*/}
			{/*	validations={ {*/}
			{/*		name: Form.isRequired,*/}
			{/*	} }*/}
			{/*	onSubmit={ ( values ) => {*/}
			{/*		console.log( values );*/}
			{/*	} }*/}
			{/*>*/}
			{/*	<div>lorem ipsum</div>*/}
			{/*</Form>*/}
		</Suspense>
	);
}
