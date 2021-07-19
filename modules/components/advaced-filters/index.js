/**
 * External dependencies
 */
import PropTypes from 'prop-types';
import { partial, difference, isEmpty } from 'lodash';

/**
 * WordPress dependencies
 */
import {
	Button,
	Icon,
	Dropdown,
	Card,
	CardBody,
	CardFooter,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { useState, useRef } from '@wordpress/element';

/**
 * Internal dependencies
 */
import {
	getActiveFiltersFromQuery,
	getDefaultOptionValue,
	getQueryFromActiveFilters,
	getPath,
	getHistory,
	generatePath,
} from '@eaccounting/navigation';
import Link from '../link';
import FilterItem from './filter-item';
import './style.scss';

const getInstanceNumber = ( key, instanceCounts = {} ) => {
	if ( ! instanceCounts.hasOwnProperty( key ) ) {
		instanceCounts[ key ] = 1;
	}
	return instanceCounts[ key ]++;
};

const getActiveFilters = ( filtersFromQuery, filters, instanceCounts ) => {
	return filtersFromQuery.map( ( filter ) => {
		if ( filters[ filter.key ].allowMultiple ) {
			filter.instance = getInstanceNumber( filter.key, instanceCounts );
		}
		return filter;
	} );
};

function AdvancedFilters( props ) {
	const { filters, onFilterAction, query = {}, isDisabled } = props;
	// eslint-disable-next-line no-unused-vars
	const filterListRef = useRef();
	const instanceCounts = {};
	const filtersFromQuery = getActiveFiltersFromQuery( query, filters );

	const [ activeFilters, setActiveFilters ] = useState(
		getActiveFilters( filtersFromQuery, filters, instanceCounts )
	);

	const getAvailableFilterKeys = () => {
		const activeFilterKeys = activeFilters.map( ( f ) => f.key );
		const multipleValueFilterKeys = Object.keys( filters ).filter(
			( f ) => filters[ f ].allowMultiple || false
		);
		const inactiveFilterKeys = difference(
			Object.keys( filters ),
			activeFilterKeys,
			multipleValueFilterKeys
		);

		// Ensure filters that allow multiples are always present.
		return [ ...inactiveFilterKeys, ...multipleValueFilterKeys ];
	};

	const orderFilters = ( a, b ) => {
		const qs = window.location.search;
		const aPos = qs.indexOf( a.key );
		const bPos = qs.indexOf( b.key );
		// If either isn't in the url, it means its just been added, so leave it as is.
		if ( aPos === -1 || bPos === -1 ) {
			return 0;
		}
		// Otherwise use the url to determine order in which filter was added.
		return aPos - bPos;
	};

	const addFilter = ( key, onClose ) => {
		const filterConfig = filters[ key ];
		const newFilter = { key };
		if (
			Array.isArray( filterConfig.rules ) &&
			filterConfig.rules.length
		) {
			newFilter.rule = filterConfig.rules[ 0 ].value;
		}
		if ( filterConfig.input && filterConfig.input.options ) {
			newFilter.value = getDefaultOptionValue(
				filterConfig,
				filterConfig.input.options
			);
		}
		if ( filterConfig.input && filterConfig.input.component === 'Search' ) {
			newFilter.value = '';
		}
		if ( filterConfig.allowMultiple ) {
			newFilter.instance = getInstanceNumber( key, instanceCounts );
		}
		setActiveFilters( [ ...activeFilters, newFilter ] );
		onFilterAction( 'add', newFilter );
		onClose();
	};

	const removeFilter = ( index ) => {
		const newActiveFilters = activeFilters;
		onFilterAction( 'remove', activeFilters[ index ] );
		newActiveFilters.splice( index, 1 );
		setActiveFilters( () => [ ...newActiveFilters ] );
		if ( newActiveFilters.length === 0 ) {
			const history = getHistory();
			history.push( getUpdateHref( [] ) );
		}
	};

	const onFilterChange = ( index, property, value ) => {
		const newActiveFilters = [ ...activeFilters ];
		newActiveFilters[ index ] = {
			...newActiveFilters[ index ],
			[ property ]: value,
		};
		setActiveFilters( newActiveFilters );
	};

	const clearFilters = () => {
		onFilterAction( 'clear_all' );
		setActiveFilters( [] );
		const history = getHistory();
		history.push( getUpdateHref( [] ) );
	};

	// eslint-disable-next-line no-unused-vars
	const getUpdateHref = ( activeFilters ) => {
		const { query, filters } = props;
		const updatedQuery = getQueryFromActiveFilters(
			activeFilters,
			query,
			filters
		);

		return generatePath( { ...updatedQuery }, getPath(), query );
	};

	const onFilter = () => {
		const updatedQuery = getQueryFromActiveFilters(
			activeFilters,
			query,
			filters
		);
		onFilterAction( 'filter', { ...updatedQuery } );
	};

	if ( isEmpty( filters ) ) {
		return null;
	}

	const availableFilterKeys = getAvailableFilterKeys();
	const updateHref = getUpdateHref( activeFilters );
	const updateDisabled =
		'admin.php' + window.location.search === updateHref ||
		activeFilters.length === 0;
	return (
		<div className="ea-advanced-filters">
			<div className="ea-advanced-filters__container">
				<div className="ea-advanced-filters__title">
					{ __( 'Filters' ) }
				</div>
				<Card size="small">
					<CardBody size={ null }>
						<ul
							className="ea-advanced-filters__list"
							ref={ filterListRef }
						>
							{ !! activeFilters.length &&
								activeFilters
									.sort( orderFilters )
									.map( ( filter, idx ) => {
										const { instance, key } = filter;
										return (
											<FilterItem
												key={ key + ( instance || '' ) }
												filter={ filter }
												config={ filters[ key ] }
												onFilterChange={ partial(
													onFilterChange,
													idx
												) }
												query={ query }
												removeFilter={ () =>
													removeFilter( idx )
												}
											/>
										);
									} ) }

							{ !! availableFilterKeys.length && (
								<li className="ea-advanced-filters__list-item">
									<Dropdown
										className="ea-advanced-filters__add-filter-dropdown"
										position="bottom center"
										renderToggle={ ( {
											isOpen,
											onToggle,
										} ) => (
											<Button
												className="ea-advanced-filters__add-button"
												onClick={ onToggle }
												aria-expanded={ isOpen }
											>
												<Icon icon="insert" />
												&nbsp;
												{ __( 'Add Filter' ) }
											</Button>
										) }
										renderContent={ ( { onClose } ) => (
											<ul className="ea-advanced-filters__add-dropdown">
												{ availableFilterKeys.map(
													( key ) => (
														<li key={ key }>
															<Button
																onClick={ partial(
																	addFilter,
																	key,
																	onClose
																) }
															>
																{
																	filters[
																		key
																	].title
																}
															</Button>
														</li>
													)
												) }
											</ul>
										) }
									/>
								</li>
							) }
						</ul>
					</CardBody>
					<CardFooter
						className="ea-advanced-filters__footer"
						size="small"
						justify="flex-start"
					>
						{ ( isDisabled || updateDisabled ) && (
							<Button
								className="ea-advanced-filters__submit-button"
								isPrimary
								disabled
							>
								{ __( 'Filter' ) }
							</Button>
						) }
						{ ! updateDisabled && (
							<Link
								className="ea-advanced-filters__submit-button components-button is-primary is-button"
								href={ updateHref }
								onClick={ onFilter }
							>
								{ __( 'Filter' ) }
							</Link>
						) }
						{ !! activeFilters.length && (
							<Button
								className="ea-advanced-filters__clear-button"
								isLink
								onClick={ clearFilters }
							>
								{ __( 'Clear Filter' ) }
							</Button>
						) }
					</CardFooter>
				</Card>
			</div>
		</div>
	);
}

AdvancedFilters.propTypes = {
	// Title of the filter
	title: PropTypes.string,
	/**
	 * The filters object.
	 */
	filters: PropTypes.objectOf(
		PropTypes.shape( {
			title: PropTypes.string,
			mixedString: PropTypes.string,
			rules: PropTypes.arrayOf( PropTypes.object ),
			input: PropTypes.object,
		} )
	).isRequired,
	/**
	 * The query string represented in object form.
	 */
	query: PropTypes.object,
	/**
	 * Function to be called after an advanced filter action has been taken.
	 */
	onFilterAction: PropTypes.func,
};

AdvancedFilters.defaultProps = {
	query: {},
	onFilterAction: () => {},
};

export default AdvancedFilters;
