import {Component} from '@wordpress/element';
import {createHigherOrderComponent} from '@wordpress/compose';
import {xor, pickBy, isNumber, isEmpty} from "lodash";
import {COLLECTIONS_STORE_KEY, QUERY_STATE_STORE_KEY} from "@eaccounting/data";
import {withDispatch, withSelect} from '@wordpress/data';
import {compose} from '@wordpress/compose';
import {removeDefaultQueries} from "./utils";
import isShallowEqual from '@wordpress/is-shallow-equal';

function withTable(resourceName, defaultQuery = {orderby: 'id'}) {

	return createHigherOrderComponent((WrappedComponent) => {
		class Wrapper extends Component {
			constructor() {
				super(...arguments);
				this.state = {
					selected: [],
					total: 0,
				}
			}

			componentDidUpdate(prevProps) {
				if (!isNaN(this.props.total) && !isShallowEqual(this.state.total, this.props.total)) {
					this.setState({
						total: this.props.total
					});
				}
			}

			setQuery = (queryKey, queryValue) => {
				this.props.setQueryValue(resourceName, queryKey, queryValue)
			};

			onSelected = id => {
				this.setState({
					selected: xor(this.state.selected, [id])
				})
			};

			onAllSelected = onoff => {
				this.setState({
					selected: onoff ? this.props.items.map(item => item.id) : []
				})
			};

			setBulkAction = (action) => {
				this.props.remove( resourceName, this.props.selected);
			};

			setPageChange = (page) => {
				this.props.setQueryValue(resourceName, 'page', page)
			};

			setSearch = (search) => {
				this.props.setQueryValue(resourceName, 'search', search);
			};

			setOrderByOrder = (orderby, order) => {
				this.props.setQueryValue(resourceName, 'orderby', orderby);
				this.props.setQueryValue(resourceName, 'order', order);
			};

			setFilter = (filter) => {
				this.props.setValueForQueryContext(resourceName, Object.assign({}, {...this.props.query,page:1}, filter))
			};

			resetFilter = () => {

			};

			render() {
				const {total, selected} = this.state;
				return (<WrappedComponent
					{...this.props}
					total={total}
					selected={selected}
					onOrderBy={this.setOrderByOrder}
					onPageChange={this.setPageChange}
					onFilter={this.setFilter}
					onSearch={this.setSearch}
					onSelected={this.onSelected}
					onAllSelected={this.onAllSelected}
					onBulkAction={this.setBulkAction}
					setQuery={this.setQuery}/>);
			}
		}

		return compose([
			withSelect((select) => {
				const {getCollection, getCollectionHeader, getCollectionError, hasFinishedResolution} = select(COLLECTIONS_STORE_KEY);
				const {getValueForQueryContext} = select(QUERY_STATE_STORE_KEY);
				const query = removeDefaultQueries(pickBy({...defaultQuery, ...getValueForQueryContext(resourceName)}, value => isNumber(value) || !isEmpty(value)), defaultQuery.orderby);
				const args = [resourceName, query];
				let status = hasFinishedResolution('getCollection', args) !== true ? "STATUS_IN_PROGRESS" : "STATUS_COMPLETE";
				if (getCollectionError('getCollection', resourceName, query)) {
					status = "STATUS_FAILED"
				}
				const {page = 1} = query;

				return {
					items: getCollection(resourceName, query),
					total: parseInt(getCollectionHeader('x-wp-total',  resourceName, query), 10),
					status: status,
					page: page,
					query: query
				}
			}),

			withDispatch((dispatch) => {
				const {create, update, remove} = dispatch(COLLECTIONS_STORE_KEY);
				const {setQueryValue, setValueForQueryContext} = dispatch(QUERY_STATE_STORE_KEY);
				return {
					setValueForQueryContext,
					setQueryValue,
					create,
					update,
					remove
				};
			})
		])(Wrapper);
	}, 'withTable');
}

export default withTable;
