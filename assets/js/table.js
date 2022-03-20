/**
 * External dependencies
 */
import { useApi } from '@eaccounting/data';

/**
 * WordPress dependencies
 */
import { Spinner } from '@wordpress/components';

export const Table = ( { endpoint, onClick } ) => {
	const [ { data = [], error, setLocalData, isLoading }, fetch ] = useApi.get(
		endpoint
	);
	if ( isLoading ) return <Spinner />;
	if ( error ) return <div>Error: { JSON.stringify( error ) }</div>;
	return (
		<div>
			<h1>
				<code>{ endpoint }</code>
			</h1>
			<ul>
				{ data.map( ( item ) => (
					<li key={ item.id } onClick={ () => onClick( item ) }>
						{ item.id } - { item.name || item.amount }
					</li>
				) ) }
			</ul>
		</div>
	);
};

export default Table;
