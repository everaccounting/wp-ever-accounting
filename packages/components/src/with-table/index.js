/**
 * WordPress dependencies
 */
import { Component } from '@wordpress/element';
import { createHigherOrderComponent } from '@wordpress/compose';
import {__} from "@wordpress/i18n";

/**
 * External dependencies
 */
import { xor, pickBy, isNumber, isEmpty } from 'lodash';
import { withDispatch, withSelect } from '@wordpress/data';
import { compose } from '@wordpress/compose';
/**
 * Internal dependencies
 */
import { removeDefaultQueries } from './utils';
import isShallowEqual from '@wordpress/is-shallow-equal';

function withTable(resourceName, defaultQuery = { orderby: 'id' }) {
	return createHigherOrderComponent(WrappedComponent => {
		class Wrapper extends Component {
			static _Mounted = false;

			constructor() {
				super(...arguments);
				this.state = {
					selected: [],
					total: 0,
				};
			}

			componentDidMount() {
				this._Mounted = true;
			}

			componentDidUpdate(prevProps) {
				if (!isNaN(this.props.total) && !isShallowEqual(this.state.total, this.props.total)) {
					this._Mounted && this.setState({
						total: this.props.total,
					});
				}
			}

			componentWillUnmount() {
				this._Mounted = false;
			}

			setQuery = (query) => {
				this.props.setQuery(resourceName, query);
			};

			onSelected = id => {
				this.setState({
					selected: xor(this.state.selected, [id]),
				});
			};

			onAllSelected = onoff => {
				this.setState({
					selected: onoff ? this.props.items.map(item => item.id) : [],
				});
			};

			setRemove = (id) => {
				if (!confirm(__('Are you sure you want to delete the items?'))) {
					return;
				}
				console.log(id);
				this.props.remove(resourceName, id);
			};


			setBulkAction = action => {
				this.setRemove(this.state.selected);
				this.setState({
					selected:[]
				});
			};
			
			setPageChange = page => {
				this.setQuery(resourceName, 'page', page);
			};

			setSearch = search => {
				this.props.setQuery(resourceName, 'search', search);
			};

			setOrderByOrder = (orderby, order) => {
				this.props.setQuery(resourceName, 'orderby', orderby);
				this.props.setQuery(resourceName, 'order', order);
			};

			setFilter = filter => {
				console.log(filter);
				this.props.setContextQuery(resourceName, Object.assign({}, { ...this.props.query, page: 1 }, filter));
			};

			resetFilter = () => {};

			render() {
				const { total, selected } = this.state;
				return (
					<WrappedComponent
						{...this.props}
						total={total}
						selected={selected}
						onOrderBy={this.setOrderByOrder}
						onPageChange={this.setPageChange}
						onFilter={this.setFilter}
						onSearch={this.setSearch}
						onRemove={this.setRemove}
						onSelected={this.onSelected}
						onAllSelected={this.onAllSelected}
						onBulkAction={this.setBulkAction}
						setQuery={this.setQuery}
					/>
				);
			}
		}

		return compose([
			withSelect(select => {
				const { getCollection, getTotal, getCollectionStatus, getQuery } = select('ea/store');
				const query = removeDefaultQueries(
					pickBy(
						{ ...defaultQuery, ...getQuery(resourceName) },
						value => isNumber(value) || !isEmpty(value)
					),
					defaultQuery.orderby
				)||{};
				const { page = 1 } = query;
				return {
					items: getCollection(resourceName, query),
					total: parseInt(getTotal(resourceName, query), 10),
					status:getCollectionStatus(resourceName, query),
					page:page,
					query:getQuery,
				};
			}),

			withDispatch(dispatch => {
				const {setQuery, setContextQuery, resetQuery,  create, update, remove, resetStore } = dispatch('ea/store');
				return {
					setContextQuery,
					setQuery,
					resetQuery,
					create,
					update,
					remove,
					resetStore
				};
			}),
		])(Wrapper);
	}, 'withTable');
}

export default withTable;
