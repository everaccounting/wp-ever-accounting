import {render} from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
import '@wordpress/notices';
import {useEffect, useState} from '@wordpress/element';
// const {assets_url} = window.eaccounting_i10n
// __webpack_public_path__ = `${assets_url}/`;
import {useDispatch, useSelect} from '@wordpress/data';

function App(props){
	// const {items} = useSelect((select) => {
	// 	const {getAccount} = select('ea/store');
	// 	return {
	// 		items: getAccount(),
	// 	}
	// }, [query]);
	// const items = useSelect( ( select ) => {
	// 	return select( 'ea/store' ).getAccount()
	// }, [props] );
	// console.log(items);
	// useEffect(() => {
	// 	console.log(items);
	// }, [items]);

	return(
		<div>
			APP
		</div>
	)
}

domReady(() => {
	const root = document.getElementById('ea-cash-flow');
	return render(<App/>, root);
});


// import $ from "jquery" ;
// import { blockUI } from '@eaccounting/utils';
// import { STORE_NAME } from '@eaccounting/data';
// const { withSelect } = wp.data;
//
// $(document).ready(function () {
// 	// const authors = wp.data.select( 'core' ).getEntityRecords( 'root', 'user', { per_page: 3 } );
// 	// console.log(authors);
// 	// blockUI({el: '.ea-cash-flow'});
// 	// blockUI({el: '#ea-latest-income'});
// 	// console.log(STORE_NAME);
// });


// function MyAuthorsListBase( { authors } ) {
// 	console.log(authors);
// }
//
// const MyAuthorsList = withSelect( ( select ) => ( {
// 	authors: select( 'core' ).getAuthors(),
// } ) )( MyAuthorsListBase );
//
// MyAuthorsList();
