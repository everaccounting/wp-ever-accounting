/**
 * WordPress dependencies
 */
import { useMemo } from '@wordpress/element';
import { SearchControl } from '@wordpress/components';
import { __ } from '@wordpress/i18n';

function Toolbar( props ) {
	const { actions, search, query, onSearch, renderTools, isLoading } = props;
	const searchNode = useMemo( () => {
		if ( false === search ) {
			return null;
		}
		return (
			<SearchControl
				className="eac-table__search"
				disabled={ isLoading }
				value={ query.search }
				onChange={ onSearch }
				placeholder={ __( 'Search', 'wp-ever-accounting' ) }
				{ ...( typeof search === 'object' ? search : {} ) }
			/>
		);
	}, [ search, isLoading, query.search, onSearch ] );

	const actionsNode = useMemo( () => {
		if ( ! actions ) {
			return null;
		}
	}, [ actions ] );

	const toolsNode = useMemo( () => {
		if ( ! renderTools ) {
			return null;
		}
	}, [ renderTools ] );

	if ( ! actionsNode && ! searchNode && ! toolsNode ) {
		return null;
	}

	return (
		<div className="eac-table__section eac-table__section--toolbar">
			{ actionsNode }
			{ searchNode }
			{ toolsNode }
		</div>
	);
}

export default Toolbar;
