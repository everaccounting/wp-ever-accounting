/**
 * External dependencies
 */
import { Input, useQueryResult } from '@eac/components';
/**
 * WordPress dependencies
 */

const App = () => {
	const { isLoading, result } = useQueryResult( {
		endpoint: '/eac/v1/items',
	} );

	console.log( isLoading );
	console.log( result );

	return (
		<div>
			<h1>App</h1>
			<Input.Amount
				label="Amount"
				placeholder="Please enter a number"
				defaultValue={ 1000 }
				onValueChange={ ( value, name, values ) => console.log( value, name, values ) }
				prefix="$"
				decimalSeparator="."
				thousandSeparator=","
			/>
			<Input.Date label="Date" placeholder="Please enter a number" />
		</div>
	);
};

export default App;
