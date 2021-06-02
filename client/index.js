/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
/**
 * Internal dependencies
 */
/**
 * External dependencies
 */
import {
	NoticeContainer,
	// ContactSelect,
	// CurrencySelect,
	// CategorySelect,
	// ItemSelect,
	// AccountSelect,
	DatePicker,
} from '@eaccounting/components';
import Invoice from './invoice';
/**
 * Internal dependencies
 */
function App() {
	return (
		<>
			{/*<ContactSelect*/}
			{/*	creatable={true}*/}
			{/*	label={'Customer'}*/}
			{/*	type={'customers'}*/}
			{/*/>*/}

			{/*<CategorySelect*/}
			{/*	creatable={true}*/}
			{/*	label={'Item category'}*/}
			{/*	type={'item'}*/}
			{/*/>*/}
			{/*<CategorySelect*/}
			{/*	creatable={true}*/}
			{/*	label={'Income category'}*/}
			{/*	type={'income'}*/}
			{/*/>*/}
			{/*<CategorySelect*/}
			{/*	creatable={true}*/}
			{/*	label={'Expense category'}*/}
			{/*	type={'expense'}*/}
			{/*/>*/}
			{/*<AccountSelect creatable={true} label={'Account'} />*/}
			{/*<CurrencySelect creatable={true} label={'Currency'} />*/}
			{/*<ItemSelect creatable={true} label={'Item'} />*/}
			<DatePicker
				label={'Date'}
				dateFormat={'YYYY-MM-DD'}
				onUpdate={(val) => console.log(val)}
			/>
			{/*<Invoice />*/}
			<NoticeContainer />
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-react');
	return root ? render(<App />, root) : null;
});
