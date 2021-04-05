import { __ } from '@wordpress/i18n';
import { useState, useEffect } from '@wordpress/element';
import { Button } from '@wordpress/components';
import { get, isEmpty, partial, pickBy } from 'lodash';
import PropTypes from 'prop-types';
import SelectFilter from './select-filter';
import TextFilter from './text-filter';

import './style.scss';
// import Select from '../select';

function TableFilter( { filters, onFilter, value = {} } ) {
	const [ applied, setApplied ] = useState( value );

	const setFilter = ( property, value ) => {
		setApplied( ( applied ) => ( { ...applied, [ property ]: value } ) );
	};

	const reset = () => {
		setApplied( {} );
	};

	useEffect( () => {
		const values = {};
		Object.keys( filters ).map( ( key, idx ) => {
			const filter = filters[ key ];
			const { transform = ( x ) => x } = filter;
			const value = applied[ key ] && transform( applied[ key ] );
			const { input } = filter;
			const { defaultVal } = input;
			values[ key ] = value || defaultVal;
		} );
		onFilter( values );
	}, [ applied ] );

	const flatten = ( values ) => {
		const FilterValue = {};
		Object.keys( filters ).map( ( key, idx ) => {
			const filter = filters[ key ];
			const { transform = ( x ) => x } = filter;
			FilterValue[ key ] = values[ key ] && transform( values[ key ] );
		} );
		return pickBy( FilterValue, ( value ) => ! isEmpty( value ) );
	};

	const componentMap = {
		SelectControl: SelectFilter,
		Select,
		TextControl: TextFilter,
	};

	return (
		<div className="ea-table-filter alignleft actions">
			{ Object.keys( filters ).map( ( key, idx ) => {
				const filter = filters[ key ];
				const { component, input } = filter;
				const FilterComponent = componentMap[ component ];
				const { defaultVal } = input;
				return (
					<FilterComponent
						key={ key + idx }
						className="ea-table-filter__item"
						filter={ {
							...filter,
							input: {
								...filter.input,
								value: get( applied, [ key ], defaultVal ),
							},
						} }
						onFilterChange={ partial( setFilter, key ) }
					/>
				);
			} ) }

			{ ! isEmpty( flatten( applied ) ) && (
				<Button onClick={ reset } isSecondary>
					{ __( 'Clear all filters', 'wp-ever-accounting' ) }
				</Button>
			) }
		</div>
	);
}

// export default class TableFilter extends Component {
//
// 	constructor({filters}) {
// 		super(...arguments);
// 		this.state = {
// 			activeFilters: {},
// 		}
// 		this.onFilterChange = this.onFilterChange.bind(this);
// 		this.clearFilters = this.clearFilters.bind(this);
// 	}
//
// 	componentDidUpdate() {
// 		const {activeFilters} = this.state;
// 		this.props.onFilter(this.flatten(activeFilters));
// 	}
//
// 	onFilterChange(property, value) {
// 		this.setState({activeFilters: {...this.state.activeFilters, [property]: value}}, this.props.onFilter(this.state));
// 	}
//
// 	clearFilters() {
// 		this.setState({
// 			activeFilters: {},
// 		});
// 		this.props.onFilter(this.state.activeFilters);
// 	}
//
// 	flatten(values) {
// 		const {filters} = this.props;
// 		const FilterValue = {};
//
// 		Object.keys(filters).map((key, idx) => {
// 			const filter = filters[key];
// 			const {transform = x => x} = filter;
// 			FilterValue[key]= values[key] && transform(values[key]);
// 		})
//
// 		return pickBy(FilterValue, value => !isEmpty(value));
// 	}
//
// 	render() {
// 		const {filters} = this.props;
// 		const {activeFilters} = this.state;
//
// 		const componentMap = {
// 			SelectControl: SelectFilter,
// 			TextControl: TextFilter,
// 		};
//
// 		return (
// 			<div className='ea-table-filter alignleft actions'>
// 				{Object.keys(filters).map((key, idx) => {
// 					const filter = filters[key];
// 					const {component, input} = filter;
// 					const FilterComponent = componentMap[component];
// 					const {defaultVal} = input;
// 					return (
// 						<FilterComponent
// 							key={key + idx}
// 							className="ea-table-filter__item"
// 							filter={{...filter, input: {...filter.input, value: get(activeFilters, [key], defaultVal)}}}
// 							onFilterChange={partial(this.onFilterChange, key)}
// 						/>
// 					)
// 				})}
//
// 				{!isEmpty(this.flatten(activeFilters)) && <Button onClick={this.clearFilters} isSecondary>{__('Clear all filters', 'woocommerce-admin')}</Button>}
// 			</div>
// 		)
// 	}
// }

TableFilter.propTypes = {
	filters: PropTypes.objectOf(
		PropTypes.shape( {
			input: PropTypes.object,
			component: PropTypes.string,
			transform: PropTypes.func,
		} )
	).isRequired,
	onFilter: PropTypes.func,
};

export default TableFilter;
