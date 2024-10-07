/**
 * External dependencies
 */
import { Input, useQueryResult } from '@eac/components';
/**
 * WordPress dependencies
 */
import { useState, useEffect } from '@wordpress/element';
import { Modal } from '@wordpress/components';

const App = () => {
	const [ date, setDate ] = useState();
	const [ amount, setAmount ] = useState( 100 );

	// after 2 seconds, set a new date.
	useEffect( () => {
		setTimeout( () => {
			setDate( new Date() );
			setAmount( 200 );
		}, 2000 );
	}, [] );

	const loadOptions = ( inputValue, callback ) => {
		setTimeout( () => {
			const options = [
				{ value: '1', label: 'One' },
				{ value: '2', label: 'Two' },
				{ value: '3', label: 'Three' },
			];
			callback( options );
		}, 1000 );
	};

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
			<Input.Amount
				label="Amount"
				help="Enter an amount"
				value={ amount }
				onChange={ ( val ) => {
					console.log( val );
					setAmount( val );
				} }
			/>

			<Input.Autocomplete
				label="Autocomplete"
				help="Select an option"
				defaultMenuIsOpen
				options={ [
					{ value: '1', label: 'One' },
					{ value: '2', label: 'Two' },
					{ value: '3', label: 'Three' },
				] }
			/>
			<Input.Autocomplete
				label="Autocomplete"
				help="Select an option"
				isMulti
				options={ [
					{ value: '1', label: 'One' },
					{ value: '2', label: 'Two' },
					{ value: '3', label: 'Three' },
				] }
			/>
			<Input.Autocomplete
				label="Async"
				help="Async"
				isMulti
				loadOptions={loadOptions}
			/>
		</div>
	);
};

export default App;
