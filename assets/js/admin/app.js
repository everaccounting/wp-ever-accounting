/**
 * WordPress dependencies
 */
// eslint-disable-next-line import/no-extraneous-dependencies
import { render } from '@wordpress/element';
import domReady from '@wordpress/dom-ready';
// import { __ } from '@wordpress/i18n';
// import {STORE_NAME, useSelectWithRefresh} from '@eaccounting/data';

/**
 * External dependencies
 */
/**
 * Internal dependencies
 */
// import {useSelect} from '@wordpress/data';
// import {TextControl} from '@wordpress/components';
import Demo from "./demo";
import Table from "./table";

function App() {
	// const [page, setPage] = useState(1);
	// const {products, total, loading} = useSelect( ( select ) => {
	// 	return {
	// 		products:select( STORE_NAME ).getProducts({per_page: 1, page} ),
	// 		total:select( STORE_NAME ).getTotalProducts({per_page: 1, page} ),
	// 		// loading:select( STORE_NAME ).isRequesting( 'getProducts', {per_page: 1, page} ),
	// 		loading:select( STORE_NAME ).getIsResolving( 'getProducts', [{per_page: 1, page}] ),
	// 	}
	// }, [page] );
	//

	// console.log(useSelectWithRefresh(( select ) => {
	// 	return {
	// 		products:select( STORE_NAME ).getProducts({per_page: 1, page} ),
	// 		total:select( STORE_NAME ).getTotalProducts({per_page: 1, page} ),
	// 		// loading:select( STORE_NAME ).isRequesting( 'getProducts', {per_page: 1, page} ),
	// 		loading:select( STORE_NAME ).getIsResolving( 'getProducts', [{per_page: 1, page}] ),
	// 	}
	// }, function () {
	// 	console.log('invalidate')
	// }, 1000));
	return (
		<>
		<Demo/>
		<Table/>
		</>
	);
}

domReady( () => {
	const root = document.getElementById( 'ea-react' );
	return render( <App />, root );

} );
