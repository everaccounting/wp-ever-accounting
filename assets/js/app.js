/**
 * WordPress dependencies
 */
import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
/**
 * Internal dependencies
 */
import Table from './table';
import Payment from './payment';
function App() {
	const [ payment, setPayment ] = useState( {} );
	return (
		<div>
			<Payment payment={ payment } />
			<Table
				endpoint={ 'ea/v1/payments' }
				onClick={ ( item ) => setPayment( item ) }
			/>
			<Table endpoint={ 'ea/v1/currencies' } />
			<Table endpoint={ 'ea/v1/categories' } />
			<Table endpoint={ 'ea/v1/customers' } />
			<Table endpoint={ 'ea/v1/vendors' } />
			<Table endpoint={ 'ea/v1/items' } />
			<Table endpoint={ 'ea/v1/accounts' } />
		</div>
	);
}

// const App = Form.withFormik( {
// 	mapPropsToValues: () => ( { name: '' } ),
//
// 	// Custom sync validation
// 	validate: ( values ) => {
// 		const errors = {};
//
// 		if ( ! values.name ) {
// 			errors.name = 'Required';
// 		}
//
// 		return errors;
// 	},
//
// 	handleSubmit: ( values, { setSubmitting } ) => {
// 		setTimeout( () => {
// 			alert( JSON.stringify( values, null, 2 ) );
// 			setSubmitting( false );
// 		}, 1000 );
// 	},
//
// 	displayName: 'BasicForm',
// } )( ExampleForm );

export default App;
