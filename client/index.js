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
	EntitySelect,
	ContactModal,
} from '@eaccounting/components';

/**
 * Internal dependencies
 */
function App() {
	return (
		<>
			<EntitySelect
				creatable={true}
				modal={<ContactModal />}
				label={'Customer'}
				entity={'customers'}
				modalItem={{ type: 'customer' }}
			/>
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
	return render(<App />, root);
});
