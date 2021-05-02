/**
 * WordPress dependencies
 */
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
/**
 * Internal dependencies
 */
import { IncomeCategoryModal } from './forms/category';
import { ItemModal } from './forms/item';
/**
 * External dependencies
 */
import { NoticeContainer, EntitySelect } from '@eaccounting/components';

/**
 * Internal dependencies
 */
function App() {
	// addSnackbar('Settings Updated', 'updated_user');
	return (
		<>
			<EntitySelect
				label={'Category'}
				entity={'categories'}
				creatable={true}
				modal={IncomeCategoryModal}
			/>
			<EntitySelect
				label={'Item'}
				entity={'items'}
				creatable={true}
				modal={ItemModal}
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
