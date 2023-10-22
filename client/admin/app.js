/**
 * External dependencies
 */
import { ENTITIES_STORE_NAME } from '@eac/data';

/**
 * Internal dependencies
 */
import './style.scss';

export function App() {
	// const entities = useSelect( ( select ) => {
	// 	return select( ENTITIES_STORE_NAME ).getRecords( 'item' );
	// }, [] );
	console.log( ENTITIES_STORE_NAME );
	// console.log( entities );
	return <div className="text-3xl font-bold underline">Hello world!</div>;
}

export default App;
