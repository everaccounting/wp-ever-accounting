/**
 * External dependencies
 */
import { Input, useQueryResult } from '@eac/components';
/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';

const App = () => {
	const [ date, setDate ] = useState();

	// after 2 seconds, set a new date.
	useEffect( () => {
		setTimeout( () => {
			console.log( 'Setting date to 2021-01-01' );
			setDate( new Date() );
		}, 2000 );
	}, [] );

	console.log( date );

	return (
		<div>
			<h1>App</h1>
			<Input
				label="Text"
				help="Enter some text"
				// disabled={ true }
				value="Some text"
			/>
			<Input.Date
				label="Date"
				help="Select a date"
				suffix="Suffix"
				selected={ date }
				onChange={ ( _date ) => setDate( _date ) }
			/>
		</div>
	);
};

export default App;
