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
	ContactSelect,
	CurrencySelect,
	CategorySelect,
	ItemSelect,
} from '@eaccounting/components';

/**
 * Internal dependencies
 */
function App() {
	return (
		<>
			<ContactSelect
				creatable={true}
				label={'Customer'}
				type={'customers'}
			/>

			<CategorySelect
				creatable={true}
				label={'Item category'}
				type={'item'}
			/>
			<CategorySelect
				creatable={true}
				label={'Income category'}
				type={'income'}
			/>
			<CategorySelect
				creatable={true}
				label={'Expense category'}
				type={'expense'}
			/>
			<CurrencySelect creatable={true} />
			<ItemSelect creatable={true} label={'Item'} />
			<NoticeContainer />
			{/*<CurrencyModal />*/}
			{/*<CurrencyForm />*/}
			{/*<CategoryModal*/}
			{/*	item={{ type: 'income' }}*/}
			{/*	onSave={(item) => console.log(item)}*/}
			{/*/>*/}
		</>
	);
}

domReady(() => {
	const root = document.getElementById('ea-react');
	return root ? render(<App />, root) : null;
});
